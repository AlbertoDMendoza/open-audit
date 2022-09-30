<?php
/**
#  Copyright 2022 Firstwave (www.firstwave.com)
#
#  This file is part of Open-AudIT.
#
#  Open-AudIT is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as published
#  by the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  Open-AudIT is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
#
#  You should have received a copy of the GNU Affero General Public License
#  along with Open-AudIT (most likely in a file named LICENSE).
#  If not, see <http://www.gnu.org/licenses/>
#
#  For further information on Open-AudIT or for a license other than AGPL please see
#  www.firstwave.com or email sales@firstwave.com
#
# *****************************************************************************
*
* PHP version 5.3.3
* 
* @category  Model
* @package   Users
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.2
* @link      http://www.open-audit.org
*/

/**
* Base Model Logon
*
* @access   public
* @category Model
* @package  Users
* @author   Mark Unwin <mark.unwin@firstwave.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class M_logon extends MY_Model
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
        $this->log = new stdClass();
        $this->log->status = 'reading data';
        $this->log->type = 'system';
    }

    /**
     * [logon description]
     * @return [type] [description]
     */
    public function logon()
    {
        // order of authenticating is against ldap_server(s) (if set), then against a local account with sha256.
        $CI = & get_instance();
        $CI->user = new stdClass();

        $log = new stdClass();
        $log->file = 'system';
        $log->controller = 'm_logon';
        $log->function = strtolower(__METHOD__);
        $log->collection = 'logon';
        $log->severity = 7;


        // get $username from $_POST
        $username = @$this->input->post('username');
        if (empty($username) && ! empty($_SERVER['HTTP_USERNAME'])) {
            $username = $_SERVER['HTTP_USERNAME'];
        }

        // get $password from $_POST
        // $password = @$this->input->post('password');
        // Do NOT use input->post as this will append a ; if the password contains an &
        // Changed on 2018-07-05, MU, OMK-5103
        $password = @$_POST['password'];
        if (empty($password) && ! empty($_SERVER['HTTP_PASSWORD'])) {
            $password = $_SERVER['HTTP_PASSWORD'];
        }


        // make sure we have a supplied username and password
        if (empty($username) OR empty($password)) {
            // incomplete credentials supplied
            log_error('ERR-0015', $CI->response->meta->collection . ':' . $CI->response->meta->action . ' Incomplete credentials');
            if ($CI->response->meta->format === 'json') {
                echo json_encode($CI->response);
                return false;
            } else {
                $CI->session->set_flashdata('error', 'Incomplete credentials (missing username or password).');
                return false;
            }
        }

        if (strpos($username, '@') !== false) {
            $temp = explode('@', $username);
            $user['username'] = $temp[0];
            $user['domain'] = $temp[1];
            $user['password'] = $password;
            $log->summary = 'Splitting username';
            $log->detail = "{$username} supplied, splitting and using {$user['username']}.";
            $log->status = 'reading data';
            $log->severity = 7;
            stdlog($log);
            $username = $user['username'];
            $domain = $user['domain'];
        } else {
            $user['username'] = $username;
            $user['domain'] = '';
            $user['password'] = $password;
        }

        // Roles
        if ($this->db->table_exists('roles')) {
            $roles_sql = '/* m_logon::logon */ ' . 'SELECT * FROM roles';
            $roles_query = $this->db->query($roles_sql);
            $roles = $roles_query->result();
        }
        if (empty($roles)) {
            $log->summary = 'Error retrieving roles';
            $log->detail = 'No Roles retrieved from database';
            $log->status = 'fail';
            $log->severity = 3;
            stdlog($log);
            $CI->session->set_flashdata('error', 'No Roles retrieved from database.');
            // return false;
        }

        // Orgs
        if ($this->db->table_exists('orgs')) {
            $orgs_sql = '/* m_logon::logon */ ' . 'SELECT * FROM orgs';
            $orgs_query = $this->db->query($orgs_sql);
            $orgs = $orgs_query->result();
        }
        if (empty($orgs)) {
            $log->summary = 'Error retrieving Orgs';
            $log->detail = 'No Orgs retrieved from database.';
            $log->status = 'fail';
            $log->severity = 3;
            stdlog($log);
            $CI->session->set_flashdata('error', 'No Orgs retrieved from database.');
            // return false;
        }

        // Auth against any configured LDAP servers
        if ($this->db->table_exists('ldap_servers')) {
            if ( ! empty($user['domain'])) {
                $sql = '/* m_logon::logon */ ' . 'SELECT * FROM ldap_servers WHERE domain LIKE ?';
                $data = array($user['domain']);
                $query = $this->db->query($sql, $data);
            } else {
                $sql = '/* m_logon::logon */ ' . 'SELECT * FROM ldap_servers';
                $query = $this->db->query($sql);
            }
            $ldap_servers = $query->result();
            if ( ! empty($ldap_servers)) {
                # Added so we do not fail when checking self-signed certificates
                # See here - https://community.opmantek.com/display/OA/Troubleshooting+LDAP+logins
                putenv('LDAPTLS_REQCERT=never');
                $log->summary = 'Retrieved LDAP Servers';
                $log->detail = count($ldap_servers) . ' LDAP servers retrieved from database.';
                $log->status = 'reading data';
                $log->severity = 7;
                stdlog($log);
                // We have configured ldap_servers - validate
                foreach ($ldap_servers as $ldap) {
                    if ($ldap->type !== 'active directory' && $ldap->type !== 'openldap') {
                        $log->summary = 'Invalid LDAP server type';
                        $log->detail = 'An invalid LDAP server type was supplied (' . $ldap->type . '), skipping.';
                        $log->status = 'fail';
                        $log->severity = 3;
                        stdlog($log);
                        $CI->session->set_flashdata('error', 'Invalid LDAP server type supplied (' . $ldap->type . '), skipping.');
                        continue;
                    }
                    // New for 3.3.0 - use_auth, by default set to 'y', but if a user changes this, skip.
                    if ( ! empty($ldap->use_auth) && $ldap->use_auth === 'n') {
                        continue;
                    }
                    ldap_set_option(null, LDAP_OPT_NETWORK_TIMEOUT, 5);
                    ldap_set_option(null, LDAP_OPT_DEBUG_LEVEL, 7);
                    $ldap->version = intval($ldap->version);
                    if (intval($ldap->version) === 2 OR intval($ldap->version) === 3) {
                        ldap_set_option(null, LDAP_OPT_PROTOCOL_VERSION, $ldap->version);
                    } else {
                        $CI->session->set_flashdata('error', 'Invalid LDAP version (' . $ldap->version . ')');
                        $log->summary = 'An invalid LDAP version was supplied (' . $ldap->version . '), skipping.';
                        $log->detail = 'Invalid LDAP version';
                        $log->status = 'fail';
                        $log->severity = 3;
                        stdlog($log);
                        continue;
                    }
                    if (count($ldap_servers) === 1) {
                        // We only have a single ldap_server. Add the domain to the username if not already present
                        if (empty($user['domain'])) {
                            $user['domain'] = $ldap->domain;
                            $domain = $ldap->domain;
                        }
                    }
                    $ldap_connect_string = '';
                    if ($ldap->secure === 'y') {
                        $ldap_connect_string = 'ldaps://' . $ldap->host . ':' . $ldap->port;
                    } else {
                        $ldap_connect_string = 'ldap://' . $ldap->host . ':' . $ldap->port;
                    }
                    if ($ldap_connection = @ldap_connect($ldap_connect_string)) {
                        $bind_string = '';
                        $bind_password = '';
                        if ($ldap->type === 'active directory') {
                            $bind_string = $user['username'] . '@' . $user['domain'];
                            $bind_password = $user['password'];
                            $bind = @ldap_bind($ldap_connection, $bind_string, $bind_password);
                        }
                        if ($ldap->type === 'openldap') {
                            $bind_string = str_replace('@username', $user['username'], $ldap->user_dn);
                            $bind_string = str_replace('@domain', $user['domain'], $bind_string) . ',' . $ldap->base_dn;
                            $bind_password = $user['password'];
                            $bind = @ldap_bind($ldap_connection, $bind_string, $bind_password);
                        }
                        if (empty($bind)) {
                            $error = (string)ldap_error($ldap_connection);
                            if ($error === 'Invalid credentials') {
                                $log->detail = 'Invalid user supplied credentials for LDAP server at ' . $ldap->host . ', skipping.';
                            } else if ($error === "Can't contact LDAP server") {
                                $log->detail = 'LDAP server could not be reached at ' . $ldap->host . ', skipping.';
                            } else {
                                $log->detail = 'Could not bind to LDAP server at ' . $ldap->host . ', skipping.';
                            }
                            $log->summary = (string)ldap_error($ldap_connection);
                            $log->status = 'fail';
                            $log->severity = 6;
                            stdlog($log);
                            $CI->session->set_flashdata('error', $log->detail . '.');
                            $CI->user->id = '';
                            continue;
                        } else {
                            $log->summary = 'Successful LDAP bind';
                            $log->detail = 'Successful bind using credentials for LDAP server at ' . $ldap->host . ': ' . (string)ldap_error($ldap_connection);
                            $log->status = 'success';
                            $log->severity = 7;
                            stdlog($log);
                        }
                        $ldap->dn_password = (string)simpleDecrypt($ldap->dn_password);
                        if ( ! empty($ldap->dn_account) && empty($ldap->dn_password)) {
                            $CI->session->set_flashdata('error', 'DN Account set, but no DN Password.');
                        }
                        if ( ! empty($ldap->dn_account) && ! empty($ldap->dn_password)) {
                            $bind_string = $ldap->dn_account;
                            $bind_password = $ldap->dn_password;
                            $bind = @ldap_bind($ldap_connection, $bind_string, $bind_password);
                            if (empty($bind)) {
                                $log->summary = 'Invalid LDAP DN';
                                $log->detail = 'Invalid DN supplied credentials for LDAP server at ' . $ldap->host . ', skipping: ' . (string)ldap_error($ldap_connection);
                                $log->status = 'fail';
                                $log->severity = 6;
                                stdlog($log);
                                $CI->session->set_flashdata('error', 'Invalid DN supplied credentials for LDAP server at ' . $ldap->host . ', skipping');
                            } else {
                                $log->summary = 'Bound to LDAP';
                                $log->detail = 'Bound to LDAP using supplied dn details: ' . $ldap->dn_account;
                                $log->status = 'success';
                                $log->severity = 7;
                                stdlog($log);
                                $CI->session->set_flashdata('error', 'Bound to LDAP using supplied dn details');
                            }
                        }
                        if ($ldap->type === 'active directory') {
                            $ldap->filter = '(samaccountname=' . $user['username'] . ')';
                        }
                        if ($ldap->type === 'openldap') {
                            $ldap->filter = '(' . $ldap->user_dn . ')';
                            $ldap->filter = str_replace('@username', $user['username'], $ldap->filter);
                            $ldap->filter = str_replace('@domain', $user['domain'], $ldap->filter);
                            $temp = explode(',', $ldap->user_dn);
                            for ($i=0; $i < count($temp); $i++) {
                                if (stripos($temp[$i], '@username') !== false) {
                                    $ldap->filter = '(' . str_replace('@username', $user['username'], $temp[$i]) . ')';
                                }
                            }
                        }

                        if (strtolower($ldap->use_roles) !== 'y') {
                            $sql = '/* m_logon::logon */' . " SELECT * FROM users WHERE name = ? AND active = 'y' LIMIT 1";
                            $data = array($username);
                            $query = $this->db->query($sql, $data);
                            $users = $query->result();
                            if (count($users) === 1) {
                                $userdata = array('user_id' => $users[0]->id, 'user_debug' => '');
                                $this->session->set_userdata($userdata);
                                $CI->user = $users[0];
                                return $users[0];
                            } else {
                                $log->summary = 'Cannot authorise user';
                                $log->detail = "User {$username} in LDAP {$ldap->name} but not in Open-AudIT and not using LDAP for roles. Trying next LDAP Server.";
                                $log->status = 'fail';
                                $log->severity = 6;
                                stdlog($log);
                                $CI->session->set_flashdata('error', "User {$username} in LDAP {$ldap->name} but not in Open-AudIT and not using LDAP for roles. Trying next LDAP Server.");
                                // Skip the rest of this ldap server.
                                // There may be other ldap server's we use for roles.
                                break;
                            }
                        }


                        $log->summary = 'LDAP filter';
                        $log->detail = (string)$ldap->filter;
                        $log->status = 'reading data';
                        $log->severity = 7;
                        stdlog($log);

                        $log->summary = 'LDAP BaseDN';
                        $log->detail = (string)$ldap->base_dn;
                        $log->status = 'reading data';
                        $log->severity = 7;
                        stdlog($log);

                        // Get the user details
                        if ($result = @ldap_search($ldap_connection, $ldap->base_dn, $ldap->filter)) {
                            $log->summary = 'LDAP found user';
                            $log->detail = "LDAP search successful for user {$user['username']} at {$ldap->host}, ldap_search(\$ldap_connection, '{\$ldap->base_dn}', '{\$ldap->filter}')";
                            $log->status = 'success';
                            $log->severity = 7;
                            stdlog($log);
                            //$CI->session->set_flashdata('error', "LDAP search successful for user " . $user['username'] . " at " . $ldap->host);
                            unset($user);
                            $user = new stdClass();
                            $user->name = $username;
                            $user_ldap_groups = '';
                            if ($entries = @ldap_get_entries($ldap_connection, $result)) {
                                $CI->session->set_flashdata('error', "LDAP entries retrieval successful for user {$username} at {$ldap->host}");
                                $log->summary = 'LDAP retrieved entries';
                                $log->detail = 'LDAP entries retrieval successful for user ' . $username . ' at ' . $ldap->host;
                                $log->status = 'success';
                                $log->severity = 7;
                                stdlog($log);
                                // NOTE - attribute order must match SQL schema order
                                $user->name = $username;
                                $user->org_id = intval($ldap->org_id);
                                $user->password = '';
                                $user->full_name = @(string)$entries[0]['givenname'][0] . ' ' . @(string)$entries[0]['sn'][0];
                                $user->email = @(string)$entries[0]['mail'][0];
                                $user->roles = array();
                                $user->orgs = array();
                                $user->lang = (string)$ldap->lang;
                                $user->active = 'y';
                                $user->ldap = '';
                                if ($ldap->type === 'active directory') {
                                    $user->ldap = @(string)$entries[0]['distinguishedname'][0];
                                }
                                if ($ldap->type === 'openldap') {
                                    $user->ldap = @(string)$entries[0]['dn'];
                                }
                                $user->type = 'user';
                                $user->dashboard_id = 1;
                                $user->devices_default_display_columns = '';
                                $user->access_token = '';
                                $user->edited_by = 'system';
                                $user->edited_by = 'system';
                                $user->uid = @(string)$entries[0]['uid'][0];
                            } else {
                                $log->summary = 'LDAP retrieve entries failed';
                                $log->detail = 'LDAP entries retrieval failed for user ' . $username . ' at ' . $ldap->host . ', ' . (string)ldap_error($ldap_connection);
                                $log->status = 'fail';
                                $log->severity = 6;
                                stdlog($log);
                                $CI->session->set_flashdata('error', $log->detail);
                                continue;
                            }
                        } else {
                            $log->summary = 'LDAP search failed';
                            $log->detail = 'LDAP search failed for user ' . $username . ' at ' . $ldap->host . ', ' . (string)ldap_error($ldap_connection);
                            $log->status = 'fail';
                            $log->severity = 5;
                            stdlog($log);
                            $CI->session->set_flashdata('error', $log->detail);
                            continue;
                        }
                        $log->detail = '';
                        // get the roles groups and match
                        $ad_users_groups = array();
                        if ($ldap->type === 'active directory') {
                            $log->summary = 'LDAP authorising user';
                            $log->detail = 'Checking AD group membership for ' . $user->name;
                            $log->status = 'reading data';
                            $log->severity = 7;
                            stdlog($log);
                            foreach ($roles as $role) {
                                if ( ! empty($role->ad_group)) {
                                    foreach ($entries[0]['memberof'] as $key => $group) {
                                        if (is_integer($key)) {
                                            $ad_users_groups[] = '<br />"' . $group . '"';
                                            if (strpos($group, $role->ad_group) !== false) {
                                                $user->roles[] = $role->name;
                                                $log->summary = 'LDAP group for Role hit';
                                                $log->detail = 'User ' . $user->name . ' is a member of LDAP group for Role ' . $role->ad_group;
                                                $log->status = 'reading data';
                                                $log->severity = 7;
                                                stdlog($log);
                                            }
                                        }
                                    }
                                } else {
                                    $log->summary = 'LDAP group for Role miss';
                                    $log->details = 'No AD group associated with role ' . $role->name . ', skipping.';
                                    $log->status = 'reading data';
                                    $log->severity = 5;
                                    stdlog($log);
                                }
                            }
                            foreach ($orgs as $org) {
                                if ( ! empty($org->ad_group)) {
                                    foreach ($entries[0]['memberof'] as $key => $group) {
                                        if (is_integer($key)) {
                                            if (strpos($group, $org->ad_group) !== false) {
                                                $user->orgs[] = intval($org->id);
                                                $log->summary = 'LDAP group for Org hit';
                                                $log->detail = "User {$user->name} is a member of LDAP group for Org {$org->ad_group}";
                                                $log->status = 'reading data';
                                                $log->severity = 7;
                                                stdlog($log);
                                            }
                                        }
                                    }
                                } else {
                                    $log->summary = 'LDAP group for Org miss';
                                    $log->details = "No AD group associated with org {$org->name}, skipping.";
                                    $log->status = 'reading data';
                                    $log->severity = 5;
                                    stdlog($log);
                                }
                            }
                        }
                        $ad_users_groups = array_unique($ad_users_groups);
                        $user_ldap_groups = implode(' ', $ad_users_groups);
                        $ad_users_groups = array();
                        if ($ldap->type === 'openldap') {
                            foreach ($roles as $role) {
                                if ( ! empty($role->ad_group)) {
                                    $ldap->filter = "(&(cn={$role->ad_group})({$ldap->user_membership_attribute}={$user->uid}))";
                                    if ($result = @ldap_search($ldap_connection, $ldap->base_dn, $ldap->filter)) {
                                        $entries = @ldap_get_entries($ldap_connection, $result);
                                        if ( ! empty($entries[0]['cn'][0])) {
                                            $user->roles[] = $role->name;
                                            $log->summary = 'LDAP search for role ' . $role->ad_group . ' succeeded, ' . $user->name . ' is in group.';
                                            $log->detail = $ldap->filter;
                                            $log->status = 'reading data';
                                            $log->severity = 7;
                                            stdlog($log);
                                        } else {
                                            $log->summary = 'LDAP search for role ' . $role->ad_group . ' succeeded, ' . $user->name . ' is NOT in group.';
                                            $log->detail = $ldap->filter;
                                            $log->status = 'reading data';
                                            $log->severity = 7;
                                            stdlog($log);
                                        }
                                    } else {
                                        $log->summary = "LDAP search failed for groups (roles) {$user->name} at {$ldap->host}";
                                        $log->detail = (string)ldap_error($ldap_connection);
                                        $log->status = 'fail';
                                        $log->severity = 5;
                                        stdlog($log);
                                    }
                                } else {
                                    $log->summary = "No AD group associated with role {$role->name}, skipping.";
                                    $log->details = json_encode($role);
                                    $log->status = 'reading data';
                                    $log->severity = 5;
                                    stdlog($log);
                                }
                            }
                            foreach ($orgs as $org) {
                                if ( ! empty($org->ad_group)) {
                                    $ldap->filter = "(&(cn={$org->ad_group})({$ldap->user_membership_attribute}={$user->uid}))";
                                    if ($result = ldap_search($ldap_connection, $ldap->base_dn, $ldap->filter)) {
                                        $entries = ldap_get_entries($ldap_connection, $result);
                                        if ( ! empty($entries[0]['cn'][0])) {
                                            $user->orgs[] = intval($org->id);
                                            $log->summary = "LDAP search for org {$org->ad_group} succeeded, {$user->name} is in group.";
                                            $log->detail = $ldap->filter;
                                            $log->status = 'reading data';
                                            $log->severity = 7;
                                            stdlog($log);
                                        } else {
                                            $log->summary = "LDAP search for org {$org->ad_group} succeeded, {$user->name} is NOT in group.";
                                            $log->detail = $ldap->filter;
                                            $log->status = 'reading data';
                                            $log->severity = 7;
                                            stdlog($log);
                                        }
                                    } else {
                                        $log->summary = "LDAP search failed for groups (orgs) {$user->name} at {$ldap->host}";
                                        $log->detail = (string)ldap_error($ldap_connection);
                                        $log->status = 'fail';
                                        $log->severity = 5;
                                        stdlog($log);
                                    }
                                } else {
                                    $log->summary = "No AD group associated with org {$org->name}, skipping.";
                                    $log->details = json_encode($org);
                                    $log->status = 'reading data';
                                    $log->severity = 5;
                                    stdlog($log);
                                }
                            }
                        }

                        if ( ! empty($user->roles) && ! empty($user->orgs)) {
                            $user->roles = json_encode($user->roles);
                            $user->orgs = json_encode($user->orgs);
                            if ($this->db->table_exists('users')) {
                                $user_sql = '/* m_logon::logon */' . "SELECT * FROM users WHERE name = ? and ldap = ? and active = 'y' LIMIT 1";
                            } else {
                                $user_sql = '/* m_logon::logon */' . "SELECT * FROM oa_user WHERE name = ? and ldap = ? and active = 'y' LIMIT 1";
                            }
                            $user_data = array((string)$user->name, (string)$user->ldap);
                            $user_query = $this->db->query($user_sql, $user_data);
                            $user_result = $user_query->result();
                            if (count($user_result) === 0) {
                                // The user does not exist, insert
                                $log->summary = 'User logged on';
                                $log->detail = "New user {$username} logged on (AD account), " . json_encode($user);
                                $log->status = 'success';
                                $log->severity = 5;
                                stdlog($log);
                                if ($this->db->table_exists('users')) {
                                    $user_sql = '/* m_logon::logon */ ' . "INSERT INTO users VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                                } else {
                                    $user_sql = '/* m_logon::logon */ ' . 'INSERT INTO oa_user VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
                                }

                                $user_query = $this->db->query($user_sql, (array)$user);
                                $user->id = $this->db->insert_id();
                            } else {
                                // The user exists, update
                                $log->summary = 'User logged on';
                                $log->detail = "Existing user {$username} logged on (AD account).";
                                $log->status = 'success';
                                $log->severity = 5;
                                stdlog($log);
                                if ($this->db->table_exists('users')) {
                                    $user_sql = '/* m_logon::logon */ ' . 'UPDATE users SET full_name = ?, email = ?, orgs = ?, roles = ?, ldap = ? WHERE id = ?';
                                } else {
                                    $user_sql = '/* m_logon::logon */ ' . 'UPDATE oa_user SET full_name = ?, email = ?, orgs = ?, roles = ?, ldap = ? WHERE id = ?';
                                }
                                $user_data = array($user->full_name,
                                                    $user->email,
                                                    $user->orgs,
                                                    $user->roles,
                                                    $user->ldap,
                                                    $user_result[0]->id);
                                $this->db->query($user_sql, $user_data);
                                $user->id = $user_result[0]->id;
                            }
                            $CI->user = $user;
                            $userdata = array('user_id' => $CI->user->id, 'user_debug' => '');
                            $this->session->set_userdata($userdata);
                            return $user;
                        } else {
                            if (empty($user->roles) && empty($user->orgs)) {
                                // The user exists in AD, but has no Open-AudIT roles or Organisations
                                $log->summary = 'User has no roles and no orgs';
                                $log->detail = "User {$username} exists in LDAP ({$ldap->name}) and attempted to logon, but does not belong to any OA groups for Roles or Organisations.";
                                if ($ldap->type === 'active directory') {
                                    $log->detail .= " Users AD groups are: {$user_ldap_groups}";
                                }
                                $log->status = 'fail';
                                $log->severity = 5;
                                stdlog($log);
                                $CI->session->set_flashdata('error', $log->message);
                            } else if (empty($user->orgs)) {
                                // The user exists in AD, but has no Open-AudIT Organisations
                                $log->summary = 'User has no orgs';
                                $log->detail = "User {$username} exists in LDAP ({$ldap->name}) and attempted to logon, but does not belong to any OA groups for Organisations.";
                                if ($ldap->type === 'active directory') {
                                    $log->detail .= " Users AD groups are: {$user_ldap_groups}";
                                }
                                $log->status = 'fail';
                                $log->severity = 5;
                                stdlog($log);
                                $CI->session->set_flashdata('error', $log->message);
                            } else if (empty($user->roles)) {
                                // The user exists in AD, but has no Open-AudIT roles
                                $log->summary = 'User has no roles';
                                $log->detail = "User {$username} exists in LDAP ({$ldap->name}) and attempted to logon, but does not belong to any OA groups for Roles.";
                                if ($ldap->type === 'active directory') {
                                    $log->detail .= " Users AD groups are: {$user_ldap_groups}";
                                }
                                $log->status = 'fail';
                                $log->severity = 5;
                                stdlog($log);
                                $CI->session->set_flashdata('error', $log->message);
                            }
                        }
                    } else {
                        // ERROR - could not connect to LDAP / AD server
                        $log->summary = 'LDAP connect failed';
                        $log->detail = "LDAP connect failed for LDAP server at {$ldap->host}. Check your host, port and secure settings. Attempted to use {$ldap_connect_string}, " . (string)ldap_error($ldap_connection);
                        $log->status = 'fail';
                        $log->severity = 5;
                        stdlog($log);
                        $CI->session->set_flashdata('error', "LDAP connect failed for LDAP server at {$ldap->host}. Check your host, port and secure settings. Attempted to use {$ldap_connect_string}");
                        continue;
                    }
                }
            }
        }

        // Check for a local account
        if ($this->db->table_exists('users')) {
            $sql = '/* m_logon::logon */ ' . "SELECT * FROM users WHERE name = ? and active = 'y'";
        } elseif ($this->db->field_exists('user_name', 'oa_user')) {
            $sql = '/* m_logon::logon */ ' . 'SELECT `user_id` AS `id`, `user_name` AS `name`, `user_password` AS `password` FROM oa_user WHERE user_name = ?';
        } else {
            $sql = '/* m_logon::logon */ ' . "SELECT * FROM oa_user WHERE name = ? and active = 'y'";
        }
        $data = array($username);
        $query = $this->db->query($sql, $data);
        $result = $query->result();
        if (count($result) > 0) {
            set_include_path($CI->config->config['base_path'] . '/code_igniter/application/third_party/sodium_compat');
            require_once 'autoload.php';
            foreach ($result as $db_user) {
                // get the salt from the front of the hash
                $salt = substr($db_user->password, 0, 64);
                // the SHA256 form the end of the hash
                $valid_hash = substr($db_user->password, 64, 64);
                // hash the password being tested
                $test_hash = hash('sha256', $salt.$password);
                // if the hashes are exactly the same, the password is valid
                if ($test_hash === $valid_hash) {
                    $log->summary = 'User logged on';
                    $log->detail = "Existing user {$username} logged on (local account).";
                    $log->status = 'success';
                    $log->severity = 5;
                    stdlog($log);
                    $CI->user = $db_user;
                    $userdata = array('user_id' => $CI->user->id, 'user_debug' => '');
                    $this->session->set_userdata($userdata);
                    return $db_user;
                }
            }
        }
        $log->summary = 'Invalid logon attempt.';
        $log->detail = "Could not authenticate and/or authorise user {$username} from IP {$_SERVER['REMOTE_ADDR']}";
        $log->status = 'HTTP/1.1 401 Unauthorized';
        $log->severity = 5;
        stdlog($log);
        sleep(5);
        $CI->session->set_flashdata('error', 'Invalid credentials.');
        return false;
    }
}
// End of file m_logon.php
// Location: ./models/m_logon.php

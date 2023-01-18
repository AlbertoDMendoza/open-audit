<?php
#
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

/**
* @category  Controller
* @package   Open-AudIT
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.4.1
* @link      http://www.open-audit.org
 */

/**
 * Base Object MY_Controller.
 *
 * @access	 public
 *
 * @category Object
 *
 * @author   Mark Unwin <mark.unwin@firstwave.com>
 * @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
 *
 * @link     http://www.open-audit.org
 *
 * @return	 Admin
 */
class MY_Controller extends CI_Controller
{
    /**
     * The custom default controller object.
     */
    public function __construct()
    {
        parent::__construct();

        $timer_start = microtime(true);
        $GLOBALS['timer_start'] = $timer_start;
        $GLOBALS['timer_log'] = array();

        $entry = new stdClass();
        $entry->time = 0;
        $entry->detail = 'my_controller::construct';
        $entry->time_now = time();
        $GLOBALS['timer_log'][] = $entry;

        // ensure our URL doesn't have a trailing / as this may break image (and other) relative paths
        $this->load->helper('url');
        if ( ! empty($_SERVER['REQUEST_URI'])) {
            if (strrpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) {
                redirect(uri_string());
            }
        }
        $this->load->helper('log');
        $log = new stdClass();
        $log->status = 'start';
        $log->function = 'MY_Controller::__construct';
        stdlog($log);
        $this->benchmark->mark('code_start');
        $this->load->library('session');
        $this->load->model('m_configuration');
        // $this->m_configuration->load();
        $this->load->model('m_users');
        // $this->m_users->validate();
        $this->load->helper('response');
        $this->load->helper('input');
        $this->load->helper('output');
        $this->load->helper('error');
        $this->load->helper('json');
        $this->load->helper('security');
        $this->load->helper('collections');
        $this->load->model('m_orgs');
        $timer_end = microtime(true);
        $entry = new stdClass();
        $entry->time = ($timer_end - $timer_start);
        $entry->detail = 'my_controller::Load models and helpers.';
        $entry->time_now = time();
        $GLOBALS['timer_log'][] = $entry;
        $timer_start = microtime(true);
        $this->m_configuration->load();
        $timer_end = microtime(true);
        $entry = new stdClass();
        $entry->time = ($timer_end - $timer_start);
        $entry->detail = 'my_controller::Load configuration.';
        $entry->time_now = time();
        $GLOBALS['timer_log'][] = $entry;
        $timer_start = microtime(true);
        $this->m_users->validate();
        $timer_end = microtime(true);
        $entry = new stdClass();
        $entry->time = ($timer_end - $timer_start);
        $entry->detail = 'my_controller::Validate User.';
        $entry->time_now = time();
        $GLOBALS['timer_log'][] = $entry;
        $timer_start = microtime(true);
        set_time_limit(600);
        // For any Orgs I have permission on, get their descendants
        $this->user->org_list = implode(',', $this->m_users->get_orgs($this->user->id));
        // For my users.org_id, get it's ascendants and store in user->org_parents
        if ( ! empty($this->user->org_id)) {
            $this->user->org_parents = implode(',', $this->m_users->get_parent_orgs($this->user->org_id));
        }
        if ( ! empty($this->user->roles) && $this->user->roles !== 'null') {
            $this->user->roles = json_decode($this->user->roles);
        } else {
            if ($this->config->config['internal_version'] < 20160904) {
                $this->user->roles = array('admin', 'org_admin');
            } else {
                $log = new stdClass();
                $log->severity = 4;
                $log->file = 'system';
                $log->summary = 'Could not determine roles for user.';
                stdlog($log);
                $this->session->unset_userdata('user_id');
                $this->session->set_flashdata('error', 'Could not determine roles for user.');
                redirect('logon');
            }
        }
        if ( ! empty($this->user->orgs)) {
            $this->user->orgs = json_decode($this->user->orgs);
        } else {
            if ($this->config->config['internal_version'] < 20160904) {
                $this->user->orgs = array(0);
            } else {
                $log = new stdClass();
                $log->severity = 4;
                $log->file = 'system';
                $log->summary = 'Could not determine orgs for user.';
                stdlog($log);
            }
        }

        $this->temp_dictionary = new stdClass();
        $this->temp_dictionary->link = 'For more detailed information, check the Open-AudIT <a href="https://community.opmantek.com/display/OA/$collection">Knowledge Base</a>.';
        $this->temp_dictionary->purchase_link = '<strong>To upgrade to an Enterprise License, click <a href="#" id="buy_more_licenses" data-toggle="modal" data-target="#myModalLicense">HERE</a>.</strong>';
        $this->temp_dictionary->purchase_link = '<strong>To upgrade to an Enterprise License, click <a href="#" class="buy_more_licenses">here</a>.</strong>';
        $this->temp_dictionary->id = 'The internal identifier column in the database (read only).';
        $this->temp_dictionary->name = 'The name given to this item. Ideally it should be unique.';
        $this->temp_dictionary->org_id = 'The Organisation that owns this item. Links to <code>orgs.id</code>.';
        $this->temp_dictionary->description = 'Your description of this item.';
        $this->temp_dictionary->options = 'A JSON object containing collection specific options.';
        $this->temp_dictionary->edited_by = 'The name of the user who last changed or added this item (read only).';
        $this->temp_dictionary->edited_date = 'The date this item was changed or added (read only). NOTE - This is the timestamp from the server.';
        $this->temp_dictionary->system_id = 'The id of the linked device. Links to <code>system.id</code>';

        $timer_end = microtime(true);
        $entry = new stdClass();
        $entry->time = ($timer_end - $timer_start);
        $entry->detail = 'my_controller::Load user details.';
        $entry->time_now = time();
        $GLOBALS['timer_log'][] = $entry;
    }

    public function log_access()
    {
        $log = new stdClass();
        $log->object = $this->response->meta->collection;
        $log->function = strtolower($this->response->meta->collection) . '::' . strtolower($this->response->meta->action);
        $log->severity = 6;
        $log->status = 'success';
        $log->summary = 'finish';
        $log->type = 'access';
        if ($this->config->config['log_level'] == 7) {
            $log->detail = json_encode($this->response->meta);
        }
        stdLog($log);
    }
}
// End of file MY_Controller.php
// Location: ./core/MY_Controller.php

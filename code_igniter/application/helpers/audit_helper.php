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
* @category  Helper
* @package   Open-AudIT
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.4
* @link      http://www.open-audit.org
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


# Return FALSE on error, or an object if the input can be converted
# from JSON or XML
if (!function_exists('accept_input')) {
    function accept_input($input = '')
    {
        if (empty($input)) {
            return false;
        }
        if (is_string($input)) {
            $json = html_entity_decode($input);
            if (mb_detect_encoding($json) !== 'UTF-8') {
                $json = utf8_encode($json);
            }
            $json = @json_decode($json);
            if ($json) {
                unset($input);
                return $json;
            }
        }
        if (is_string($input)) {
            $xml = html_entity_decode($input);
            if (mb_detect_encoding($xml) !== 'UTF-8') {
                $xml = utf8_encode($xml);
            }
            $xml = iconv('UTF-8', 'UTF-8//TRANSLIT', $xml);
            $xml = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $xml);
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string($xml);
            if ($xml !== false) {
                unset($input);
                return $xml;
            }
        }
        return false;
    }
}







if (!function_exists('audit_convert')) {
    function audit_convert($parameters)
    {
        if (empty($parameters) OR empty($parameters->input)) {
            $mylog = new stdClass();
            $mylog->severity = 4;
            $mylog->status = 'fail';
            $mylog->message = 'Function audit_convert called without correct params object';
            $mylog->file = 'audit_helper';
            $mylog->function = 'audit_convert';
            stdlog($mylog);
            return false;
        }

        $input = $parameters->input;
        if (empty($parameters->log)) {
            $log = new stdClass();
            if ( ! empty($parameters->discovery_log)) {
                $log->discovery_log = $parameters->discovery_log;
            }
            if ( ! empty($parameters->ip)) {
                $log->ip = $parameters->ip;
            }
        } else {
            $log = $parameters->log;
        }
        $log->discovery_id = @$parameters->discovery_id;
        $log->ip = @ip_address_from_db($log->ip);
        $log->file = 'audit_helper';
        $log->function = 'audit_convert';
        $log->command = '';

        if (is_string($input)) {
            // See if we have stringified JSON
            $json = html_entity_decode($input);
            if (mb_detect_encoding($json) !== 'UTF-8') {
                $json = utf8_encode($json);
            }
            $json = @json_decode($json);
            if ($json) {
                $audit = new stdClass();
                if ( ! empty($json->sys)) {
                    $audit->system = $json->sys;
                    unset($json->sys);
                }
                if ( ! empty($json->system)) {
                    $audit->system = $json->system;
                    unset($json->system);
                }
                foreach ($audit->system as $key => $value) {
                    if (empty($value)) {
                        unset($audit->system->{$key});
                    }
                }
                foreach ($json as $section => $something) {
                    $audit->{$section} = array();
                    if ( ! empty($json->{$section}->item) and is_array($json->{$section}->item)) {
                        $audit->{$section}[] = $json->{$section}->item[0];
                    } else {
                        if (is_array($json->{$section})) {
                            $audit->{$section} = $json->{$section};
                        }
                    }
                }
                foreach ($audit as $section => $something) {
                    if ($section !== 'system' && $section !== 'sys') {
                        for ($i=0; $i < count($audit->{$section}); $i++) {
                            if ( ! empty($audit->{$section}[$i])) {
                                foreach ($audit->{$section}[$i] as $key => $value) {
                                    if (empty($value)) {
                                        unset ($audit->{$section}[$i]->{$key});
                                    }
                                }
                            }
                        }
                    }
                }
                unset($input);
                $log->message = 'string converted from JSON';
                $input = $audit;
            }
        }

        if (is_string($input)) {
            // See if we have stringified XML
            $xml = html_entity_decode($input);
            if (mb_detect_encoding($xml) !== 'UTF-8') {
                $xml = utf8_encode($xml);
            }
            $xml = iconv('UTF-8', 'UTF-8//TRANSLIT', $xml);
            $xml = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $xml);
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string($xml);
            if ($xml === false) {
                foreach (libxml_get_errors() as $error) {
                    $log->message = 'Could not convert string to XML';
                    $log->command_status = 'fail';
                    $log->command_output = $error->message . ' at ' . $error->line . ', column ' . $error->column . ', with code ' . $error->code;
                    discovery_log($log);
                }
                return false;
            }
            if ( ! empty($xml)) {
                $newxml = json_encode($xml);
                $newxml = json_decode($newxml);
                $audit = new stdClass();
                $audit->system = new stdClass();
                if ( ! empty($newxml->sys)) {
                    foreach ($newxml->sys as $key => $value) {
                        if (gettype($value) !== "object" && @(string)$value !== '') {
                            $audit->system->{$key} = @(string)$newxml->sys->{$key};
                        }
                    }
                }
                if ( ! empty($newxml->system)) {
                    foreach ($newxml->system as $key => $value) {
                        if (gettype($value) !== "object" && @(string)$value !== '') {
                            $audit->system->{$key} = @(string)$newxml->system->{$key};
                        }
                    }
                }
                unset($newxml);
                foreach ($xml as $section => $something) {
                    if ($section !== 'sys') {
                        $audit->{$section} = array();
                        foreach ($xml->{$section}->item as $item) {
                            $newitem = new stdClass();
                            foreach ($item as $key => $value) {
                                if ($key === 'options' && $section === 'policy') {
                                    $json = @json_decode($value);
                                    if ( ! empty($json)) {
                                        $values = $json;
                                    } else {
                                        $values = $value;
                                    }
                                    $new = new stdClass();
                                    foreach ($values as $k => $v) {
                                        $new->{$k} = (string) $v;
                                    }
                                    $newitem->options = @json_encode($new);

                                } else if ($key === 'keys' && $section === 'user') {
                                    $new = array();
                                    foreach ($value->key as $k => $v) {
                                        $new[] = (string)$v;
                                    }
                                    $newitem->keys = @json_encode($new);
                                } else {
                                    if ((string)$value !== '') {
                                        $newitem->{$key} = (string)$value;
                                    }
                                }
                            }
                            $audit->{$section}[] = $newitem;
                        }
                    }
                }
                unset($input);
                $input = $audit;
            }
        }

        if (is_string($input)) {
            // We have a string that could not be converted
            $log->severity = 5;
            if ( ! empty($parameters->discovery_id)) {
                $log->message = 'Could not convert string to JSON or XML';
                $log->command_status = 'fail';
                discovery_log($log);
            } else {
                $log->summary = 'Could not convert string to JSON or XML';
                $log->status = 'fail';
                stdlog($log);
            }
            return false;
        } else {
            if ( ! empty($audit->system->discovery_id)) {
                $log->discovery_id = intval($audit->system->discovery_id);
            }
            if ( ! empty($audit->system->id)) {
                $log->system_id = intval($audit->system->id);
            }
            if ( ! empty($audit->system->ip) && empty($log->ip)) {
                $log->ip = $audit->system->ip;
            }
        }

        $log->severity = 7;
        $log->message = 'Audit converted';
        if ( ! empty($log->discovery_id)) {
            $log->command_status = 'success';
            discovery_log($log);
        } else {
            $log->status = 'success';
            stdlog($log);
        }
        return $input;
    }
}





if ( ! function_exists('audit_format_system')) {
    /**
     * [audit_format_system description]
     * @param  object $parameters [description]
     * @return object             [description]
     */
    function audit_format_system($parameters)
    {
        $CI =& get_instance();

        if ( ! empty($parameters->log)) {
            $log = $parameters->log;
        } else {
            $log = new stdClass();
        }

        if ( ! empty($parameters->discovery_id)) {
            $log->discovery_id = $parameters->discovery_id;
        } else if ( ! empty($parameters->input->discovery_id)) {
            $log->discovery_id = $parameters->input->discovery_id;
        }

        if ( ! empty($parameters->ip)) {
            $log->ip = ip_address_from_db($parameters->ip);
        } else if ( ! empty($parameters->input->ip)) {
            $log->ip = ip_address_from_db($parameters->input->ip);
        }

        $log->message = 'Formatting system details';
        $log->file = 'audit_helper';
        $log->function = 'audit_format_system';
        $log->command_ouput = '';
        $log->command_status = 'notice';

        if (empty($parameters)) {
            $log->severity = 4;
            $log->message = 'Function audit_format_system called without parameters object.';
            $log->status = 'fail';
            if ( ! empty($log->discovery_id)) {
                discovery_log($log);
            } else {
                stdlog($log);
            }
            return false;
        }

        if (empty($parameters->input)) {
            $log->severity = 4;
            $log->message = 'Function audit_format_system called without parameters->input.';
            $log->status = 'fail';
            if ( ! empty($log->discovery_id)) {
                discovery_log($log);
            } else {
                stdlog($log);
            }
            return false;
        } else {
            $input = @$parameters->input;
        }


        if (empty($input->id)) {
            $input->id = '';
        } else {
            $sql = 'SELECT `status` FROM system WHERE id = ?';
            $data = array(intval($input->id));
            $query = $CI->db->query($sql, $data);
            $result = $query->result();
            $log->system_id = intval($input->id);
            if (empty($result[0]->status) OR $result[0]->status === 'deleted') {
                $log->message = 'Removing supplied system ID (' . intval($input->id) . ') as the device is in a deleted status.';
                $log->ip = @$input->ip;
                $log->command_status = 'fail';
                $log->severity = 4;
                if ( ! empty($log->discovery_id)) {
                    discovery_log($log);
                } else {
                    stdlog($log);
                }
                $input->id = '';
            }
        }

        $input->audits_ip = '127.000.000.001';
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $input->audits_ip = ip_address_to_db($_SERVER['REMOTE_ADDR']);
        }

        if (empty($input->discovery_id)) {
            $input->discovery_id = '';
        }

        if (empty($input->domain)) {
            $input->domain = '';
        }

        if (empty($input->fqdn)) {
            $input->fqdn = '';
        }

        if (empty($input->hostname)) {
            $input->hostname = '';
        }

        if (empty($input->last_seen)) {
            $input->last_seen = $CI->config->config['timestamp'];
        }

        if (empty($input->timestamp)) {
            $input->timestamp = $CI->config->config['timestamp'];
        }

        if ( ! empty($input->type)) {
            $input->type = strtolower($input->type);
        }

        if (empty($input->uuid)) {
            $input->uuid = '';
        }

        if (empty($input->vm_uuid)) {
            $input->vm_uuid = '';
        }

        // This is set by m_device::insert or update.
        unset($input->icon);

        if ( ! filter_var($input->hostname, FILTER_VALIDATE_IP)) {
            if (strpos($input->hostname, '.') !== false) {
                // we have a fqdn in the hostname field
                if (empty($input->fqdn)) {
                    $input->fqdn = $input->hostname;
                }
                $temp = explode('.', $input->hostname);
                $input->hostname = $temp[0];
                unset($temp[0]);
                if (empty($input->domain)) {
                    $input->domain = implode('.', $temp);
                }
                unset($temp);
                $log->message = 'FQDN supplied in hostname, converting.';
                $log->command_output = 'Hostname: ' . $input->hostname . ' Domain: ' .  $input->domain;
                if ( ! empty($log->discovery_id)) {
                    discovery_log($log);
                } else {
                    stdlog($log);
                }
            }
        }

        if (filter_var($input->hostname, FILTER_VALIDATE_IP)) {
            // we have an ip address in the hostname field
            if (empty($input->ip)) {
                $input->ip = $input->hostname;
                $log->message = 'IP supplied in hostname, setting device IP.';
                $log->command_output = 'IP: ' . $input->ip;
                if ( ! empty($log->discovery_id)) {
                    discovery_log($log);
                } else {
                    stdlog($log);
                }
            }
            $input->hostname = '';
        }

        $log->command_output = '';

        if (empty($input->fqdn) && ! empty($input->hostname) && ! empty($input->domain)) {
            $input->fqdn = $input->hostname . '.' . $input->domain;
            $log->message = 'No FQDN, but hostname and domain supplied, setting FQDN.';
            if ( ! empty($log->discovery_id)) {
                discovery_log($log);
            } else {
                stdlog($log);
            }
        }

        if (isset($input->os_name)) {
            $input->os_name = str_ireplace('(r)', '', $input->os_name);
            $input->os_name = str_ireplace('(tm)', '', $input->os_name);
        }

        if (empty($input->ip) OR $input->ip === '0.0.0.0' OR $input->ip === '000.000.000.000') {
            unset($input->ip);
        }

        if ( ! empty($input->ip) && filter_var($input->ip, FILTER_VALIDATE_IP)) {
            $input->ip = ip_address_to_db($input->ip);
        }

        if ( ! empty($input->mac_address)) {
            $input->mac_address = strtolower($input->mac_address);
            if ($input->mac_address === '00:00:00:00:00:00') {
                unset($input->mac_address);
            }
        }

        // because Windows doesn't supply an identical UUID, but it does supply the required digits, make a UUID from the serial
        if ( ! empty($input->uuid) && ! empty($input->serial) && stripos($input->serial, 'vmware-') !== false && ! empty($input->os_name) && stripos($input->os_name, 'windows') !== false) {
            // serial is taken from Win32_ComputerSystemProduct.IdentifyingNumber
            // Vmware supplies - 564d3739-b4cb-1a7e-fbb1-b10dcc0335e1
            // audit_windows supples - VMware-56 4d 37 39 b4 cb 1a 7e-fb b1 b1 0d cc 03 35 e1
            $log->command_output = $input->serial;
            $input->vm_uuid = str_ireplace('VMware-', '', $input->serial);
            $input->vm_uuid = str_ireplace('-', ' ', $input->vm_uuid);
            $input->vm_uuid = strtolower($input->vm_uuid);
            $input->vm_uuid = str_ireplace(' ', '', $input->vm_uuid);
            $input->vm_uuid = substr($input->vm_uuid, 0, 8) . '-'. substr($input->vm_uuid, 8, 4) . '-' . substr($input->vm_uuid, 12, 4) . '-' . substr($input->vm_uuid, 16, 4) . '-' . substr($input->vm_uuid, 20, 12);
            $log->message = 'Windows VMware style serial detected, creating vm_uuid.';
            $log->command_output .= ' -> ' . $input->vm_uuid;
            if ( ! empty($log->discovery_id)) {
                discovery_log($log);
            } else {
                stdlog($log);
            }
            $log->command_output = '';
        }

        if ( ! empty($input->uuid) && empty($input->vm_uuid)) {
            $input->vm_uuid = $input->uuid;
        }

        return $input;
    }
}

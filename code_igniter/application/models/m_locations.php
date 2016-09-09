<?php
#  Copyright 2003-2015 Opmantek Limited (www.opmantek.com)
#
#  ALL CODE MODIFICATIONS MUST BE SENT TO CODE@OPMANTEK.COM
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
#  www.opmantek.com or email contact@opmantek.com
#
# *****************************************************************************

/**
 * @author Mark Unwin <marku@opmantek.com>
 *
 * 
 * @version 1.12.8
 *
 * @copyright Copyright (c) 2014, Opmantek
 * @license http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
 */
class M_locations extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create()
    {
        $CI = & get_instance();
        if (empty($CI->response->meta->received_data->attributes->name)) {
            return false;
        } else {
            $name = $CI->response->meta->received_data->attributes->name;
        }
        if (empty($CI->response->meta->received_data->attributes->org_id)) {
            $CI->response->meta->received_data->attributes->org_id = 0;
        }
        $data = array((string)$CI->response->meta->received_data->attributes->org_id, 
                        (string)$name,
                        (string)$CI->response->meta->received_data->attributes->type,
                        (string)$CI->response->meta->received_data->attributes->room,
                        (string)$CI->response->meta->received_data->attributes->suite,
                        (string)$CI->response->meta->received_data->attributes->level,
                        (string)$CI->response->meta->received_data->attributes->address,
                        (string)$CI->response->meta->received_data->attributes->city,
                        (string)$CI->response->meta->received_data->attributes->state,
                        (string)$CI->response->meta->received_data->attributes->postcode,
                        (string)$CI->response->meta->received_data->attributes->country,
                        (string)$CI->response->meta->received_data->attributes->phone,
                        (string)$CI->response->meta->received_data->attributes->latitude,
                        (string)$CI->response->meta->received_data->attributes->longitude,
                        (string)$CI->response->meta->received_data->attributes->geo);

        $sql = "INSERT INTO `oa_location` VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, '', ?, '', '', '', ?, ?, ?, '', ?, '', ?, ?, ?, '', '', 0)";
        $this->run_sql($sql, $data);
        return $this->db->insert_id();
    }

    public function read($id = '')
    {
        if ($id == '') {
            $CI = & get_instance();
            $id = intval($CI->response->meta->id);
        } else {
            $id = intval($id);
        }
        $sql = "SELECT * FROM oa_location WHERE id = ?";
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'locations');
        return ($result);
    }

    public function sub_resource($id = '')
    {
        if ($id == '') {
            $CI = & get_instance();
            $id = intval($CI->response->meta->id);
        } else {
            $id = intval($id);
        }
        $sql = "SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, system.description AS `system.description`, system.os_family AS `system.os_family`, system.status AS `system.status` FROM system WHERE system.location_id = ?";
        $data = array((string)$id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'devices');
        return $result;
    }

    public function collection()
    {
        $CI = & get_instance();
        $sql = $this->collection_sql('locations', 'sql');
        $result = $this->run_sql($sql, array());
        $result = $this->format_data($result, 'locations');
        return ($result);
    }

    public function update()
    {
        $CI = & get_instance();
        $sql = '';
        $fields = ' name type room suite level address city state postcode country phone geo latitude longitude org_id ';
        foreach ($CI->response->meta->received_data->attributes as $key => $value) {
            if (strpos($fields, ' '.$key.' ') !== false) {
                if ($sql == '') {
                    $sql = "SET `" . $key . "` = '" . $value . "'";
                } else {
                    $sql .= ", `" . $key . "` = '" . $value . "'";
                }
            }
        }
        $sql = "UPDATE `oa_location` " . $sql . " WHERE id = " . intval($CI->response->meta->id);
        $this->run_sql($sql, array());
        return;
    }

    public function delete($id = '')
    {
        if ($id == '') {
            $CI = & get_instance();
            $id = intval($CI->response->meta->id);
        } else {
            $id = intval($id);
        }
        if ($id != 0) {
            $CI = & get_instance();
            $sql = "DELETE FROM `oa_location` WHERE id = ?";
            $data = array(intval($id));
            $this->run_sql($sql, $data);
            return true;
        } else {
            log_error('ERR-0013', 'm_locations::delete');
            return false;
        }
    }

}

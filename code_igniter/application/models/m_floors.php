<?php
/**
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
*
* PHP version 5.3.3
* 
* @category  Model
* @package   Racks
* @author    Mark Unwin <marku@opmantek.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.3
* @link      http://www.open-audit.org
*/

/**
* Base Model Floors
*
* @access   public
* @category Model
* @package  Racks
* @author   Mark Unwin <marku@opmantek.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class M_floors extends MY_Model
{
    /**
    * Constructor
    *
    * @access public
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create an individual item in the database
     * NOTE - Also create sub-items (a room and row)
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function create($data = null)
    {
        if ($id = $this->insert_collection('floors', $data)) {
            $user = 'system';
            if ( ! empty($this->user->full_name)) {
                $user = $this->user->full_name;
            }
            $sql = "INSERT INTO `rooms` VALUES (NULL, 'Default Room', ?, ?, 'The default entry for a room at this location.', '', '', '', ?, NOW())";
            $data_array = array($data->org_id, $id, $user);
            $room_id = intval($this->run_sql($sql, $data_array));
            if ( ! empty($room_id)) {
                $sql = "INSERT INTO `rows` VALUES (NULL, 'Default Row', ?, ?, 'The default entry for a row at this location.', '', '', '', ?, NOW())";
                $data_array = array($data->org_id, $room_id, $user);
                $this->run_sql($sql, $data_array);
            }
            return intval($id);
        } else {
            return false;
        }
    }

    /**
     * Read an individual item from the database, by ID
     *
     * @param  int $id The ID of the requested item
     * @return array The array of requested items
     */
    public function read($id = 0)
    {
        $id = intval($id);
        $sql = 'SELECT floors.*, orgs.id AS `orgs.id`, orgs.name AS `orgs.name`, buildings.id AS `buildings.id`, buildings.name as `buildings.name`, locations.id AS `locations.id`, locations.name as `locations.name`, count(rooms.id) as `rooms_count` FROM `floors` LEFT JOIN orgs ON (floors.org_id = orgs.id) LEFT JOIN buildings ON (buildings.id = floors.building_id) LEFT JOIN locations ON (buildings.location_id = locations.id) LEFT JOIN rooms ON (rooms.floor_id = floors.id)  WHERE floors.id = ?';
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'floors');
        return ($result);
    }

    /**
     * Delete an individual item from the database, by ID
     *
     * @param  int $id The ID of the requested item
     * @return bool True = success, False = fail
     */
    public function delete($id = 0)
    {
        $data = array(intval($id));
        $sql = 'DELETE FROM `floors` WHERE `id` = ?';
        $test = $this->run_sql($sql, $data);
        if ( ! empty($test)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Read the associated items parents from the DB by ID
     * 
     * @param  int|integer $id [description]
     * @return [type]          [description]
     */
    public function parent($id = 0)
    {
        $id = intval($id);
        $sql = 'SELECT buildings.*, floors.id AS `floors.id`, floors.name AS `floors.name` FROM buildings LEFT JOIN floors ON (buildings.id = floors.building_id) WHERE floors.id = ?';
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'buildings');
        return ($result);
    }

    /**
     * Read the associated items children from the DB by ID
     * 
     * @param  int|integer $id [description]
     * @return [type]          [description]
     */
    public function children($id = 0)
    {
        $id = intval($id);
        $sql = 'SELECT rooms.*, orgs.name AS `orgs.name`, floors.name as `floors.name`, buildings.name as `buildings.name`, locations.name as `locations.name`, count(`rows`.`id`) as `rows_count` FROM `rooms` LEFT JOIN orgs ON (rooms.org_id = orgs.id) LEFT JOIN floors ON (floors.id = rooms.floor_id) LEFT JOIN buildings ON (floors.building_id = buildings.id)  LEFT JOIN locations ON (buildings.location_id = locations.id) LEFT JOIN `rows` ON (`rows`.`room_id` = `rooms`.`id`) WHERE rooms.floor_id = ? GROUP BY rooms.id';
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'rooms');
        return ($result)    ;
    }

    /**
     * Read the collection from the database
     *
     * @param  int $user_id  The ID of the requesting user, no $response->meta->filter used and no $response->data populated
     * @param  int $response A flag to tell us if we need to use $response->meta->filter and populate $response->data
     * @return bool True = success, False = fail
     */
    public function collection($user_id = null, $response = null)
    {
        $CI = & get_instance();
        if ( ! empty($user_id)) {
            $org_list = implode(',', array_unique(array_merge($CI->user->orgs, $CI->m_orgs->get_user_descendants($user_id))));
            $sql = "SELECT floors.*, buildings.id AS `buildings.id`, buildings.name AS `buildings.name`, orgs.id AS `orgs.id`, orgs.name AS `orgs.name` FROM floors LEFT JOIN buildings ON (floors.building_id = buildings.id) LEFT JOIN orgs ON (floors.org_id = orgs.id) WHERE orgs.id IN ({$org_list})";
            $result = $this->run_sql($sql, array());
            $result = $this->format_data($result, 'floors');
            return $result;
        }
        if ( ! empty($response)) {
            $total = $this->collection($CI->user->id);
            $CI->response->meta->total = count($total);
            $sql = "SELECT {$CI->response->meta->internal->properties}, orgs.id AS `orgs.id`, orgs.name AS `orgs.name`, buildings.id AS `buildings.id`, buildings.name as `buildings.name`, count(rooms.id) as `rooms_count` FROM `floors` LEFT JOIN orgs ON (floors.org_id = orgs.id) LEFT JOIN buildings ON (buildings.id = floors.building_id) LEFT JOIN rooms ON (rooms.floor_id = floors.id) " .
                    $CI->response->meta->internal->filter . ' ' . 
                    'GROUP BY floors.id ' . 
                    $CI->response->meta->internal->sort . ' ' . 
                    $CI->response->meta->internal->limit;
            $result = $this->run_sql($sql, array());
            $CI->response->data = $this->format_data($result, 'floors');
            $CI->response->meta->filtered = count($CI->response->data);
        }
    }

    /**
     * [dictionary description]
     * @return [type] [description]
     */
    public function dictionary()
    {
        $CI = & get_instance();
        $collection = 'floors';
        $CI->temp_dictionary->link = str_replace('$collection', $collection, $CI->temp_dictionary->link);
        $this->load->helper('collections');

        $dictionary = new stdClass();
        $dictionary->table = $collection;
        $dictionary->about = '';
        $dictionary->marketing = '';
        $dictionary->notes = '';
        $dictionary->columns = new stdClass();
        $dictionary->attributes = new stdClass();
        $dictionary->attributes->fields = $this->db->list_fields($collection);
        $dictionary->attributes->create = mandatory_fields($collection);
        $dictionary->attributes->update = update_fields($collection);
        $dictionary->sentence = 'Define your floors and assign them to a building of your choosing.';
        $dictionary->marketing = '<p>Your floors help refine exactly where your assets are located.<br /><br />' . $CI->temp_dictionary->link . '<br /><br /></p>';
        $dictionary->about = '<p>Your floors help refine exactly where your assets are located.<br /><br />' . $CI->temp_dictionary->link . '<br /><br /></p>';
        $dictionary->product = 'enterprise';
        $dictionary->notes = '<p></p>';

        $dictionary->columns->id = $CI->temp_dictionary->id;
        $dictionary->columns->name = $CI->temp_dictionary->name;
        $dictionary->columns->org_id = $CI->temp_dictionary->org_id;
        $dictionary->columns->description = $CI->temp_dictionary->description;
        $dictionary->columns->location_id = 'The location where the building is located. Links to <code>locations.id</code>.';
        $dictionary->columns->building_id = 'The building the floor is located in. Links to <code>buildings.id</code>.';
        $dictionary->columns->options = 'unused';
        $dictionary->columns->notes = 'unused';
        $dictionary->columns->tags = 'unused';
        $dictionary->columns->edited_by = $CI->temp_dictionary->edited_by;
        $dictionary->columns->edited_date = $CI->temp_dictionary->edited_date;
        return $dictionary;
    }
}
// End of file m_floors.php
// Location: ./models/m_floors.php

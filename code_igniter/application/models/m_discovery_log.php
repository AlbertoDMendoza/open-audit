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
* @package   Discoveries
* @author    Mark Unwin <marku@opmantek.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.3
* @link      http://www.open-audit.org
*/

/**
* Base Model Discoveries
*
* @access   public
* @category Model
* @package  Discoveries
* @author   Mark Unwin <marku@opmantek.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class M_discovery_log extends MY_Model
{
    /**
    * Constructor
    *
    * @access public
    */
    public function __construct()
    {
        parent::__construct();
        $this->log = new stdClass();
        $this->log->status = 'reading data';
        $this->log->type = 'system';
    }

    /**
     * Read an individual item from the database, by ID
     *
     * @param  int $id The ID of the requested item
     * @return array The array of requested items
     */
    public function read($id = 0)
    {
        $sql = 'SELECT * FROM `discovery_log` WHERE id = ?';
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'discovery_log');
        return ($result);
    }

    /**
     * Delete All items from the DB
     *
     * @param  int $id The ID of the requested item
     * @return bool True = success, False = fail
     */
    public function delete($id = 0)
    {
        $data = array(intval($id));
        $sql = 'DELETE FROM `discovery_log` WHERE `id` = ?';
        $test = $this->run_sql($sql, $data);
        if ( ! empty($test)) {
            return true;
        } else {
            return false;
        }
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
            $org_list = array_unique(array_merge($CI->user->orgs, $CI->m_orgs->get_user_descendants($user_id)));
            $sql = 'SELECT * FROM discovery_log LEFT JOIN discoveries ON (discovery_log.discovery_id = discoveries.id) LEFT JOIN orgs ON (discoveries.org_id = orgs.id) WHERE orgs.id IN (' . implode(',', $org_list) . ')';
            $result = $this->run_sql($sql, array());
            $result = $this->format_data($result, 'discovery_log');
            return $result;
        }
        if ( ! empty($response)) {
            // Don't use the above as large datasets will exhaust the allocated PHP memory (when all we need is a count)
            $select_filter = '';
            foreach ($CI->response->meta->filter as $filter) {
                if ($filter->name === 'discovery_log.discovery_id') {
                    $select_filter = ' AND discovery_log.discovery_id = ' . intval($filter->value);
                }
            }
            $org_list = array_unique(array_merge($CI->user->orgs, $CI->m_orgs->get_user_descendants($user_id)));
            $sql = 'SELECT count(*) AS `count` FROM discovery_log LEFT JOIN discoveries ON (discovery_log.discovery_id = discoveries.id) LEFT JOIN orgs ON (discoveries.org_id = orgs.id) WHERE orgs.id IN (' . implode(',', $org_list) . ')' . $select_filter;
            $result = $this->run_sql($sql, array());
            $CI->response->meta->total = intval($result[0]->count);
            $sql = 'SELECT ' . $CI->response->meta->internal->properties . ' FROM discovery_log LEFT JOIN discoveries ON (discovery_log.discovery_id = discoveries.id) LEFT JOIN orgs ON (discoveries.org_id = orgs.id) ' .
                    $CI->response->meta->internal->filter . ' ' .
                    $CI->response->meta->internal->groupby . ' ' .
                    $CI->response->meta->internal->sort . ' ' .
                    $CI->response->meta->internal->limit;
            $result = $this->run_sql($sql, array());
            $CI->response->data = $this->format_data($result, 'discovery_log');
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
        $collection = 'discovery_log';
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
        #$dictionary->attributes->create = mandatory_fields($collection);
        #$dictionary->attributes->update = update_fields($collection);
        $dictionary->sentence = '';
        $dictionary->marketing = '';
        $dictionary->about = '';
        $dictionary->product = 'community';
        $dictionary->notes = '';
        $dictionary->columns->id = $CI->temp_dictionary->id;
        return $dictionary;
    }
}
// End of file m_discovery_log.php
// Location: ./models/m_discovery_log.php

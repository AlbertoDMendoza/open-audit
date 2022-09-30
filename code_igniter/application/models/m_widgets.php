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
* @package   Dashboards
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.2
* @link      http://www.open-audit.org
*/

/**
* Base Model Widgets
*
* @access   public
* @category Model
* @package  Dashboards
* @author   Mark Unwin <mark.unwin@firstwave.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class M_widgets extends MY_Model
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
     * Create an individual item in the database
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function create($data = null)
    {
        if ( ! empty($data->sql)) {
            if (stripos($data->sql, 'update ') !== false OR stripos($data->sql, 'update`') !== false) {
                log_error('ERR-0045', 'm_widgets::create', 'SQL cannot contain UPDATE clause');
                return false;
            }
            if (stripos($data->sql, 'delete from ') !== false OR stripos($data->sql, 'delete from`') !== false) {
                log_error('ERR-0045', 'm_widgets::create', 'SQL cannot contain DELETE clause.');
                return false;
            }
            if (stripos($data->sql, 'insert into ') !== false OR stripos($data->sql, 'insert into`') !== false) {
                log_error('ERR-0045', 'm_widgets::create', 'SQL cannot contain INSERT clause.');
                return false;
            }
        }
        if ($id = $this->insert_collection('widgets', $data)) {
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
    public function read($id = '')
    {
        $id = intval($id);
        $sql = "SELECT * FROM widgets WHERE id = ?";
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'widgets');
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
        $sql = 'DELETE FROM `widgets` WHERE `id` = ?';
        $test = $this->run_sql($sql, $data);
        if ( ! empty($test)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Count the number of rows a user is allowed to see
     * @return int The count
     */
    public function count()
    {
        $CI = & get_instance();
        $org_list = $CI->m_orgs->get_user_all($CI->user->id);
        $sql = 'SELECT COUNT(id) AS `count` FROM widgets WHERE org_id IN (' . implode(',', $org_list) . ')';
        $result = $this->run_sql($sql, array());
        return intval($result[0]->count);
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
            $org_list = $CI->m_orgs->get_user_all($user_id);
            $sql = 'SELECT * FROM widgets WHERE org_id IN (' . implode(',', $org_list) . ')';
            $result = $this->run_sql($sql, array());
            $result = $this->format_data($result, 'widgets');
            return $result;
        }
        if ( ! empty($response)) {
            $CI->response->meta->total = $this->count();
            $sql = "SELECT {$CI->response->meta->internal->properties}, orgs.id AS `orgs.id`, orgs.name AS `orgs.name` FROM widgets LEFT JOIN orgs ON (widgets.org_id = orgs.id) " . 
                    $CI->response->meta->internal->filter .  ' ' .
                    $CI->response->meta->internal->groupby . ' ' .
                    $CI->response->meta->internal->sort . ' ' .
                    $CI->response->meta->internal->limit;
            $result = $this->run_sql($sql, array());
            $CI->response->data = $this->format_data($result, 'widgets');
            $CI->response->meta->filtered = count($CI->response->data);
        }
    }

    /**
     * [pie_data description]
     * @param  [type] $widget   [description]
     * @param  [type] $org_list [description]
     * @return [type]           [description]
     */
    private function pie_data($widget, $org_list) {
        $device_tables = array('bios','connections','disk','dns','ip','log','memory','module','monitor','motherboard','netstat','network','nmap','optical','pagefile','partition','print_queue','processor','route','san','scsi','server','server_item','service','share','software','software_key','sound','task','user','user_group','variable','video','vm','warranty','windows');
        $other_tables = array('agents','attributes','collectors','connections','credentials','dashboards','discoveries','fields','files','groups','ldap_servers','licenses','locations','networks','orgs','queries','scripts','summaries','tasks','users','widgets');
        $sql = '';
        $group_by = $widget->group_by;
        if (empty($group_by)) {
            $group_by = $widget->primary;
        }
        $temp = explode('.', $widget->primary);
        $primary_table = $temp[0];
        $primary_table = $this->sql_unesc($primary_table);
        unset($temp);
        $temp = explode('.', $widget->secondary);
        $secondary_table = $temp[0];
        $secondary_table = $this->sql_unesc($secondary_table);
        unset($temp);

        $CI = & get_instance();
        if ( ! empty($widget->sql)) {
            // $sql = $widget->sql;
            // remove excessive white space and line breaks
            $sql = preg_replace ('/\s+/u', ' ', $widget->sql);
            if (stripos($sql, 'where @filter and') === false && stripos($sql, 'where @filter group by') === false) {
                // invalid query
                return false;
            }
            $temp = explode(' ', $sql);
            $full = $temp[1];
            $temp = explode('.', $full);
            $primary_table = $temp[0];
            $attribute = $full;
            unset($temp);
            if ($primary_table === 'system' OR in_array($primary_table, $device_tables)) {
                $collection = 'devices';
                $filter = "system.org_id IN ({$org_list})";
                if ( ! empty($CI->response->meta->requestor)) {
                    $filter = "system.org_id IN ({$org_list}) AND system.oae_manage = 'y'";
                }
                $sql = str_replace('@filter', $filter, $sql);
            } else if (in_array($primary_table, $other_tables)) {
                $collection = $primary_table;
                if ($collection !== 'orgs') {
                    $sql = str_replace('@filter', $this->sql_esc($primary_table.'.org_id') . ' IN (' . $org_list . ')', $sql);
                } else {
                    $filter = "system.org_id in ({$org_list})";
                    if ( ! empty($CI->response->meta->requestor)) {
                        $filter = "system.org_id in ({$org_list}) AND system.oae_manage = 'y'";
                    }
                    $sql = str_replace('@filter', $filter, $sql);
                }
            } else {
                // invalid query
                $collection = 'devices';
                $filter = "system.org_id in ({$org_list})";
                if ( ! empty($CI->response->meta->requestor)) {
                    $filter = "system.org_id in ({$org_list}) AND system.oae_manage = 'y'";
                }
                $sql = str_replace('@filter', $filter, $sql);
            }

        } else if (in_array($primary_table, $device_tables)) {
            $collection = 'devices';
            $attribute = $widget->primary;
            $sql = 'SELECT ' .  $this->sql_esc($widget->primary) . ' AS ' . $this->sql_esc('name') . ', ' . 
                                $this->sql_esc($widget->secondary) . ' AS ' . $this->sql_esc('description') . ', ' . 
                                $this->sql_esc($widget->ternary) . ' AS ' . $this->sql_esc('ternary') . ', ' . 
                                " COUNT(" . $this->sql_esc($widget->primary) . ') AS ' . $this->sql_esc('count') . 
                                " FROM " .  $this->sql_esc('system') . ' LEFT JOIN ' . $this->sql_esc($primary_table) . 
                                " ON (" . $this->sql_esc('system.id') . ' = ' . $this->sql_esc($primary_table . '.system_id') . 
                                " AND " . $this->sql_esc($primary_table.'.current') . " = 'y' ) " . 
                                " WHERE @filter GROUP BY " . $this->sql_esc($group_by);
            $filter = "system.org_id in (" . $org_list . ")";
            if ( ! empty($CI->response->meta->requestor)) {
                $filter = "system.org_id in (" . $org_list . ") AND system.oae_manage = 'y'";
            }
            if ( ! empty($widget->where)) {
                $filter .= " AND " . $widget->where;
            }
            $sql = str_replace('@filter', $filter, $sql);
            if ( ! empty($widget->limit)) {
                $limit = intval($widget->limit);
                $sql .= ' LIMIT ' . $limit;
            }

        } else if ($primary_table === 'system') {
            $collection = 'devices';
            $attribute = $widget->primary;
            $sql = "SELECT " .  $this->sql_esc($widget->primary) . " AS " . $this->sql_esc('name') . ", " . 
                                $this->sql_esc($widget->secondary) . " AS " . $this->sql_esc('description') . ", " . 
                                $this->sql_esc($widget->ternary) . " AS " . $this->sql_esc('ternary') . ", " . 
                                " COUNT(" . $this->sql_esc($widget->primary) . ") AS " . $this->sql_esc('count') . ", " . 
                                " CAST((COUNT(*) / (SELECT COUNT(" . $this->sql_esc($widget->primary) . ") FROM " . $this->sql_esc($primary_table) . " WHERE " . $this->sql_esc('system.org_id') . " IN (" . $org_list . ")) * 100) AS unsigned) AS 'percent'" . 
                                " FROM " .  $this->sql_esc('system') . 
                                " WHERE @filter GROUP BY " . $this->sql_esc($group_by);
            $filter = "system.org_id in (" . $org_list . ")";
            if ( ! empty($CI->response->meta->requestor)) {
                $filter = "system.org_id in (" . $org_list . ") AND system.oae_manage = 'y'";
            }
            if ( ! empty($widget->where)) {
                $filter .= " AND " . $widget->where;
            }
            $sql = str_replace('@filter', $filter, $sql);
            if ( ! empty($widget->limit)) {
                $limit = intval($widget->limit);
                $sql .= ' ORDER BY `count` DESC LIMIT ' . $limit;
            }
        }
        $result = $this->run_sql($sql, array());
        $CI->response->meta->sql[] = $sql;
        if ( ! empty($result)) {
            for ($i=0; $i < count($result); $i++) {
                if (empty($result[$i]->name) and empty($result[$i]->count)) {
                    unset($result[$i]);
                }
            }
        }
        if ( ! empty($result)) {
            $result = array_values($result);
        }
        $total_count = 0;
        // We need to allow for grouping using a column name that is NOT 'name' as this can clash with existing schema.
        //   In this case (always in custom SQL), you should use my_name instead
        if ( ! empty($result)) {
            for ($i=0; $i < count($result); $i++) { 
                foreach ($result[$i] as $key => $value) {
                    if (strpos($key, 'my_') === 0) {
                        $new_key = str_replace('my_', '', $key);
                        $result[$i]->{$new_key} = $value;
                        unset($result[$i]->{$key});
                    }
                }
            }
            for ($i=0; $i < count($result); $i++) {
                $total_count += intval($result[$i]->count);
                if (intval($result[$i]->count) === 0 && is_null($result[$i]->name)) {
                    unset($result[$i]);
                }
            }
            $result = array_values($result);
            for ($i=0; $i < count($result); $i++) {
                if ( ! empty($result[$i]->count) && ! empty($total_count)) {
                    $result[$i]->percent = intval(($result[$i]->count / $total_count) * 100);
                } else {
                    $result[$i]->percent = 0;
                }
                if ( ! empty($widget->link)) {
                    $result[$i]->link = $widget->link;
                    if (isset($result[$i]->name)) {
                        $result[$i]->link = str_ireplace('@name', $result[$i]->name, $result[$i]->link);
                    }
                    if (isset($result[$i]->description)) {
                        $result[$i]->link = str_ireplace('@description', $result[$i]->description, $result[$i]->link);
                    }
                    if (isset($result[$i]->ternary)) {
                        $result[$i]->link = str_ireplace('@ternary', $result[$i]->ternary, $result[$i]->link);
                    }
                } else {
                    $result[$i]->link = $collection . '?' . $attribute . '=' . $result[$i]->name;
                }
            }
        } else {
            $item = new stdClass();
            $item->name = '';
            $item->description = '';
            $item->ternary = '';
            $item->count = 0;
            $item->percent = 100;
            $item->link = '';
            $result[] = $item;
        }
        return $result;
    }

    /**
     * [line_data description]
     * @param  [type] $widget   [description]
     * @param  [type] $org_list [description]
     * @return [type]           [description]
     */
    private function line_data($widget, $org_list) {
        if ( ! empty($widget->sql)) {
            $sql = $widget->sql;
            if (stripos($sql, 'where @filter and') === false && stripos($sql, 'where @filter group by') === false) {
                // These entries musy only be created by a user with Admin role as no filter allows anything in the DB to be queried (think multi-tenancy).
            } else {
                $filter = "system.org_id IN ({$org_list})";
                if ( ! empty($CI->response->meta->requestor)) {
                    $filter = "system.org_id IN ({$org_list}) AND oae_manage = 'y'";
                }
                $sql = str_replace('@filter', $filter, $sql);
            }
            $result = $this->run_sql($sql, array());
            if ( ! empty($result)) {
                foreach ($result as $row) {
                    $row->timestamp = strtotime($row->date);
                }
                usort($result, array($this,'cmp_timestamp'));
                for ($i=0; $i < count($result); $i++) {
                    $result[$i]->link = $widget->link;
                    if (isset($result[$i]->name)) {
                        $result[$i]->link = str_ireplace('@name', $result[$i]->name, $result[$i]->link);
                    }
                    if (isset($result[$i]->description)) {
                        $result[$i]->link = str_ireplace('@description', $result[$i]->description, $result[$i]->link);
                    }
                    if (isset($result[$i]->ternary)) {
                        $result[$i]->link = str_ireplace('@ternary', $result[$i]->ternary, $result[$i]->link);
                    }
                    if (isset($result[$i]->date)) {
                        $result[$i]->link = str_ireplace('@date', $result[$i]->date, $result[$i]->link);
                    }
                    if (isset($result[$i]->timestamp)) {
                        $result[$i]->link = str_ireplace('@timestamp', $result[$i]->timestamp, $result[$i]->link);
                    }
                }

                if (count($result) < 2) {
                    $start = date('Y-m-d', strtotime('-' . $widget->limit . ' days'));
                    $begin = new DateTime( $start );
                    $finish = date('Y-m-d', strtotime('+1 days'));
                    $end = new DateTime($finish);
                    $interval = new DateInterval('P1D');
                    $period = new DatePeriod($begin, $interval, $end);
                } else {
                    $start = date('Y-m-d', strtotime($result[0]->date));
                    $begin = new DateTime( $start );
                    $i = count($result)-1;
                    $end = new DateTime($result[$i]->date);
                    $interval = DateInterval::createFromDateString('1 day');
                    $period = new DatePeriod($begin, $interval, $end);
                }

                foreach ( $period as $dt ) {
                    $the_date = $dt->format('Y-m-d');
                    $add_row = true;
                    for ($i=0; $i < count($result); $i++) {
                        if (!empty($result[$i]->date) and $result[$i]->date == $the_date) {
                            $add_row = false;
                            $result[$i]->timestamp = strtotime($the_date);
                        }
                    }
                    if ($add_row) {
                        $row = new stdClass();
                        $row->timestamp = strtotime($the_date);
                        $row->date = $the_date;
                        $row->count = 0;
                        $row->link = '';
                        $result[] = $row;
                    }
                }

            } else {
                $item = new stdClass();
                $item->timestamp = strtotime(date('Y-m-d'));
                $item->date = date('Y-m-d');
                $item->count = 0;
                $item->link = '';
                $result[] = $item;
            }
            usort($result, array($this,'cmp_timestamp'));
            return $result;
        }

        if (empty($widget->sql)) {
            $device_tables = array('bios','disk','dns','ip','log','memory','module','monitor','motherboard','netstat','network','nmap','optical','pagefile','partition','print_queue','processor','route','san','scsi','server','server_item','service','share','software','software_key','sound','system','task','user','user_group','variable','video','vm','warranty','windows');
            if (!in_array($widget->primary, $device_tables)) {
                return false;
            }
            $sql = "SELECT DATE(" . $this->sql_esc('change_log.timestamp') . ") AS " . $this->sql_esc('date') . ", count(DATE(" . $this->sql_esc('change_log.timestamp') . " )) AS " . $this->sql_esc('count') . "  FROM " . $this->sql_esc('change_log') . " LEFT JOIN " . $this->sql_esc('system') . " ON (" . $this->sql_esc('system.id') . " = " . $this->sql_esc('change_log.system_id') . ") WHERE @filter AND " . $this->sql_esc('change_log.timestamp') . " >= DATE_SUB(CURDATE(), INTERVAL " . intval($widget->limit) . " DAY) AND " . $this->sql_esc('change_log.db_table') . " = '" . $widget->primary . "'  AND " . $this->sql_esc('change_log.db_action') . " = '" . $widget->secondary . "' GROUP BY DATE(" . $this->sql_esc('change_log.timestamp') . ")";
            $filter = "system.org_id IN (" . $org_list . ")";
            if (!empty($CI->response->meta->requestor)) {
                $filter = "system.org_id IN (" . $org_list . ") AND system.oae_manage = 'y'";
            }
            if (!empty($widget->where)) {
                $sql .= " AND " . $widget->where;
            }
            $sql = str_replace('@filter', $filter, $sql);
            $result = $this->run_sql($sql, array());
            if (!empty($result)) {
                foreach ($result as $row) {
                    if (empty($widget->link)) {
                        $row->name = strtotime($row->date);
                        $row->link = 'devices?sub_resource=change_log&change_log.db_table=' . $widget->primary . '&change_log.db_action=' . $widget->secondary . '&change_log.timestamp=LIKE' . $row->date;
                    } else {
                        $row->link = $widget->link;
                        if (isset($row->name)) {
                            $row->link = str_ireplace('@name', $row->name, $row->link);
                        }
                        if (isset($row->description)) {
                            $row->link = str_ireplace('@description', $row->description, $row->link);
                        }
                        if (isset($row->ternary)) {
                            $row->link = str_ireplace('@ternary', $row->ternary, $row->link);
                        }
                        if (isset($row->date)) {
                            $row->link = str_ireplace('@date', $row->date, $row->link);
                        }
                        if (isset($row->timestamp)) {
                            $row->link = str_ireplace('@timestamp', $row->timestamp, $row->link);
                        }
                    }
                }
            }
            $start = date('Y-m-d', strtotime('-' . $widget->limit . ' days'));
            $begin = new DateTime( $start );
            $finish = date('Y-m-d', strtotime('+1 days'));
            $end = new DateTime($finish);
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($begin, $interval, $end);

            foreach ( $period as $dt ) {
                $the_date = $dt->format('Y-m-d');
                $add_row = true;
                if (!empty($result)) {
                    for ($i=0; $i < count($result); $i++) {
                        if (!empty($result[$i]->date) and $result[$i]->date == $the_date) {
                            $add_row = false;
                            $result[$i]->timestamp = strtotime($the_date);
                        }
                    }
                }
                if ($add_row) {
                    $row = new stdClass();
                    $row->timestamp = strtotime($the_date);
                    $row->date = $the_date;
                    $row->count = 0;
                    $row->link = '';
                    $result[] = $row;
                }
            }
            usort($result, array($this,'cmp_timestamp'));
            return $result;
        }
    }

    function cmp_name($a, $b) {
        if (!empty($a->name) and !empty($b->name)) {
            return strcmp(strtolower($a->name), strtolower($b->name));
        } else {
            return;
        }
    }

    function cmp_timestamp($a, $b) {
        if (!empty($a->timestamp) and !empty($b->timestamp)) {
            return strcmp(strtolower($a->timestamp), strtolower($b->timestamp));
        } else {
            return;
        }
    }

    public function execute($id = 0) {
        $id = intval($id);
        $sql = "SELECT * FROM widgets WHERE id = ?";
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $widget = $result[0];
        if ($widget->type === 'pie') {
            $result = $this->pie_data($widget, $this->user->org_list);
        }
        if ($widget->type === 'line') {
            $result = $this->line_data($widget, $this->user->org_list);
        }
        $result = $this->format_data($result, 'widgets');
        return ($result);
    }

    private function build_properties() {
        $CI = & get_instance();
        $properties = '';
        $temp = explode(',', $CI->response->meta->properties);
        for ($i=0; $i<count($temp); $i++) {
            if (strpos($temp[$i], '.') === false) {
                $temp[$i] = 'roles.'.trim($temp[$i]);
            } else {
                $temp[$i] = trim($temp[$i]);
            }
        }
        $properties = implode(',', $temp);
        return($properties);
    }

    private function build_filter() {
        $CI = & get_instance();
        $reserved = ' properties limit resource action sort current offset format ';
        $filter = '';
        foreach ($CI->response->meta->filter as $item) {
            if (strpos(' '.$item->name.' ', $reserved) === false) {
                $filter .= ' AND ' . $item->name . ' ' . $item->operator . ' ' . '"' . $item->value . '"';
            }
        }
        if ($filter != '') {
            $filter = substr($filter, 5);
            $filter = ' WHERE ' . $filter;
        }
        return($filter);
    }

    /**
     * [dictionary description]
     * @return [type] [description]
     */
    public function dictionary()
    {
        $CI = & get_instance();
        $collection = 'widgets';
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
        $dictionary->sentence = 'Widgets are used on Dashboards and are completely open to user design.';
        $dictionary->about = '<p>Widgets can easily be created to show whatever is specific to your environment on your dashboards.<br /><br />
        ' . $CI->temp_dictionary->link . '<br /><br /></p>';
        $dictionary->marketing = '<p>Widgets are the building blocks of Open-AudIT Dashboards.<br /><br />
        ' . $CI->temp_dictionary->link . '<br /><br /></p>';
        $dictionary->product = 'enterprise';
        $dictionary->notes = 'The primary and optional secondary items should be fully qualified - ie, system.type or software.name.';

        $dictionary->columns->id = $CI->temp_dictionary->id;
        $dictionary->columns->name = $CI->temp_dictionary->name;
        $dictionary->columns->org_id = $CI->temp_dictionary->org_id;
        $dictionary->columns->description = $CI->temp_dictionary->description;
        $dictionary->columns->table = 'The primary database table upon which to base this widget.';
        $dictionary->columns->primary = 'The fully qualified column upon which to group by.';
        $dictionary->columns->secondary = 'The optional secondary column.';
        $dictionary->columns->ternary = 'The optional third column.';
        $dictionary->columns->where = 'Any required filter.';
        $dictionary->columns->limit = 'Limit the query to the first X items.';
        $dictionary->columns->options = 'unused';
        $dictionary->columns->group_by = 'This is generally the primary column, unless otherwise configured.';
        $dictionary->columns->type = "Can be 'line' or 'pie'.";
        $dictionary->columns->dataset_title = 'The text for the bottom of the chart in a line chart (only).';
        $dictionary->columns->sql = "For advanced entry of a raw SQL query. As per Queries, you must include 'WHERE @filter AND' in your SQL.";
        $dictionary->columns->link = 'The template for the link to be generated per result line.';
        $dictionary->columns->edited_by = $CI->temp_dictionary->edited_by;
        $dictionary->columns->edited_date = $CI->temp_dictionary->edited_date;

        $dictionary->valid_columns = array('bios.current','bios.description','bios.manufacturer','bios.version','disk.current','disk.description','disk.interface_type','disk.manufacturer','disk.model','disk.model_family','disk.partition_count','disk.status','disk.version','ip.cidr','ip.current','ip.netmask','ip.network','ip.version','log.current','log.file_name','log.name','memory.current','memory.detail','memory.form_factor','memory.size','memory.speed','memory.type','module.class_text','module.current','module.description','monitor.aspect_ratio','monitor.current','monitor.description','monitor.manufacturer','monitor.model','monitor.size','motherboard.current','motherboard.manufacturer','motherboard.memory_slot_count','motherboard.model','motherboard.processor_slot_count','network.connection_status','network.current','network.dhcp_enabled','network.dhcp_server','network.dns_domain','network.dns_server','network.manufacturer','network.model','network.type','optical.current','optical.model','optical.mount_point','pagefile.current','pagefile.max_size','pagefile.name','pagefile_initial_size','partition.bootable','partition.current','partition.description','partition.format','partition.mount_point','partition.mount_type','partition.name','partition.type','print_queue.color','print_queue.current','print_queue.duplex','print_queue.location','print_queue.manufacturer','print_queue.model','print_queue.port_name','print_queue.shared','print_queue.status','print_queue.type','processor.architecture','processor.core_count','processor.current','processor.description','processor.logical_count','processor.manufacturer','processor.physical_count','processor.socket','route.current','route.destination','route.mask','route.next_hop','route.type','server.current','server.description','server.edition','server.full_name','server.name','server.status','server.type','server.version','server.version_string','server_item.current','server_item.type','service.current','service.executable','service.name','service.start_mode','service.state','service.user','share.current','share.name','share.path','software.current','software.install_source','software.name','software_key.current','software_key.edition','software_key.name','software_key.rel','software_key.string','sound.current','sound.manufacturer','sound.model','system.class','system.cloud_id','system.contact_name','system.environment','system.form_factor','system.function','system.icon','system.instance_provider', 'system.instance_state', 'system.instance_type','system.invoice_id','system.last_seen_by','system.lease_expiry_date','system.location_id','system.location_latitude','system.location_level','system.location_longitude','system.location_rack','system.location_rack_position','system.location_rack_size','system.location_room','system.location_suite','system.manufacturer','system.memory_count','system.model','system.oae_manage','system.org_id','system.os_bit','system.os_family','system.os_group','system.os_installation_date','system.os_name','system.os_version','system.owner','system.patch_panel','system.printer_color','system.printer_duplex','system.printer_port_name','system.printer_shared','system.printer_shared_name','system.processor_count','system.purchase_amount','system.purchase_cost_center','system.purchase_date','system.purchase_invoice','system.purchase_order_number','system.purchase_service_contract_number','system.purchase_vendor','system.service_network','system.service_number','system.service_plan','system.service_provider','system.service_type','system.snmp_oid','system.status','system.sysContact','system.sysDescr','system.sysLocation','system.sysObjectID','system.type','system.wall_port','system.warranty_duration','system.warranty_expires','system.warranty_type','user.current','user.domain','user.password_changeable','user.password_required','user.status','user.type','user_group.current','user_group.name','video.current','video.manufacturer','video.model','video.size','vm.current','vm.cpu_count','vm.memory_count','vm.status','windows.active_directory_ou','windows.boot_device','windows.build_number','windows.client_site_name','windows.country_code','windows.current','windows.domain_controller_address','windows.domain_controller_name','windows.domain_role','windows.domain_short','windows.id_number','windows.install_directory','windows.language','windows.organisation','windows.part_of_domain','windows.registered_user','windows.service_pack','windows.time_caption','windows.time_daylight','windows.version','windows.workgroup');
        $dictionary->valid_tables = array('bios','disk','dns','ip','log','memory','module','monitor','motherboard','netstat','network','nmap','optical','pagefile','partition','print_queue','processor','route','san','scsi','server','server_item','service','share','software','software_key','sound','system','task','user','user_group','variable','video','vm','warranty','windows');
        return $dictionary;
    }
}
// End of file m_widgets.php
// Location: ./models/m_widgets.php

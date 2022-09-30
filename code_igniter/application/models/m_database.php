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
* @package   Database
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.2
* @link      http://www.open-audit.org
*/

/**
* Base Model Database
*
* @access   public
* @category Model
* @package  Database
* @author   Mark Unwin <mark.unwin@firstwave.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class M_database extends MY_Model
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
     * Read the details fo a table
     * @param  string $id The name of the table
     * @return array      The table details in an array
     */
    public function read($id = '')
    {
        if ($id === '') {
            $CI = & get_instance();
            $id = $CI->response->meta->id;
        } else {
            $id = $id;
        }
        if ($id === 'devices') {
            $id = 'system';
        }
        $return = array();
        $tables = $this->db->list_tables();
        foreach ($tables as $table) {
            if ($id === $table) {
                $item = new stdClass();
                $item->type = 'database';
                $item->id = $table;
                $item->attributes = new stdClass();
                $item->attributes->name = $table;
                $sql = 'SELECT COUNT(*) AS `count` FROM `' . $table . '`';
                $result = $this->run_sql($sql, array());
                $item->attributes->count = intval($result[0]->count);
                if ($this->db->field_exists('current', $table)) {
                    $sql = 'SELECT COUNT(*) AS `count` FROM `' . $table . "` WHERE current = 'y'";
                    $result = $this->run_sql($sql, array());
                    $item->attributes->current_row = true;
                    $item->attributes->current = intval($result[0]->count);
                    $item->attributes->non_current = intval($item->attributes->count -$item->attributes->current);
                } else {
                    $item->attributes->current_row = false;
                }
                if ($this->db->field_exists('org_id', $table)) {
                    $item->attributes->org_id_row = true;
                } else {
                    $item->attributes->org_id_row = false;
                }
                if ($table === 'system') {
                    $item->attributes->status = array();
                    $sql = 'SELECT status, COUNT(*) AS `count` FROM system GROUP BY `status`';
                    $query = $this->db->query($sql);
                    $item->attributes->status = $query->result();
                }
                // TODO - add in if the column has an index or is a foreign key
                $item->attributes->columns = array();
                $item->attributes->columns = $this->db->field_data($table);
                foreach ($item->attributes->columns as &$column) {
                    if ($column->type === 'enum') {
                        $sql = "SELECT SUBSTRING(COLUMN_TYPE,5) AS `values` FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND TABLE_NAME = '" . $table . "' AND COLUMN_NAME = '" . $column->name . "'";
                        $result = $this->run_sql($sql, array());
                        $column->values = $result[0]->values;
                    }
                }
                $return[] = $item;
            }
        }
        return ($return);
    }

    /**
     * Delete the contents of a table
     * @param  string $table   [description]
     * @param  string $current [description]
     * @param  string $status  [description]
     * @return [type]          [description]
     */
    public function delete($table = '', $current = '', $status = '')
    {
        $CI = & get_instance();
        if ($table === '') {
            $table = $CI->response->meta->id;
        }
        if ($current === '') {
            if ( ! empty($this->response->meta->current)) {
                $current = $this->response->meta->current;
            } else {
                $current = 'n';
            }
        }
        if ($status === '') {
            if ( ! empty($CI->response->meta->filter)) {
                foreach ($CI->response->meta->filter as $filter) {
                    if ($filter->name === 'status') {
                        $status = $filter->value;
                    }
                }
            }
        }
        if ($this->db->table_exists($table)) {
            if ($this->db->field_exists('current', $table)) {
                $sql = 'DELETE FROM `' . $table . "` WHERE current = '" . $current . "'";
                $this->run_sql($sql, array());
                return true;
            } elseif ($table === 'system') {
                if ($status !== '') {
                    $sql = 'DELETE FROM system WHERE status = ?';
                    $this->run_sql($sql, array($status));
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($current === 'all') {
                    $sql = 'DELETE FROM `' . $table . '`';
                    $this->run_sql($sql, array());
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * [execute description]
     * @param  string $table      [description]
     * @param  string $action     [description]
     * @param  string $format     [description]
     * @param  array  $attributes [description]
     * @return [type]             [description]
     */
    public function execute($table = '', $action = '', $format = '', $attributes = array())
    {
        $CI = & get_instance();
        if ($table === '') {
            $table = $CI->response->meta->id;
        }
        if ( ! $this->db->table_exists($table)) {
            return;
        }
        if ($action === '') {
            $action = $CI->response->meta->sub_resource;
        }
        if ($format === '') {
            $format = $CI->response->meta->format;
        }

        $sql = '';

        switch ($action) {
            case 'table and row count':
                $sql = "SELECT TABLE_NAME AS `table`, TABLE_ROWS AS `count` FROM `information_schema`.`tables` WHERE `table_schema` = '". $this->db->database . "'";
                $return = 'array';
                break;

            case 'row count':
                $sql = 'SELECT COUNT(*) AS `count` FROM `' . $table . '`';
                $return = 'count';
                break;

            case 'export table':
                $sql = 'SELECT COUNT(*) AS `count` FROM `' . $table . '`';
                $result = $this->run_sql($sql, array());
                if ($format === 'csv') {
                    $sql = 'SELECT * FROM `' . $table . '`';
                    $query = $this->db->query($sql);
                    $result = $query->result();
                    if ( ! empty($result) and is_array($result) and $table === 'credentials') {
                        for ($i=0; $i < count($result); $i++) {
                            $result[$i]->credentials = json_decode(simpleDecrypt($result[$i]->credentials));
                        }
                    }
                    if ( ! empty($result) and is_array($result) and $table === 'clouds') {
                        for ($i=0; $i < count($result); $i++) {
                            $result[$i]->credentials = json_decode(simpleDecrypt($result[$i]->credentials));
                        }
                    }
                    if ($table === 'dashboards') {
                        for ($i=0; $i < count($result); $i++) {
                            $result[$i]->options = json_encode($result[$i]->options);
                        }
                    }
                    if ($table === 'discoveries') {
                        for ($i=0; $i < count($result); $i++) {
                            $result[$i]->scan_options = json_encode($result[$i]->scan_options);
                            $result[$i]->match_options = json_encode($result[$i]->match_options);
                            $result[$i]->command_options = json_encode($result[$i]->command_options);
                            $result[$i]->other = '';
                        }
                    }
                    if ($table === 'tasks') {
                        for ($i=0; $i < count($result); $i++) {
                            $result[$i]->options = json_encode($result[$i]->options);
                        }
                    }
                    $result = $this->format_data($result, 'database');
                    return $result;
                }
                if ($format === 'json') {
                    $sql = 'SELECT * FROM `' . $table . '`';
                    $query = $this->db->query($sql);
                    $result = $query->result();
                    $backup = json_encode($result);
                    $this->load->helper('download');
                    force_download('open-audit_' . $table . '.json', $backup);
                    return;
                }
                if ($format === 'xml') {
                    $this->load->dbutil();
                    $sql = 'SELECT * FROM `' . $table . '`';
                    $query = $this->db->query($sql);
                    $config = array ('root' => 'root', 'element' => 'item', 'newline' => "\n", 'tab' => "\t");
                    $backup = $this->dbutil->xml_from_result($query, $config);
                    $this->load->helper('download');
                    force_download('open-audit_' . $table . '.xml', $backup);
                    return;
                }
                if ($format === 'sql') {
                    if (php_uname('s') === 'Windows NT') {
                        $mysqldump = 'c:\\xampplite\\mysql\\bin\\mysqldump.exe';
                        if (file_exists('c:\\xampp\\mysql\\bin\\mysqldump.exe')) {
                            $mysqldump = 'c:\\xampp\\mysql\\bin\\mysqldump.exe';
                        }
                    }
                    if (php_uname('s') === 'Darwin') {
                        $mysqldump = '/usr/local/mysql/bin/mysqldump';
                    }
                    if (php_uname('s') === 'Linux') {
                        exec('which mysqldump', $temp);
                        $mysqldump = $temp[0];
                        unset($temp);
                    }
                    $command = '"' . $mysqldump . '" --extended-insert=FALSE -u ' . $CI->db->username . ' -p' . $CI->db->password . ' -h' . $CI->db->hostname . ' ' . $CI->db->database . ' ' . $table;
                    exec($command, $backup);
                    $backup = implode("\n", $backup);
                    $this->load->helper('download');
                    force_download('open-audit_' . $table . '.sql', $backup);
                    return;
                }
                break;
            
            case 'tables':
                $sql = 'SHOW TABLES';
                $return = 'array';
                break;
            
            case 'fields':
                $sql = "SELECT COLUMN_NAME FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA` = '". $this->db->database . "' AND TABLE_NAME = '" . $table . "'";
                $return = 'array';
                break;
            
            case 'distinct fields':
                $sql = 'SELECT DISTINCT(@field) AS `field` FROM `' . $table . '` ORDER BY `field`';
                $return = 'array';
                break;
            
            case 'delete non-current':
                $sql = 'DELETE FROM `' . $table . "` WHERE current = 'n'";
                $return = 'delete_count';
                break;

            case 'delete user sessions':
                $sql = 'DELETE FROM `oa_user_sessions` WHERE `last_activity` < UNIX_TIMESTAMP(NOW() - INTERVAL @day DAY)';
                $return = 'delete_count';
                break;
            
            default:
                break;
        }

        foreach ($attributes as $key => $value) {
            if (stripos($sql, '@'.$key) !== false) {
                $sql = str_replace('@'.$key, $value, $sql);
            }
        }
        if ($return !== 'text') {
            $result = $this->run_sql($sql, array());
            switch ($return) {
                case 'array':
                    return $result;
                    break;

                case 'count':
                    return intval($result[0]->count);
                    break;

                case 'delete_count':
                    return intval($this->db->affected_rows());
                    break;

                default:
                    return $result;
                    break;
            }
        } else {
            $result = shell_exec($command);
            return $result;
        }
    }

    /**
     * [collection description]
     * @return [type] [description]
     */
    public function collection()
    {
        $CI = & get_instance();
        $return = array();
        $tables = $this->db->list_tables();
        foreach ($tables as $table) {
            $item = new stdClass();
            $item->type = 'database';
            $item->id = $table;
            $item->attributes = new stdClass();
            
            $sql = 'SELECT COUNT(*) AS `count` FROM `' . $table . '`';
            $query = $this->db->query($sql);
            $result = $query->result();
            $item->attributes->name = $table;
            $item->attributes->count = intval($result[0]->count);

            if ($this->db->field_exists('current', $table)) {
                $sql = 'SELECT COUNT(*) AS `count` FROM `' . $table . "` WHERE current = 'y'";
                $query = $this->db->query($sql);
                $result = $query->result();
                $item->attributes->current_row = true;
                $item->attributes->current = intval($result[0]->count);
                $item->attributes->non_current = intval($item->attributes->count -$item->attributes->current);
            } else {
                $item->attributes->current_row = false;
            }

            if ($this->db->field_exists('org_id', $table)) {
                $item->attributes->org_id_row = true;
            } else {
                $item->attributes->org_id_row = false;
            }

            if ($this->db->field_exists('system_id', $table)) {
                $item->attributes->system_id_row = true;
            } else {
                $item->attributes->system_id_row = false;
            }
            $item->links = new stdClass();
            $item->links->self = $this->config->config['base_url'] . 'index.php/database/' . $item->id;

            $return[] = $item;
        }

        $CI->response->data = $return;
        $CI->response->meta->filtered = count($CI->response->data);
        $CI->response->meta->total = count($CI->response->data);
    }
}
// End of file m_database.php
// Location: ./models/m_database.php

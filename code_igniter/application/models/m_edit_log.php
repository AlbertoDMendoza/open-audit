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
* @package   Devices
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.2
* @link      http://www.open-audit.org
*/

/**
* Base Model EditLog
*
* @access   public
* @category Model
* @package  Devices
* @author   Mark Unwin <mark.unwin@firstwave.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class M_edit_log extends MY_Model
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
     * Create an individual entry in the DB
     * 
     * @param  [type] $system_id      [description]
     * @param  string $details        [description]
     * @param  string $db_table       [description]
     * @param  string $db_column      [description]
     * @param  string $timestamp      [description]
     * @param  string $value          [description]
     * @param  string $previous_value [description]
     * @return [type]                 [description]
     */
    public function create($system_id, $details = '', $db_table = 'system', $db_column = '', $timestamp = '', $value = '', $previous_value = '')
    {
        $this->log->function = strtolower(__METHOD__);
        $this->log->status = 'creating data';
        stdlog($this->log);
        $system_id = intval($system_id);
        if ($system_id !== '' && $system_id !== 0) {
            if ($details === '') {
                if (isset($this->session->userdata['user_full_name'])) {
                    $details = $this->session->userdata['user_full_name'] . ' changed data';
                } else {
                    $details = 'Data was changed';
                }
            }
            if ($db_table === '') {
                $db_table = 'system';
            }
            if ($timestamp === '') {
                $timestamp = $this->config->config['timestamp'];
            }
            $sql = "INSERT INTO edit_log (user_id, system_id, details, source, weight, db_table, db_column, timestamp, value, previous_value) VALUES (?, ?, ?, 'user', 1000, ?, ?, NOW(), ?, ?)";
            $sql = $this->clean_sql($sql);
            $data = array($this->session->userdata['user_id'], $system_id, "{$details}", "{$db_table}", "{$db_column}", "{$value}", "{$previous_value}");
            $this->db->query($sql, $data);
        }
    }

    /**
     * Read an individual item from the database, by system.ID
     * @param  [type] $system_id [description]
     * @return [type]            [description]
     */
    public function read($system_id)
    {
        $this->log->function = strtolower(__METHOD__);
        stdlog($this->log);
        $sql = 'SELECT edit_log.*, users.full_name FROM edit_log, users WHERE edit_log.system_id = ? AND users.id = edit_log.user_id';
        $sql = $this->clean_sql($sql);
        $data = array("{$system_id}");
        $query = $this->db->query($sql, $data);
        $result = $query->result();
        return ($result);
    }
}
// End of file m_edit_log.php
// Location: ./models/m_edit_log.php

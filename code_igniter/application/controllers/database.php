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
* @category  Controller
* @package   Database
* @author    Mark Unwin <marku@opmantek.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.3
* @link      http://www.open-audit.org
*/

/**
* Base Object Database
*
* @access   public
* @category Controller
* @package  Database
* @author   Mark Unwin <marku@opmantek.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class Database extends MY_Controller
{
    /**
    * Constructor
    *
    * @access    public
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('network');
        $this->load->helper('error');
        $this->load->model('m_database');
        // inputRead();
        $this->response = response_create();
        $this->output->url = $this->config->config['oa_web_index'];
    }

    /**
    * Constructor
    *
    * @access    public
    * @return NULL
    */
    public function index()
    {
    }

    /**
    * Our remap function to override the inbuilt controller->method functionality
    *
    * @access public
    * @return NULL
    */
    public function _remap()
    {
        $this->{$this->response->meta->action}();
    }

    /**
    * Read a single object
    *
    * @access public
    * @return NULL
    */
    public function read()
    {
        $this->response->data = $this->{'m_'.$this->response->meta->collection}->read($this->response->meta->id);
        if ( ! empty($this->response->data) && is_array($this->response->data)) {
            $this->response->meta->total = 1;
            $this->response->meta->filtered = 1;
            $this->load->model('m_orgs');
            // TODO - A dictionary for each database table
            $models_with_dictionary = response_valid_collections();
            if (in_array($this->response->meta->id, $models_with_dictionary)) {
                $this->load->model('m_' . $this->response->meta->id);
                $this->response->dictionary = @$this->{'m_'.$this->response->meta->id}->dictionary();
            }
        } else {
            log_error('ERR-0002', $this->response->meta->collection . ':read');
            $this->session->set_flashdata('error', 'No object could be retrieved when ' . $this->response->meta->collection . ' called m_' . $this->response->meta->collection . '->read.');
            if ($this->response->meta->format !== 'json') {
                redirect($this->response->meta->collection);
            }
        }
        output($this->response);
    }

    /**
    * Delete an existing object
    *
    * @access public
    * @return NULL
    */
    public function delete()
    {
        if ($this->{'m_database'}->delete($this->response->meta->id)) {
            $this->response->data = array();
            $temp = new stdClass();
            $temp->type = 'database';
            $this->response->data[] = $temp;
            unset($temp);
            $this->session->set_flashdata('success', 'Object in database deleted.');
        } else {
            log_error('ERR-0013');
            $this->session->set_flashdata('error', 'Object in database not deleted.');
        }
        if ($this->response->meta->format === 'json') {
            output($this->response);
        } else {
            redirect('database');
        }
    }

    /**
    * Collection of objects
    *
    * @access public
    * @return NULL
    */
    public function collection()
    {
        $this->{'m_'.$this->response->meta->collection}->collection(0, 1);
        output($this->response);
    }

    /**
    * Execute one of various actions upon the database
    *
    * @access public
    * @return NULL
    */
    public function execute()
    {
        if ($this->response->meta->format === 'csv') {
            $this->response->meta->collection = $this->response->meta->id;
            $this->response->data = $this->m_database->execute($this->response->meta->id, 'export table', $this->response->meta->format);
            output($this->response);
            exit;
        }
        $this->data = $this->m_database->execute($this->response->meta->id, 'export table', $this->response->meta->format);
        $this->response->meta->action = 'read';
        $this->response->meta->format = 'screen';
        if ( ! empty($this->response->meta->groupby)) {
            $this->response->meta->format = 'json';
        }
        $this->response->special = $this->data;
        $this->response->errors = array();
        $this->response->data = $this->{'m_'.$this->response->meta->collection}->read($this->response->meta->id);
        if ( ! empty($this->response->data) && is_array($this->response->data)) {
            $this->response->meta->total = 1;
            $this->response->meta->filtered = 1;
            if ( ! empty($this->response->data[0]->attributes->org_id)) {
                $this->load->model('m_orgs');
                if ($this->response->meta->format === 'screen') {
                    $this->response->included = array_merge($this->response->included, $this->m_orgs->collection($this->user->id));
                } else if ($this->response->meta->format === 'json') {
                    $this->response->included = array_merge($this->response->included, $this->m_orgs->read($this->response->data[0]->attributes->org_id));
                }
            }
        } else {
            log_error('ERR-0002', $this->response->meta->collection . ':read');
            $this->session->set_flashdata('error', 'No object could be retrieved when ' . $this->response->meta->collection . ' called m_' . $this->response->meta->collection . '->read.');
            if ($this->response->meta->format !== 'json') {
                redirect($this->response->meta->collection);
            }
        }
        output($this->response);
    }

    /**
    * Drop a foreign key on a table
    *
    * @access public
    * @param  string $table The table to be altered
    * @param  string $key   The foreign key to be added
    * @return void
    */
    private function drop_foreign_key($table = '', $key = '')
    {
        if (empty($table) or empty($key)) {
            $this->log_db("WARNING - Required attributes not provided to 'drop_foreign_key'");
            return;
        }
        # SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'openaudit' AND `TABLE_NAME` = 'bios';
        # $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND `TABLE_NAME` = '" . $table . "'";
        # SHOW TABLES WHERE Tables_in_openaudit = 'ip';
        $sql = "SHOW TABLES WHERE Tables_in_" . $this->db->database . " = '" . $table . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 0) {
            $this->log_db("WARNING - Table '" . $table . "' does not exist as passed to 'drop_foreign_key'.");
            return;
        }
        # SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = 'openaudit' AND TABLE_NAME = 'bios' AND CONSTRAINT_NAME = 'bios_system_id';
        $sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = '" . $this->db->database . "' AND TABLE_NAME = '" . $table . "' AND CONSTRAINT_NAME = '" . $key . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 1) {
            $sql = "ALTER TABLE `" . $table . "` DROP FOREIGN KEY " . $key;
            $query = $this->db->query($sql);
            $this->log_db($this->db->last_query() . ';');
            if ($this->db->_error_message()) {
                $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
                log_error('ERR-0023', $this->db->_error_message());
            }
            return;
        } else {
            $this->log_db("WARNING - Key '" . $key . "' does not exist in table '" . $table . "' as passed to drop_foreign_key.");
            return;
        }
    }

    /**
     * Add a key on a table
     * @param string $table The table to be altered
     * @param string $key   The foreign key to be added
     * @param string $value [description]
     */
    private function add_foreign_key($table = '', $key = '', $value = '')
    {
        if ($table == '' or $key == '' or $value == '') {
            $this->log_db("WARNING - Required attributes not provided to 'add_foreign_key'");
            return;
        }
        # SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'openaudit' AND `TABLE_NAME` = 'bios';
        # $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND `TABLE_NAME` = '" . $table . "'";
        # SHOW TABLES WHERE Tables_in_openaudit = 'ip';
        $sql = "SHOW TABLES WHERE Tables_in_" . $this->db->database . " = '" . $table . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 0) {
            $this->log_db("WARNING - Table '" . $table . "' does not exist as passed to 'add_foreign_key'.");
            return;
        }

        # SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = 'openaudit' AND TABLE_NAME = 'bios' AND CONSTRAINT_NAME = 'bios_system_id';
        $sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = '" . $this->db->database . "' AND TABLE_NAME = '" . $table . "' AND CONSTRAINT_NAME = '" . $key . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 0) {
            # ALTER TABLE `bios` ADD CONSTRAINT `bios_system_id` FOREIGN KEY (system_id) REFERENCES system (system_id) ON DELETE CASCADE;
            $sql = "ALTER TABLE `" . $table . "` ADD CONSTRAINT `" . $key . "` " . $value;
            $query = $this->db->query($sql);
            $this->log_db($this->db->last_query() . ';');
            if ($this->db->_error_message()) {
                $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
                log_error('ERR-0023', $this->db->_error_message());
            }
            return;
        } else {
            $this->log_db("WARNING - Key '" . $key . "' already exists in table '" . $table . "' as passed to add_foreign_key.");
            return;
        }
    }

    /**
    * Drop a key on a table
    *
    * @access public
    * @param  string    table      The table to be altered
    * @param  string    key        The foreign key to be added
    * @return void
    */
    private function drop_key($table = '', $key = '')
    {
        if ($table == '' or $key == '') {
            $this->log_db("WARNING - Required attributes not provided to 'drop_key'");
            return;
        }
        # SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'openaudit' AND `TABLE_NAME` = 'bios';
        # $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND `TABLE_NAME` = '" . $table . "'";
        # SHOW TABLES WHERE Tables_in_openaudit = 'ip';
        $sql = "SHOW TABLES WHERE Tables_in_" . $this->db->database . " = '" . $table . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 0) {
            $this->log_db("WARNING - Table '" . $table . "' does not exist as passed to 'drop_key'.");
            return;
        }
        # SHOW INDEX FROM `system` WHERE Key_name = 'ip';
        $sql = "SHOW INDEX FROM `" . $table . "` WHERE Key_name = '" . $key . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 1) {
            $sql = "ALTER TABLE `" . $table . "` DROP KEY " . $key;
            $query = $this->db->query($sql);
            $this->log_db($this->db->last_query() . ';');
            if ($this->db->_error_message()) {
                $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
                log_error('ERR-0023', $this->db->_error_message());
            }
            return;
        } else {
            $this->log_db("WARNING - Key '" . $key . "' does not exist in table '" . $table . "' as passed to drop_key.");
            return;
        }
    }

    /**
    * Add a key on a table
    *
    * @access public
    * @param  string    table      The table to be altered
    * @param  string    key        The foreign key to be added
    * @return void
    */
    private function add_key($table = '', $key = '')
    {
        if ($table == '' or $key == '') {
            $this->log_db("WARNING - Required attributes not provided to 'add_key')");
            return;
        }
        # SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'openaudit' AND `TABLE_NAME` = 'bios';
        # $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND `TABLE_NAME` = '" . $table . "'";
        # SHOW TABLES WHERE Tables_in_openaudit = 'ip';
        $sql = "SHOW TABLES WHERE Tables_in_" . $this->db->database . " = '" . $table . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 0) {
            $this->log_db("WARNING - Table '" . $table . "' does not exist as passed to 'add_key'.");
            return;
        }
        # SHOW INDEX FROM `system` WHERE Key_name = 'ip';
        $sql = "SHOW INDEX FROM `" . $table . "` WHERE Key_name = '" . $key . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 0) {
            $sql = "ALTER TABLE `" . $table . "` ADD KEY `" . $key . "` (`" . $key . "`)";
            $query = $this->db->query($sql);
            $this->log_db($this->db->last_query() . ';');
            if ($this->db->_error_message()) {
                $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
                log_error('ERR-0023', $this->db->_error_message());
            }
            return;
        } else {
            $this->log_db("WARNING - Key '" . $key . "' already exists in table '" . $table . "' as passed to add_key.");
            return;
        }
    }

    /**
    * Drop a database table if it exists
    *
    * @access public
    * @param  string    table      The table to be altered
    * @return void
    */
    private function drop_table($table = '')
    {
        if ($table == '') {
            $this->log_db("Required attribute 'table' not provided to 'alter_table'");
        }
        # DROP TABLE `bios` IF EXISTS;
        $sql = "DROP TABLE IF EXISTS `" . $table . "`";
        $this->db->query($sql);
        $this->log_db($this->db->last_query() . ';');
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
        }
        return;
    }


    /**
    * Rename a database table if it exists
    *
    * @access public
    * @param  string    table      The table to be renamed
    * @param  string    new_table  The new name for the table
    * @return void
    */
    private function rename_table($table = '', $new_table = '')
    {
        if ($table == '') {
            $this->log_db("WARNING - Required attribute 'table' not provided to 'rename_table'");
            return;
        }
        if ($new_table == '') {
            $this->log_db("WARNING - Required attribute 'new_table' not provided to 'rename_table'");
            return;
        }
        # SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'openaudit' AND `TABLE_NAME` = 'bios';
        $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND `TABLE_NAME` = '" . $table . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) === 0) {
            $this->log_db("WARNING - Table '" . $table . "' does not exist as passed to 'rename_table'.");
            return;
        }
        # RENAME TABLE `sys_hw_bios` TO `bios`;
        $sql = "RENAME TABLE `" . $table . "` TO `" . $new_table . "`";
        $query = $this->db->query($sql);
        $this->log_db($this->db->last_query() . ';');
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
        }
        return;
    }

    /**
    * Execute one of various actions upon the database
    *
    * @access public
    * @param  string    table     The table to be altered
    * @param  string    column    The column to be altered
    * @param  string    change    The change to perform on the table / column - change, drop, add.
    * @param  string    type      Either change, add or drop
    * @return void
    */
    private function alter_table($table = '', $column = '', $change = '', $type = 'change')
    {
        if ($table == '') {
            $this->log_db("WARNING - Required attribute 'table' not provided to 'alter_table' when attempting to run " . $change);
            return;
        }
        if ($change == '') {
            $this->log_db("WARNING - Required attribute 'change' not provided to 'alter_table' when changing '$table'");
            return;
        }
        if (empty($type) or ($type !== 'change' and $type !== 'add' and $type !== 'drop')) {
            $this->log_db("WARNING - Required attribute 'type' not provided to 'alter_table' when attempting to run " . $change);
            return;
        }
        if (empty($column) and $type !== 'add') {
            $this->log_db("WARNING - Required attribute 'column' not provided to 'alter_table' when attempting to run " . $change);
            return;
        }
        # Check we have a table named $table in the DB
        # SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'openaudit' AND `TABLE_NAME` = 'bios';
        $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . $this->db->database . "' AND `TABLE_NAME` = '" . $table . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if (count($result) !== 1) {
            $this->log_db("WARNING - Table '" . $table . "' does not exist as passed to 'alter_table'.");
            return;
        }
        # See if we have a column named $column in the DB
        # SHOW COLUMNS FROM `ip` WHERE Field = 'netmask';
        $sql = "SHOW COLUMNS FROM `" . $table . "` WHERE Field = '" . $column . "'";
        $query = $this->db->query($sql);
        $result = $query->result();
        if ($this->db->_error_message()) {
            $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
            log_error('ERR-0023', $this->db->_error_message());
            return;
        }
        if ($type === 'change' or $type === 'drop') {
            if (count($result) === 1) {
                if ($type === 'change') {
                    $sql = "ALTER TABLE `" . $table . "` CHANGE `" . $column . "` " . $change;
                    $query = $this->db->query($sql);
                    $this->log_db($this->db->last_query() . ';');
                    if ($this->db->_error_message()) {
                        $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
                        log_error('ERR-0023', $this->db->_error_message());
                    }
                    return;
                } else {
                    $sql = "ALTER TABLE `" . $table . "` " . $change;
                    $query = $this->db->query($sql);
                    $this->log_db($this->db->last_query() . ';');
                    if ($this->db->_error_message()) {
                        $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
                        log_error('ERR-0023', $this->db->_error_message());
                    }
                    return;
                }
            } else {
                if ($type === 'change') {
                    $this->log_db("WARNING - Column '" . $column . "' does not exist in '" . $table . "' when it should as passed to 'alter_table'.");
                } else {
                    $this->log_db("NOTICE - Column '" . $column . "' does not exist in '" . $table . "' when it should as passed to 'alter_table' for drop column.");
                }
                return;
            }
        } else {
            if (count($result) === 0) {
                $sql = "ALTER TABLE `" . $table . "` " . $change;
                $temp_debug = $this->db->db_debug;
                $this->db->db_debug = false;
                $query = $this->db->query($sql);
                $this->db->db_debug = $temp_debug;
                $this->log_db($this->db->last_query() . ';');
                if ($this->db->_error_message()) {
                    $this->log_db('ERROR - ' . $this->db->_error_message(), 3);
                    log_error('ERR-0023', $this->db->_error_message());
                }
                return;
            } else {
                $this->log_db("WARNING - Column '" . $column . "' exists in '" . $table . "' when it should not as passed to 'alter_table'.");
                return;
            }
        }
    }

    /**
     * [log_db description]
     * @param  string  $message  Your message
     * @param  integer $severity The severity as per standard logging
     * @return [type]            Does not return anything
     */
    private function log_db($message = '', $severity = 5)
    {
        $log = new stdClass();
        $log->type = 'system';
        $log->severity = $severity;
        $log->user = $this->user->full_name;
        $log->collection = 'database';
        $log->action = 'upgrade';
        $log->status = 'running sql';
        $log->summary = 'running sql';
        $log->detail = $message;
        stdlog($log);
        unset($log);
        $this->data['output'] .= $message . "\n\n";
    }

    /**
     * [update description]
     * @return [type] [description]
     */
    public function update()
    {

        // Our update form
        if (strtoupper($this->input->server('REQUEST_METHOD')) === 'GET') {
            $this->response->include = 'v_database_update_form';
            output($this->response);
            exit;
        }

        error_reporting(E_ALL);

        $log = new stdClass();
        $log->type = 'system';
        $log->severity = 7;
        $log->user = $this->user->full_name;
        $log->collection = 'database';
        $log->action = 'upgrading database';
        $log->status = 'running sql';
        $log->summary = 'running sql';
        $log->detail = 'log test';
        stdlog($log);

        $this->log_db = array();
        $db_internal_version = $this->config->config['internal_version'];
        $this->data['output'] = "";
        
        include "db_upgrades/db_1.08.04.php";

        if (($db_internal_version < '20160104') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.10
            include "db_upgrades/db_1.10.00.php";
        }

        if (($db_internal_version < '20160126') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.10.1
            include "db_upgrades/db_1.10.01.php";
        }

        if (($db_internal_version < '20160130') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.12
            include "db_upgrades/db_1.12.00.php";
        }

        if (($db_internal_version < '20160303') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.12.2
            include "db_upgrades/db_1.12.02.php";
        }

        if (($db_internal_version < '20160401') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.12.4
            include "db_upgrades/db_1.12.04.php";
        }

        if (($db_internal_version < '20160409') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.12.6
            include "db_upgrades/db_1.12.06.php";
        }

        if (($db_internal_version < '20160620') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.12.8
            include "db_upgrades/db_1.12.08.00.php";
        }

        if (($db_internal_version < '20160810') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.12.8.1
            include "db_upgrades/db_1.12.08.01.php";
        }

        if (($db_internal_version < '20160811') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.12.10
            include "db_upgrades/db_1.12.10.php";
        }

        if (($db_internal_version < '20160904') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.14
            include "db_upgrades/db_1.14.00.php";
        }

        if (($db_internal_version < '20161130') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.14.2
            include "db_upgrades/db_1.14.02.php";
        }

        if (($db_internal_version < '20170104') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 1.14.4
            include "db_upgrades/db_1.14.04.php";
        }

        if (($db_internal_version < '20170601') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0a (v2 alpha)
            include "db_upgrades/db_2.0.0a.php";
        }

        if (($db_internal_version < '20170620') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.1
            include "db_upgrades/db_2.0.1.php";
        }

        if (($db_internal_version < '20170701') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.2
            include "db_upgrades/db_2.0.2.php";
        }

        if (($db_internal_version < '20170719') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.3
            include "db_upgrades/db_2.0.3.php";
        }

        if (($db_internal_version < '20170810') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.4
            include "db_upgrades/db_2.0.4.php";
        }

        if (($db_internal_version < '20170820') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.6
            include "db_upgrades/db_2.0.6.php";
        }

        if (($db_internal_version < '20170919') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.8
            include "db_upgrades/db_2.0.8.php";
        }

        if (($db_internal_version < '20171010') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.10
            include "db_upgrades/db_2.0.10.php";
        }

        if (($db_internal_version < '20171025') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.11
            include "db_upgrades/db_2.0.11.php";
        }

        if (($db_internal_version < '20171130') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.0.12
            include "db_upgrades/db_2.0.12.php";
        }

        if (($db_internal_version < '20171225') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.1.0
            include "db_upgrades/db_2.1.0.php";
        }

        if (($db_internal_version < '20180105') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.1.1
            include "db_upgrades/db_2.1.1.php";
        }

        if (($db_internal_version < '20180218') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.0
            include "db_upgrades/db_2.2.0.php";
        }

        if (($db_internal_version < '20180512') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.1
            include "db_upgrades/db_2.2.1.php";
        }

        if (($db_internal_version < '20180620') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.2
            include "db_upgrades/db_2.2.2.php";
        }

        if (($db_internal_version < '20180625') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.3
            include "db_upgrades/db_2.2.3.php";
        }

        if (($db_internal_version < '20180630') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.4
            include "db_upgrades/db_2.2.4.php";
        }

        if (($db_internal_version < '20180705') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.5
            include "db_upgrades/db_2.2.5.php";
        }

        if (($db_internal_version < '20180710') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.6
            include "db_upgrades/db_2.2.6.php";
        }

        if (($db_internal_version < '20180810') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.2.7
            include "db_upgrades/db_2.2.7.php";
        }

        if (($db_internal_version < '20180925') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.3.0
            include "db_upgrades/db_2.3.0.php";
        }

        if (($db_internal_version < '20181212') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.3.1
            include "db_upgrades/db_2.3.1.php";
        }

        if (($db_internal_version < '20181225') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.3.2
            include "db_upgrades/db_2.3.2.php";
            $this->data['warning'] = "As at Open-AudIT 2.3.2 we have changed the default Nmap discovery options. Please see the wiki page at <a href=\"https://community.opmantek.com/display/OA/Discovery+Scan+Options\">https://community.opmantek.com/display/OA/Discovery+Scan+Options</a> for more details.";
        }

        if (($db_internal_version < '20190211') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 2.3.3
            include "db_upgrades/db_2.3.3.php";
        }

        if (($db_internal_version < '20190214') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.0.0
            include "db_upgrades/db_3.0.0.php";
        }

        if (($db_internal_version < '20190301') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.0.1
            include "db_upgrades/db_3.0.1.php";
        }

        if (($db_internal_version < '20190401') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.0.2
            include "db_upgrades/db_3.0.2.php";
        }

        if (($db_internal_version < '20190512') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.1.0
            include "db_upgrades/db_3.1.0.php";
        }

        if (($db_internal_version < '20190620') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.1.1
            include "db_upgrades/db_3.1.1.php";
        }

        if (($db_internal_version < '20190630') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.1.2
            include "db_upgrades/db_3.1.2.php";
        }

        if (($db_internal_version < '20190810') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.2.0
            include "db_upgrades/db_3.2.0.php";
        }

        if (($db_internal_version < '20190919') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.2.1
            include "db_upgrades/db_3.2.1.php";
        }

        if (($db_internal_version < '20190923') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.2.2
            include "db_upgrades/db_3.2.2.php";
        }

        if (($db_internal_version < '20191010') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.3.0
            include "db_upgrades/db_3.3.0.php";
        }

        if (($db_internal_version < '20200420') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.3.1
            include "db_upgrades/db_3.3.1.php";
        }

        if (($db_internal_version < '20200512') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.3.2
            include "db_upgrades/db_3.3.2.php";
        }

        if (($db_internal_version < '20200620') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.4.0
            include "db_upgrades/db_3.4.0.php";
        }

        if (($db_internal_version < '20200810') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.5.0
            include "db_upgrades/db_3.5.0.php";
        }

        if (($db_internal_version < '20201012') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.5.1
            include "db_upgrades/db_3.5.1.php";
        }

        if (($db_internal_version < '20201115') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.5.2
            include "db_upgrades/db_3.5.2.php";
        }

        if (($db_internal_version < '20210104') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.5.3
            include "db_upgrades/db_3.5.3.php";
        }

        if (($db_internal_version < '20210126') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 3.5.4
            include "db_upgrades/db_3.5.4.php";
        }

        if (($db_internal_version < '20210512') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 4.1.1
            include "db_upgrades/db_4.1.1.php";
        }

        if (($db_internal_version < '20210620') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 4.1.2
            include "db_upgrades/db_4.1.2.php";
        }

        if (($db_internal_version < '20210810') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 4.2.0
            include "db_upgrades/db_4.2.0.php";
        }

        if (($db_internal_version < '20211112') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 4.3.0
            include "db_upgrades/db_4.3.0.php";
        }

        if (($db_internal_version < '20211213') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 4.3.1
            include "db_upgrades/db_4.3.1.php";
        }

        if (($db_internal_version < '20220126') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 4.3.2
            include "db_upgrades/db_4.3.2.php";
        }

        if (($db_internal_version < '20220321') and ($this->db->platform() == 'mysql' or $this->db->platform() == 'mysqli')) {
            # upgrade for 4.3.3
            include "db_upgrades/db_4.3.3.php";
        }

        $this->data['include'] = 'v_database_update';
        $this->data['heading'] = 'Database Upgrade';
        $this->data['success'] = "Database upgraded successfully. New database version is ".$this->config->config['display_version']." (".$this->config->config['internal_version'].")";
        $this->response->include = 'v_database_update';

        $sql = "SELECT * FROM `logs` WHERE (detail LIKE 'WARNING - %' OR detail LIKE 'NOTICE - %' OR detail LIKE 'ERROR - %') AND request_microtime = " . $this->config->config['microtime'];
        $query = $this->db->query($sql);
        $result = $query->result();
        $this->data['issues'] = $result;


        $this->load->view('v_template', $this->data);
    }
}


// End of file darabase.php
// Location: ./controllers/database.php

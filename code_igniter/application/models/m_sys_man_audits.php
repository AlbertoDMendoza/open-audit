<?php
/**
 * OAv2
 *
 * An open source network auditing application
 *
 * @package OAv2
 * @author Mark Unwin <mark.unwin@gmail.com>
 * @version beta 8
 * @copyright Copyright (c) 2011, Mark Unwin
 * @license http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
 */

class M_sys_man_audits extends MY_Model {

	function __construct() {
		parent::__construct();
	}

	function get_system_audits($system_id) {
		$sql = "SELECT 
				system_audits_id, 
				system_audits_username, 
				system_audits_type, 
				system_audits_time, 
				sys_man_audits.timestamp
			FROM 
				sys_man_audits,
				system
			WHERE 
				sys_man_audits.system_id = system.system_id AND
				system.system_id = ?
			GROUP BY 
				system_audits_id";
		$sql = $this->clean_sql($sql);
		$data = array($system_id);
		$query = $this->db->query($sql, $data);
		$result = $query->result();
		return ($result);
	}

	function insert_audit($details) {
		// TODO - create and use an "audits" section in audit_windows.vbs
		$sql = "INSERT INTO sys_man_audits ( system_id, system_audits_username, system_audits_type, system_audits_time, timestamp ) VALUES (?, ?, ?, ?, ?)";
		$data = array("$details->system_id", "web page insert", "online", "$details->timestamp", date('Y-m-d G:i:s'));
		$query = $this->db->query($sql, $data);
	}
	
	function update_audit($details, $update) {
		$sql = "UPDATE sys_man_audits SET audit_debug = ? WHERE system_id = ? AND system_audits_time = ?";
		$data = array("$update", "$details->system_id", "$details->timestamp");
		$query = $this->db->query($sql, $data);
	}

	function update_wmi_fails($input, $details) {
		$sql = "UPDATE sys_man_audits SET audit_wmi_fails = ? WHERE system_id = ? AND system_audits_time = ?";
		$data = array("$input->audit_wmi_fails", "$details->system_id", "$details->timestamp");
		$query = $this->db->query($sql, $data);
	}
}
?>
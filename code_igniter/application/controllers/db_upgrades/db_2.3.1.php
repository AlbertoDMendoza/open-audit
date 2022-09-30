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
**/

$this->log_db('Upgrade database to 2.3.1 commenced');

# configuration
$value = $this->config->config['default_network_address'];
if (!empty($value)) {
	$value = str_replace('https://', '', $value);
	$value = str_replace('http://', '', $value);
	$value = str_replace('/open-audit/', '', $value);
	$value = str_replace('/open-audit', '', $value);
	$value = 'http://' . $value . '/open-audit/';
} else {
	$value = '';
}
$data = array($value);
$sql = "UPDATE `configuration` SET `value` = ? WHERE `name` = 'default_network_address'";
$this->db->query($sql, $data);
$this->log_db($this->db->last_query());

$sql = "DELETE FROM `configuration` WHERE `name` = 'discovery_scan_limit'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO `configuration` VALUES (NULL,'discovery_scan_limit','50','number','y','system','2000-01-01 00:00:00','The maximum number of concurrent device scans we should process.')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

# locations
$this->alter_table('locations', 'cloud_id', "ADD `cloud_id` int(10) unsigned DEFAULT NULL AFTER `geo`", 'add');

# networks
$this->alter_table('networks', 'cloud_id', "ADD `cloud_id` int(10) unsigned DEFAULT NULL AFTER `external_ident`", 'add');

# queue
$this->alter_table('queue', 'pid', "ADD `pid` int(10) unsigned NOT NULL DEFAULT '0' AFTER `type`", 'add');

# system
$this->alter_table('system', 'storage_count', "ADD `storage_count` int(10) unsigned NOT NULL DEFAULT '0' AFTER `processor_count`", 'add');
$this->alter_table('system', 'discovery_id', "ADD `discovery_id` int(10) unsigned DEFAULT NULL AFTER `instance_options`", 'add');

# widgets
$sql = "DELETE FROM `widgets` WHERE `name` = 'Last Seen By' AND edited_by = 'system'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO `widgets` VALUES (NULL,'Last Seen By',1,'','pie','','system.last_seen_by','','','Devices Last Seen By','','',0,'','','','system','2000-01-01')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `widgets` SET `link` = 'devices?@description&properties=system.id,system.type,system.name,system.ip,system.os_family,system.last_seen,system.status' WHERE `name` = 'Devices Not Seen (0-180+ Days)' AND `edited_by` = 'system'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

# set our versions
$sql = "UPDATE `configuration` SET `value` = '20181212' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET `value` = '2.3.1' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$this->log_db("Upgrade database to 2.3.1 completed");
$this->config->config['internal_version'] = '20181212';
$this->config->config['display_version'] = '2.3.1';

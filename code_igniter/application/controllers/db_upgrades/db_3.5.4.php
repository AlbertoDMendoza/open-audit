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
#  www.firstwave.com or email sales@firstwave.com
#
# *****************************************************************************
*
**/

/*
UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table = \'file\'' WHERE name = 'Files';

UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table in (\'bios\', \'disk\', \'memory\', \'module\', \'monitor\', \'motherboard\', \'optical\', \'partition\', \'processor\', \'network\', \'scsi\', \'sound\', \'video\')' WHERE `name` = 'Hardware';

UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table = \'system\'' WHERE `name` = 'New Devices';

UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table in (\'dns\', \'ip\', \'log\', \'netstat\', \'pagefile\', \'print_queue\', \'route\', \'task\', \'user\', \'user_group\', \'variable\')' WHERE `name` = 'Settings';

UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table in (\'service\', \'server\', \'server_item\', \'software\', \'software_key\')' WHERE `name` = 'Software';

DROP TABLE IF EXISTS `radio`;

CREATE TABLE `radio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `system_id` int(10) unsigned DEFAULT NULL,
  `current` enum('y','n') NOT NULL DEFAULT 'y',
  `first_seen` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `last_seen` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `name` varchar(200) NOT NULL DEFAULT '',
  `net_index` varchar(200) NOT NULL DEFAULT '',
  `rx_level` varchar(200) NOT NULL DEFAULT '',
  `rx_profile` varchar(200) NOT NULL DEFAULT '',
  `rx_freq` varchar(200) NOT NULL DEFAULT '',
  `rx_power` varchar(200) NOT NULL DEFAULT '',
  `rx_bitrate` varchar(200) NOT NULL DEFAULT '',
  `tx_level` varchar(200) NOT NULL DEFAULT '',
  `tx_profile` varchar(200) NOT NULL DEFAULT '',
  `tx_freq` varchar(200) NOT NULL DEFAULT '',
  `tx_power` varchar(200) NOT NULL DEFAULT '',
  `tx_bitrate` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `system_id` (`system_id`),
  CONSTRAINT `radio_system_id` FOREIGN KEY (`system_id`) REFERENCES `system` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELETE FROM `configuration` WHERE name = 'create_change_log_radio';

INSERT INTO `configuration` VALUES (NULL,'create_change_log_radio','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the radio table.');

DELETE FROM `configuration` WHERE name = 'delete_noncurrent_radio';

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_radio','y','bool','y','system','2000-01-01 00:00:00','Should we delete non-current radio data.');


ALTER TABLE discoveries DROP IF EXISTS subnet;

ALTER TABLE discoveries ADD subnet VARCHAR(45) NOT NULL DEFAULT '' AFTER type;

ALTER TABLE discoveries DROP IF EXISTS seed_ip;

ALTER TABLE discoveries ADD seed_ip varchar(45) NOT NULL DEFAULT '' AFTER subnet;

ALTER TABLE discoveries DROP IF EXISTS seed_restrict_to_subnet;

ALTER TABLE discoveries ADD seed_restrict_to_subnet enum('y','n') NOT NULL DEFAULT 'y' AFTER seed_ip;

ALTER TABLE discoveries DROP IF EXISTS seed_restrict_to_private;

ALTER TABLE discoveries ADD seed_restrict_to_private enum('y','n') NOT NULL DEFAULT 'y' AFTER seed_restrict_to_subnet;

ALTER TABLE discoveries DROP IF EXISTS seed_ping;

ALTER TABLE discoveries ADD seed_ping enum('y','n') NOT NULL DEFAULT 'y' AFTER seed_restrict_to_private;

ALTER TABLE discoveries DROP IF EXISTS ad_domain;

ALTER TABLE discoveries ADD ad_domain varchar(200) NOT NULL DEFAULT '' AFTER seed_ping;

ALTER TABLE discoveries DROP IF EXISTS ad_server;

ALTER TABLE discoveries ADD ad_server varchar(45) NOT NULL DEFAULT '' AFTER ad_domain;

ALTER TABLE discoveries DROP IF EXISTS options;

ALTER TABLE discoveries DROP IF EXISTS scan_options;

ALTER TABLE discoveries ADD scan_options text NOT NULL AFTER other;

ALTER TABLE discoveries DROP IF EXISTS match_options;

ALTER TABLE discoveries ADD match_options text NOT NULL AFTER scan_options;

ALTER TABLE discoveries DROP IF EXISTS command_options;

ALTER TABLE discoveries ADD command_options text NOT NULL AFTER match_options;


ALTER TABLE discovery_scan_options DROP IF EXISTS ports_in_order;

ALTER TABLE discovery_scan_options ADD `ports_in_order` enum('','y','n') NOT NULL DEFAULT 'n' after options;

ALTER TABLE discovery_scan_options DROP IF EXISTS ports_stop_after;

ALTER TABLE discovery_scan_options ADD `ports_stop_after` tinyint(3) unsigned NOT NULL DEFAULT 0 after ports_in_order;

ALTER TABLE discovery_scan_options DROP IF EXISTS command_options;

ALTER TABLE discovery_scan_options ADD command_options text NOT NULL AFTER ports_stop_after;

ALTER TABLE discovery_scan_options DROP IF EXISTS snmp_timeout;

ALTER TABLE discovery_scan_options ADD snmp_timeout tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER timeout;

ALTER TABLE discovery_scan_options DROP IF EXISTS ssh_timeout;

ALTER TABLE discovery_scan_options ADD ssh_timeout tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER snmp_timeout;

ALTER TABLE discovery_scan_options DROP IF EXISTS wmi_timeout;

ALTER TABLE discovery_scan_options ADD wmi_timeout tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER ssh_timeout;

ALTER TABLE discovery_scan_options DROP IF EXISTS script_timeout;

ALTER TABLE discovery_scan_options ADD script_timeout tinyint(5) unsigned NOT NULL DEFAULT '0' AFTER wmi_timeout;

ALTER TABLE networks CHANGE `secutity_zone` `security_zone` varchar(200) NOT NULL DEFAULT '';

ALTER TABLE networks ADD `admin_status` enum('allocated','delegated','planning','reserved','unallocated','unknown','unmanaged') NOT NULL DEFAULT 'allocated' AFTER security_zone;

ALTER TABLE networks ADD `environment` varchar(100) NOT NULL DEFAULT 'Production' AFTER admin_status;

UPDATE rules SET weight = 90 WHERE name like 'Form Factor based on Manufacturer (like %';

ALTER TABLE system DROP IF EXISTS os_arch;
ALTER TABLE system ADD `os_arch` varchar(50) NOT NULL DEFAULT '' AFTER os_bit;

ALTER TABLE system DROP IF EXISTS os_license;
ALTER TABLE system ADD `os_license` varchar(250) NOT NULL DEFAULT '' AFTER os_arch;

ALTER TABLE system DROP IF EXISTS os_license_code;
ALTER TABLE system ADD `os_license_code` varchar(250) NOT NULL DEFAULT '' AFTER os_license;

ALTER TABLE system DROP IF EXISTS os_license_mode;
ALTER TABLE system ADD `os_license_mode` varchar(250) NOT NULL DEFAULT '' AFTER os_license_code;

ALTER TABLE system DROP IF EXISTS os_license_type;
ALTER TABLE system ADD `os_license_type` varchar(250) NOT NULL DEFAULT '' AFTER os_license_mode;

ALTER TABLE system DROP IF EXISTS os_licence_expiry;
ADD os_licence_expiry date NOT NULL DEFAULT '2000-01-01' AFTER os_license_type;

UPDATE `roles` SET permissions = \'{"collectors":"crud","configuration":"r","credentials":"crud","dashboards":"r","devices":"crud","discoveries":"crud","discovery_scan_options":"crud","locations":"crud","networks":"crud","orgs":"crud","sessions":"crud","tasks":"crud","users":"r","widgets":"r"}\' WHERE name = "collector";

UPDATE `configuration` SET `value` = '20210126' WHERE `name` = 'internal_version';

UPDATE `configuration` SET `value` = '3.5.4' WHERE `name` = 'display_version';
*/

$this->log_db('Upgrade database to 3.5.4 commenced');

$sql = "UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table = \'file\'' WHERE name = 'Files'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');


$sql = "UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table in (\'bios\', \'disk\', \'memory\', \'module\', \'monitor\', \'motherboard\', \'optical\', \'partition\', \'processor\', \'network\', \'scsi\', \'sound\', \'video\')' WHERE `name` = 'Hardware'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');


$sql = "UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table = \'system\'' WHERE `name` = 'New Devices'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');


$sql = "UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table in (\'dns\', \'ip\', \'log\', \'netstat\', \'pagefile\', \'print_queue\', \'route\', \'task\', \'user\', \'user_group\', \'variable\')' WHERE `name` = 'Settings'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');


$sql = "UPDATE `queries` SET `sql` = 'SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, change_log.timestamp AS `change_log.timestamp`, change_log.db_table AS `change_log.db_table`, change_log.db_action AS `change_log.db_action`, change_log.details AS `change_log.details`, change_log.id AS `change_log.id`, CONCAT(\"devices?sub_resource=change_log&change_log.id=\", change_log.id) AS `link` FROM change_log LEFT JOIN system ON (change_log.system_id = system.id) WHERE @filter AND change_log.ack_time = \'2000-01-01 00:00:00\' AND change_log.db_table in (\'service\', \'server\', \'server_item\', \'software\', \'software_key\')' WHERE `name` = 'Software'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DROP TABLE IF EXISTS `radio`";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "CREATE TABLE `radio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `system_id` int(10) unsigned DEFAULT NULL,
  `current` enum('y','n') NOT NULL DEFAULT 'y',
  `first_seen` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `last_seen` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `name` varchar(200) NOT NULL DEFAULT '',
  `net_index` varchar(200) NOT NULL DEFAULT '',
  `rx_level` varchar(200) NOT NULL DEFAULT '',
  `rx_profile` varchar(200) NOT NULL DEFAULT '',
  `rx_freq` varchar(200) NOT NULL DEFAULT '',
  `rx_power` varchar(200) NOT NULL DEFAULT '',
  `rx_bitrate` varchar(200) NOT NULL DEFAULT '',
  `tx_level` varchar(200) NOT NULL DEFAULT '',
  `tx_profile` varchar(200) NOT NULL DEFAULT '',
  `tx_freq` varchar(200) NOT NULL DEFAULT '',
  `tx_power` varchar(200) NOT NULL DEFAULT '',
  `tx_bitrate` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `system_id` (`system_id`),
  CONSTRAINT `radio_system_id` FOREIGN KEY (`system_id`) REFERENCES `system` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DELETE FROM `configuration` WHERE name = 'create_change_log_radio'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_radio','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the radio table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DELETE FROM `configuration` WHERE name = 'delete_noncurrent_radio'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_radio','y','bool','y','system','2000-01-01 00:00:00','Should we delete non-current radio data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

if ($this->db->field_exists('subnet', 'discoveries')) {
    $this->alter_table('discoveries', 'subnet', "DROP `subnet`", 'drop');
}
$this->alter_table('discoveries', 'subnet', "ADD subnet varchar(45) NOT NULL DEFAULT '' AFTER type", 'add');


if ($this->db->field_exists('seed_ip', 'discoveries')) {
    $this->alter_table('discoveries', 'seed_ip', "DROP `seed_ip`", 'drop');
}
$this->alter_table('discoveries', 'seed_ip', "ADD seed_ip varchar(45) NOT NULL DEFAULT '' AFTER subnet", 'add');


if ($this->db->field_exists('seed_restrict_to_subnet', 'discoveries')) {
    $this->alter_table('discoveries', 'seed_restrict_to_subnet', "DROP `seed_restrict_to_subnet`", 'drop');
}
$this->alter_table('discoveries', 'seed_restrict_to_subnet', "ADD seed_restrict_to_subnet enum('y','n') NOT NULL DEFAULT 'y' AFTER seed_ip", 'add');


if ($this->db->field_exists('seed_restrict_to_private', 'discoveries')) {
    $this->alter_table('discoveries', 'seed_restrict_to_private', "DROP `seed_restrict_to_private`", 'drop');
}
$this->alter_table('discoveries', 'seed_restrict_to_private', "ADD seed_restrict_to_private enum('y','n') NOT NULL DEFAULT 'y' AFTER seed_restrict_to_subnet", 'add');


if ($this->db->field_exists('seed_ping', 'discoveries')) {
    $this->alter_table('discoveries', 'seed_ping', "DROP `seed_ping`", 'drop');
}
$this->alter_table('discoveries', 'seed_ping', "ADD seed_ping enum('y','n') NOT NULL DEFAULT 'y' AFTER seed_restrict_to_private", 'add');


if ($this->db->field_exists('ad_domain', 'discoveries')) {
    $this->alter_table('discoveries', 'ad_domain', "DROP `ad_domain`", 'drop');
}
$this->alter_table('discoveries', 'ad_domain', "ADD ad_domain varchar(200) NOT NULL DEFAULT '' AFTER seed_ping", 'add');


if ($this->db->field_exists('ad_server', 'discoveries')) {
    $this->alter_table('discoveries', 'ad_server', "DROP `ad_server`", 'drop');
}
$this->alter_table('discoveries', 'ad_server', "ADD ad_server varchar(45) NOT NULL DEFAULT '' AFTER ad_domain", 'add');


if ($this->db->field_exists('options', 'discoveries')) {
    $this->alter_table('discoveries', 'options', "DROP `options`", 'drop');
}

if ($this->db->field_exists('scan_options', 'discoveries')) {
    $this->alter_table('discoveries', 'scan_options', "DROP `scan_options`", 'drop');
}
$this->alter_table('discoveries', 'scan_options', "ADD scan_options text NOT NULL AFTER other", 'add');


if ($this->db->field_exists('match_options', 'discoveries')) {
    $this->alter_table('discoveries', 'match_options', "DROP `match_options`", 'drop');
}
$this->alter_table('discoveries', 'match_options', "ADD match_options text NOT NULL AFTER scan_options", 'add');


if ($this->db->field_exists('command_options', 'discoveries')) {
    $this->alter_table('discoveries', 'command_options', "DROP `command_options`", 'drop');
}
$this->alter_table('discoveries', 'command_options', "ADD command_options text NOT NULL AFTER match_options", 'add');

$sql = "SELECT * FROM discoveries";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query() . ';');
$result = $query->result();

foreach ($result as $item) {
	$id = $item->id;
	$json = json_decode($item->other);
	$subnet = @$json->subnet;
	$ad_server = @$json->ad_server;
	$ad_domain = @$json->ad_domain;
    if (isset($json->nmap->discovery_scan_option_id)) {
        $json->nmap->id = intval($json->nmap->discovery_scan_option_id);
        unset($json->nmap->discovery_scan_option_id);
    }
	$scan_options = $json->nmap;
	$scan_options = json_encode($scan_options);
	$match_options = $json->match;
	$match_options = json_encode($match_options);
	$sql = 'UPDATE discoveries SET subnet = ?, ad_server = ?, ad_domain = ?, scan_options = ?, match_options = ? WHERE id = ?';
	$data = array($subnet, $ad_server, $ad_domain, $scan_options, $match_options, $id);
	$this->db->query($sql, $data);
	$this->log_db($this->db->last_query() . ';');
}

if ($this->db->field_exists('ports_in_order', 'discovery_scan_options')) {
    $this->alter_table('discovery_scan_options', 'ports_in_order', "DROP `ports_in_order`", 'drop');
}
$this->alter_table('discovery_scan_options', 'ports_in_order', "ADD ports_in_order enum('','y','n') NOT NULL DEFAULT 'n' AFTER options", 'add');


if ($this->db->field_exists('ports_stop_after', 'discovery_scan_options')) {
    $this->alter_table('discovery_scan_options', 'ports_stop_after', "DROP `ports_stop_after`", 'drop');
}
$this->alter_table('discovery_scan_options', 'ports_stop_after', "ADD ports_stop_after tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER ports_in_order", 'add');


if ($this->db->field_exists('command_options', 'discovery_scan_options')) {
    $this->alter_table('discovery_scan_options', 'command_options', "DROP `command_options`", 'drop');
}
$this->alter_table('discovery_scan_options', 'command_options', "ADD command_options text NOT NULL AFTER ports_stop_after", 'add');


if ($this->db->field_exists('snmp_timeout', 'discovery_scan_options')) {
    $this->alter_table('discovery_scan_options', 'snmp_timeout', "DROP `snmp_timeout`", 'drop');
}
$this->alter_table('discovery_scan_options', 'snmp_timeout', "ADD snmp_timeout tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER timeout", 'add');


if ($this->db->field_exists('ssh_timeout', 'discovery_scan_options')) {
    $this->alter_table('discovery_scan_options', 'ssh_timeout', "DROP `ssh_timeout`", 'drop');
}
$this->alter_table('discovery_scan_options', 'ssh_timeout', "ADD ssh_timeout tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER snmp_timeout", 'add');


if ($this->db->field_exists('wmi_timeout', 'discovery_scan_options')) {
    $this->alter_table('discovery_scan_options', 'wmi_timeout', "DROP `wmi_timeout`", 'drop');
}
$this->alter_table('discovery_scan_options', 'wmi_timeout', "ADD wmi_timeout tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER ssh_timeout", 'add');


if ($this->db->field_exists('script_timeout', 'discovery_scan_options')) {
    $this->alter_table('discovery_scan_options', 'script_timeout', "DROP `script_timeout`", 'drop');
}
$this->alter_table('discovery_scan_options', 'script_timeout', "ADD script_timeout tinyint(5) unsigned NOT NULL DEFAULT '0' AFTER wmi_timeout", 'add');

$this->alter_table('networks', 'secutity_zone', "security_zone varchar(200) NOT NULL DEFAULT ''", 'change');

if ($this->db->field_exists('admin_status', 'networks')) {
    $this->alter_table('networks', 'admin_status', "DROP `admin_status`", 'drop');
}
$this->alter_table('networks', 'admin_status', "ADD admin_status enum('allocated','delegated','planning','reserved','unallocated','unknown','unmanaged') NOT NULL DEFAULT 'allocated' AFTER security_zone", 'add');

if ($this->db->field_exists('environment', 'networks')) {
    $this->alter_table('networks', 'environment', "DROP `environment`", 'drop');
}
$this->alter_table('networks', 'environment', "ADD `environment` varchar(100) NOT NULL DEFAULT 'Production' AFTER admin_status", 'add');

$sql = "UPDATE rules SET weight = 90 WHERE name like 'Form Factor based on Manufacturer (like %'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

if ($this->db->field_exists('os_arch', 'system')) {
    $this->alter_table('system', 'os_arch', "DROP `os_arch`", 'drop');
}
$this->alter_table('system', 'os_arch', "ADD os_arch varchar(50) NOT NULL DEFAULT '' AFTER os_bit", 'add');

if ($this->db->field_exists('os_license', 'system')) {
    $this->alter_table('system', 'os_license', "DROP `os_license`", 'drop');
}
$this->alter_table('system', 'os_license', "ADD os_license varchar(250) NOT NULL DEFAULT '' AFTER os_arch", 'add');

if ($this->db->field_exists('os_license_code', 'system')) {
    $this->alter_table('system', 'os_license_code', "DROP `os_license_code`", 'drop');
}
$this->alter_table('system', 'os_license_code', "ADD os_license_code varchar(250) NOT NULL DEFAULT '' AFTER os_license", 'add');

if ($this->db->field_exists('os_license_mode', 'system')) {
    $this->alter_table('system', 'os_license_mode', "DROP `os_license_mode`", 'drop');
}
$this->alter_table('system', 'os_license_mode', "ADD os_license_mode varchar(250) NOT NULL DEFAULT '' AFTER os_license_code", 'add');

if ($this->db->field_exists('os_license_type', 'system')) {
    $this->alter_table('system', 'os_license_type', "DROP `os_license_type`", 'drop');
}
$this->alter_table('system', 'os_license_type', "ADD os_license_type varchar(250) NOT NULL DEFAULT '' AFTER os_license_mode", 'add');

if ($this->db->field_exists('os_licence_expiry', 'system')) {
    $this->alter_table('system', 'os_licence_expiry', "DROP `os_licence_expiry`", 'drop');
}
$this->alter_table('system', 'os_licence_expiry', "ADD os_licence_expiry date NOT NULL DEFAULT '2000-01-01' AFTER os_license_type", 'add');

// Update permissions for the Collector role
$sql = 'UPDATE `roles` SET permissions = \'{"collectors":"crud","configuration":"r","credentials":"crud","dashboards":"r","devices":"crud","discoveries":"crud","discovery_scan_options":"crud","locations":"crud","networks":"crud","orgs":"crud","sessions":"crud","tasks":"crud","users":"r","widgets":"r"}\' WHERE name = "collector"';
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

// set our versions
$sql = "UPDATE `configuration` SET `value` = '20210126' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "UPDATE `configuration` SET `value` = '3.5.4' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$this->log_db('Upgrade database to 3.5.4 completed');
$this->config->config['internal_version'] = '20210126';
$this->config->config['display_version'] = '3.5.4';

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

/*
UPDATE `roles` SET `permissions` = '{\"applications\":\"crud\",\"attributes\":\"crud\",\"baselines\":\"crud\",\"buildings\":\"crud\",\"charts\":\"crud\",\"clouds\":\"crud\",\"connections\":\"crud\",\"credentials\":\"crud\",\"dashboards\":\"crud\",\"errors\":\"r\",\"floors\":\"crud\",\"queue\":\"cr\",\"summaries\":\"crud\",\"devices\":\"crud\",\"discoveries\":\"crud\",\"discovery_scan_options\":\"crud\",\"fields\":\"crud\",\"files\":\"crud\",\"graph\":\"crud\",\"groups\":\"crud\",\"integrations\":\"crud\",\"invoice\":\"crud\",\"licenses\":\"crud\",\"locations\":\"crud\",\"networks\":\"crud\",\"orgs\":\"crud\",\"queue\":\"cr\",\"queries\":\"crud\",\"racks\":\"crud\",\"rack_devices\":\"crud\",\"reports\":\"r\",\"rooms\":\"crud\",\"rows\":\"crud\",\"scripts\":\"crud\",\"search\":\"crud\",\"sessions\":\"crud\",\"tasks\":\"crud\",\"users\":\"crud\",\"widgets\":\"crud\"}' WHERE `name` = 'org_admin';

DELETE FROM `attributes` WHERE `resource` = 'locations' AND `type` = 'type' AND `name` = 'Cloud Region';

DELETE FROM `attributes` WHERE `resource` = 'locations' AND `type` = 'type' AND `name` = 'Cloud Zone';

INSERT INTO `attributes` VALUES (NULL,1,'locations','type','Cloud Region','Cloud Region','system','2000-01-01 00:00:00');

INSERT INTO `attributes` VALUES (NULL,1,'locations','type','Cloud Zone','Cloud Zone','system','2000-01-01 00:00:00');

CREATE INDEX audit_log_system_id_type ON audit_log (`system_id`, `type`);

CREATE INDEX change_log_timestamp ON change_log (`timestamp`);

CREATE INDEX change_log_db_table ON change_log (`db_table`);

CREATE INDEX change_log_db_action ON change_log (`db_action`);

ALTER TABLE `system` ADD `snmp_version` varchar(10) NOT NULL DEFAULT '' AFTER `sysLocation`;

DELETE FROM `configuration` WHERE `name` = 'discovery_use_vintage_service';

INSERT INTO `configuration` VALUES (NULL,'discovery_use_vintage_service','n','bool','y','system','2000-01-01 00:00:00','On Windows, use the old way of running discovery with the Apache service account.');

UPDATE `configuration` SET `value` = 'y', `type` = 'bool', description = 'Tells Open-AudIT to advise the browser to download as a file or display the csv, xml, json reports.' WHERE `name` = 'download_reports' AND value = 'download';

UPDATE `configuration` SET `value` = 'n', `type` = 'bool', description = 'Tells Open-AudIT to advise the browser to download as a file or display the csv, xml, json reports.' WHERE `name` = 'download_reports' AND value != 'y';

UPDATE `configuration` SET `name` = 'create_change_log', `value` = 'y', `type` = 'bool', `description` = 'Should Open-AudIT create an entry in the change log table if a change is detected.' WHERE `name` = 'discovery_create_alerts' and `value` != 'n';

UPDATE `configuration` SET `name` = 'create_change_log', `value` = 'n', `type` = 'bool', `description` = 'Should Open-AudIT create an entry in the change log table if a change is detected.' WHERE `name` = 'discovery_create_alerts' and `value` != 'y';

DELETE FROM `configuration` WHERE `name` LIKE 'create_change_log_%';

INSERT INTO `configuration` VALUES (NULL,'create_change_log','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_bios','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the bios table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_disk','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the disk table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_dns','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the dns table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_file','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the file table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_ip','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the ip table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_log','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the log table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_memory','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the memory table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_module','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the module table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_monitor','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the monitor table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_motherboad','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the motherboard table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_network','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the network table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_nmap','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the nmap table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat_well_known','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table and the port is 1023 or lower.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat_registered','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table and the port is in the range of 1024 to 49151.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat_dynamic','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table and the port is 49152 or greater.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_optical','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the optical table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_pagefile','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the pagefile table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_partition','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the partition table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_policy','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the policy table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_print_queue','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the print_queue table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_processor','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the processor table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_route','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the route table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_san','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the san table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_scsi','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the scsi table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_server','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the server table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_server_item','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the server_item table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_service','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the service table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_share','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the share table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_software','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the software table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_software_key','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the software_key table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_sound','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the sound table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_task','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the task table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_user','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the user table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_user_group','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the user_group table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_variable','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the variable table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_video','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the video table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_vm','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the vm table.');

INSERT INTO `configuration` VALUES (NULL,'create_change_log_windows','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the windows table.');

DELETE FROM `configuration` WHERE `name` LIKE 'delete_noncurrent%';

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent','n','bool','y','system','2000-01-01 00:00:00','Should we delete all non-current data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_bios','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current bios data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_disk','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current disk data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_dns','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current dns data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_file','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current file data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_ip','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current ip data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_log','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current log data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_memory','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current memory data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_module','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current module data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_monitor','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current monitor data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_motherboard','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current motherboard data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_netstat','y','bool','y','system','2000-01-01 00:00:00','Should we delete non-current netstat data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_network','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current network data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_nmap','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current nmap data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_optical','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current optical data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_pagefile','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current pagefile data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_partition','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current partition data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_policy','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current policy data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_print_queue','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current print_queue data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_processor','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current processor data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_route','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current route data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_san','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current san data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_scsi','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current scsi data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_server','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current server data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_server_item','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current server_item data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_service','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current service data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_share','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current share data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_software','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current software data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_software_key','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current software_key data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_sound','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current sound data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_task','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current task data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_user','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current user data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_user_group','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current user_group data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_variable','y','bool','y','system','2000-01-01 00:00:00','Should we delete non-current variable data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_video','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current video data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_vm','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current vm data.');

INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_windows','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current windows data.');

DELETE FROM `configuration` WHERE `name` LIKE 'match_sysname%';

INSERT INTO `configuration` VALUES (NULL,'match_sysname','y','bool','y','system','2000-01-01 00:00:00','Should we match a device based only on its SNMP sysName.');

INSERT INTO `configuration` VALUES (NULL,'match_sysname_serial','y','bool','y','system','2000-01-01 00:00:00','Should we match a device based only on its SNMP sysName and serial.');

ALTER TABLE `policy` CHANGE `value` `value` TEXT NOT NULL;

DELETE FROM `queries` WHERE `name` = 'MS Office';

INSERT INTO `queries` VALUES (NULL,1,'MS Office','Software','y','MS Office installations.','SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.name AS `system.name`, system.domain AS `system.domain`, system.os_family AS `system.os_family`, orgs.name AS `orgs.name`, windows.user_name AS `windows.user_name`, software.name as `software.name` FROM software LEFT JOIN system ON (software.system_id = system.id) LEFT JOIN orgs ON (orgs.id = system.org_id) LEFT JOIN windows ON (windows.system_id = system.id AND windows.current = \'y\') WHERE @filter AND software.current = \'y\' AND software.name LIKE \'Microsoft Office%\' AND (software.name LIKE \'%Starter%\' OR software.name LIKE \'%Basic%\' OR software.name LIKE \'%Personal%\' OR software.name LIKE \'%Home%\' OR software.name LIKE \'%Student%\' OR software.name LIKE \'%Business%\' OR software.name LIKE \'%Standard%\' OR software.name LIKE \'%Ultimate%\' OR software.name LIKE \'%Enterprise%\' OR software.name LIKE \'%Professional%\' OR software.name LIKE \'%Professional Plus%\') AND (software.name LIKE \'%2003%\' OR software.name LIKE \'%2007%\' OR software.name LIKE \'%2010%\' OR software.name LIKE \'%2013%\' OR software.name LIKE \'%2016%\' OR software.name LIKE \'%365%\')','','system','2000-01-01 00:00:00');

DELETE FROM `queries` WHERE `name` = 'Integration Default for NMIS';

INSERT INTO `queries` VALUES (NULL,1,'Integration Default for NMIS','Other','y','The default query for integration with NMIS. Uses all devices with nmis_manage set to y.','SELECT system.id AS `system.id`, system.name AS `system.name`, system.hostname AS `system.hostname`, system.dns_hostname AS `system.dns_hostname`, system.fqdn AS `system.fqdn`, system.ip AS `system.ip`, system.type AS `system.type`, system.credentials AS `system.credentials`, system.nmis_group AS `system.nmis_group`, system.nmis_name AS `system.nmis_name`, system.nmis_role AS `system.nmis_role`, system.nmis_manage AS `system.nmis_manage`, system.nmis_business_service AS `system.nmis_business_service`, system.nmis_poller AS `system.nmis_poller`, system.snmp_version AS `system.snmp_version`, system.omk_uuid AS `system.omk_uuid`, locations.name AS `locations.name`, IF(system.snmp_version != \'\', \'true\', \'false\') AS `system.collect_snmp`, IF(system.os_group LIKE \'%windows%\', \'true\', \'false\') AS `system.collect_wmi` FROM `system` LEFT JOIN `locations` ON system.location_id = locations.id WHERE @filter AND system.nmis_manage = \'y\'','','system','2000-01-01 00:00:00');

DROP TABLE IF EXISTS `integrations`;

CREATE TABLE `integrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `org_id` int(10) unsigned NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `type` varchar(45) NOT NULL DEFAULT 'nmis',
  `options` longtext NOT NULL,
  `last_run` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `edited_by` varchar(200) NOT NULL DEFAULT '',
  `edited_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP INDEX audit_log_system_id_type ON audit_log;

CREATE INDEX audit_log_system_id_type ON audit_log (`system_id`, `type`);

DROP INDEX change_log_timestamp ON change_log;

DROP INDEX change_log_db_table ON change_log;

DROP INDEX change_log_db_action ON change_log;

CREATE INDEX change_log_timestamp ON change_log (`timestamp`);

CREATE INDEX change_log_db_table ON change_log (`db_table`);

CREATE INDEX change_log_db_action ON change_log (`db_action`);

UPDATE `configuration` SET `value` = '20190512' WHERE `name` = 'internal_version';

UPDATE `configuration` SET `value` = '3.1.0' WHERE `name` = 'display_version';

*/

$this->log_db('Upgrade database to 3.1.0 commenced');

$this->m_roles->update_permissions('org_admin', 'integrations', 'crud');

$sql = "DELETE FROM `attributes` WHERE `resource` = 'locations' AND `type` = 'type' AND `name` = 'Cloud Region'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DELETE FROM `attributes` WHERE `resource` = 'locations' AND `type` = 'type' AND `name` = 'Cloud Zone'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `attributes` VALUES (NULL,1,'locations','type','Cloud Region','Cloud Region','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `attributes` VALUES (NULL,1,'locations','type','Cloud Zone','Cloud Zone','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$audit_log_system_id_type = false;
$sql = "SHOW INDEX FROM `audit_log`";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query() . ';');
if ($query->num_rows() > 0) {
	$result = $query->result();
	foreach ($result as $row) {
		if ($row->Key_name === 'audit_log_system_id_type') {
			$audit_log_system_id_type = true;
		}
	}
}

if ($audit_log_system_id_type) {
	$sql = "DROP INDEX audit_log_system_id_type ON audit_log";
	$this->db->query($sql);
	$this->log_db($this->db->last_query() . ';');
}

$sql = "CREATE INDEX audit_log_system_id_type ON audit_log (`system_id`, `type`)";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$change_log_timestamp = false;
$change_log_db_table = false;
$change_log_db_action = false;
$sql = "SHOW INDEX FROM `change_log`";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query() . ';');
if ($query->num_rows() > 0) {
	$result = $query->result();
	foreach ($result as $row) {
		if ($row->Key_name === 'change_log_timestamp') {
			$change_log_timestamp = true;
		}
		if ($row->Key_name === 'change_log_db_table') {
			$change_log_db_table = true;
		}
		if ($row->Key_name === 'change_log_db_action') {
			$change_log_db_action = true;
		}
	}
}

if ($change_log_timestamp) {
	$sql = "DROP INDEX change_log_timestamp ON change_log";
	$this->db->query($sql);
	$this->log_db($this->db->last_query() . ';');
}

if ($change_log_db_table) {
	$sql = "DROP INDEX change_log_db_table ON change_log";
	$this->db->query($sql);
	$this->log_db($this->db->last_query() . ';');
}

if ($change_log_db_action) {
	$sql = "DROP INDEX change_log_db_action ON change_log";
	$this->db->query($sql);
	$this->log_db($this->db->last_query() . ';');
}

$sql = "CREATE INDEX change_log_timestamp ON change_log (`timestamp`)";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "CREATE INDEX change_log_db_table ON change_log (`db_table`)";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "CREATE INDEX change_log_db_action ON change_log (`db_action`)";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$this->alter_table('system', 'snmp_version', "ADD `snmp_version` varchar(10) NOT NULL DEFAULT '' AFTER `sysLocation`", 'add');

# change our download_reports option
$sql = "UPDATE `configuration` SET `value` = 'y', `type` = 'bool', description = 'Tells Open-AudIT to advise the browser to download as a file or display the csv, xml, json reports.' WHERE `name` = 'download_reports' AND value = 'download'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

# new config item to allow 'old' way of working
$sql = "DELETE FROM `configuration` WHERE `name` = 'discovery_use_vintage_service'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'discovery_use_vintage_service','n','bool','y','system','2000-01-01 00:00:00','On Windows, use the old way of running discovery with the Apache service account.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "UPDATE `configuration` SET `value` = 'n', `type` = 'bool', description = 'Tells Open-AudIT to advise the browser to download as a file or display the csv, xml, json reports.' WHERE `name` = 'download_reports' AND value != 'y'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

# remove no longer required config item (replaced by create_change_log items below)
$sql = "UPDATE `configuration` SET `name` = 'create_change_log', `value` = 'y', `type` = 'bool', `description` = 'Should Open-AudIT create an entry in the change log table if a change is detected.' WHERE `name` = 'discovery_create_alerts' and `value` != 'n'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "UPDATE `configuration` SET `name` = 'create_change_log', `value` = 'n', `type` = 'bool', `description` = 'Should Open-AudIT create an entry in the change log table if a change is detected.' WHERE `name` = 'discovery_create_alerts' and `value` != 'y'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DELETE FROM `configuration` WHERE `name` LIKE 'create_change_log_%'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_bios','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the bios table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_disk','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the disk table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_dns','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the dns table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_file','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the file table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_ip','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the ip table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_log','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the log table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_memory','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the memory table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_module','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the module table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_monitor','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the monitor table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_motherboad','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the motherboard table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_network','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the network table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_nmap','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the nmap table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat_well_known','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table and the port is 1023 or lower.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat_registered','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table and the port is in the range of 1024 to 49151.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_netstat_dynamic','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the netstat table and the port is 49152 or greater.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_optical','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the optical table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_pagefile','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the pagefile table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_partition','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the partition table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_policy','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the policy table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_print_queue','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the print_queue table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_processor','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the processor table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_route','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the route table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_san','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the san table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_scsi','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the scsi table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_server','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the server table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_server_item','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the server_item table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_service','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the service table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_share','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the share table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_software','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the software table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_software_key','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the software_key table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_sound','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the sound table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_task','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the task table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_user','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the user table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_user_group','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the user_group table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_variable','n','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the variable table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_video','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the video table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_vm','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the vm table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'create_change_log_windows','y','bool','y','system','2000-01-01 00:00:00','Should Open-AudIT create an entry in the change log table if a change is detected in the windows table.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DELETE FROM `configuration` WHERE `name` LIKE 'delete_noncurrent%'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent','n','bool','y','system','2000-01-01 00:00:00','Should we delete all non-current data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_bios','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current bios data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_disk','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current disk data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_dns','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current dns data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_file','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current file data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_ip','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current ip data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_log','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current log data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_memory','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current memory data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_module','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current module data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_monitor','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current monitor data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_motherboard','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current motherboard data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_netstat','y','bool','y','system','2000-01-01 00:00:00','Should we delete non-current netstat data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_network','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current network data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_nmap','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current nmap data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_optical','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current optical data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_pagefile','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current pagefile data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_partition','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current partition data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_policy','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current policy data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_print_queue','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current print_queue data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_processor','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current processor data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_route','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current route data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_san','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current san data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_scsi','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current scsi data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_server','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current server data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_server_item','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current server_item data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_service','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current service data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_share','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current share data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_software','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current software data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_software_key','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current software_key data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_sound','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current sound data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_task','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current task data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_user','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current user data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_user_group','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current user_group data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_variable','y','bool','y','system','2000-01-01 00:00:00','Should we delete non-current variable data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_video','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current video data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_vm','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current vm data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'delete_noncurrent_windows','n','bool','y','system','2000-01-01 00:00:00','Should we delete non-current windows data.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DELETE FROM `configuration` WHERE `name` LIKE 'match_sysname%'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'match_sysname','y','bool','y','system','2000-01-01 00:00:00','Should we match a device based only on its SNMP sysName.')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `configuration` VALUES (NULL,'match_sysname_serial','y','bool','y','system','2000-01-01 00:00:00','Should we match a device based only on its SNMP sysName and serial.');";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$this->alter_table('policy', 'value', "`value` TEXT NOT NULL");

$sql = "DELETE FROM `queries` WHERE `name` = 'Integration Default for NMIS'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `queries` VALUES (NULL,1,'Integration Default for NMIS','Other','y','The default query for integration with NMIS. Uses all devices with nmis_manage set to y.','SELECT system.id AS `system.id`, system.name AS `system.name`, system.hostname AS `system.hostname`, system.dns_hostname AS `system.dns_hostname`, system.fqdn AS `system.fqdn`, system.ip AS `system.ip`, system.type AS `system.type`, system.credentials AS `system.credentials`, system.nmis_group AS `system.nmis_group`, system.nmis_name AS `system.nmis_name`, system.nmis_role AS `system.nmis_role`, system.nmis_manage AS `system.nmis_manage`, system.nmis_business_service AS `system.nmis_business_service`, system.nmis_poller AS `system.nmis_poller`, system.snmp_version AS `system.snmp_version`, system.omk_uuid AS `system.omk_uuid`, locations.name AS `locations.name`, IF(system.snmp_version != \'\', \'true\', \'false\') AS `system.collect_snmp`, IF(system.os_group LIKE \'%windows%\', \'true\', \'false\') AS `system.collect_wmi` FROM `system` LEFT JOIN `locations` ON system.location_id = locations.id WHERE @filter AND system.nmis_manage = \'y\'','','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');


$sql = "DELETE FROM `queries` WHERE `name` = 'MS Office'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "INSERT INTO `queries` VALUES (NULL,1,'MS Office','Software','y','MS Office installations.','SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.name AS `system.name`, system.domain AS `system.domain`, system.os_family AS `system.os_family`, orgs.name AS `orgs.name`, windows.user_name AS `windows.user_name`, software.name as `software.name` FROM software LEFT JOIN system ON (software.system_id = system.id) LEFT JOIN orgs ON (orgs.id = system.org_id) LEFT JOIN windows ON (windows.system_id = system.id AND windows.current = \'y\') WHERE @filter AND software.current = \'y\' AND software.name LIKE \'Microsoft Office%\' AND (software.name LIKE \'%Starter%\' OR software.name LIKE \'%Basic%\' OR software.name LIKE \'%Personal%\' OR software.name LIKE \'%Home%\' OR software.name LIKE \'%Student%\' OR software.name LIKE \'%Business%\' OR software.name LIKE \'%Standard%\' OR software.name LIKE \'%Ultimate%\' OR software.name LIKE \'%Enterprise%\' OR software.name LIKE \'%Professional%\' OR software.name LIKE \'%Professional Plus%\') AND (software.name LIKE \'%2003%\' OR software.name LIKE \'%2007%\' OR software.name LIKE \'%2010%\' OR software.name LIKE \'%2013%\' OR software.name LIKE \'%2016%\' OR software.name LIKE \'%365%\')','','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "DROP TABLE IF EXISTS `integrations`";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "CREATE TABLE `integrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `org_id` int(10) unsigned NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `type` varchar(45) NOT NULL DEFAULT 'nmis',
  `options` longtext NOT NULL,
  `last_run` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `edited_by` varchar(200) NOT NULL DEFAULT '',
  `edited_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

# set our versions
$sql = "UPDATE `configuration` SET `value` = '20190512' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "UPDATE `configuration` SET `value` = '3.1.0' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$this->log_db("Upgrade database to 3.1.0 completed");
$this->config->config['internal_version'] = '20190512';
$this->config->config['display_version'] = '3.1.0';

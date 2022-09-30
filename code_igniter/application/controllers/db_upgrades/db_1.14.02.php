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

$this->log_db('Upgrade database to 1.14.2 commenced');

// $temp_debug = $this->db->db_debug;
// $this->db->db_debug = false;

# configuration
$sql = "UPDATE `configuration` SET name = 'match_ip', description = 'Should we match a device based on its ip.' WHERE name = 'discovery_ip_match'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET name = 'match_mac', description = 'Should we match a device based on its mac address.' WHERE name = 'discovery_mac_match'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET name = 'match_hostname', description = 'Should we match a device based only on its hostname.' WHERE name = 'discovery_name_match'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET name = 'match_serial' WHERE name = 'discovery_serial_match'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_dbus'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_dbus','n','y','system',NOW(),'Should we match a device based on its dbus id.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_uuid'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_uuid','y','y','system',NOW(),'Should we match a device based on its UUID.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_hostname_dbus'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_hostname_dbus','y','y','system',NOW(),'Should we match a device based on its hostname and dbus id.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_hostname_uuid'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_hostname_uuid','y','y','system',NOW(),'Should we match a device based on its hostname and UUID.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_hostname_serial'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_hostname_serial','y','y','system',NOW(),'Should we match a device based on its hostname and serial.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_serial_type'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_serial_type','y','y','system',NOW(),'Should we match a device based on its serial and type.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_fqdn'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_fqdn','y','y','system',NOW(),'Should we match a device based on its fqdn.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "SELECT COUNT(*) FROM `configuration` WHERE name = 'match_mac_vmware'";
$query = $this->db->query($sql);
$this->log_db($this->db->last_query());
$result = $query->result();
if (count($result) === 0) {
    $sql = "INSERT INTO `configuration` VALUES (NULL, 'match_mac_vmware','n','y','system',NOW(),'Should we match a device based mac address even if it\'s a known likely duplicate from VMware.')";
    $this->db->query($sql);
    $this->log_db($this->db->last_query());
}

$sql = "DELETE FROM `configuration` WHERE name = 'homepage'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO `configuration` VALUES (NULL, 'homepage','groups','y','system',NOW(),'Any links to the default page should be directed to this endpoint.')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "DELETE FROM `configuration` WHERE name = 'network_group_auto_create'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "DELETE FROM `configuration` WHERE name = 'network_group_homepage_limit'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "DELETE FROM `configuration` WHERE name = 'network_group_subnet'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "DELETE FROM `configuration` WHERE name = 'non_admin_search'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "DELETE FROM `configuration` WHERE name = 'show_passwords'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "DELETE FROM `configuration` WHERE name = 'show_snmp_community'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

# connections
$this->rename_table('oa_connection', 'connections');

# credentials
#$sql = "INSERT INTO credentials VALUES (NULL, 'Default SNMP', '', 'snmp', 'ZO6BkpM46ukP0SjCV7oJKkV/ab1pf2KXVgBxstNZIP9a9pEVoHG6oytxCp2C9GtG3wx2qDHjuIO8bo2wm1MwwQ==', 1, 'system', NOW())";
#$query = $this->db->query($sql);
#$this->log_db($this->db->last_query());

# field / fields
if ($this->db->table_exists('additional_field')) {
    if (!$this->db->field_exists('group_id', 'additional_field')) {
        // for the beta users, this field might have been removed upon the 1.14 upgrade
        $this->alter_table('additional_field', 'group_id', "ADD `group_id` int(10) unsigned NOT NULL DEFAULT '1' AFTER org_id", 'add');
    }
}
$this->alter_table('additional_field_item', 'additional_field_id', "`fields_id` int(10) unsigned NOT NULL DEFAULT '0'");
$this->rename_table('additional_field', 'fields');
$this->rename_table('additional_field_item', 'field');
$this->drop_foreign_key('field', 'additional_field_item_system_id');
$this->add_foreign_key('field', 'field_system_id', "FOREIGN KEY (`system_id`) REFERENCES `system` (`id`) ON DELETE CASCADE");
$this->alter_table('fields', 'group_id', "`group_id` int(10) unsigned NOT NULL DEFAULT '1' AFTER `org_id`");

# locations
$this->rename_table('oa_location', 'locations');

# notes
$this->alter_table('notes', 'edited_by', "`edited_by` varchar(200) NOT NULL DEFAULT '' AFTER `comment`");

# queries
$sql = "UPDATE `queries` SET `sql` = \"SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, oa_org.name AS `oa_org.name`, system.last_seen AS `system.last_seen`, system.last_seen_by AS `system.last_seen_by`, system.manufacturer AS `system.manufacturer`, system.model AS `system.model`, system.serial AS `system.serial`, system.class AS `system.class`, windows.user_name AS `windows.user_name`, locations.name AS `locations.name` FROM system LEFT JOIN locations ON (system.location_id = locations.id) LEFT JOIN windows ON (system.id = windows.system_id AND windows.current = 'y') LEFT JOIN oa_org ON (system.org_id = oa_org.id) WHERE @filter\" WHERE name = \"Billing Report\" ";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `queries` SET `sql` = \"SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, system.manufacturer AS `system.manufacturer`, system.model AS `system.model`, system.serial AS `system.serial`, system.os_family AS `system.os_family`, system.memory_count AS `system.memory_count`, system.form_factor AS `system.form_factor`, processor.description AS `processor.description` FROM system LEFT JOIN processor ON (processor.system_id = system.id AND processor.current = 'y') WHERE @filter AND system.type = 'computer' AND system.class != 'server' AND system.class != 'hypervisor' ORDER BY system.name\" WHERE name = \"Hardware - Workstations\" ";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO `queries` VALUES (NULL, 1, 'Servers - AD Controllers', 'Active Directory Domain Controllers', \"SELECT system.id AS `system.id`, system.icon AS `system.icon`, system.type AS `system.type`, system.name AS `system.name`, system.domain AS `system.domain`, system.ip AS `system.ip`, system.description AS `system.description`, system.os_family AS `system.os_family`, system.status AS `system.status` FROM system LEFT JOIN windows ON (system.id = windows.system_id AND windows.current = 'y') WHERE windows.domain_role LIKE '%Domain Controller' AND system.status = 'production'\", '', 'y', 'system', NOW())";
$this->db->query($sql);
$this->log_db($this->db->last_query());

# oa_group_column
$this->drop_table('oa_group_column');

# oa_group_sys
$this->drop_table('oa_group_sys');

# oa_group_user
$this->drop_table('oa_group_user');

# oa_report_column
$this->drop_table('oa_report_column');

# oa_user
$this->alter_table('oa_user', 'ldap', "`ldap` text NOT NULL AFTER `active`");

# scripts
# Update with the new endpoint for devices to submit audit results to
$sql = "SELECT * FROM `scripts`";
$query = $this->db->query($sql);
$result = $query->result();
$this->log_db($this->db->last_query());
foreach ($result as $script) {
    $json = @json_decode($script->options);
    if (!empty($json)) {
        $json->url = str_replace('/system/add_system', '/input/devices', $json->url);
        $script->options = json_encode($json);
        $sql = "UPDATE `scripts` SET options = ? WHERE id = ?";
        $data = array($script->options, intval($script->id));
        $query = $this->db->query($sql, $data);
        $this->log_db($this->db->last_query());
    }
}

# summaries
$sql = "DELETE FROM summaries";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "ALTER TABLE summaries AUTO_INCREMENT = 1";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Device Classes',1,'system','class','','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Device Status',1,'system','status','','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Device Types',1,'system','type','','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'DNS Domains',1,'system','dns_domain','','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Form Factors',1,'system','form_factor','system.form_factor,system.class','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Manufacturers',1,'system','manufacturer','system.model','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Operating Systems',1,'system','os_family','','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Server Types',1,'server','type','server.name,server.full_name,server.version,server.status','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Services',1,'service','name','service.name,service.state','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Software',1,'software','name','software.name,software.version','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Software Keys',1,'software_key','name','software_key.name,software_key.string,software_key.rel,software_key.edition','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO summaries VALUES (NULL,'Active Directory OU\'s',1,'windows','active_directory_ou','windows.active_directory_ou,windows.client_site_name','system','2000-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

# task
$this->alter_table('task', 'task', "`task` TEXT NOT NULL DEFAULT ''");

# set our versions
$sql = "UPDATE `configuration` SET `value` = '20161130' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET `value` = '1.14.2' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());


#$this->db->db_debug = $temp_debug;
$this->log_db('Upgrade database to 1.14.2 completed');
$this->config->config['internal_version'] = '20161130';
$this->config->config['display_version'] = '1.14.2';

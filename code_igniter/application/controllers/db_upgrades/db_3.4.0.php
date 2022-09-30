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

INSERT INTO `rules` VALUES (NULL,'Ubiquiti RP-5AC-Gen2 set type',1,'Set type based on model.',100,'[{\"attribute\":\"model\",\"operator\":\"li\",\"table\":\"system\",\"value\":\"RP-5AC-Gen2\"}]','[{\"attribute\":\"type\",\"table\":\"system\",\"value\":\"wireless link\",\"value_type\":\"string\"}]','system','2001-01-01 00:00:00');

CREATE INDEX discovery_id ON discovery_log (`discovery_id`);

UPDATE `configuration` SET `value` = '20200620' WHERE `name` = 'internal_version';

UPDATE `configuration` SET `value` = '3.4.0' WHERE `name` = 'display_version';
*/

$this->log_db('Upgrade database to 3.4.0 commenced');

$sql = "INSERT INTO `rules` VALUES (NULL,'Ubiquiti RP-5AC-Gen2 set type',1,'Set type based on model.',100,'[{\"attribute\":\"model\",\"operator\":\"li\",\"table\":\"system\",\"value\":\"RP-5AC-Gen2\"}]','[{\"attribute\":\"type\",\"table\":\"system\",\"value\":\"wireless link\",\"value_type\":\"string\"}]','system','2001-01-01 00:00:00')";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$this->drop_key('discovery_log', 'discovery_log_discovery_id');

$this->drop_key('discovery_log', 'discovery_id');

$sql = 'CREATE INDEX discovery_id ON discovery_log (`discovery_id`)';
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = 'CREATE INDEX networks_cloud_id ON networks (`cloud_id`)';
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

// set our versions
$sql = "UPDATE `configuration` SET `value` = '20200620' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "UPDATE `configuration` SET `value` = '3.4.0' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$this->log_db('Upgrade database to 3.4.0 completed');
$this->config->config['internal_version'] = '20200620';
$this->config->config['display_version'] = '3.4.0';

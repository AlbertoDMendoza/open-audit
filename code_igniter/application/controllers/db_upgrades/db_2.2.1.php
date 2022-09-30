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

$this->log_db('Upgrade database to 2.2.1 commenced');

# cluster
$this->alter_table('cluster', 'type', "`type` enum('high availability','load balancing','perforance','storage','other', '') NOT NULL DEFAULT '' AFTER `org_id`");
$this->alter_table('cluster', 'purpose', "`purpose` enum('application','database','file','virtualisation','web','other', '') NOT NULL DEFAULT '' AFTER `type`");

# configuration
$sql = "DELETE FROM `configuration` WHERE `name` = 'gui_trim_characters'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "INSERT INTO `configuration` VALUES (NULL,'gui_trim_characters','25','number','y','system','2000-01-01 00:00:00','When showing a table of information in the web GUI, replace characters greater than this with \"...\".')";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET `value` = '/omk/open-audit/map' WHERE `value` = '/omk/oae/map'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET `value` = '/omk/open-audit' WHERE `value` = '/omk/oae'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

# locations
$this->alter_table('locations', 'type', "`type` varchar(100) NOT NULL DEFAULT '' AFTER `org_id`");

# set our versions
$sql = "UPDATE `configuration` SET `value` = '20180512' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET `value` = '2.2.1' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$this->log_db("Upgrade database to 2.2.1 completed");
$this->config->config['internal_version'] = '20180512';
$this->config->config['display_version'] = '2.2.1';

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
ALTER TABLE `logs` ADD `request_microtime` varchar(50) NOT NULL DEFAULT '' AFTER `timestamp`;

UPDATE `configuration` SET `value` = '20190620' WHERE `name` = 'internal_version';

UPDATE `configuration` SET `value` = '3.1.1' WHERE `name` = 'display_version';
*/

$this->log_db('Upgrade database to 3.1.1 commenced');

// 	$this->alter_table('logs', 'request_microtime', "ADD `request_microtime` varchar(50) NOT NULL DEFAULT '' AFTER `timestamp`", 'add');

# set our versions
$sql = "UPDATE `configuration` SET `value` = '20190620' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$sql = "UPDATE `configuration` SET `value` = '3.1.1' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query() . ';');

$this->log_db("Upgrade database to 3.1.1 completed");
$this->config->config['internal_version'] = '20190620';
$this->config->config['display_version'] = '3.1.1';

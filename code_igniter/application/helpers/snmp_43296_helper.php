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
* PHP version 5.3.3
* 
* @category  Helper
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.2
* @link      http://www.open-audit.org
*/

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

# Vendor Exablaze

$get_oid_details = function ($ip, $credentials, $oid) {
    $details = new stdClass();
    $details->model = my_snmp_get($ip, $credentials, "1.3.6.1.4.1.43296.3.1.3");
    $details->serial = my_snmp_get($ip, $credentials, "1.3.6.1.4.1.43296.3.1.1");
    return($details);
};

$get_modules = function ($ip, $credentials, $discovery_id, $system_id) {

    $modules = array();
    # line cards
    $modules_list = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.1.5.1.1');
    if (!empty($modules_list)) {
        $names = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.1.5.1.2');
    }
    if (!empty($modules_list) and !empty($names)) {
        $types = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.1.5.1.3');
    }
    if (!empty($modules_list) and !empty($names) and !empty($types)) {
        foreach ($modules_list as $key => $value) {
            $module = new stdClass();
            $index = str_replace('.1.3.6.1.4.1.43296.3.1.5.1.1.', '', $key);
            $module->description = 'Line Card - ' . $value . ' - ' . $names['.1.3.6.1.4.1.43296.3.1.5.1.2.'.$index];
            $module->module_index = $index;
            $module->object_ident = $key;
            $module->contained_in = '';
            $module->class = '1';
            $module->class_text = 'other';
            $module->hardware_revision = '';
            $module->firmware_revision = '';
            $module->software_revision = '';
            $module->serial = '';
            $module->asset_ident = '';
            $module->is_fru = '';
            $modules[] = $module;
        }
    }
    unset($modules_list, $names, $types);

    # modules
    $modules_list = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.1.6.1.1');
    if (!empty($modules_list)) {
        $names = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.1.6.1.2');
    }
    if (!empty($modules_list) and !empty($names)) {
        $boards = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.1.6.1.3');
    }
    if (!empty($modules_list) and !empty($names) and !empty($boards)) {
        $functions = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.1.6.1.4');
    }
    if (!empty($modules_list) and !empty($names) and !empty($types) and !empty($boards)) {
        foreach ($modules_list as $key => $value) {
            $module = new stdClass();
            $index = str_replace('.1.3.6.1.4.1.43296.3.1.6.1.1.', '', $key);
            $module->description = 'Module - ' . $value . ' - ' . $names['.1.3.6.1.4.1.43296.3.1.6.1.2.'.$index];
            $module->module_index = $index;
            $module->object_ident = $key;
            $module->contained_in = $boards['.1.3.6.1.4.1.43296.3.1.6.1.3.'.$index];
            $module->class = '9';
            $module->class_text = 'module';
            $module->hardware_revision = '';
            $module->firmware_revision = $function['.1.3.6.1.4.1.43296.3.1.6.1.4.'.$index];
            $module->software_revision = '';
            $module->serial = '';
            $module->asset_ident = '';
            $module->is_fru = '';
            $modules[] = $module;
        }
    }
    unset($modules_list, $names, $boards, $functions);

    # power supplies
    $modules_list = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.3.1.1');
    if (!empty($modules_list)) {
        $names = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.3.1.2');
    }
    if (!empty($modules_list) and !empty($names)) {
        foreach ($modules_list as $key => $value) {
            $module = new stdClass();
            $index = str_replace('.1.3.6.1.4.1.43296.3.3.1.1.', '', $key);
            $module->description = 'Power Supply - ' . $value . ' - ' . $names['.1.3.6.1.4.1.43296.3.3.1.2.'.$index];
            $module->module_index = $index;
            $module->object_ident = $key;
            $module->contained_in = '';
            $module->class = '6';
            $module->class_text = 'powerSupply';
            $module->hardware_revision = '';
            $module->firmware_revision = '';
            $module->software_revision = '';
            $module->serial = '';
            $module->asset_ident = '';
            $module->is_fru = '';
            $modules[] = $module;
        }
    }
    unset($modules_list, $names);

    # ports
    $modules_list = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.4.1.2');
    if (!empty($modules_list)) {
        $names = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.4.1.3');
    }
    if (!empty($modules_list) and !empty($names)) {
        $speeds = my_snmp_real_walk($ip, $credentials, '1.3.6.1.4.1.43296.3.4.1.9');
    }
    if (!empty($modules_list) and !empty($names)) {
        foreach ($modules_list as $key => $value) {
            $module = new stdClass();
            $index = str_replace('.1.3.6.1.4.1.43296.3.4.1.2.', '', $key);
            $module->description = 'Port - ' . $value . ' - ' . $names['.1.3.6.1.4.1.43296.3.4.1.3.'.$index] . ' at ' . $speeds['.1.3.6.1.4.1.43296.3.4.1.9.'.$index] . ' Mpbs';
            $module->module_index = $index;
            $module->object_ident = $key;
            $module->contained_in = '';
            $module->class = '10';
            $module->class_text = 'port';
            $module->hardware_revision = '';
            $module->firmware_revision = '';
            $module->software_revision = '';
            $module->serial = '';
            $module->asset_ident = '';
            $module->is_fru = '';
            $modules[] = $module;
        }
    }
    return($modules);
};
<?php
if (!defined('BASEPATH')) {
     exit('No direct script access allowed');
}
#
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

/*
* @category  Helper
* @package   Open-AudIT
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.4
* @link      http://www.open-audit.org
 */

# Vendor Frogfoot Networks
# Ubiquiti / Airfiber tend to use this OID :-(

$get_oid_details = function ($ip, $credentials, $oid) {
    $details = new stdClass();
    $details->manufacturer = my_snmp_get($ip, $credentials, "1.2.840.10036.3.1.2.1.2.5");
    $details->os_name = my_snmp_get($ip, $credentials, "1.2.840.10036.3.1.2.1.4.5");
    $details->serial = my_snmp_get($ip, $credentials, "1.2.840.10036.1.1.1.1.5");
    $details->model = my_snmp_get($ip, $credentials, "1.2.840.10036.3.1.2.1.3.5");
    if (empty($details->model)) {
        $details->model = my_snmp_get($ip, $credentials, "1.2.840.10036.3.1.2.1.3.10");
    }
    if (empty($details->model)) {
        $details->model = my_snmp_get($ip, $credentials, "1.2.840.10036.3.1.2.1.3.7");
    }
    if (empty($details->model)) {
        $details->model = my_snmp_get($ip, $credentials, "1.3.6.1.4.1.41112.1.6.3.3");
    }
    return($details);
};

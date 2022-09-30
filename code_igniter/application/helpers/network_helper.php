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
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.4
* @link      http://www.open-audit.org
 */

if (! function_exists('is_ssl')) {
    function is_ssl()
    {
        if (!empty($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS']) == 'on') {
            return true;
        }
        if (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == '1') {
            return true;
        }
        if (!empty($_SERVER['SERVER_PORT']) and ($_SERVER['SERVER_PORT'] == '443')) {
            return true;
        }
        if (!empty($_SERVER['X-Forwarded-Proto']) and strtolower($_SERVER['X-Forwarded-Proto']) == 'https') {
            return true;
        }
        if (!empty($_SERVER['Front-End-Https']) and strtolower($_SERVER['Front-End-Https']) == 'on') {
            return true;
        }
        return false;
    }
}

if (! function_exists('is_private_ip')) {
    function is_private_ip($ip)
    {
        $ip = ip_address_to_db($ip);
        $private = false;
        if (($ip >= '010.000.000.000' AND $ip <= '010.255.255.255') ||
            ($ip >= '172.016.000.000' AND $ip <= '172.031.255.255') ||
            ($ip >= '192.168.000.000' AND $ip <= '192.168.255.255')) {
            $private = true;
        }
        return $private;
    }
}

if (! function_exists('server_ip')) {
    # return a comman string of system ip addresses
    function server_ip()
    {
        $ip_address_array = array();

        # osx
        if (php_uname('s') == 'Darwin') {
            $command = "ifconfig | grep inet | grep -v inet6 | grep -v '127.0.0.1' | awk '{print $2}'";
            exec($command, $output, $return_var);
            if ($return_var == 0) {
                foreach ($output as $line) {
                    $ip_address_array[] = trim($line);
                }
            }
        }

        # linux
        if (php_uname('s') == 'Linux') {
            $command = "ip addr | grep inet | grep -v inet6 | grep -v '127.0.0.1' | awk '{print $2}' | cut -f1  -d'/'";
            exec($command, $output, $return_var);
            if ($return_var == 0) {
                foreach ($output as $line) {
                    $ip_address_array[] = trim($line);
                }
            }
        }

        # windows
        if (php_uname('s') == 'Windows NT') {
            $command = "wmic nicconfig get ipaddress | findstr /B {";
            exec($command, $output, $return_var);
            if ($return_var == 0) {
                # success
                # each line is returned thus: {"192.168.1.140", "fe80::e837:7bea:99a6:13e"} or thus {"192.168.1.140"}
                # there are multiple empty lines as well
                foreach ($output as $line) {
                    $line = trim($line);
                    if ($line != '') {
                        $line = str_replace('{', '', $line);
                        $line = str_replace('}', '', $line);
                        $line = str_replace('"', '', $line);
                        $line = str_replace(',', '', $line);
                        if (strpos($line, ' ') !== false) {
                            $line_array = explode(' ', $line);
                            foreach ($line_array as $ip) {
                                $ip_address_array[] = $ip;
                            }
                        } else {
                            $ip_address_array[] = $line;
                        }
                    }
                }
            }
        }
        return implode(',', $ip_address_array);
    }
}

if (! function_exists('network_details')) {
    # accept $ip as either '192.168.0.12/24' or '192.168.0.12 255.255.255.0'
    function network_details($ip)
    {
        $my_net_info = rtrim($ip);
        $details = new stdClass();

        if (! preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}(( ([0-9]{1,3}\.){3}[0-9]{1,3})|(\/[0-9]{1,2}))$/', $my_net_info)) {
            $details->error = "Error - invalid input";
            return($details);
        }

        if (preg_match('/\//', $my_net_info)) {
            //if cidr type mask
            $dq_host = strtok("$my_net_info", "/");
            $cdr_nmask = strtok("/");
            if (!($cdr_nmask >= 0 && $cdr_nmask <= 32)) {
                $details->error = "Invalid CIDR value. Try an integer 0 - 32.";
                return($details);
            }
            $bin_nmask = cdrtobin($cdr_nmask);
            $bin_wmask = binnmtowm($bin_nmask);
        } else {
            //Dotted quad mask?
            $dqs = explode(" ", $my_net_info);
            $dq_host = $dqs[0];
            $bin_nmask = dqtobin($dqs[1]);
            $bin_wmask = binnmtowm($bin_nmask);
            if (preg_match('/0/', rtrim($bin_nmask, "0"))) {
                //Wildcard mask then? hmm?
                $bin_wmask = dqtobin($dqs[1]);
                $bin_nmask = binwmtonm($bin_wmask);
                if (preg_match('/0/', rtrim($bin_nmask, "0"))) {
                    //If it's not wcard, whussup?
                    $details->error = "Invalid Netmask.";
                    return($details);
                }
            }
            $cdr_nmask = bintocdr($bin_nmask);
        }
        //Check for valid $dq_host
        if (! preg_match('/^0./', $dq_host)) {
            foreach (explode(".", $dq_host) as $octet) {
                if ($octet > 255) {
                    $details->error = "Invalid IP Address";

                    return($details);
                }
            }
        }

        $bin_host = dqtobin($dq_host);
        $bin_bcast = (str_pad(mb_substr($bin_host, 0, $cdr_nmask), 32, 1));
        $bin_net = (str_pad(mb_substr($bin_host, 0, $cdr_nmask), 32, 0));
        $bin_first = (str_pad(mb_substr($bin_net, 0, 31), 32, 1));
        $bin_last = (str_pad(mb_substr($bin_bcast, 0, 31), 32, 0));
        $host_total = (bindec(str_pad("", (32-$cdr_nmask), 1)) - 1);
        if ($host_total <= 0) {
            //Takes care of 31 and 32 bit masks.
            $bin_first = "N/A";
            $bin_last = "N/A";
            $host_total = "N/A";
            if ($bin_net === $bin_bcast) {
                $bin_bcast = "N/A";
            }
        }

        //Determine Class
        if (preg_match('/^0/', $bin_net)) {
            $class = "A";
            $dotbin_net = "0".mb_substr(dotbin($bin_net, $cdr_nmask), 1);
        } elseif (preg_match('/^10/', $bin_net)) {
            $class = "B";
            $dotbin_net = "10".mb_substr(dotbin($bin_net, $cdr_nmask), 2);
        } elseif (preg_match('/^110/', $bin_net)) {
            $class = "C";
            $dotbin_net = "110".mb_substr(dotbin($bin_net, $cdr_nmask), 3);
        } elseif (preg_match('/^1110/', $bin_net)) {
            $class = "D";
            $dotbin_net = "1110".mb_substr(dotbin($bin_net, $cdr_nmask), 4);
            $special = "Class D = Multicast Address Space.";
        } else {
            $class = "E";
            $dotbin_net = "1111".mb_substr(dotbin($bin_net, $cdr_nmask), 4);
            $special = "Class E = Experimental Address Space.";
        }

        if (preg_match('/^(00001010)|(101011000001)|(1100000010101000)/', $bin_net)) {
            $special = '<a href="http://www.ietf.org/rfc/rfc1918.txt">( RFC-1918 Private Internet Address. )</a>';
        }

        // Print Results
        #tr("Address: ".$dq_host."<br />");
        #tr("Netmask: ".bintodq($bin_nmask)." = $cdr_nmask<br />");
        #tr("Wildcard: " .bintodq($bin_wmask)."<br />");
        #tr("Network: "  .bintodq($bin_net)." (Class $class)<br/>");
        #tr("Broadcast: ".bintodq($bin_bcast)."<br />");
        #tr("HostMin: "  .bintodq($bin_first)."<br />");
        #tr("HostMax: "  .bintodq($bin_last)."<br />");
        #tr("Hosts/Net: ".$host_total." $special"."<br />");

        $details = new stdClass();
        $details->error = '';
        $details->address = $dq_host;
        $details->netmask = bintodq($bin_nmask);
        $details->network_class = $class;
        $details->wildcard = bintodq($bin_wmask);
        $details->network = bintodq($bin_net);
        $details->broadcast = bintodq($bin_bcast);
        $details->host_min = bintodq($bin_first);
        $details->host_max = bintodq($bin_last);
        $details->hosts_total = $host_total;
        $details->network_slash = $cdr_nmask;

        return($details);
    }
}

if (! function_exists('binnmtowm')) {
    function binnmtowm($binin)
    {
        $binin = rtrim($binin, "0");
        if (!preg_match('/0/', $binin)) {
            return str_pad(str_replace("1", "0", $binin), 32, "1");
        } else {
            return "1010101010101010101010101010101010101010";
        }
    }
}

if (! function_exists('bintocdr')) {
    function bintocdr($binin)
    {
        return mb_strlen(rtrim($binin, "0"));
    }
}

if (! function_exists('bintodq')) {
    function bintodq($binin)
    {
        if ($binin == "N/A") {
            return $binin;
        }
        $binin = explode(".", chunk_split($binin, 8, "."));
        for ($i = 0; $i<4; $i++) {
            $dq[$i] = bindec($binin[$i]);
        }
        return implode(".", $dq);
    }
}

if (! function_exists('bintoint')) {
    function bintoint($binin)
    {
        return bindec($binin);
    }
}

if (! function_exists('bintoint')) {
    function binwmtonm($binin)
    {
        $binin = rtrim($binin, "1");
        if (!preg_match('/1/', $binin)) {
            return str_pad(str_replace("0", "1", $binin), 32, "0");
        } else {
            return "1010101010101010101010101010101010101010";
        }
    }
}

if (! function_exists('cdrtobin')) {
    function cdrtobin($cdrin)
    {
        return str_pad(str_pad("", $cdrin, "1"), 32, "0");
    }
}

if (! function_exists('dotbin')) {
    function dotbin($binin, $cdr_nmask)
    {
        // splits 32 bit bin into dotted bin octets
        if ($binin == "N/A") {
            return $binin;
        }
        $oct = rtrim(chunk_split($binin, 8, "."), ".");
        if ($cdr_nmask > 0) {
            $offset = sprintf("%u", $cdr_nmask/8) + $cdr_nmask;

            return mb_substr($oct, 0, $offset)."&nbsp;&nbsp;&nbsp;".mb_substr($oct, $offset);
        } else {
            return $oct;
        }
    }
}

if (! function_exists('dqtobin')) {
    function dqtobin($dqin)
    {
        $dq = explode(".", $dqin);
        for ($i = 0; $i<4; $i++) {
            $bin[$i] = str_pad(decbin($dq[$i]), 8, "0", STR_PAD_LEFT);
        }
        return implode("", $bin);
    }
}

if (! function_exists('inttobin')) {
    function inttobin($intin)
    {
        return str_pad(decbin($intin), 32, "0", STR_PAD_LEFT);
    }
}

if (! function_exists('ip_address_from_db')) {
    function ip_address_from_db($ip = '')
    {
        if ($ip != "") {
            if (stripos($ip, '.') !== false) {
                // this is an ip v4 address
                $myip = explode(".", $ip);
                $myip[0] = ltrim($myip[0], "0");
                if ($myip[0] == "") {
                    $myip[0] = "0";
                }
                if (isset($myip[1])) {
                    $myip[1] = ltrim($myip[1], "0");
                }
                if (!isset($myip[1]) or $myip[1] == "") {
                    $myip[1] = "0";
                }
                if (isset($myip[2])) {
                    $myip[2] = ltrim($myip[2], "0");
                }
                if (!isset($myip[2]) or $myip[2] == "") {
                    $myip[2] = "0";
                }
                if (isset($myip[3])) {
                    $myip[3] = ltrim($myip[3], "0");
                }
                if (!isset($myip[3]) or $myip[3] == "") {
                    $myip[3] = "0";
                }
                $ip = $myip[0].".".$myip[1].".".$myip[2].".".$myip[3];
            }
            if (stripos($ip, ':') !== false) {
                // this is an ip v6 address
            }
        } else {
            $ip = "";
        }
        return $ip;
    }
}

if (! function_exists('ip_address_to_db')) {
    function ip_address_to_db($ip = '')
    {
        if ($ip != "") {
            if (stripos($ip, '.') !== false) {
                // this is an ip v4 address
                $myip = explode(".", $ip);
                $myip[0] = mb_substr("000".$myip[0], -3);
                $myip[1] = mb_substr("000".$myip[1], -3);
                $myip[2] = mb_substr("000".$myip[2], -3);
                $myip[3] = mb_substr("000".$myip[3], -3);
                $ip = $myip[0].".".$myip[1].".".$myip[2].".".$myip[3];
            }
            if (stripos($ip, ':') !== false) {
                // this is an ip v6 address
            }
        } else {
            $ip = "";
        }
        return $ip;
    }
}

if (! function_exists('dns_validate')) {
    # pass this function the $details object and it will check hostname, domain and fqdn
    # we need an ip or a hostname and a working DNS to get all details
    function dns_validate($details)
    {
        if (!isset($details)) {
            # no object passed
            return;
        }
        if (empty($details->ip)) {
            // $log->command_status = 'fail';
            // $log->severity = 4;
            // $log->message = 'No IP passed to network_helper::dns_validate.';
            // discovery_log($log);
            return $details;
        }
        if (!filter_var($details->ip, FILTER_VALIDATE_IP)) {
            // $log->command_status = 'fail';
            // $log->severity = 4;
            // $log->message = 'Invalid IP passed to network_helper::dns_validate.';
            // discovery_log($log);
            return $details;
        }
        $log = new stdClass();
        $log->ip = $details->ip;
        $log->severity = 7;
        $log->command_status = 'notice';
        if (!empty($details->discovery_id)) {
            $log->discovery_id = $details->discovery_id;
        }
        $details->dns_hostname = strtolower(gethostbyaddr($details->ip));
        if (strpos($details->dns_hostname, ".") !== false) {
            if (!filter_var($details->dns_hostname, FILTER_VALIDATE_IP)) {
                # we got a FQDN back from DNS - split it up
                $details->dns_fqdn = strtolower($details->dns_hostname);
                $log->message = 'Set DNS FQDN for ' . $details->ip . ' in network_helper::dns_validate';
                $log->command_output = $details->dns_fqdn;
                discovery_log($log);

                $temp = explode(".", $details->dns_hostname);
                $details->dns_hostname = $temp[0];
                unset($temp[0]);
                $log->message = 'Set DNS hostname for ' . $details->ip . ' in network_helper::dns_validate';
                $log->command_output = $details->dns_hostname;
                discovery_log($log);

                $details->dns_domain = implode(".", $temp);
                unset($temp);
                $log->message = 'Set DNS domain for ' . $details->ip . ' in network_helper::dns_validate';
                $log->command_output = $details->dns_domain;
                discovery_log($log);
            } else {
                # we got an ip address back from DNS, remove it
                $details->dns_hostname = '';
                $log->message = 'Received IP, not hostname for ' . $details->ip . ' in network_helper::dns_validate';
                $log->command_output = $details->hostname;
                discovery_log($log);
            }
        } else {
            $log->message = 'DNS returned a hostname only for ' . $details->ip . ' in network_helper::dns_validate';
            $log->command_output = $details->dns_hostname;
            discovery_log($log);
        }
        return $details;
    }
}

/* End of file network_helper.php */
/* Location: ./system/application/helpers/network_helper.php */

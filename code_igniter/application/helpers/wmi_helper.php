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
#  www.opmantek.com or email contact@opmantek.com
#
# *****************************************************************************

/*
 * @package Open-AudIT
 * @author Mark Unwin <marku@opmantek.com>
 *
 * @version   GIT: Open-AudIT_4.3.3
 * @license http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
 */


if (! function_exists('windows_credentials')) {
    /**
     * The SSH credentials test.
     *
     * @access    public
     *
     * @category  Function
     *
     * @author    Mark Unwin <marku@opmantek.com>
     *
     * @param     ip          The target device's ip address
     *
     * @param     credentials An array of credentials objects
     *
     * @param     display     Should we echo the output to te screen?
     *
     * @return    false || credentials object with an additional flag for 'sudo' and root
     */
    function windows_credentials($ip = '', $credentials = array(), $log = null)
    {
        if (empty($log)) {
            $log = new stdClass();
        }
        $log->severity = 7;
        $log->file = 'wmi_helper';
        $log->function = 'windows_credentials';
        $log->message = 'Windows credentials starting';
        discovery_log($log);


        if (empty($credentials)) {
            $log->severity = 3;
            $log->message = 'No credentials array passed to windows_credentials.';
            $log->command_status = 'fail';
            discovery_log($log);
            return false;
        }
        if (empty($ip) or !filter_var($ip, FILTER_VALIDATE_IP)) {
            $log->severity = 3;
            $log->message = 'No IP passed or bad IP to windows_credentials ('. $ip . ').';
            $log->command_status = 'fail';
            discovery_log($log);
            return false;
        }
        $log->ip = $ip;

        foreach ($credentials as $credential) {
            if ($credential->type == 'windows') {
                $log->file = 'wmi_helper';
                $log->function = 'windows_credentials';
                $log->message = 'Testing credential set ' . $credential->name;
                $log->command = 'csproduct get uuid';
                $wmi_result = wmi_command($ip, $credential, $log->command, $log);
                if ($wmi_result != false and $wmi_result['status'] == 0) {
                    $log->message = "Windows credentials complete. Credential set " . $credential->name . " working on " . $ip;
                    $log->command_status = 'success';
                    discovery_log($log);
                    return $credential;
                } else {
                    $log->message = "Credential set for Windows named " . $credential->name . " not working on " . $ip;
                    $log->command_status = 'notice';
                    discovery_log($log);
                }
            }
        }
        unset($log->command);
        $log->command_status = 'warning';
        $log->file = 'wmi_helper';
        $log->function = 'windows_credentials';
        $log->message = "WMI detected but no valid Windows credentials for $ip.";
        $log->severity = 7;
        discovery_log($log);
        return false;
    }
}

if (! function_exists('execute_windows')) {
    /**
     * The SSH credentials test.
     *
     * @access    public
     *
     * @category  Function
     *
     * @author    Mark Unwin <marku@opmantek.com>
     *
     * @param     ip        The target device's ip address
     *
     * @param     credentials The credentials object
     *
     * @param     command   The command to be run using SSH
     *
     * @param     display   Should we output to the screen or not?
     *
     * @return    false || $return array containing the output and status flag
     */
    function execute_windows($ip = '', $credentials = null, $command = '', $log = null)
    {

        $log->file = 'wmi_helper';
        $log->function = 'execute_windows';
        $log->command = '';
        $CI = & get_instance();

        if (empty($ip)) {
            $log->message = 'No IP supplied to wmi_helper::execute_windows';
            discovery_log($log);
            return false;
        }

        if ( ! filter_var($ip, FILTER_VALIDATE_IP)) {
            $log->message = 'No valid IP supplied to wmi_helper::execute_windows';
            discovery_log($log);
            return false;
        }

        if ( ! is_object($credentials)) {
            $log->message = 'No credentials passed to wmi_helper::execute_windows';
            discovery_log($log);
            return false;
        }

        if ($command === '') {
            $log->message = 'No command passed to wmi_helper::execute_windows';
            discovery_log($log);
            return false;
        }

        if (php_uname('s') === 'Darwin') {
            $filepath = $CI->config->config['base_path'] . '/other';
            if ( ! file_exists('/usr/local/bin/winexe')) {
                $log->message = 'Winexe not installed on OSX, cannot run execute_windows.';
                discovery_log($log);
                return false;
            }
            $temp = explode('@', $credentials->credentials->username);
            $username = $temp[0];
            $domain = $temp[1];
            unset($temp);
            $command_string = 'winexe -U ' . $domain . '/' . $username . '%' . $credentials->credentials->password . ' //' . $ip . ' \'' . $command . '\'';
            $log->command   = 'winexe -U ' . $domain . '/' . $username . '%' . '******' .                            ' //' . $ip . ' \'' . $command . '\'';
            discovery_log($log);
            exec($command_string, $output, $return_var);
        }

        if (php_uname('s') === 'Linux') {
            $filepath = $CI->config->config['base_path'] . '/other';
            $filename = credentials_file($ip, $credentials);
            // For an unknown reason, if we attempt to execute an SMB2 command first and it does not work, the return var is NULL, which means success.
            // So before we attempt to actually run the audit script, try a WMIC query using SMB2 and determine which to use for the script.
            $command_string = "timeout 1m " . $filepath . "/winexe-static-2 -A {$filename} --uninstall //".$ip." \"wmic csproduct get uuid\" 2>&1";
            $item_start = microtime(true);
            exec($command_string, $output, $return_var);
            $log->command_time_to_execute = (microtime(true) - $item_start);
            $log->output = @json_encode($output);
            $log->command_status = 'notice';
            $log->command = $command_string;
            $output = '';
            if ($return_var == 0) {
                // Success, use SMB2
                $log->message = 'Winexe 2 tested and working.';
                $command_string = "timeout 15m ${filepath}/winexe-static-2 -A $filename --uninstall //$ip \"$command\" 2>&1";
                $win = 'winexe-static-2';
            } else {
                // Failure, use SMB1
                $log->message = 'Winexe 2 tested and failed, so using Winexe.';
                $command_string = "timeout 15m ${filepath}/winexe-static -A $filename --uninstall //$ip \"$command\" 2>&1";
                $win = 'winexe-static';
            }
            discovery_log($log);
            unset($return_var);

            $log->command_status = '';
            $log->command = $command_string;
            $item_start = microtime(true);
            exec($command_string, $output, $return_var);
            $log->command_time_to_execute = (microtime(true) - $item_start);
            $log->command_output = @json_encode($output);
            $log->command_status = 'notice';
            $log->message = 'Attempting to execute command using ' . $win . ' succeeded.';
            if (empty($output)) {
                $log->command_status = 'fail';
                $log->message = 'Attempting to execute command using ' . $win . ' failed.';
            }
            discovery_log($log);
            unlink($filename);
        }

        if (php_uname('s') == 'Windows NT') {
            $password = str_replace('"', '\"', $credentials->credentials->password);
            $command_string  = $CI->config->config['base_path'] . '\\other\\paexec.exe \\\\' . $ip . ' -s -noname -u ' . $credentials->credentials->username . ' -p "' . $password . '" cmd /c "' . $command . '"';
            $log->command    = $CI->config->config['base_path'] . '\\other\\paexec.exe \\\\' . $ip . ' -s -noname -u ' . $credentials->credentials->username . ' -p "' . '*******' . '" cmd /c "' . $command . '"';
            exec($command_string, $output, $return_var);
            $log->message = 'Running command script on ' . $ip;
            $log->command_output = json_encode($output);
            $log->command_status = 'fail';
            foreach ($output as $line) {
                if (stripos($line, "Microsoft (R) Windows Script") !== false) {
                    $log->command_status = 'success';
                }
            }
            discovery_log($log);
        }

        unset($log->id, $log->command, $log->command_status, $log->command_time_to_execute, $log->command_output, $log->file, $log->function);
        if ($return_var == 0) {
            return $output;
        } else {
            return false;
        }
    }
}

if (! function_exists('copy_to_windows')) {
    /**
     * The SSH credentials test.
     *
     * @access    public
     *
     * @category  Function
     *
     * @author    Mark Unwin <marku@opmantek.com>
     *
     * @param     username  The username used to connect
     *
     * @param     password  The password used to connect
     *
     * @param     ip        The target device's ip address
     *
     * @param     command   The command to be run using SSH
     *
     * @param     display   Should we output to the screen or not?
     *
     * @return    false || true
     */
    function copy_to_windows($ip = '', $credentials, $share, $source = '', $destination, $log)
    {
        $log->file = 'wmi_helper';
        $log->function = 'copy_to_windows';
        $CI = & get_instance();

        if (empty($ip)) {
            $log->message = 'No IP supplied to wmi_helper::copy_to_windows';
            discovery_log($log);
            return false;
        }

        if ( ! filter_var($ip, FILTER_VALIDATE_IP)) {
            $log->message = 'No valid IP supplied to wmi_helper::copy_to_windows ' . $ip;
            discovery_log($log);
            return false;
        }

        if ( ! is_object($credentials)) {
            $log->message = 'No credentials passed to wmi_helper::copy_to_windows';
            discovery_log($log);
            return false;
        }

        if ($share === '') {
            $log->message = 'No share passed to wmi_helper::copy_to_windows';
            discovery_log($log);
            return false;
        }

        if ($source === '') {
            $log->message = 'No source passed to wmi_helper::copy_to_windows';
            discovery_log($log);
            return false;
        }

        if ($destination === '') {
            $log->message = 'No destination passed to wmi_helper::copy_to_windows';
            discovery_log($log);
            return false;
        }

        if (php_uname('s') === 'Darwin') {
            $timestamp = date('Y_m_d_H_i_s');
            $temp = explode('@', $credentials->credentials->username);
            $username = $temp[0];
            $domain = $temp[1];
            unset($temp);
            $password = $credentials->credentials->password;
            if ( ! is_dir('/private/tmp')) {
                mkdir('/private/tmp') or die('OSX attempt to create /private/tmp failed in wmi_helper::copy_to_windows failed');
            }
            $log->command = "mkdir('/private/tmp')";
            if (mkdir('/private/tmp/' . $timestamp)) {
                $log->message = 'Attempt to create /tmp/' . $timestamp . ' in wmi_helper::copy_to_windows succeeded.';
                $log->severity = 5;
                discovery_log($log);
            } else {
                $log->message = 'Attempt to create /tmp/' . $timestamp . ' in wmi_helper::copy_to_windows failed.';
                $log->severity = 5;
                discovery_log($log);
            }
            $log->command = '';

            $command =      'mount -t smbfs "smb://' . $domain . ';' . $username . ':' . $password . '@' . $ip . '/admin$" /private/tmp/' . $timestamp;
            $log->command = 'mount -t smbfs "smb://' . $domain . ';' . $username . ':' . '*******' . '@' . $ip . '/admin$" /private/tmp/' . $timestamp;
            exec($command, $output, $return_var);
            if ($return_var != 0) {
                $log->message = 'Attempt to mount admin$ share in wmi_helper::copy_to_windows failed.';
                if (!empty($output[0])) {
                    $log->command_error_message = $output[0];
                }
                $log->severity = 5;
                discovery_log($log);
                $log->severity = 7;
                unset($log->command_error_message);
                return false;
            } else {
                $log->message = 'Attempt to mount admin$ share in wmi_helper::copy_to_windows succeeded.';
                discovery_log($log);
            }
            $log->command = '';

            $log->command = "copy($source, '/tmp/$timestamp/$destination')";
            if (copy($source, '/tmp/'.$timestamp.'/'.$destination) or die('Could not copy ' . $source . ' to /tmp/' . $timestamp . '/' . $destination)) {
                $log->message = 'Attempt to copy ' . $destination . ' in wmi_helper::copy_to_windows succeeded.';
                discovery_log($log);
            } else {
                $log->message = 'Attempt to copy ' . $destination . ' in wmi_helper::copy_to_windows failed.';
                $log->severity = 5;
                discovery_log($log);
                $log->severity = 7;
            }
            $log->command = '';

            $command = 'umount /private/tmp/'.$timestamp;
            $log->command = $command;
            exec($command, $output, $return_var);
            if ($return_var != 0) {
                $log->message = 'Attempt to unmount /private/tmp/' . $timestamp . ' in wmi_helper::copy_to_windows failed.';
                $log->severity = 5;
                $log->command_error_message = $output[0];
                discovery_log($log);
                $log->severity = 7;
                unset($log->command_error_message);
                return false;
            } else {
                $log->message = 'Attempt to unmount /private/tmp/' . $timestamp . ' in wmi_helper::copy_to_windows succeeded.';
                discovery_log($log);
            }
            $log->command = '';
        }

        if (php_uname('s') == 'Linux') {
            $command = 'which smbclient';
            exec($command, $output, $return_var);
            if ($return_var != 0) {
                $log->command = 'which smbclient';
                $log->message = 'Linux attempt to copy file to windows, without useable smbclient in wmi_helper::copy_to_windows';
                $log->severity = 5;
                discovery_log($log);
                $log->severity = 7;
                return false;
            }
            $log->command = '';
            $filename = credentials_file($ip, $credentials);
            $command = 'smbclient -m SMB2 \\\\\\\\'.$ip.'\\\\' . $share . ' -A ' . $filename . ' -c "put ' . $source . ' ' . $destination . ' 2>&1"';
            $log->command = $command;
            exec($command, $output, $return_var);
            if ($return_var == 0) {
                $log->message = 'Linux attempt using SMB2 to copy file to ' . $ip . ' succeeded in wmi_helper::copy_to_windows';
                $log->command_status = 'success';
                $log->command_output = '';
                $log->severity = 7;
                discovery_log($log);
            } else {
                $log->message = 'Linux attempt using SMB2 to copy file to ' . $ip . ' failed in wmi_helper::copy_to_windows.';
                $log->command_status = 'notice';
                $log->command_output = json_encode($output);
                $log->severity = 7;
                discovery_log($log);
                $command = 'smbclient \\\\\\\\'.$ip.'\\\\' . $share . ' -A ' . $filename . ' -c "put ' . $source . ' ' . $destination . ' 2>&1"';
                $log->command = $command;
                exec($command, $output, $return_var);
                if ($return_var == 0) {
                    $log->message = 'Linux attempt (SMB domain\\user) to copy file to ' . $ip . ' succeeded in wmi_helper::copy_to_windows';
                    $log->command_status = 'success';
                    $log->command_output = '';
                    $log->severity = 7;
                    discovery_log($log);
                } else {
                    $log->message = 'Linux attempt (SMB domain\\user) to copy file to ' . $ip . ' failed in wmi_helper::copy_to_windows';
                    $log->command_status = 'fail';
                    $log->command_output = json_encode($output);
                    $log->severity = 5;
                    discovery_log($log);
                }
            }
            unlink($filename);
            if ($return_var == 0) {
                return true;
            } else {
                return false;
            }
        }

        if (php_uname('s') === 'Windows NT') {
            $return = false;
            $password = str_replace('"', '\"', $credentials->credentials->password);
            $username = $credentials->credentials->username;

            $command      = 'net use "\\\\' . $ip . '\\admin$" /u:' . $username . ' "' . $password . '"';
            $log->command = 'net use "\\\\' . $ip . '\\admin$" /u:' . $username . ' "' . '*******' . '"';
            $output = '';

            exec($command, $output, $return_var);
            $log->command_status = 'fail';
            $log->message = 'Net Use';
            $log->command_output = json_encode($output);
            if ($output[0] === 'The command completed successfully.' or $return_var === 0) {
                $log->command_status = 'success';
            }
            discovery_log($log);

            $command      = 'copy "' . $source . '" "\\\\' . $ip . '\\admin$\\' .  $destination . '"';
            $log->command = 'copy "' . $source . '" "\\\\' . $ip . '\\admin$\\' .  $destination . '"';
            $output = '';
            exec($command, $output, $return_var);
            $log->command_status = 'fail';
            $log->message = 'Copy to ' . $ip;
            $log->command_output = json_encode($output);
            if (stripos($output[0], 'file(s) copied.') !== false or $return_var === 0) {
                $log->command_status = 'success';
                $return = true;
            }
            discovery_log($log);

            $command      = 'net use "\\\\' . $ip . '\\admin$" /D';
            $log->command = 'net use "\\\\' . $ip . '\\admin$" /D';
            $output = '';
            exec($command, $output, $return_var);
            $log->command_status = 'fail';
            $log->message = 'Net Use Delete';
            $log->command_output = json_encode($output);
            if (stripos($output[0], 'was deleted successfully') !== false or $return_var === 0) {
                $log->command_status = 'success';
            }
            discovery_log($log);

            $log->message = '';
            $log->command = '';
            $log->command_output = '';
            $log->command_status = '';

            return $return;
        }
    }
}

if (! function_exists('delete_windows_result')) {
    /**
     * Delete the audit result from a Windows target
     *
     * @access    public
     *
     * @category  Function
     *
     * @author    Mark Unwin <marku@opmantek.com>
     *
     * @param     parameters    Object containing ip, share, file, credentials, log
     *
     * @return    false || true Depending on success
     */
    function delete_windows_result($parameters)
    {
        if (!empty($parameters->log)) {
            $log = $parameters->log;
        } else {
            $log = new stdClass();
        }
        $log->file = 'wmi_helper';
        $log->function = 'delete_windows_result';
        if (!empty($parameters->discovery_id)) {
            $log->discovery_id = $parameters->discovery_id;
        }
        $CI = & get_instance();

        if (empty($parameters->ip)) {
            $log->message = 'No IP supplied to wmi_helper::delete_windows_result';
            discovery_log($log);
            return false;
        }

        if (!filter_var($parameters->ip, FILTER_VALIDATE_IP)) {
            $log->message = 'No valid IP supplied to wmi_helper::delete_windows_result ' . $parameters->ip;
            discovery_log($log);
            return false;
        }

        $log->ip = $parameters->ip;

        if (!is_object($parameters->credentials)) {
            $log->message = 'No credentials passed to wmi_helper::delete_windows_result';
            discovery_log($log);
            return false;
        }

        if ($parameters->share == '') {
            $log->message = 'No share passed to wmi_helper::delete_windows_result';
            discovery_log($log);
            return false;
        }

        if ($parameters->file == '') {
            $log->message = 'No filename passed to wmi_helper::delete_windows_result';
            discovery_log($log);
            return false;
        }

        if (php_uname('s') == 'Linux') {
            $filename = credentials_file($parameters->ip, $parameters->credentials);
            $command = 'smbclient -m SMB2 \\\\\\\\'.$parameters->ip.'\\\\' . $parameters->share . ' -A ' . $filename . ' -c "del ' . $parameters->file . '"';
            $log->command = $command;
            exec($command, $output, $return_var);
            if ($return_var == 0) {
                $log->message = 'Linux attempt using SMB2 to delete file from ' . $parameters->ip . ' succeeded in wmi_helper::delete_windows_result';
                $log->command_status = 'success';
                $log->command_output = '';
                $log->severity = 7;
                discovery_log($log);
            } else {
                $log->message = 'Linux attempt using SMB2 to delete file from ' . $parameters->ip . ' failed in wmi_helper::delete_windows_result';
                $log->command_status = 'notice';
                $log->command_output = json_encode($output);
                $log->severity = 6;
                discovery_log($log);
                $command = 'smbclient \\\\\\\\'.$parameters->ip.'\\\\' . $parameters->share . ' -A ' . $filename . ' -c "del ' . $parameters->file . '"';
                $log->command = $command;
                exec($command, $output, $return_var);
                if ($return_var == 0) {
                    $log->message = 'Linux attempt using SMB1 to delete file from ' . $parameters->ip . ' succeeded in wmi_helper::delete_windows_result';
                    $log->command_status = 'success';
                    $log->command_output = '';
                    $log->severity = 7;
                    discovery_log($log);
                } else {
                    $log->message = 'Linux attempt using SMB1 to delete file from ' . $parameters->ip . ' failed in wmi_helper::delete_windows_result';
                    $log->command_status = 'fail';
                    $log->command_output = json_encode($output);
                    $log->severity = 5;
                    discovery_log($log);
                }
            }
            unlink($filename);
            if ($return_var == 0) {
                return true;
            } else {
                return false;
            }
        }

        if (php_uname('s') == 'Windows NT') {
            # Must have paexec
            if (!file_exists($CI->config->config['base_path'] . '\\other\\paexec.exe')) {
                $log->message = 'You must have paexec.exe in ' . $CI->config->config['base_path'] . '\\open-audit\\other\\';
                $log->command = '';
                $log->command_status = 'fail';
                discovery_log($log);
                return false;
            }
            $password = str_replace('"', '\"', $parameters->credentials->credentials->password);
            $username = $parameters->credentials->credentials->username;
            $command =      $CI->config->config['base_path'] . '\\other\\paexec.exe \\\\' . $parameters->ip . ' -s -u ' . $username . ' -p "' . $password . '" cmd /c "del \\\\' . $parameters->ip . '\\' . $parameters->share . '\\' . $parameters->file . '"';
            $log->command = $CI->config->config['base_path'] . '\\other\\paexec.exe \\\\' . $parameters->ip . ' -s -u ' . $username . ' -p "' . '*******' . '" cmd /c "del \\\\' . $parameters->ip . '\\' . $parameters->share . '\\' . $parameters->file . '"';
            exec($command, $output, $return_var);
            $log->message = 'Delete file on ' . $parameters->ip;
            $log->command_output = json_encode($output);
            if (empty($return_var)) {
                $return = false;
                $log->command_status = 'success';
            } else {
                $log->command_status = 'fail';
                $log->severity = 6;
                $return = true;
            }
            discovery_log($log);
            return $return;
        }
    }
}


if ( ! function_exists('copy_from_windows')) {
    /**
     * [copy_from_windows description]
     * @param  string $ip          The target device's ip address
     * @param  [type] $credentials The credential set
     * @param  string $source      The source on the target
     * @param  [type] $destination The local destination
     * @param  object $log         Our standard loging object so we can add to the correct discovery log
     * @return bool                false || true Depending on success
     */
    function copy_from_windows($ip = '', $credentials, $source = '', $destination, $log)
    {
        $log->file = 'wmi_helper';
        $log->function = 'copy_from_windows';
        $CI = & get_instance();

        if (empty($ip)) {
            $log->message = 'No IP supplied to wmi_helper::copy_from_windows';
            discovery_log($log);
            return false;
        }

        if ( ! filter_var($ip, FILTER_VALIDATE_IP)) {
            $log->message = 'No valid IP supplied to wmi_helper::copy_from_windows ' . $ip;
            discovery_log($log);
            return false;
        }

        if ( ! is_object($credentials)) {
            $log->message = 'No credentials passed to wmi_helper::copy_from_windows';
            discovery_log($log);
            return false;
        }

        if ($source === '') {
            $log->message = 'No source passed to wmi_helper::copy_from_windows';
            discovery_log($log);
            return false;
        }

        if ($destination === '') {
            $log->message = 'No destination passed to wmi_helper::copy_from_windows';
            discovery_log($log);
            return false;
        }

        if (php_uname('s') === 'Darwin') {
            $timestamp = date('Y_m_d_H_i_s');
            $temp = explode('@', $credentials->credentials->username);
            $username = $temp[0];
            $domain = $temp[1];
            unset($temp);
            $password = $credentials->credentials->password;
            if (!is_dir('/private/tmp')) {
                mkdir('/private/tmp') or die ('OSX attempt to create /private/tmp failed in wmi_helper::copy_from_windows failed');
            }
            $log->command = "mkdir('/private/tmp')";
            if (mkdir('/private/tmp/' . $timestamp)) {
                $log->message = 'Attempt to create /tmp/' . $timestamp . ' in wmi_helper::copy_from_windows succeeded.';
                $log->severity = 5;
                discovery_log($log);
            } else {
                $log->message = 'Attempt to create /tmp/' . $timestamp . ' in wmi_helper::copy_from_windows failed.';
                $log->severity = 5;
                discovery_log($log);
            }
            $log->command = '';

            $command =      'mount -t smbfs "smb://' . $domain . ';' . $username . ':' . $password . '@' . $ip . '/admin$" /private/tmp/' . $timestamp;
            $log->command = 'mount -t smbfs "smb://' . $domain . ';' . $username . ':' . '*******' . '@' . $ip . '/admin$" /private/tmp/' . $timestamp;
            exec($command, $output, $return_var);
            if ($return_var != 0) {
                $log->message = 'Attempt to mount admin$ share in wmi_helper::copy_from_windows failed.';
                if (!empty($output[0])) {
                    $log->command_error_message = $output[0];
                }
                $log->severity = 5;
                discovery_log($log);
                $log->severity = 7;
                unset($log->command_error_message);
                return false;
            } else {
                $log->message = 'Attempt to mount admin$ share in wmi_helper::copy_from_windows succeeded.';
                discovery_log($log);
            }

            $log->command = "copy($source, '/tmp/$timestamp/$destination')";
            if (copy($source, '/tmp/'.$timestamp.'/'.$destination) or die ('Could not copy ' . $source . ' to /tmp/' . $timestamp . '/' . $destination)) {
                $log->message = 'Attempt to copy ' . $destination . ' in wmi_helper::copy_from_windows succeeded.';
                discovery_log($log);
            } else {
                $log->message = 'Attempt to copy ' . $destination . ' in wmi_helper::copy_from_windows failed.';
                $log->severity = 5;
                discovery_log($log);
                $log->severity = 7;
            }

            $command =      'umount /private/tmp/'.$timestamp;
            $log->command = 'umount /private/tmp/'.$timestamp;
            exec($command, $output, $return_var);
            if ($return_var != 0) {
                $log->message = 'Attempt to unmount /private/tmp/' . $timestamp . ' in wmi_helper::copy_from_windows failed.';
                $log->severity = 5;
                $log->command_error_message = $output[0];
                discovery_log($log);
                $log->severity = 7;
                unset($log->command_error_message);
                return false;
            } else {
                $log->message = 'Attempt to unmount /private/tmp/' . $timestamp . ' in wmi_helper::copy_from_windows succeeded.';
                discovery_log($log);
            }
            $log->command = '';
        }

        if (php_uname('s') == 'Linux') {
            $command = 'which smbclient';
            exec($command, $output, $return_var);
            if ($return_var != 0) {
                $log->command = 'which smbclient';
                $log->message = 'Linux attempt to copy file from windows, without useable smbclient in wmi_helper::copy_from_windows';
                $log->severity = 5;
                discovery_log($log);
                $log->severity = 7;
                return false;
            }
            $filename = credentials_file($ip, $credentials);
            $command = 'smbclient -m SMB2 \\\\\\\\'.$ip.'\\\\admin\$ -A ' . $filename . ' -c "get ' . $source . ' ' . $destination . ' 2>&1"';
            $log->command = $command;
            $output = '';
            exec($command, $output, $return_var);
            $log->command_output = json_encode($output);
            if ($return_var == 0) {
                $log->message = 'Linux attempt using SMB2 to copy file from ' . $ip . ' succeeded in wmi_helper::copy_from_windows';
                $log->command_status = 'success';
                $log->command_output = '';
                $log->severity = 7;
                discovery_log($log);
                unlink($filename);
                return true;
            } else {
                $log->message = 'Linux attempt using SMB2 to copy file from ' . $ip . ' failed in wmi_helper::copy_from_windows';
                $log->command_status = 'notice';
                $log->command_output = json_encode($output);
                $log->severity = 7;
                discovery_log($log);
            }
            $command = 'smbclient \\\\\\\\'.$ip.'\\\\admin\$ -A ' . $filename . ' -c "get ' . $source . ' ' . $destination . ' 2>&1"';
            $log->command = $command;
            exec($command, $output, $return_var);
            $log->command_output = json_encode($output);
            if ($return_var == 0) {
                $log->message = 'Linux attempt using SMB1 to copy file from ' . $ip . ' succeeded in wmi_helper::copy_from_windows';
                $log->command_status = 'success';
                $log->command_output = '';
                $log->severity = 7;
                discovery_log($log);
                unlink($filename);
                return true;
            } else {
                $log->message = 'Linux attempt using SMB1 to copy file from ' . $ip . ' failed in wmi_helper::copy_from_windows.';
                $log->command_status = 'fail';
                $log->command_output = json_encode($output);
                $log->severity = 5;
                discovery_log($log);
                unlink($filename);
                return false;
            }
        }

        if (php_uname('s') == 'Windows NT') {
            $password = str_replace('"', '\"', $credentials->credentials->password);

            exec($command, $output, $return_var);
            $log->command_status = 'fail';
            $log->message = 'Net Use';
            $log->command_output = json_encode($output);
            if ($output[0] == "The command completed successfully.") {
                $log->command_status = 'success';
            }
            discovery_log($log);

            $command =      'copy "\\\\' . $ip . '\\admin$\\' . $source . '" "' . $destination . '"';
            $log->command = 'copy "\\\\' . $ip . '\\admin$\\' . $source . '" "' . $destination . '"';
            $output = '';
            exec($command, $output, $return_var);
            $log->command_status = 'fail';
            $log->message = 'Copy from ' . $source;
            $log->command_output = json_encode($output);
            if (stripos($output[0], 'file(s) copied.') !== false) {
                $log->command_status = 'success';
            }
            discovery_log($log);

            $command =      'net use "\\\\' . $ip . '\\admin$" /D';
            $log->command = 'net use "\\\\' . $ip . '\\admin$" /D';
            $output = '';
            exec($command, $output, $return_var);
            $log->command_status = 'fail';
            $log->message = 'Net Use Delete';
            $log->command_output = json_encode($output);
            if (stripos($output[0], 'was deleted successfully') !== false) {
                $log->command_status = 'success';
            }
            discovery_log($log);

            $log->message = '';
            $log->command = '';
            $log->command_output = '';
            $log->command_status = '';

            if (file_exists($destination)) {
                return true;
            } else {
                return false;
            }
        }
    }
}

if (! function_exists('wmi_command')) {
    /**
     * The SSH credentials test. 
     *
     * @access    public
     *
     * @category  Function
     *
     * @author    Mark Unwin <marku@opmantek.com>
     *
     * @param     username  The username used to connect
     *
     * @param     password  The password used to connect
     *
     * @param     ip        The target device's ip address
     *
     * @param     command   The command to be run using SSH
     *
     * @param     display   Should we output to the screen or not?
     *
     * @return    false || $return array containing the output and status flag
     */
    function wmi_command($ip = '', $credentials, $command = '', $log)
    {

        $log->file = 'wmi_helper';
        $log->function = 'wmi_command';
        $log->severity = 7;
        $return = array('output' => '', 'status' => '');
        $CI = & get_instance();

        $log->message = 'Using credentials named ' . $credentials->name;

        if (empty($ip)) {
            $log->message = 'No IP supplied to wmi_helper::wmi_command';
            discovery_log($log);
            return false;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $log->message = 'Invalid IP supplied to wmi_helper::wmi_command';
            discovery_log($log);
            return false;
        }

        if (!is_object($credentials)) {
            $log->message = 'No credentials passed to wmi_helper::wmi_command';
            discovery_log($log);
            return false;
        }

        if ($command == '') {
            $log->message = 'No command passed to wmi_helper::wmi_command';
            discovery_log($log);
            return false;
        }

        if (empty($credentials->credentials->username)) {
            $log->message = 'Missing username passed to wmi_helper::wmi_command';
            discovery_log($log);
            return false;
        }

        if (empty($credentials->credentials->password)) {
            $log->message = 'Missing password passed to wmi_helper::wmi_command';
            discovery_log($log);
            return false;
        }

        if (!empty($credentials->credentials->password)) {
            $password = str_replace('$', '\$', $credentials->credentials->password);
        } else {
            $password = false;
        }

        if (php_uname('s') == 'Darwin' and !file_exists('/usr/local/bin/winexe')) {
            $log->message = 'Winexe not installed on OSX, cannot run wmi_command.';
            discovery_log($log);
            return false;
        }
        if (php_uname('s') == 'Darwin') {
            $command_string = "/usr/local/bin/winexe";
            $temp = explode('@', $credentials->credentials->username);
            $username = $temp[0];
            $domain = $temp[1];
            if ($domain != '') {
                $domain .= '/';
            }
            unset($temp);
            $password = escapeshellarg($credentials->credentials->password);
            $username = escapeshellarg(str_replace("'", "", $username));
            $command_string .= " -U ".$domain.$username."%****** --uninstall //".$ip." \"wmic $command\" ";
            $log->command   = $command_string;
            $log->message = "Attempting to execute command";
            $log->id = discovery_log($log);
            $item_start = microtime(true);
            $command_string = str_replace("******", $password, $command_string);
            exec($command_string, $return['output'], $return['status']);
        }

        if (php_uname('s') == 'Linux') {
            $filepath = $CI->config->config['base_path'] . '/other';
            $filename = credentials_file($ip, $credentials);
            $command_string = "timeout 1m " . $filepath . "/winexe-static-2 -A {$filename} --uninstall //".$ip." \"wmic $command\" 2>&1";
            $log->command   = $command_string;
            $log->message = 'Using credentials named ' . $credentials->name . ' to execute command using winexe-static-2';
            $item_start = microtime(true);
            exec($command_string, $return['output'], $return['status']);
            if ($return['status'] != '0') {
                $log->command_time_to_execute = (microtime(true) - $item_start);
                $log->command_status = 'notice';
                $log->command_output = json_encode($return['output']);
                discovery_log($log);
                unset($log->id, $log->command_status, $log->command_time_to_execute, $log->command_output);

                $log->command   = str_replace('winexe-static-2', 'winexe-static', $log->command);
                $log->message = 'Using credentials named ' . $credentials->name . ' to execute command using winexe-static';
                $return['output'] = '';
                $command_string = str_replace('winexe-static-2', 'winexe-static', $command_string);
                $item_start = microtime(true);
                exec($command_string, $return['output'], $return['status']);
            }
            unlink($filename);
        }

        if (php_uname('s') == 'Windows NT') {
            $temp = explode('@', $credentials->credentials->username);
            $username = $temp[0];
            $domain = $temp[1];
            if ($domain != '') {
                $domain .= '\\';
            }
            unset($temp);
            $password = $credentials->credentials->password;
            # $ doesn't require escaping
            # ' doesn't require escaping when using "password"
            # " doesn't seem to work even when escaped using \"
            # | can only be escaped by "
            if ((strpos($password, '"') !== false) and (strpos($password, "'") !== false)) {
                $log->severity = 5;
                $log->message = 'Incompatible password (cannot have " or \' in a wmic password).';
                $log->command = '';
                discovery_log($log);
                return false;
            }
            $log->message = 'Attempting to execute command';
            $log->severity = 7;
            $log->command = '%comspec% /c start /b wmic /Node:"' . $ip . '" /user:"' . $domain.$username . '" /password:"' . '*******' . '" ' . $command;
            $command =      '%comspec% /c start /b wmic /Node:"' . $ip . '" /user:"' . $domain.$username . '" /password:"' . $password . '" ' . $command;
            $item_start = microtime(true);
            exec($command, $return['output'], $return['status']);
            if (empty($return['output'][0])) {
                $return['status'] = 1;
            }
        }

        $log->command_time_to_execute = (microtime(true) - $item_start);
        if ($return['status'] != '0') {
            $log->command_status = 'notice';
        } else {
            $log->command_status = 'success';
        }
        $log->command_output = json_encode($return['output']);
        discovery_log($log);
        unset($log->id, $log->command, $log->command_status, $log->command_time_to_execute, $log->command_output);
        return($return);
    }
}


if ( ! function_exists('windows_ips_found')) {
    /**
     * [wmi_audit description]
     * @param  string $ip           [description]
     * @param  [type] $credentials  [description]
     * @param  [type] $log          [description]
     * @param  [type] $discovery_id [description]
     * @return [type]               [description]
     */
    function windows_ips_found($ip = '', $credentials, $discovery_id = null)
    {
        $log = new stdClass();
        $log->discovery_id = $discovery_id;
        $log->file = 'wmi_helper';
        $log->function = 'windows_ips_found';
        $log->severity = 7;
        $log->command_status = 'notice';
        $log->message = 'Retrieving arp table from Windows';
        $log->ip = @$ip;
        discovery_log($log);

        if (empty($ip)) {
            $log->message = 'No IP supplied to wmi_helper::windows_ips_found.';
            discovery_log($log);
            return false;
        }

        if ( ! filter_var($ip, FILTER_VALIDATE_IP)) {
            $log->message = 'No valid IP supplied to wmi_helper::windows_ips_found.';
            discovery_log($log);
            return false;
        }

        if ( ! is_object($credentials)) {
            $log->message = 'No credentials supplied to wmi_helper::windows_ips_found.';
            discovery_log($log);
            return false;
        }
        $temp = execute_windows($ip, $credentials, 'arp -a', $log);
        $ips_found = array();
        foreach ($temp as $line) {
            if (strpos($line, 'dynamic') === false) {
                continue;
            }
            $line = trim(preg_replace("/ {2,}/", " ", $line));
            $line_array = explode(' ', $line);
            $ips_found[strtolower(str_replace('-', ':', $line_array[1]))] = $line_array[0];
        }
        return $ips_found;
    }
}


if ( ! function_exists('credentials_file')) {
    function credentials_file($ip = '', $credentials = null) {
        $filename = str_replace('.', '', microtime());
        $filename = str_replace(' ', '', $filename);
        $filename = dirname(dirname(dirname(dirname(dirname(__FILE__)))))."/open-audit/other/scripts/" . str_replace('.', '', $ip) . '_' . $filename . '.txt';
        $temp = explode('@', $credentials->credentials->username);
        $username = $temp[0];
        $domain = @$temp[1];
        $password = $credentials->credentials->password;
        $file = fopen($filename, 'w');
        $text = "username = {$username}\npassword = {$password}\n";
        if (!empty($domain)) {
            $text .= "domain   = {$domain}\n";
        }
        fwrite($file, $text);
        fclose($file);
        sleep(1);
        return $filename;
    }
}


if ( ! function_exists('wmi_audit')) {
    /**
     * [wmi_audit description]
     * @param  string $ip           [description]
     * @param  [type] $credentials  [description]
     * @param  [type] $log          [description]
     * @param  [type] $discovery_id [description]
     * @return [type]               [description]
     */
    function wmi_audit($ip = '', $credentials, $log = null, $discovery_id = null)
    {
        if (is_null($log)) {
            $log = new stdClass();
        }
        if ( ! is_null($discovery_id)) {
            $log->discovery_id = $discovery_id;
        }
        $log->file = 'wmi_helper';
        $log->function = 'wmi_audit';
        $log->severity = 7;
        $log->command_status = 'notice';
        $log->message = 'WMI audit starting';
        $log->ip = @$ip;
        discovery_log($log);

        if (empty($ip)) {
            $log->message = 'No IP supplied to wmi_helper::wmi_audit.';
            discovery_log($log);
            return false;
        }

        if ( ! filter_var($ip, FILTER_VALIDATE_IP)) {
            $log->message = 'No valid IP supplied to wmi_helper::wmi_audit.';
            discovery_log($log);
            return false;
        }

        if ( ! is_object($credentials)) {
            $log->message = 'No credentials supplied to wmi_helper::wmi_audit.';
            discovery_log($log);
            return false;
        }

        $details = new stdClass();

        // UUID
        $command = 'csproduct get uuid';
        $wmi_result = wmi_command($ip, $credentials, $command, $log);
        if ($wmi_result['status'] === 0) {
            if ( ! empty($wmi_result['output'][1])) {
                $details->uuid = $wmi_result['output'][1];
            }
            $details->type = 'computer';
        }

        // Serial
        $command = 'csproduct get IdentifyingNumber';
        $wmi_result = wmi_command($ip, $credentials, $command, $log);
        if ($wmi_result['status'] === 0) {
            if ( ! empty($wmi_result['output'][1])) {
                $details->serial = $wmi_result['output'][1];
            }
        }

        // Manufacturer
        $command = 'csproduct get vendor';
        $wmi_result = wmi_command($ip, $credentials, $command, $log);
        if ($wmi_result['status'] === 0) {
            if ( ! empty($wmi_result['output'][1])) {
                $details->manufacturer = $wmi_result['output'][1];
            }
        }

        // Description
        $command = 'os get description';
        $wmi_result = wmi_command($ip, $credentials, $command, $log);
        if ($wmi_result['status'] === 0) {
            if ( ! empty($wmi_result['output'][1])) {
                $details->description = $wmi_result['output'][1];
            }
        }

        // Hostname / name
        $command = 'computersystem get name';
        $wmi_result = wmi_command($ip, $credentials, $command, $log);
        if ($wmi_result['status'] === 0) {
            if ( ! empty($wmi_result['output'][1])) {
                $details->hostname = strtolower($wmi_result['output'][1]);
                $details->name = $details->hostname;
            }
        }

        // Domain / fqdn
        $command = 'computersystem get domain';
        $wmi_result = wmi_command($ip, $credentials, $command, $log);
        if ($wmi_result['status'] === 0) {
            if ( ! empty($wmi_result['output'][1])) {
                $details->domain = strtolower($wmi_result['output'][1]);
                $details->fqdn = $details->hostname . '.' . $details->domain;
            }
        }

        // OS Name
        $command = 'os get name';
        $wmi_result = wmi_command($ip, $credentials, $command, $log);
        if ($wmi_result['status'] === 0) {
            if ( ! empty($wmi_result['output'][1])) {
                $details->os_group = 'Windows';
                if (stripos($details->os_name, ' 95') !== false) {
                    $details->os_family = 'Windows 95';
                }
                if (stripos($details->os_name, ' 98') !== false) {
                    $details->os_family = 'Windows 98';
                }
                if (stripos($details->os_name, ' NT') !== false) {
                    $details->os_family = 'Windows NT';
                }
                if (stripos($details->os_name, ' 2000') !== false) {
                    $details->os_family = 'Windows 2000';
                }
                if (stripos($details->os_name, ' XP') !== false) {
                    $details->os_family = 'Windows XP';
                }
                if (stripos($details->os_name, '2003') !== false) {
                    $details->os_family = 'Windows 2003';
                }
                if (stripos($details->os_name, 'Vista') !== false) {
                    $details->os_family = 'Windows Vista';
                }
                if (stripos($details->os_name, '2008') !== false) {
                    $details->os_family = 'Windows 2008';
                }
                if (stripos($details->os_name, 'Windows 7') !== false) {
                    $details->os_family = 'Windows 7';
                }
                if (stripos($details->os_name, 'Windows 8') !== false) {
                    $details->os_family = 'Windows 8';
                }
                if (stripos($details->os_name, '2012') !== false) {
                    $details->os_family = 'Windows 2012';
                }
                if (stripos($details->os_name, 'Windows 10') !== false) {
                    $details->os_family = 'Windows 10';
                }
                if (stripos($details->os_name, 'Windows 11') !== false) {
                    $details->os_family = 'Windows 11';
                }
                if (stripos($details->os_name, '2016') !== false) {
                    $details->os_family = 'Windows 2016';
                }
                if (stripos($details->os_name, '2019') !== false) {
                    $details->os_family = 'Windows 2019';
                }
                $details->os_name = $wmi_result['output'][1];
                $details->os_name = str_replace('®', '', $details->os_name);
                $details->os_name = trim(substr($details->os_name, 0, stripos($details->os_name, '|')));
            }
            if ( ! empty($wmi_result['output'][1])) {
                $temp = explode('|', $wmi_result['output'][1]);
                $details->install_dir = trim($temp[1]);
                unset($temp);
            }
        }

        $log->file = 'wmi_helper';
        $log->function = 'wmi_audit';
        $log->command_status = 'notice';
        $log->message = 'WMI audit complete';
        discovery_log($log);

        return($details);
    }
}
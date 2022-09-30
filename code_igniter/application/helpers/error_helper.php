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

if (! function_exists('getError')) {
    /**
     * The standard log function for Open-AudIT. Writes logs to a text file in the desired format (json or syslog).
     *
     * @access	  public
     *
     * @category  Function
     *
     * @author    Mark Unwin <mark.unwin@firstwave.com>
     *
     * @param	  Object	log_details		An object containing details you wish to log
     *
     * @return NULL [logs the provided string to the log file]
     */
    function getError ($error_id = '', $extra = '') {

        $error = new stdClass();
        $CI = & get_instance();
        $error->controller = @$CI->response->meta->collection;
        $error->function = @$CI->response->meta->action;
        $error->code = $error_id;

        
        $CI = & get_instance();
        if (empty($extra)) {
            $extra = ' (User:' . @$CI->user->id . ', Collection:' . @$CI->response->meta->collection . ', Action:' . @$CI->response->meta->action;
        } else {
            $extra = ' (' . $extra;
        }
        if (!empty($CI->response->meta->id)) {
            $extra .= ', ID:' . $CI->response->meta->id;
        }
        $extra .= ').';

        $error_array = getErrors();

        if (!isset($error->code) or is_null($error->code) or (!isset($error_array[$error->code]))) {
            return $error_array;
        } else {
            $error_array[$error->code]->title .= $extra;
            if (isset($error->function)) {
                $error_array[$error->code]->function = $error->function;
            } else {
                $error->function = '';
            }
            if (isset($error->controller)) {
                $error_array[$error->code]->controller = $error->controller;
            } else {
                $error->controller = '';
            }
            return $error_array[$error->code];
        }
    }

if (! function_exists('getErrors')) {
    function getErrors () {
        $error_array = array();

        $error_array['ERR-0001'] = new stdClass();
        $error_array['ERR-0001']->code = 'ERR-0001';
        $error_array['ERR-0001']->status = 'HTTP/1.1 404 Not Found';
        $error_array['ERR-0001']->severity = 3;
        $error_array['ERR-0001']->title = "No groups returned for user";
        $error_array['ERR-0001']->detail = 'When requesting the list of groups the user is assigned access to, no groups were returned. This usually indicates either (rightly) that the user has no permissions on any groups (which will result in this user not being able to access any device data in Open-AudIT) or that something has gone wrong inside Open-AudIT. You might go to menu -> Resources -> Users -> List Users, click on edit for this user and make sure they have an access level on at least one group.';

        $error_array['ERR-0002'] = new stdClass();
        $error_array['ERR-0002']->code = 'ERR-0002';
        $error_array['ERR-0002']->status = 'HTTP/1.1 404 Not Found';
        $error_array['ERR-0002']->severity = 3;
        $error_array['ERR-0002']->title = "No object could be retrieved";
        $error_array['ERR-0002']->detail = "When calling this function an identifier (usually but not always an integer based id) should be supplied. The supplied item was either blank, not an integer based id or we could not determine the corresponding object based on the details provided. Please check the log file for the controller and model this occurred on and report the issue to Firstwave.";

        $error_array['ERR-0003'] = new stdClass();
        $error_array['ERR-0003']->code = 'ERR-0003';
        $error_array['ERR-0003']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0003']->severity = 3;
        $error_array['ERR-0003']->title = "No group columns could be retrieved";
        $error_array['ERR-0003']->detail = "When requesting the columns for a group, no group columns either for the original group id, nor group id #1 were found.";

        $error_array['ERR-0004'] = new stdClass();
        $error_array['ERR-0004']->code = 'ERR-0004';
        $error_array['ERR-0004']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0004']->severity = 4;
        $error_array['ERR-0004']->title = "Name, not id passed";
        $error_array['ERR-0004']->detail = "Controllers should pass an integer based id as a first preference. Controllers should determine (where possible) the id if supplied a name.";

        $error_array['ERR-0005'] = new stdClass();
        $error_array['ERR-0005']->code = 'ERR-0005';
        $error_array['ERR-0005']->status = 'HTTP/1.1 404 Not Found';
        $error_array['ERR-0005']->severity = 5;
        $error_array['ERR-0005']->title = "No data returned";
        $error_array['ERR-0005']->detail = 'A request was made to a model, but no data was retrieved from the database.';

        $error_array['ERR-0006'] = new stdClass();
        $error_array['ERR-0006']->code = 'ERR-0006';
        $error_array['ERR-0006']->status = 'HTTP/1.1 403 Forbidden';
        $error_array['ERR-0006']->severity = 5;
        $error_array['ERR-0006']->title = "User is not authorised to view group";
        $error_array['ERR-0006']->detail = 'A user attempted to view the details of a group he is not authorised to. To enable this user to view this group, edit the user via menu -> Resources -> Users -> List Users and allow at least View Group level of access.';

        $error_array['ERR-0007'] = new stdClass();
        $error_array['ERR-0007']->code = 'ERR-0007';
        $error_array['ERR-0007']->status = 'HTTP/1.1 404 Not Found';
        $error_array['ERR-0007']->severity = 5;
        $error_array['ERR-0007']->title = "Resource does not exist";
        $error_array['ERR-0007']->detail = 'A user attempted to view a resource which does not exist.';

        $error_array['ERR-0008'] = new stdClass();
        $error_array['ERR-0008']->code = 'ERR-0008';
        $error_array['ERR-0008']->status = 'HTTP/1.1 403 Forbidden';
        $error_array['ERR-0008']->severity = 5;
        $error_array['ERR-0008']->title = 'User insufficient access.';
        $error_array['ERR-0008']->detail = 'A user attempted to access a resource for which they do not have authorisation.';

        $error_array['ERR-0009'] = new stdClass();
        $error_array['ERR-0009']->code = 'ERR-0009';
        $error_array['ERR-0009']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0009']->severity = 4;
        $error_array['ERR-0009']->title = "Parameters you have provided failed use";
        $error_array['ERR-0009']->detail = 'Parameters you have provided failed use.';

        $error_array['ERR-0010'] = new stdClass();
        $error_array['ERR-0010']->code = 'ERR-0010';
        $error_array['ERR-0010']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0010']->severity = 3;
        $error_array['ERR-0010']->title = "Cannot create resource with supplied data";
        $error_array['ERR-0010']->detail = 'Cannot create resource with supplied data. Likely a reserved word has been used for a field name or there is already a field with this name or an invalid value for a field has been supplied.';

        $error_array['ERR-0011'] = new stdClass();
        $error_array['ERR-0011']->code = 'ERR-0011';
        $error_array['ERR-0011']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0011']->severity = 3;
        $error_array['ERR-0011']->title = "Cannot create read uploaded file.";
        $error_array['ERR-0011']->detail = 'Cannot create read uploaded file.';

        $error_array['ERR-0012'] = new stdClass();
        $error_array['ERR-0012']->code = 'ERR-0012';
        $error_array['ERR-0012']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0012']->severity = 3;
        $error_array['ERR-0012']->title = "Uploaded XML is invalid.";
        $error_array['ERR-0012']->detail = 'Uploaded XML is invalid.';

        $error_array['ERR-0013'] = new stdClass();
        $error_array['ERR-0013']->code = 'ERR-0013';
        $error_array['ERR-0013']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0013']->severity = 3;
        $error_array['ERR-0013']->title = "Could not delete specified resource.";
        $error_array['ERR-0013']->detail = 'Could not delete specified resource.';

        $error_array['ERR-0014'] = new stdClass();
        $error_array['ERR-0014']->code = 'ERR-0014';
        $error_array['ERR-0014']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0014']->severity = 4;
        $error_array['ERR-0014']->title = "Cannot delete default resource.";
        $error_array['ERR-0014']->detail = 'Cannot delete default resource.';

        $error_array['ERR-0015'] = new stdClass();
        $error_array['ERR-0015']->code = 'ERR-0015';
        $error_array['ERR-0015']->status = 'HTTP/1.1 403 Forbidden';
        $error_array['ERR-0015']->severity = 5;
        $error_array['ERR-0015']->title = "User not authorised";
        $error_array['ERR-0015']->detail = 'User attempted to perform an operation for which they are not authorised';

        $error_array['ERR-0016'] = new stdClass();
        $error_array['ERR-0016']->code = 'ERR-0016';
        $error_array['ERR-0016']->status = 'HTTP/1.1 404 Not Found';
        $error_array['ERR-0016']->severity = 4;
        $error_array['ERR-0016']->title = "File does not exist";
        $error_array['ERR-0016']->detail = 'A user attempted to access an file which does not exist, could not be read or is incorrectly formatted.';

        $error_array['ERR-0017'] = new stdClass();
        $error_array['ERR-0017']->code = 'ERR-0017';
        $error_array['ERR-0017']->status = 'HTTP/1.1 404 Not Found';
        $error_array['ERR-0017']->severity = 4;
        $error_array['ERR-0017']->title = "File not writable";
        $error_array['ERR-0017']->detail = 'A user attempted to write to an file which does not have write permissions set.';

        $error_array['ERR-0018'] = new stdClass();
        $error_array['ERR-0018']->code = 'ERR-0018';
        $error_array['ERR-0018']->status = 'HTTP/1.1 403 Forbidden';
        $error_array['ERR-0018']->severity = 5;
        $error_array['ERR-0018']->title = "User not authorised to use Org";
        $error_array['ERR-0018']->detail = 'A user attempted to write to an org_id to an object for which they do not have permission.';

        $error_array['ERR-0019'] = new stdClass();
        $error_array['ERR-0019']->code = 'ERR-0019';
        $error_array['ERR-0019']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0019']->severity = 4;
        $error_array['ERR-0019']->title = "Could not connect to LDAP";
        $error_array['ERR-0019']->detail = 'When attempting to connect to LDAP for Active Directory, could not.';

        $error_array['ERR-0020'] = new stdClass();
        $error_array['ERR-0020']->code = 'ERR-0020';
        $error_array['ERR-0020']->status = 'HTTP/1.1 401 Unauthorized';
        $error_array['ERR-0020']->severity = 6;
        $error_array['ERR-0020']->title = "User not authorised, credentials required";
        $error_array['ERR-0020']->detail = 'When attempting to access a resource, credentials are required.';

        $error_array['ERR-0021'] = new stdClass();
        $error_array['ERR-0021']->code = 'ERR-0021';
        $error_array['ERR-0021']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0021']->severity = 4;
        $error_array['ERR-0021']->title = "Required attributes not supplied";
        $error_array['ERR-0021']->detail = 'When attempting to create a resource, some attributes are required but missing.';

        $error_array['ERR-0022'] = new stdClass();
        $error_array['ERR-0022']->code = 'ERR-0022';
        $error_array['ERR-0022']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0022']->severity = 4;
        $error_array['ERR-0022']->title = "Required attributes not supplied (WHERE @filter)";
        $error_array['ERR-0022']->detail = 'When attempting to create a query, the supplied SQL did not contain the required WHERE @filter.';

        $error_array['ERR-0023'] = new stdClass();
        $error_array['ERR-0023']->code = 'ERR-0023';
        $error_array['ERR-0023']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0023']->severity = 3;
        $error_array['ERR-0023']->title = '';
        $error_array['ERR-0023']->detail = 'SQL command failed.';

        $error_array['ERR-0024'] = new stdClass();
        $error_array['ERR-0024']->code = 'ERR-0024';
        $error_array['ERR-0024']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0024']->severity = 4;
        $error_array['ERR-0024']->title = "Cannot create resource with supplied data";
        $error_array['ERR-0024']->detail = 'Cannot create resource with supplied data. A required field is missing.';

        $error_array['ERR-0025'] = new stdClass();
        $error_array['ERR-0025']->code = 'ERR-0025';
        $error_array['ERR-0025']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0025']->severity = 4;
        $error_array['ERR-0025']->title = '';
        $error_array['ERR-0025']->detail = 'Update did not supply PATCH data.';

        $error_array['ERR-0026'] = new stdClass();
        $error_array['ERR-0026']->code = 'ERR-0026';
        $error_array['ERR-0026']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0026']->severity = 3;
        $error_array['ERR-0026']->title = "Could not search LDAP";
        $error_array['ERR-0026']->detail = 'When attempting to search LDAP, something went wrong. Check user_dn and base_dn.';

        $error_array['ERR-0027'] = new stdClass();
        $error_array['ERR-0027']->code = 'ERR-0027';
        $error_array['ERR-0027']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0027']->severity = 3;
        $error_array['ERR-0027']->title = "Could not retrieve entries from LDAP";
        $error_array['ERR-0027']->detail = 'When attempting to retrieve the search data entries from LDAP, something went wrong.';

        $error_array['ERR-0028'] = new stdClass();
        $error_array['ERR-0028']->code = 'ERR-0028';
        $error_array['ERR-0028']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0028']->severity = 3;
        $error_array['ERR-0028']->title = "Could not bind to LDAP using dn_account";
        $error_array['ERR-0028']->detail = 'When attempting to bind to LDAP, failed. Check dn_account and dn_password.';

        $error_array['ERR-0029'] = new stdClass();
        $error_array['ERR-0029']->code = 'ERR-0029';
        $error_array['ERR-0029']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0029']->severity = 3;
        $error_array['ERR-0029']->title = "Could not bind to LDAP using user credentials";
        $error_array['ERR-0029']->detail = 'When attempting to bind to LDAP, failed. Check user credentials.';

        $error_array['ERR-0030'] = new stdClass();
        $error_array['ERR-0030']->code = 'ERR-0030';
        $error_array['ERR-0030']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0030']->severity = 4;
        $error_array['ERR-0030']->title = "Required attributes not supplied (collector UUID)";
        $error_array['ERR-0030']->detail = 'When attempting logon with a collector account, you must supply a UUID.';

        $error_array['ERR-0031'] = new stdClass();
        $error_array['ERR-0031']->code = 'ERR-0031';
        $error_array['ERR-0031']->status = 'HTTP/1.1 403 Forbidden';
        $error_array['ERR-0031']->severity = 4;
        $error_array['ERR-0031']->title = "Collector attempting to logon from unassociated ip address.";
        $error_array['ERR-0031']->detail = 'A collector can only log in from a single IP address. Supplied IP does not match IP on record.';

        $error_array['ERR-0032'] = new stdClass();
        $error_array['ERR-0032']->code = 'ERR-0032';
        $error_array['ERR-0032']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0032']->severity = 6;
        $error_array['ERR-0032']->title = "A hostname or FQDN was supplied that cannot be resolved.";
        $error_array['ERR-0032']->detail = 'A hostname or FQDN was supplied that cannot be resolved.';

        $error_array['ERR-0033'] = new stdClass();
        $error_array['ERR-0033']->code = 'ERR-0033';
        $error_array['ERR-0033']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0033']->severity = 4;
        $error_array['ERR-0033']->title = "Decoding JSON failed.";
        $error_array['ERR-0033']->detail = 'Decoding JSON failed.';

        $error_array['ERR-0034'] = new stdClass();
        $error_array['ERR-0034']->code = 'ERR-0034';
        $error_array['ERR-0034']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0034']->severity = 4;
        $error_array['ERR-0034']->title = "No access token supplied.";
        $error_array['ERR-0034']->detail = 'A valid access token was not supplied when submitting data. This is required as at version 2.2.1 because of cross site request forgery protections. Access Tokens are configurable in the Configuration.';

        $error_array['ERR-0035'] = new stdClass();
        $error_array['ERR-0035']->code = 'ERR-0035';
        $error_array['ERR-0035']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0035']->severity = 4;
        $error_array['ERR-0035']->title = "Invalid access token supplied.";
        $error_array['ERR-0035']->detail = 'An invalid access token was supplied when submitting data. Access Tokens have been configured and the token you have supplied is not among the last X valid tokens. Access Tokens are configurable in the Configuration.';

        $error_array['ERR-0036'] = new stdClass();
        $error_array['ERR-0036']->code = 'ERR-0036';
        $error_array['ERR-0036']->status = 'HTTP/1.1 403 Forbidden';
        $error_array['ERR-0036']->severity = 4;
        $error_array['ERR-0036']->title = "Invalid user supplied.";
        $error_array['ERR-0036']->detail = 'A user was supplied in the request header that does not exist within Open-AudIT. Please specify a valid user.';

        $error_array['ERR-0037'] = new stdClass();
        $error_array['ERR-0037']->code = 'ERR-0037';
        $error_array['ERR-0037']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0037']->severity = 4;
        $error_array['ERR-0037']->title = "Directory does not exist and could not be created";
        $error_array['ERR-0037']->detail = 'A required directory does not exist and could not be created.';

        $error_array['ERR-0038'] = new stdClass();
        $error_array['ERR-0038']->code = 'ERR-0038';
        $error_array['ERR-0038']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0038']->severity = 4;
        $error_array['ERR-0038']->title = "Permissions do not allow writing file.";
        $error_array['ERR-0038']->detail = 'Attempting to write to a file has been denied because of filesystem permissions.';

        $error_array['ERR-0039'] = new stdClass();
        $error_array['ERR-0039']->code = 'ERR-0039';
        $error_array['ERR-0039']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0039']->severity = 4;
        $error_array['ERR-0039']->title = "Permissions do not allow deleting file.";
        $error_array['ERR-0039']->detail = 'Attempting to delete a file has been denied because of filesystem permissions.';

        $error_array['ERR-0040'] = new stdClass();
        $error_array['ERR-0040']->code = 'ERR-0040';
        $error_array['ERR-0040']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0040']->severity = 6;
        $error_array['ERR-0040']->title = "Invalid filetype for upload.";
        $error_array['ERR-0040']->detail = 'Invalid filetype supplied.';

        $error_array['ERR-0041'] = new stdClass();
        $error_array['ERR-0041']->code = 'ERR-0041';
        $error_array['ERR-0041']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0041']->severity = 5;
        $error_array['ERR-0041']->severity_text = 'danger';
        $error_array['ERR-0041']->title = 'Windows Apache service user.';
        $error_array['ERR-0041']->detail = '<strong>ERROR</strong> - Windows is running the Apache service as "Local System". As at v3.3.0 this <b>must</b> be changed to a normal user account (with network access). See the <a href="https://community.opmantek.com/display/OA/Running+Open-AudIT+Apache+Service+under+Windows" target="_blank">Open-AudIT wiki</a> for more details.';

        $error_array['ERR-0042'] = new stdClass();
        $error_array['ERR-0042']->code = 'ERR-0042';
        $error_array['ERR-0042']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0042']->severity = 5;
        $error_array['ERR-0042']->severity_text = 'warning';
        $error_array['ERR-0042']->title = 'Old Redhat / Centos and Samba.';
        $error_array['ERR-0042']->detail = '<strong>WARNING</strong> - Redhat and Centos 6 servers require the Samba4 libraries to be installed. Please see <a href="https://community.opmantek.com/display/OA/Auditing+Windows+machines+from+Linux+using+SMB2" target="_blank">this wiki page</a> for more information.<br />We very much recommend upgrading to Centos/RedHat 8 as support for Centos/RedHat 6 will be ending very soon.';

        $error_array['ERR-0043'] = new stdClass();
        $error_array['ERR-0043']->code = 'ERR-0043';
        $error_array['ERR-0043']->status = 'HTTP/1.1 500 Internal Server Error';
        $error_array['ERR-0043']->severity = 5;
        $error_array['ERR-0043']->severity_text = 'danger';
        $error_array['ERR-0043']->title = 'Nmap not found.';
        if (php_uname('s') === 'Windows NT') {
            $error_array['ERR-0043']->detail = "<strong>ERROR</strong> - Nmap <strong>must</strong> be installed. Get it from <a style='color:#729FCF;' target='_blank' href='http://nmap.org/download.html'>http://nmap.org/download.html</a>.<br />Please see <a target='_blank' href='https://community.opmantek.com/display/OA/Open-AudIT+and+Nmap'>https://community.opmantek.com/display/OA/Open-AudIT+and+Nmap</a> for information about why Open-AudIT requires Nmap and how to install it.";
        } else {
            $error_array['ERR-0043']->detail = "<strong>ERROR</strong> - Nmap <strong>must</strong> be installed. Please install it using your package manager. See <a target='_blank' href='https://community.opmantek.com/display/OA/Open-AudIT+and+Nmap'>https://community.opmantek.com/display/OA/Open-AudIT+and+Nmap</a> for information about why Open-AudIT requires Nmap and how to install it.";
        }

        $error_array['ERR-0044'] = new stdClass();
        $error_array['ERR-0044']->code = 'ERR-0044';
        $error_array['ERR-0044']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0044']->severity = 5;
        $error_array['ERR-0044']->severity_text = 'warning';
        $error_array['ERR-0044']->title = 'Bad Configuration value supplied';
        $error_array['ERR-0044']->detail = 'The configuration value supplied is invalid for this item.';

        $error_array['ERR-0045'] = new stdClass();
        $error_array['ERR-0045']->code = 'ERR-0045';
        $error_array['ERR-0045']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0045']->severity = 5;
        $error_array['ERR-0045']->title = 'Invalid value supplied for attribute';
        $error_array['ERR-0045']->detail = 'The value as passed cannot be used for this attribute.';

        $error_array['ERR-0046'] = new stdClass();
        $error_array['ERR-0046']->code = 'ERR-0046';
        $error_array['ERR-0046']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0046']->severity = 5;
        $error_array['ERR-0046']->title = 'Invalid value supplied for ID';
        $error_array['ERR-0046']->detail = 'The value as passed is not an integer, assuming a name, but not found.';

        $error_array['ERR-0047'] = new stdClass();
        $error_array['ERR-0047']->code = 'ERR-0044';
        $error_array['ERR-0047']->status = 'HTTP/1.1 400 Bad Request';
        $error_array['ERR-0047']->severity = 4;
        $error_array['ERR-0047']->severity_text = 'danger';
        $error_array['ERR-0047']->title = 'Nmap SUID not set.';
        $error_array['ERR-0047']->detail = 'The Nmap SUID bit needs to be set. Please run "sudo chmod u+s `which nmap`" to resolve this issue.';

        foreach ($error_array as $error_each) {
            $temp = explode(' ', $error_each->status);
            $error_each->status_code = intval($temp[1]);
            if ($error_each->severity === 0) {
                $error_each->severity_text = 'emergency';
            }
            if ($error_each->severity === 1) {
                $error_each->severity_text = 'alert';
            }
            if ($error_each->severity === 2) {
                $error_each->severity_text = 'critical';
            }
            if ($error_each->severity === 3) {
                $error_each->severity_text = 'error';
            }
            if ($error_each->severity === 4) {
                $error_each->severity_text = 'warning';
            }
            if ($error_each->severity === 5) {
                $error_each->severity_text = 'notice';
            }
            if ($error_each->severity === 6) {
                $error_each->severity_text = 'informational';
            }
            if ($error_each->severity === 7) {
                $error_each->severity_text = 'debug';
            }
        }

        return $error_array;
    }
}


/* End of file error_helper.php */
/* Location: ./system/application/helpers/error_helper.php */
}

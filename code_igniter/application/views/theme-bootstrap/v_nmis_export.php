<?php
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

/**
* @category  View
* @package   Open-AudIT
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.5
* @link      http://www.open-audit.org
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php include('include_collection_panel_header.php'); ?>
    </div>
    <div class="panel-body">
    <p>
    Please copy and save the following into a file named import_nodes_sample.csv. Save the file to your NMIS server and import it using the command below:<br />
    <pre>/usr/local/nmis8/admin/import_nodes.pl csv=/usr/local/nmis8/admin/import_nodes_sample.csv nodes=/usr/local/nmis8/conf/Nodes.nmis.new</pre><br />
    This will take the CSV file and merge it with the existing /usr/local/nmis8/conf/Nodes.nmis file and create a new nodes file /usr/local/nmis8/conf/Nodes.nmis.new.  Merging the files means that you can use this script and CSV to update existing properties of devices as well as adding new devices, perfect for integration into other systems and to create automated processes.Once you have created /usr/local/nmis8/conf/Nodes.nmis.new you need to replace the existing Nodes.nmis file and it is a good idea to keep a backup, running these two commands together is a good idea.<br />
    <pre>mv /usr/local/nmis8/conf/Nodes.nmis /usr/local/nmis8/conf/Nodes.nmis.old
mv /usr/local/nmis8/conf/Nodes.nmis.new /usr/local/nmis8/conf/Nodes.nmis
/usr/local/nmis8/admin/fixperms.pl</pre><br />
    Once you have added nodes or modified nodes an NMIS Update is required which you can run in the background or run for a single node.<br />
    To run an NMIS update in the background, the command nohup is No Hangup, so you can exit the SSH session if required, this will continue running.<br />
    <pre>nohup nice /usr/local/nmis8/bin/nmis.pl type=update mthread=true maxthreads=5 &</pre>
    There is more information available on the <a gref="https://community.opmantek.com/display/NMIS/Import+Nodes+into+NMIS8+-+bulk+import+and+integration">NMIS wiki</a>.<br />
</p>
    <p>Contents of CSV are below.</p>
        <pre>
"name","host","businessService","group","role","community",uuid","notes","version","privprotocol","privpassword","authprotocol","authpassword","wmiusername","wmipassword"
<?php if (!empty($this->response->data)) {
                foreach ($this->response->data as $item):
                    $line = "\"" . str_replace('"', '\"', $item->attributes->name) . "\"" .
                    ",\""  . str_replace('"', '\"', $item->attributes->host) . "\"" .
                    ",\""  . str_replace('"', '\"', $item->attributes->businessService) . "\"" .
                    ",\""  . str_replace('"', '\"', $item->attributes->group) . "\"" .
                    ",\""  . str_replace('"', '\"', $item->attributes->role) . "\"" .
                    ",\""  . str_replace('"', '\"', $item->attributes->community) . "\"" .
                    ",\""  . str_replace('"', '\"', $item->attributes->uuid) . "\"" .
                    ",\""  . str_replace('"', '\"', $item->attributes->notes) . "\"" .
                    ",\""  . @str_replace('"', '\"', $item->attributes->version) . "\"" .
                    ",\""  . @str_replace('"', '\"', $item->attributes->privprotocol) . "\"" .
                    ",\""  . @str_replace('"', '\"', $item->attributes->privpassword) . "\"" .
                    ",\""  . @str_replace('"', '\"', $item->attributes->authprotocol) . "\"" .
                    ",\""  . @str_replace('"', '\"', $item->attributes->authpassword) . "\"" .
                    ",\""  . @str_replace('"', '\"', $item->attributes->wmiusername) . "\"" .
                    ",\""  . @str_replace('"', '\"', $item->attributes->wmipassword) . "\"";
                    echo $line . "\n";
                endforeach;
            } ?>
        </pre>
    </div>
</div>
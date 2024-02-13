<?php
# Copyright © 2023 FirstWave. All Rights Reserved.
# SPDX-License-Identifier: AGPL-3.0-or-later


$intro = 'When a discovery is run, the relevant discovery scan option is chosen and those settings are used by Nmap to scan the target devices.  The scan options determine which ports nmap scans, how fast they scan and whether or not nmap ping is first used to determine if the IP is live or not.<br>
<br>
Starting with Open-AudIT 2.3.2 we have introduced sets of pre-configured options for running the discovery scan, these pre-configured options allow a range of Nmap scan options. More detail is here: New Discovery Options<br>
<br>
As at 3.3.0 we have introduced a "filtered|open" option to discovery scan options, this option determines if an open but filtered port is considered as an interesting port on the remote device. It has a default of "y". Previously we used the "filtered" column to check for open|filtered. This change aligns the discovery scan options with Nmap return strings.<br>
<br>
As at 4.0.3 we allow the user to over-write individual discovery scan options without having to create a "custom scan".<br>
<br>
<h2>How Does it Work?</h2>
<br>
When a discovery is run, the relevant discovery scan option is chosen and those settings used by Nmap to scan the target devices. If no option set is chosen, the default configuration item (discovery_default_scan_option) is selected and used.<br>
Open-AudIT Community will use the default options as per the configuration for all discoveries.<br>
<br>
Open-AudIT Professional has the ability to choose from a pre-defined list of discovery scan options, per discovery.<br>
<br>
Open-AudIT Enterprise has the ability to choose from a pre-defined list of discovery scan options and also to customise individual options per discovery.<br>
<br>
The default discovery scan option is the UltraFast set.<br>
<br>
<br>
If a device is individually discovered using the "Discover Device" link on the device details page, we first check if this device has been discovered previously (by Discovery) and if so, use the discovery options from that scan. If it has not been previously discovered, we revert to the configuration item discovery_default_scan_option the settings.<br><br>';

$body = '
<h2>Creating a Discovery Scan Options entry</h2>
<br>
<p>Discovery Scan Options are just another item collection. Enterprise users can create, read, update and delete entries as required. Professional users can read all entries, but not create new entries, update existing entries or delete entries. Community users have no GUI that allows access to this collection.</p>
<br>
<p>The attributes for discovery scan options are as below.</p>
<br>
<table class="table">
    <tbody>
        <tr>
            <th>Attribute</th>
            <th>Description</th>
        </tr>
        <tr>
            <td>ping</td>
            <td>
                <p>Must Respond To Ping. If set, Nmap will fist attempt to send and listen for an ICMP response. If the device does not respond, no further scanning will occur.</p>
                <p>Previously a device did not have to respond to a ping for Open-AudIT to continue scanning.</p>
            </td>
        </tr>
        <tr>
            <td>service_version</td>
            <td>
                <p>Use Service Version Detection. When a detected port is detected as open, if set to "y", Nmap will query the target device in an attempt to determine the version of the service running on this port.</p>
                <p>This can be useful when identifying unclassified devices. This was not previously used.</p>
            </td>
        </tr>
        <tr>
            <td>open|filtered</td>
            <td>An open|filtered port is considered open (and will trigger device detection).<br/>
                <p>Previously, Open-AudIT considered an Nmap response of &quot;open|filtered&quot; as a device responding on this port.</p>
                <p>This has caused some customers issues where firewalls respond on behalf of a non-existing device, and hence cause false positive device detection. We now have this attribute available to set per scan.</p>
            </td>
        </tr>
        <tr>
            <td>filtered</td>
            <td>
                <p>A filtered port is considered open (and will trigger device detection).</p>
            </td>
        </tr>
        <tr>
            <td>timing</td>
            <td>The standard Nmap timing options. Previously set at T4 (aggressive).</td>
        </tr>
        <tr>
            <td>nmap_tcp_ports</td>
            <td>Top Nmap TCP Ports. The top 10, 100, 1000 ports to scan as per Nmaps &quot;top ports&quot; options. Previously we scanned the Top 1000 ports (the Nmap standard).</td>
        </tr>
        <tr>
            <td>nmap_udp_ports</td>
            <td>Top Nmap UDP Ports. The top 10, 100, 1000 ports to scan as per Nmaps &quot;top ports&quot; options. Previously we scanned UDP 161 (snmp) only.</td>
        </tr>
        <tr>
            <td>tcp_ports</td>
            <td>Custom TCP Ports. Any specific ports we would liuke scanned in addition to the Top TCP Ports. Comma seperated, no spaces.</td>
        </tr>
        <tr>
            <td>udp_ports</td>
            <td>Custom UDP Ports. Any specific ports we would liuke scanned in addition to the Top UDP Ports. Comma seperated, no spaces.</td>
        </tr>
        <tr>
            <td><br/></td>
            <td><strong><em>The below fields can be overwritten by an individual discovery, while still &quot;using&quot; a discovery_scan_options item for these if they\'re not set in the discovery (changed as at 4.0.3, see above).<br/></em></strong></td>
        </tr>
        <tr>
            <td>timeout</td>
            <td>Timeout per Target. Wait for X seconds for a target response.</td>
        </tr>
        <tr>
            <td>exclude_tcp</td>
            <td>Exclude any ports listed from being scanned. Comma seperated, no spaces.</td>
        </tr>
        <tr>
            <td>exclude_udp</td>
            <td>Exclude any ports listed from being scanned. Comma seperated, no spaces.</td>
        </tr>
        <tr>
            <td>exclude_ip</td>
            <td>Exclude IP Addresses (individual IP - 192.168.1.20, ranges - 192.168.1.30-40 or subnets - 192.168.1.100/30) listed from being scanned. Comma seperated, no spaces.</td>
        </tr>
        <tr>
            <td>ssh_ports</td>
            <td>Scan for this port(s) and if detected open, use this port for SSH communication. This is added to the list of Custom TCP POrts above, so there is no need to include it in that listr as well. Comma seperated, no spaces.</td>
        </tr>
    </tbody>
</table><br><br>
';

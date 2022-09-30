<?php
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

/**
* @category  View
* @package   Open-AudIT
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.4
* @link      http://www.open-audit.org
 */
include 'v_lang.php';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <link rel="shortcut icon" href="<?php echo $this->config->config['oa_web_folder']; ?>favicon.png" type="image/x-icon" />
    <title>Open-AudIT</title>
    <link rel="stylesheet" href="<?php echo $this->config->config['oa_web_folder']; ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $this->config->config['oa_web_folder']; ?>css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="<?php echo $this->config->config['oa_web_folder']; ?>css/bootstrap-table.min.css">
    <link rel="stylesheet" href="<?php echo $this->config->config['oa_web_folder']; ?>css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $this->config->config['oa_web_folder']; ?>css/bootstrap-dropdown.css">
<?php if (!empty($this->response->meta->action) and $this->response->meta->action == 'read_partition_graph') { ?>
    <link rel="stylesheet" href="<?php echo $this->config->config['oa_web_folder']; ?>css/chartist.min.css">
<?php } ?>


<?php
if (!empty($this->response->meta->collection) and $this->response->meta->collection == 'locations' and ($this->response->meta->action == 'read' or $this->response->meta->action == 'create_form')) { ?>
        <script src="//maps.google.com/maps/api/js?key=<?php echo $this->config->config['maps_api_key']; ?>"></script>
    <?php
} ?>
    <script src="<?php echo $this->config->config['oa_web_folder']; ?>js/jquery.min.js"></script>
    <script src="<?php echo $this->config->config['oa_web_folder']; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo $this->config->config['oa_web_folder']; ?>js/bootstrap-table.min.js"></script>
    <script src="<?php echo $this->config->config['oa_web_folder']; ?>js/jquery.tablesorter.min.js"></script>
    <script src="<?php echo $this->config->config['oa_web_folder']; ?>js/bootstrap-filestyle.min.js"></script>
    <!-- Open-AudIT specific items -->
    <script>
<?php
if (isset($meta->id) and !is_null($meta->id)) {
    echo "        var id = '" . $meta->id . "';\n";
}
if (!empty($meta->collection)) {
    echo "        var collection = '" . $meta->collection . "';\n";
}
if (!empty($meta->baseurl)) {
    echo "        var baseurl = '" . $meta->baseurl . "';\n";
} else {
    echo "        var baseurl = '';\n";
}
?>
        var web_folder = '<?php echo $this->config->config['oa_web_folder']; ?>';
        var device_auto_delete = '<?php echo $this->config->config['device_auto_delete']; ?>';

        function logout() {
            $.get( "/omk/open-audit/logout");
        }

    </script>
    <script src="<?php echo $this->config->config['oa_web_folder']; ?>js/open-audit.js"></script>
    <link rel="stylesheet" href="<?php echo $this->config->config['oa_web_folder']; ?>css/open-audit.css">
</head>
<body>

<div class="container-fluid">
<?php
include "include_header.php";
if (!empty($this->response->errors) and is_array($this->response->errors)) {
    foreach ($this->response->errors as $error) {
        echo '<div class="alert alert-danger" role="alert"><strong>' . $error->title . "</strong><br />";
        if ( ! empty($error->code)) {
            echo html_entity_decode($error->detail) . "</div>\n";
        } else {
            echo $error->detail . "</div>\n";
        }
    }
}
$temp = @$this->session->flashdata('error');
if (!empty($temp)) {
    echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $this->session->flashdata('error') . "</div>\n";
}
$temp = @$this->session->flashdata('danger');
if (!empty($temp)) {
    echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $this->session->flashdata('danger') . "</div>\n";
}
$temp = @$this->session->flashdata('success');
if (!empty($temp)) {
    echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $this->session->flashdata('success') . "</div>\n";
}
$temp = @$this->session->flashdata('warning');
if (!empty($temp)) {
    echo '<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $this->session->flashdata('warning') . "</div>\n";
}
$temp = @$this->session->flashdata('info');
if (!empty($temp)) {
    echo '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . $this->session->flashdata('info') . "</div>\n";
}

$temp1 = @$this->session->flashdata('error');
$temp2 = @$this->response->errors[0]->detail;
if (!empty($temp1) and !empty($temp2)) {
    echo '<div class="alert alert-danger" role="alert">' . $this->response->errors[0]->detail . "</div>\n";
}
$temp = @$this->response->meta->flash;
if (!empty($temp)) {
    if (!empty($this->response->meta->flash->html) and $this->response->meta->flash->html === 1) {
        echo "<div class=\"alert alert-{$this->response->meta->flash->status}\" role=\"alert\">" . html_entity_decode($this->response->meta->flash->message) . "</div>\n";
    } else {
        echo "<div class=\"alert alert-{$this->response->meta->flash->status}\" role=\"alert\">{$this->response->meta->flash->message}</div>\n";
    }
}

include($this->response->include.'.php');

unset($this->user->password);
unset($this->response->data);
unset($this->response->meta->sql);
?>

<div id="json_response" style="display:none;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <span class="text-left"><?php echo __('Debug'); ?></span>
            </h3>
        </div>
        <div class="panel-body">
            <?php
            if (!empty($this->response->errors)) {
                ?>
                <h3><?php echo __('Error'); ?></h3>
                <pre><?php print_r(json_format(json_encode($this->response->errors))); ?></pre>
            <?php
            } ?>
            <h3><?php echo __('User'); ?></h3>
            <pre><?php print_r(json_format(json_encode($this->user))); ?></pre>
            <h3>Meta</h3>
            <pre><?php print_r(json_format(json_encode($this->response->meta))); ?></pre>
            <?php if (!empty($this->response->logs)) { ?>
            <h3>Logs</h3>
            <pre><?php print_r($this->response->logs); ?></pre>
        <?php } ?>
            <h3><?php echo __('SQL Queries'); ?></h3>
            <pre><?php
                $CI =& get_instance();
                $times = $CI->db->query_times;
                 foreach ($CI->db->queries as $key => $query) {
                     echo __('Query').": " . htmlentities(str_replace("\n", " ", $query)) . "\n";
                     echo __('Time').": " . $times[$key] . "\n\n";
                 }
            ?>
            </pre>
            <?php
            if ($this->m_users->get_user_permission('', 'configuration', 'u')) {
                ?>
                <h3><?php echo __('User Session Data'); ?></h3>
                <pre><?php print_r($this->session->all_userdata()); ?></pre>
            <?php
            }
            ?>
        </div>
    </div>
</div>
</div>
</body>
</html>
<?php
function json_format($json)
{
    $tab = '  ';
    $new_json = '';
    $indent_level = 0;
    $in_string = false;

    $json_obj = json_decode($json);

    if ($json_obj === false) {
        return false;
    }

    $json = json_encode($json_obj);
    $len = strlen($json);

    for ($c = 0; $c < $len; $c++) {
        $char = $json[$c];
        switch($char)
        {
            case '{':
            case '[':
                if (!$in_string) {
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                    $indent_level++;
                } else {
                    $new_json .= $char;
                }
                break;
            case "<":
                $new_json .= "&lt;";
                break;
            case '}':
            case ']':
                if (!$in_string) {
                    $indent_level--;
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                } else {
                    $new_json .= $char;
                }
                break;
            case ',':
                if (!$in_string) {
                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
                } else {
                    $new_json .= $char;
                }
                break;
            case ':':
                if (!$in_string) {
                    $new_json .= ": ";
                } else {
                    $new_json .= $char;
                }
                break;
            case '"':
                if ($c > 0 && $json[$c-1] != '\\') {
                    $in_string = !$in_string;
                }
                break;
                // fallthrough
            default:
                $new_json .= $char;
                break;
        }
    }

    return htmlentities($new_json);
}

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
* @version   GIT: Open-AudIT_4.4.2
* @link      http://www.open-audit.org
 */
$item = $this->response->data[0];
?>
<form class="form-horizontal" id="form_update" method="post" action="<?php echo $this->response->links->self; ?>">
    <div class="panel panel-default">
        <?php include('include_read_panel_header.php'); ?>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="id" class="col-sm-3 control-label"><?php echo __('ID'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="id" name="id" value="<?php echo $item->attributes->id; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label"><?php echo __('Name'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $item->attributes->name ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label"><?php echo __('Description'); ?></label>
                        <div class="col-sm-8 input-group">
                            <textarea class="form-control" rows="5" id="sql" name="sql" disabled><?php echo $item->attributes->description; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sql" class="col-sm-3 control-label"><?php echo __('AD Group'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="description" name="description" value="<?php echo $item->attributes->ad_group; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edited_by" class="col-sm-3 control-label"><?php echo __('Edited By'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="edited_by" name="edited_by" value="<?php echo $item->attributes->edited_by; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edited_date" class="col-sm-3 control-label"><?php echo __('Edited Date'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="edited_date" name="edited_date" value="<?php echo $item->attributes->edited_date; ?>" disabled>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="col-md-8 col-md-offset-2">
                        <?php if ( ! empty($this->response->dictionary->about)) {
                            echo "<h4 class=\"text-center\">About</h4><br />";
                            echo html_entity_decode($this->response->dictionary->about);
                        } ?>
                        <?php if ( ! empty($this->response->dictionary->notes)) {
                            echo "<h4 class=\"text-center\">Notes</h4><br />";
                            echo html_entity_decode($this->response->dictionary->notes);
                        } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<?php
$endpoints = array('agents','applications','attributes','buildings','charts','clouds','collectors','configuration','connections','credentials','dashboards','database','devices','discoveries','discovery_scan_options','errors','fields','files','floors','graphs','groups','invoices','invoice_items','ldap_servers','licenses','locations','logs','networks','nmis','orgs','queries','racks','rack_devices','reports','roles','rooms','rows','scripts','search','sessions','summaries','tasks','users','widgets');

$permissions = array('c', 'r', 'u', 'd');

$item_permissions = new stdClass();
if (is_string($item->{'attributes'}->{'permissions'})) {
    $item_permissions = json_decode($item->{'attributes'}->{'permissions'});
} else if (!empty($item->{'attributes'}->{'permissions'})) {
    $item_permissions = $item->{'attributes'}->{'permissions'};
}

?>

<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <div class="panel-title">
            <?php echo __('Role Permissions'); ?>
        </div>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Endpoint</th>
                    <th class="text-center"><?php echo __('Create'); ?></th>
                    <th class="text-center"><?php echo __('Read'); ?></th>
                    <th class="text-center"><?php echo __('Update'); ?></th>
                    <th class="text-center"><?php echo __('Delete'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($endpoints as $endpoint) { ?>
                <tr><td><strong><?php echo $endpoint; ?></strong></td>
                <?php
                foreach ($permissions as $permission) {
                    $checked = '';
                    if (!empty($item_permissions->{$endpoint}) and strpos($item_permissions->{$endpoint}, $permission) !== false) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                    ?>
                    <td style="text-align:center;"><input data-permission="<?php echo $permission; ?>" name="permission.<?php echo $endpoint ?>" type="checkbox" value="<?php echo $permission; ?>" <?php echo $checked ?> disabled></td>
                <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script type='text/javascript'>
var roles = JSON.parse('<?php echo json_encode($this->user->roles); ?>');
</script>
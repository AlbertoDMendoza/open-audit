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
                            <?php if (!empty($edit)) { ?>
                            <span class="input-group-btn">
                                <button id="edit_name" data-action="edit" class="btn btn-default edit_button" type="button" data-attribute="name"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
                            </span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="parent_id" class="col-sm-3 control-label"><?php echo __('Parent Org'); ?></label>
                        <div class="col-sm-8 input-group">
                            <select class="data_type form-control" id="parent_id" name="parent_id" disabled>
                                <?php foreach ($this->response->included as $org) {
                                    if ($org->type == 'orgs' and $org->attributes->id != $item->attributes->id) { ?>
                                    <option value="<?php echo $org->attributes->id; ?>"<?php if ($org->attributes->id == $item->attributes->parent_id) { echo " selected"; } ?>><?php echo $org->attributes->name; ?></option>
                                <?php } } ?>
                            </select>
                            <?php if (!empty($edit) and $item->attributes->id != 1) { ?>
                            <span class="input-group-btn">
                                <button id="edit_parent_id" data-action="edit" class="btn btn-default edit_button" type="button" data-attribute="parent_id"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
                            </span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type" class="col-sm-3 control-label"><?php echo __('Type'); ?></label>
                        <div class="col-sm-8 input-group">
                            <select class="form-control" name="type" id="type" disabled>
                                <?php foreach ($this->response->included as $type) {
                                if ($type->type === 'attributes') {
                                    if ($type->attributes->resource === 'orgs' && $type->attributes->type === 'type') {
                                        if ($type->attributes->value === $item->attributes->type) { $selected = ' selected'; } else { $selected = ''; }
                                            echo '<option value="' . $type->attributes->value . '"' . $selected . '>' . $type->attributes->name . "</option>\n";
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <?php if (!empty($edit)) { ?>
                            <span class="input-group-btn">
                                <button id="edit_type" data-action="edit" class="btn btn-default edit_button" type="button" data-attribute="type"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
                            </span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ad_group" class="col-sm-3 control-label"><?php echo __('AD Group'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="ad_group" name="ad_group" value="<?php echo $item->attributes->ad_group; ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label"><?php echo __('Description'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="description" name="description" value="<?php echo $item->attributes->description; ?>" disabled>
                            <?php if (!empty($edit)) { ?>
                            <span class="input-group-btn">
                                <button id="edit_description" data-action="edit" class="btn btn-default edit_button" type="button" data-attribute="description"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></button>
                            </span>
                            <?php } ?>
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

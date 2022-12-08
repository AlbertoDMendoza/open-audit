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
* @version   GIT: Open-AudIT_4.4.0
* @link      http://www.open-audit.org
 */
foreach ($this->response->included as $item) {
    if ($item->type != 'script_option') {
        $data[$item->type][] = $item->attributes;
    } else {
        $data['script_option'][$item->id] = $item->attributes;
    }
}
?>
<form class="form-horizontal" id="form_update" method="post" action="<?php echo $this->response->links->self; ?>">
    <input type="hidden" value="<?php echo $this->response->meta->access_token; ?>" id="data[access_token]" name="data[access_token]" />
    <div class="panel panel-default">
        <?php include('include_read_panel_header.php'); ?>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="data[attributes][name]" class="col-sm-3 control-label"><?php echo __('Name'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="data[attributes][name]" name="data[attributes][name]" value="" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data[attributes][org_id]" class="col-sm-3 control-label"><?php echo __('Organisation'); ?></label>
                        <div class="col-sm-8 input-group">
                            <select class="form-control" id="data[attributes][org_id]" name="data[attributes][org_id]" required>
                            <?php
                            foreach ($this->response->included as $item) {
                                if ($item->type == 'orgs') { ?>     <option value="<?php echo $item->id; ?>"><?php echo str_replace("'", "", $item->attributes->name); ?></option>
                            <?php
                                }
                            } ?></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data[attributes][description]" class="col-sm-3 control-label"><?php echo __('Description'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="text" class="form-control" id="data[attributes][description]" name="data[attributes][description]" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data[attributes][based_on]" class="col-sm-3 control-label"><?php echo __('Based On'); ?></label>
                        <div class="col-sm-8 input-group">
                            <select id="data[attributes][based_on]" name="data[attributes][based_on]" onChange="based_on();" class="form-control" required>
                                <option value='' label=' '></option>
                                <option value='audit_aix.sh'>Audit AIX</option>
                                <option value='audit_esx.sh'>Audit ESX</option>
                                <option value='audit_linux.sh'>Audit Linux</option>
                                <option value='audit_osx.sh'>Audit OSX</option>
                                <option value='audit_windows.vbs'>Audit Windows</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="submit" class="col-sm-3 control-label"></label>
                        <div class="col-sm-8 input-group">
                            <input type="hidden" value="scripts" id="data[type]" name="data[type]" />
                            <button id="submit" name="submit" type="submit" class="btn btn-default"><?php echo __('Submit'); ?></button>
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

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <span class="text-left"><?php echo __('Options'); ?></span>
                            <span class="pull-right"></span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div id="options"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <span class="text-left"><?php echo __('Files'); ?></span>
                            <span class="pull-right"></span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div id="files">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php echo __('Use'); ?></th>
                                    <th><?php echo __('Name'); ?></th>
                                    <th><?php echo __('Path'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($data['files'])) { ?>
                            <?php foreach ($data['files'] as $file): ?>
                            <?php # TODO - enable per script file retrieval ?>
                            <?php # TODO - Maybe only display files per based_on ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" value="<?php echo $file->path; ?>" id="data[attributes][options][files][<?php echo $file->id; ?>]" title="data[attributes][options][files][<?php echo $file->id; ?>]" name="data[attributes][options][files][<?php echo $file->id; ?>]" checked></td>
                                    <td><?php echo $file->name; ?></td>
                                    <td><?php echo $file->description; ?></td>
                                    <td><?php echo $file->path; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<?php
if (!empty($data['script_option'])) {
    foreach ($data['script_option'] as $key => $value) {
        $script_options[$key] = generate_options($value, $data['option'], '', $data['orgs']);
    }
}
?>

<script>
function based_on(){
    switch(document.getElementById("data[attributes][based_on]").value)
    {
        <?php foreach ($script_options as $key => $value) { ?>
        case "<?php echo $key; ?>":
            based_on_text = '<?php echo $value; ?>';
        break;
        <?php } ?>
    }
    document.getElementById("options").innerHTML = based_on_text;
}
</script>


<?php
function generate_options($option_list, $options, $files, $orgs) {
    $return = '';
    foreach ($options as $option) {
        foreach ($option_list as $list_item) {
            if ($list_item == $option->name) {
                $return .= '        <div class="form-group">\
                <label for="data[edited_date]" class="col-md-3 control-label">' . $option->name . '</label>\
                <div class="col-md-9">\
                <div class="col-md-12 input-group">';

                switch ($option->type) {
                    case 'text';
                    case 'number';
                    case 'url';
                    case 'date':
                        if ($option->name != 'org_id') {
                            $return .= '<input type="' . $option->type . '" class="form-control" id="data[attributes][options][' . $option->name . ']" name="data[attributes][options][' . $option->name . ']" value="' . $option->default . '" aria-describedby="option_' . $option->name . '"><span id="option_' . $option->name . '" class="help-block">' . $option->type . '</span>';

                        } else {
                            $return .= '<select name="data[attributes][options][org_id]" id="data[attributes][options][org_id]" class="form-control" aria-describedby="option_org_id">';
                            $return .= '<option value="" label=" "></option>';
                            foreach ($orgs as $org) {
                                $return .= '<option value="' . $org->id . '">' . $org->name . '</option>';
                            }
                            $return .= '</select><span id="option_org_id" class="help-block">' . $option->type . '</span>';
                        }
                        break;

                    case 'select':
                        $return .= '<select id="data[attributes][options][' . $option->name . ']" name="data[attributes][options][' . $option->name . ']" class="form-control" aria-describedby="option_' . $option->name . '">';
                        foreach (explode(',', $option->values) as $value) {
                            if ($value == $option->default) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            $return .= '<option value="' . $value . '" ' . $selected . '>' . $value . '</option>';
                        }
                        $return .= '</select><span id="option_' . $option->name . '" class="help-block">' . $option->type . '</span>';
                        break;
                }
                $return .= '</div></div></div>\n';
            }
        }
    }
    return($return);
}
?>
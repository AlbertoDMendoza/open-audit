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
#  www.opmantek.com or email contact@opmantek.com
#
# *****************************************************************************

/**
* @category  View
* @package   Open-AudIT
* @author    Mark Unwin <marku@opmantek.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.3
* @link      http://www.open-audit.org
 */
?>
<form class="form-horizontal" id="form_update" method="post" action="<?php echo $this->response->links->self; ?>" accept-charset="utf-8" enctype="multipart/form-data">
    <input type="hidden" value="<?php echo $this->response->meta->access_token; ?>" id="data[access_token]" name="data[access_token]" />
    <input type="hidden" value="nmis" id="data[type]" name="data[type]" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <span class="text-left"><?php echo __('NMIS Import'); ?></span>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data[attributes][source]" class="col-sm-3 control-label"><?php echo __('Source'); ?></label>
                        <div class="col-sm-8 input-group">
                            <select class="form-control" id="data[attributes][source]" name="data[attributes][source]">
                                <?php $show = false; ?>
                                <?php if (file_exists('/usr/local/nmis9/admin/node_admin.pl')) { $show = true; ?><option value='nmis9'>Local NMIS 9</option><?php } ?>
                                <?php if (file_exists('/usr/local/nmis8/conf/Nodes.nmis')) { $show = true; ?><option value='nmis8'>Local NMIS 8</option><?php } ?>
                                <option value='nmis8_nodes'>Uploaded NMIS 8 Nodes file</option>
                            </select>
                        </div>
                    </div>

                    <div id="file_upload_div" style="display:none;">
                        <div class="form-group">
                            <label for="upload_file" class="col-sm-3 control-label"><?php echo __('File Upload'); ?></label>
                            <div class="col-sm-8">
                                <input type="file" id="upload_file" name="upload_file">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data[attributes][location_id]" class="col-sm-3 control-label"><?php echo __('Assign to Location'); ?></label>
                        <div class="col-sm-8 input-group">
                            <select class="form-control" id="data[attributes][location_id]" name="data[attributes][location_id]">
                            <?php
                            foreach ($this->response->included as $item) {
                                if ($item->type == 'locations') { ?>        <option value="<?php echo $item->id; ?>"><?php echo $item->attributes->name ?></option>
                            <?php
                                }
                            } ?></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data[attributes][org_id]" class="col-sm-3 control-label"><?php echo __('Assign to Organisation'); ?></label>
                        <div class="col-sm-8 input-group">
                            <select class="form-control" id="data[attributes][org_id]" name="data[attributes][org_id]">
                            <?php
                            foreach ($this->response->included as $item) {
                                if ($item->type == 'orgs') { ?>     <option value="<?php echo $item->id; ?>"><?php echo $item->attributes->name ?></option>
                            <?php
                                }
                            } ?></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data[attributes][run_discovery]" class="col-sm-3 control-label"><?php echo __('Run Discovery on devices'); ?></label>
                        <div class="col-sm-8 input-group">
                            <input type="checkbox" id="data[attributes][run_discovery]" name="data[attributes][run_discovery]">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="statusmsg" class="col-sm-3 control-label">&nbsp;</label>
                        <div class="col-sm-8 input-group">
                            <span id="statusmsg"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="submit" class="col-sm-3 control-label"></label>
                        <div class="col-sm-8 input-group">
                                <button id="submit" name="submit" type="submit" class="btn btn-default" onClick="document.getElementById('statusmsg').innerHTML = '<br />Please wait while we import the devices.<br /><br /><i class=\'fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom\'></i>';"><?php echo __('Submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    <?php if (! $show) { ?>$("#file_upload_div").css('display', 'block'); <?php } ?>

    $('#data\\[attributes\\]\\[source\\]').change(function() {
        var $type = $(this).val();
        if ($type == "nmis9_nodes") {
            $("#file_upload_div").css('display', 'block');
        } else if ($type == "nmis8_nodes") {
            $("#file_upload_div").css('display', 'block');
        } else {
            $("#file_upload_div").css('display', 'none');
        }
    });
});
</script>
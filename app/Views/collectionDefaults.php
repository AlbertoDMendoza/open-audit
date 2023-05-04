<?php
# Copyright © 2023 FirstWave. All Rights Reserved.
# SPDX-License-Identifier: AGPL-3.0-or-later
#include 'shared/collection_functions.php';
include 'shared/read_functions.php';
?>
        <main class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <?= read_card_header($meta->collection, '', $meta->icon, $user, $data[0]->id) ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td><?= __('Name') ?></td>
                                    <td><?= __('Type') ?></td>
                                    <td><?= __('Default') ?></td>
                                    <td><?= __('Max Length') ?></td>
                                    <td><?= __('Primary Key') ?></td>
                                    <td><?= __('Valid Values') ?></td>
                                    <td><?= __('Description') ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data[0]->attributes->columns as $column) { ?>
                                <tr>
                                    <td><?= $column->name ?></td>
                                    <td><?= $column->type ?></td>
                                    <td><?= $column->default ?></td>
                                    <td><?= $column->max_length ?></td>
                                    <td><?= $column->primary_key ?></td>
                                    <td><?= @$column->values ?></td>
                                    <td><?= $dictionary->columns->{$column->name} ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br />
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-3 clearfix">
                            <div class="btn-group btn-group-sm" role="group" aria-label="">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-dark oa-card-button"><span class="fa fa-database" style="margin-right:10px;" aria-hidden="true"></span><?= __('SQL Creation Statements') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php foreach ($defaults as $default) { ?>
                    <pre style="word-wrap: break-word; white-space: pre-wrap;"><?= $default ?></pre>
                    <br />
                    <?php } ?>
                </div>
            </div>
        </main>
<?php if (strpos($user->permissions[$meta->collection], 'u') !== false) { ?>
<script>
$(document).ready(function() {
    <?php if (!empty($user->toolbar_style) and $user->toolbar_style === 'icontext') { ?>
    $("#oa_panel_buttons").append('<form style="padding-left:4px;" id="<?= $meta->collection ?>ResetForm" method="post" action="<?= url_to($meta->collection . 'Reset') ?>"><button id="<?= $meta->collection ?>Reset" class="btn btn-light mb-2" type="submit" title="<?= __('Reset') ?>"><span class="fa fa-rotate-right"></span>&nbsp;<?= __('Reset') ?></button></form>');
    <?php } else if (!empty($user->toolbar_style) and $user->toolbar_style === 'icon') { ?>
    $("#oa_panel_buttons").append('<form style="padding-left:4px;" id="<?= $meta->collection ?>ResetForm" method="post" action="<?= url_to($meta->collection . 'Reset') ?>"><button id="<?= $meta->collection ?>Reset" class="btn btn-light mb-2" type="submit" title="<?= __('Reset') ?>"><span class="fa fa-rotate-right"></span></button></form>');
    <?php } else { ?>
    $("#oa_panel_buttons").append('<form style="padding-left:4px;" id="<?= $meta->collection ?>ResetForm" method="post" action="<?= url_to($meta->collection . 'Reset') ?>"><button id="<?= $meta->collection ?>Reset" class="btn btn-light mb-2" type="submit" title="<?= __('Reset') ?>"><?= __('Reset') ?></button></form>');
    <?php } ?>

    document.getElementById('<?= $meta->collection ?>ResetForm').addEventListener('submit', function(e){
        if (confirm("Are you sure?\n\nThis will delete the current rows in the <?= $meta->collection ?> table and insert the original rows.") == true) {
            return;
        }
        e.preventDefault();
    });
});
</script>
<?php } ?>

<?php
# Copyright © 2022 Mark Unwin <mark.unwin@gmail.com>
# SPDX-License-Identifier: AGPL-3.0-or-later
include 'shared/read_functions.php';
?>
        <main class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <?= read_card_header($meta->collection, $meta->id, $meta->icon, $user, $data[0]->id) ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <?= read_field('name', $data[0]->id, '', false) ?>
                            <?= read_field('Row Count', $data[0]->attributes->count, '', false) ?>
                            <br /><br />
                            <div class="col-8 offset-2">
                                <table class="table">
                                    <tr>
                                        <td class="text-center"><?= __('Export data to') ?></td>
                                        <td class="text-center"><?= __('Export data to') ?></td>
                                        <td class="text-center"><?= __('Export data to') ?></td>
                                        <td class="text-center"><?= __('Export data to') ?></td>
                                        <?php if (strpos($user->permissions[$meta->id], 'd') !== false) { ?>
                                        <td class="text-center"><?= __('Reset All Data') ?></td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><a id="export_csv" class="btn btn-sm btn-primary" href="<?= url_to('databaseRead', $data[0]->id) ?>?format=csv"><?= __('CSV') ?></a></td>
                                        <td class="text-center"><a id="export_sql" class="btn btn-sm btn-primary" href="<?= url_to('databaseRead', $data[0]->id) ?>?format=sql"><?= __('SQL') ?></a></td>
                                        <td class="text-center"><a id="export_json" class="btn btn-sm btn-primary" href="<?= url_to('databaseRead', $data[0]->id) ?>?format=json"><?= __('JSON') ?></a></td>
                                        <td class="text-center"><a id="export_xml" class="btn btn-sm btn-primary" href="<?= url_to('databaseRead', $data[0]->id) ?>?format=xml"><?= __('XML') ?></a></td>
                                        <?php if (strpos($user->permissions[$meta->id], 'd') !== false) { ?>
                                        <td class="text-center"><form id="<?= $data[0]->id ?>ResetForm" method="post" action="<?= url_to($data[0]->id.'Reset') ?>"><button id="<?= $data[0]->id ?>Reset" class="btn btn-sm btn-danger" type="submit"><?= __('Reset') ?></button></form></td>
                                        <?php } ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-6">
                            <br />
                            <div class="offset-2 col-8">
                                <?php if (!empty($dictionary->about)) { ?>
                                    <h4 class="text-center"><?= __('About') ?></h4><br />
                                    <?= $dictionary->about ?>
                                <?php } ?>
                                <?php if (!empty($dictionary->notes)) { ?>
                                    <h4 class="text-center"><?= __('Notes') ?></h4><br />
                                    <?= $dictionary->notes ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div class="card">
                <div class="card-header">
                    <div class="row" style="height:46px;">
                        <div class="col-12 clearfix">
                            <h6 style="padding-top:10px;"><?= __('Columns') ?></h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
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
                                        <td><?= @$dictionary->columns->{$column->name} ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
<script>
      document.getElementById('<?= $data[0]->id ?>ResetForm').addEventListener('submit', function(e){
        if (confirm("Are you sure?\n\nThis will delete the current rows in the <?= $data[0]->id ?> table and insert the original rows.") == true) {
            return;
        }
        e.preventDefault();
   });
</script>
<?php
# Copyright © 2023 FirstWave. All Rights Reserved.
# SPDX-License-Identifier: AGPL-3.0-or-later
include 'shared/read_functions.php';
if (!$update or $resource->editable !== 'y') {
    $update = false;
}
?>
        <main class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <?= read_card_header($meta->collection, $meta->id, $meta->icon, $user, $data[0]->attributes->name) ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <?= read_field('name', $resource->name, $dictionary->columns->name, false) ?>
                            <?= read_field('description', $resource->description, $dictionary->columns->description, false) ?>
                            <?= read_field('editable', $resource->editable, $dictionary->columns->editable, false) ?>
                            <?= read_field('type', $resource->type, $dictionary->columns->type, false) ?>
                            <?= read_field('value', $resource->value, $dictionary->columns->value, $update) ?>
                            <?= read_field('edited_by', $resource->edited_by, $dictionary->columns->edited_by, false) ?>
                            <?= read_field('edited_date', $resource->edited_date, $dictionary->columns->edited_date, false) ?>
                        </div>
                        <div class="col-6">
                            <br>
                            <div class="offset-2 col-8">
                                <?php if (!empty($dictionary->about)) { ?>
                                    <h4 class="text-center"><?= __('About') ?></h4><br>
                                    <?= $dictionary->about ?>
                                <?php } ?>
                                <?php if (!empty($dictionary->notes)) { ?>
                                    <h4 class="text-center"><?= __('Notes') ?></h4><br>
                                    <?= $dictionary->notes ?>
                                <?php } ?>
                                <?php if (!empty($dictionary->columns)) { ?>
                                    <?php $fields = array('name', 'description', 'editable', 'type', 'value', 'edited_by', 'edited_date') ?>
                                <h4 class="text-center"><?= __('Fields') ?></h4><br>
                                    <?php foreach ($fields as $key) { ?>
                                    <code><?= $key ?>: </code><?= $dictionary->columns->{$key} ?><br><br>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($resource->name === 'log_level') { ?>
                                <p>
                                    You can enable error logging by setting a threshold over zero. The threshold determines what gets logged. Any values below or equal to the threshold will be logged. Threshold options are:<br>
                                    <ol start="0">
                                        <li>Disables logging - Error logging <b>turned off</b>.</li>
                                        <li>Emergency Messages - System is unusable.</li>
                                        <li>Alert Messages - Action Must Be Taken Immediately.</li>
                                        <li>Critical Messages - Application component unavailable, unexpected exception.</li>
                                        <li>Runtime Errors - Don't need immediate action, but should be monitored.</li>
                                        <li>Warnings - Exceptional occurrences that are not errors, <b>the default</b>.</li>
                                        <li>Notices - Normal but significant events.</li>
                                        <li>Info - Interesting events, like user logging in, etc.</li>
                                        <li>Debug - Detailed debug information.</li>
                                        <li>All Messages.</li>
                                    </ol>
                                </p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

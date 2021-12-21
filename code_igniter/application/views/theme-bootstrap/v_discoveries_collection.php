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
* @version   GIT: Open-AudIT_4.3.1
* @link      http://www.open-audit.org
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php include('include_collection_panel_header.php'); ?>
    </div>
    <div class="panel-body">
        <?php include('include_collection_panel_body_links.php'); ?>
        <?php if (!empty($this->response->data)) { ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <?php if ($this->m_users->get_user_permission('', 'discoveries', 'u')) { ?>
                    <th class="text-center"><?php echo __('Execute')?></th>
                    <?php } ?>
                    <th class="text-center"><?php echo __('Details')?></th>
                    <th><?php echo __('Name')?></th>
                    <th><?php echo __('Organisation')?></th>
                    <th><?php echo __('Type')?></th>
                    <th><?php echo __('Description')?></th>
                    <th><?php echo __('Last Run')?></th>
                    <th class="text-center"><?php echo __('Status')?></th>
                    <th class="text-center"><?php echo __('Responding IPs')?></th>
                    <?php if ($this->m_users->get_user_permission('', 'discoveries', 'd')) { ?>
                    <th class="text-center"><?php echo __('Delete')?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->response->data as $item): ?>
                <tr>
                    <?php if ($this->m_users->get_user_permission('', 'discoveries', 'u')) { ?>
                    <td class="text-center"><a class="btn btn-sm btn-success" href="discoveries/<?php echo $item->id; ?>?action=execute"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></a></td>
                    <?php } ?>
                    <td class="text-center"><a class="btn btn-sm btn-primary" href="discoveries/<?php echo $item->id; ?>"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></td>
                    <?php refine('discoveries.name',$item->attributes->name); ?>
                    <?php refine('discoveries.org_id',$item->attributes->org_id,$item->attributes->{'orgs.name'}); ?>
                    <?php refine('discoveries.type',$item->attributes->type); ?>
                    <?php refine('discoveries.description',$item->attributes->description); ?>
                    <?php refine('discoveries.last_run',$item->attributes->last_run); ?>
                    <?php refine('discoveries.status',$item->attributes->status, '', 'center'); ?>
                    <td class="text-center"><?php echo $item->attributes->ip_responding_count; ?></td>
                    <?php if ($this->m_users->get_user_permission('', 'discoveries', 'd')) { ?>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger delete_link" data-id="<?php echo $item->id; ?>" data-name="<?php echo $item->attributes->name; ?>" aria-label="Left Align" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>
                    <?php } ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
</div>
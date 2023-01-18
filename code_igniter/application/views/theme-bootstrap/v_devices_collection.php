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
* @version   GIT: Open-AudIT_4.4.1
* @link      http://www.open-audit.org
 */

$refine_link = $_SERVER["REQUEST_URI"];
if (strpos($refine_link, '?') === false) {
    $refine_link .= '?';
} else if (strrpos($refine_link, '&') !== strlen($refine_link)-1) {
    $refine_link .= '&';
}

if (!empty($this->response->meta->groupby)) {
    $refine_link = str_replace('?groupby=' . $this->response->meta->groupby, '?', $refine_link);
    $refine_link = str_replace('&groupby=' . $this->response->meta->groupby, '', $refine_link);
    if (strpos($refine_link, 'properties=') !== false) {
        $refine_link = str_replace('properties=', 'properties=system.id,system.name,', $refine_link);
    } else {
        $refine_link .= '&properties=system.id,system.name';
    }
}

if (!empty($this->response->meta->sub_resource_name)) {
    $title = ' - ' . $this->response->meta->sub_resource_name;
} else {
    $title = '';
}

$export_link = $this->response->links->first;
if (strpos($this->response->links->first, '?') !== false) {
    $export_link .= '&';
} else {
    $export_link .= '?';
}

if ($this->response->meta->sub_resource !== 'group') {
?>
<div class="collapse" id="queries">
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title pull-left"><?php echo __('Queries'); ?></h3>
        <span class="glyphicon glyphicon-chevron-down pull-right myDownButton" data-menuitem="queries" data-toggle="collapse" data-target="#queries" aria-expanded="false" role="button" aria-controls="queries"></span>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
            Please note this function has been deprecated and will be removed in a future release.<br />
            You can achieve the same result by running the query directly, then filtering the result.<br />
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><?php echo __('Name'); ?></th>
                        <th><?php echo __('Description'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($this->response->included as $item) {
                    if ($item->type == 'queries') {
                        if (strpos($this->response->links->first, '?') !== false) {
                            $link = $this->response->links->first . '&sub_resource=query&sub_resource_id=' . $item->attributes->id;
                        } else {
                            $link = $this->response->links->first . '?sub_resource=query&sub_resource_id=' . $item->attributes->id;
                        }
                        echo "<tr><td><a href=\"" . $link . "\">" . $item->attributes->name . "</a></td><td>" . $item->attributes->description . "</td></tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
</div>
<?php } ?>

<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <div class="panel-title">
            <div class="pull-left"><?php echo __('Devices'). ' '; ?> <?php echo $title ?></div>

            <div class="nav navbar-nav navbar-center">
                <form id="search_form" name="search_form" class="navbar-form" style="margin-top:0px; margin-bottom:0px;" action="<?php echo $this->config->config['oa_web_folder']; ?>index.php/search" method="post">
                    <div class="form-group">
                        <input type="text"   id="data[attributes][value]"   name="data[attributes][value]"   class="form-control input-sm" placeholder="Device Name or full IP">
                        <input type="hidden" id="data[attributes][tables]"  name="data[attributes][tables]" value='["system"]' />
                        <input type="hidden" id="data[attributes][columns]" name="data[attributes][columns]" value='["name","ip","hostname","dns_hostname","sysName","domain","dns_domain"]' />
                        <input type="hidden" id="data[access_token]" name="data[access_token]" value='<?php echo $this->response->meta->access_token; ?>' />
                    </div>
                    <button type="submit" class="btn btn-default btn-sm"><?php echo __('Submit'); ?></button>
                    <button type="button" class="btn btn-default btn-sm" aria-label="Left Align" data-container="body" data-toggle="popover" data-placement="left" title="Device Search" data-content="Search the following fields: name, hostname, dns_hostname, sysName, domain, dns_domain, ip.">
                        <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
                    </button>
                </form>
            </div>

            <div class="pull-right" style="padding-left:10px;">
                <div class="btn-group" role="group" aria-label="...">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                            Export <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $export_link; ?>format=csv">CSV</a></li>
                            <li><a href="<?php echo $export_link; ?>format=html">HTML</a></li>
                            <li><a href="<?php echo $export_link; ?>format=json">JSON</a></li>
                            <li><a href="<?php echo $export_link; ?>format=xml">XML</a></li>
                        </ul>
                        <?php if ($this->response->meta->sub_resource !== 'group') { ?>
                        <button class="btn btn-default btn-sm" type="button" data-toggle="collapse" data-target="#queries" aria-expanded="false" aria-controls="queries">
                            Queries
                        </button>
                        <?php } ?>
                    </div>
                </div>
                &nbsp;&nbsp;&nbsp;<?php echo $this->response->meta->filtered . ' of ' . $this->response->meta->total . ' results'; ?>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="panel panel-default pull-right">
            <div class="panel-body">
                <div class="btn-group" role="group" aria-label="...">
                    <a class="btn btn-default" href="<?php echo $this->response->links->first; ?>" role="button"><?php echo __('first'); ?></a>
                    <a class="btn btn-default" href="<?php echo $this->response->links->prev; ?>" role="button"><?php echo __('prev'); ?></a>
                    <a class="btn btn-default" href="<?php echo $this->response->links->next; ?>" role="button"><?php echo __('next'); ?></a>
                    <a class="btn btn-default" href="<?php echo $this->response->links->last; ?>" role="button"><?php echo __('last'); ?></a>
                </div>
            </div>
        </div>
<?php
if (count($this->response->meta->filter) > 0) {
    echo '<div class="panel panel-default pull-left">';
    echo '<div class="panel-body"><h4>';
    foreach ($this->response->meta->filter as $item) {
        if ($item->operator == '=') {
            $operator = '';
        } else {
            $operator = $item->operator;
        }
        $search = $item->name . '=' . $operator . $item->value;
        $replace = '';
        $link = '?' . str_replace($search, $replace, $this->response->meta->query_string);
        if (stripos($link, '?&') === 0) {
            $link = str_replace('?&', '?', $link);
        }
        echo '<span class="label label-info">' . $item->name . ' ' . $item->operator . ' ' . $item->value . '&nbsp;&nbsp;<a href="' . $link . '">&times;</a></span>&nbsp;';
        unset($query_parameters);
    }
    echo '</h4></div>';
    echo '</div>';
}

if (!empty($this->response->data)) { ?>
    <form action="devices?action=update" method="post" id="bulk_edit" name="bulk_edit">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <?php
            if (!empty($this->response->data[0]->attributes)) {
                $properties = get_object_vars($this->response->data[0]->attributes);
            } else {
                $properties = array();
            }
            foreach ($properties as $key => $value) {
                // if (strpos($key, '.') !== false) {
                //     $key = substr($key, strpos($key, '.')+1);
                // }
                $key = str_replace('system.', '', $key);
                $key = str_replace('.', ' ', $key);
                $key = str_replace('locations ', 'location ', $key);
                $key = str_replace('orgs ', 'org ', $key);

                if (strrpos($key, 'ip_padded') === strlen($key)-9) {
                    continue;
                }
                if ($key == 'system.id' or $key == 'id') {
                    $key = 'ID';
                    $key = 'View';
                }
                $key = str_replace('_', ' ', $key);
                $key = str_replace('os ', 'OS ', $key);
                $key = str_replace(' id', ' ID', $key);
                $key = str_replace(' ip', ' IP', $key);
                $key = ucwords($key);
                if ($key == 'Ip') {
                    $key = 'IP';
                }
                if (stripos($key, 'icon') !== false or $key == 'View') {
                    echo "            <th class=\"text-center\">" . __($key) . "</th>\n";
                } else {
                    echo "            <th>" . __($key) . "</th>\n";
                }
            }
            if ($this->m_users->get_user_permission('', 'devices', 'u')) {
                foreach ($properties as $key => $value) {
                    if ($key == 'system.id') {
                        echo "            <th class=\"text-center\">\n";
                        echo "              <button type=\"button\" class=\"btn btn-primary btn-xs bulk_edit_button\">" . __('Bulk Edit') . "</button>&nbsp;\n";
                        echo "              <input type=\"checkbox\" name=\"select_all\" id=\"select_all\" onclick=\"select_all_click();\"/>\n";
                        echo "            </th>\n";
                    }
                }
            }
            ?>
          </tr>
        </thead>
        <tbody>
    <?php
    # for every returned row
    foreach ($this->response->data as $item) {
        # grab the system.id if it exists
        $system_id = '';
        if (!empty($item->attributes->{'system.id'})) {
            $system_id = $item->attributes->{'system.id'};
        }
        if ($system_id == '' and !empty($item->id)) {
            $system_id = $item->id;
        }
        echo "          <tr>\n";

        # for every attribute in our retrned row
        foreach ($properties as $property => $value) {
            # remove any extra spacing
            $property = trim($property);
            # do we have a .?
            # if so, set the property name as the first character after the . onwards
            // if (strpos($property, '.') !== false) {
            //   $property = substr($property, strpos($property, '.'));
            // }

            # never output these - we should have an attribute called ip instead
            if (strrpos($property, 'ip_padded') === strlen($property)-9) {
                continue;
            }

            if (!empty($item->attributes->$property)) {
                if ($property == 'id' or $property == 'system.id') {
                    echo '            <td class="text-center"><a style="min-width:38px;" href="' . site_url() . '/devices/' . $item->attributes->$property . '" role="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></td>';

                } elseif ((strrpos($property, 'ip') === strlen($property)-2)) {
                    if (!empty($item->attributes->{$property . '_padded'})) {
                        echo "            <td><span style='display:none;'>" . str_replace('.', '', $item->attributes->{$property . '_padded'}) . "</span>" . $item->attributes->$property . "</td>\n";
                    } else {
                        echo "            <td>" . $item->attributes->$property . "</td>\n";
                    }

                } elseif (strrpos($property, 'icon') === strlen($property)-4) {
                    echo "            <td style=\"text-align: center;\"><img src=\"" . str_replace("index.php", "", site_url()) . "device_images/" . strtolower(str_replace(" ", "_", $item->attributes->$property)) . ".svg\" style='border-width:0; width:24px; height:24px' title=\"" . $item->attributes->$property . "\" alt=\"" . $item->attributes->$property . "\" ></td>\n";

                } elseif ($property == 'system.seen_by') {
                    echo "            <td>" . $item->attributes->$property . "</td>\n";

                } elseif ($property == 'link') {
                    echo "            <td><a href=\"../../" . $item->attributes->$property . "\" target=\"_blank\" role=\"button\" class=\"btn btn-sm btn-primary\"><span class=\"glyphicon glyphicon-link\" aria-hidden=\"true\"></span></a></td>\n";

                } else {
                    refine($property, $item->attributes->$property);
                }
            } else {
                echo "            <td></td>\n";
            }
        }
        if ($this->m_users->get_user_permission('', 'devices', 'u')) {
            if ( ! empty($system_id)) {
                echo "            <td style=\"text-align: center;\"><input type='checkbox' id='ids[" . $system_id . "]' value='" . $system_id . "' name='ids[" . $system_id . "]' /></td>\n";
            }
        }
        echo "          </tr>\n";
    }
    ?>
        </tbody>
      </table>
    </form>
    <?php
}
    ?>
  </div>
</div>



<?php
/**
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
*
* @category  Controller
* @package   Open-AudIT
* @author    Mark Unwin <marku@opmantek.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.3.2
* @link      http://www.open-audit.org
*/

/**
* Base Object Users
*
* @access   public
* @category Object
* @package  Open-AudIT
* @author   Mark Unwin <marku@opmantek.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class Users extends MY_Controller
{
    /**
    * Constructor
    *
    * @access    public
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_users');
        // inputRead();
        $this->response = response_create();
        $this->output->url = $this->config->config['oa_web_index'];
    }

    /**
    * Index that is unused
    *
    * @access public
    * @return NULL
    */
    public function index()
    {
    }

    /**
    * Our remap function to override the inbuilt controller->method functionality
    *
    * @access public
    * @return NULL
    */
    public function _remap()
    {
        $this->{$this->response->meta->action}();
    }

    /**
    * Process the supplied data and create a new object
    *
    * @access public
    * @return NULL
    */
    public function create()
    {
        $this->response->meta->id = $this->{'m_'.$this->response->meta->collection}->create($this->response->meta->received_data->attributes);
        $this->response->data = $this->{'m_'.$this->response->meta->collection}->read($this->response->meta->id);
        $this->response->include = 'v_'.$this->response->meta->collection.'_read';
        output($this->response);
    }

    /**
    * Read a single object
    *
    * @access public
    * @return NULL
    */
    public function read()
    {
        $this->load->helper('url');
        if ( $this->uri->segment(3) !== 'cookie') {
            $this->load->model('m_roles');
            if ($this->response->meta->format === 'screen') {
                if ( ! empty($this->response->data[0]->attributes)) {
                    $this->response->data[0]->attributes->org_list = implode(',', $this->m_users->get_orgs($this->response->meta->id));
                }
                $this->load->model('m_dashboards');
                $this->response->included = array_merge($this->response->included, $this->m_dashboards->collection($this->user->id));
            }
            $this->response->data = $this->{'m_'.$this->response->meta->collection}->read($this->response->meta->id);
            if ( ! empty($this->response->data) && is_array($this->response->data)) {
                $this->response->meta->total = 1;
                $this->response->meta->filtered = 1;
                $this->load->model('m_orgs');
                $this->response->dictionary = $this->{'m_'.$this->response->meta->collection}->dictionary();
                $this->load->model('m_roles');
                $this->response->included = array_merge($this->response->included, $this->m_roles->collection(1, 0));
                $this->load->model('m_dashboards');
                $this->response->included = array_merge($this->response->included, $this->m_dashboards->collection(1, 0));
                if ($this->response->meta->format === 'screen') {
                    $this->response->included = array_merge($this->response->included, $this->m_orgs->collection($this->user->id));
                } else {
                    $this->response->included = array_merge($this->response->included, $this->m_orgs->read($this->response->data[0]->attributes->org_id));
                }
            } else {
                log_error('ERR-0002', $this->response->meta->collection . ':read');
                $this->session->set_flashdata('error', 'No object could be retrieved when ' . $this->response->meta->collection . ' called m_' . $this->response->meta->collection . '->read.');
                if ($this->response->meta->format !== 'json') {
                    redirect($this->response->meta->collection);
                }
            }
            output($this->response);
        } else {
            // Only allow users with config update permission (which should only be those with Admin role)
            if ($this->m_users->get_user_permission('', 'configuration', 'u')) {
                $this->response->data = $this->{'m_users'}->read($this->response->meta->id);
                $access_token = bin2hex(openssl_random_pseudo_bytes(30));
                $userdata = array('user_id' => $this->response->meta->id, 'user_debug' => '', 'access_token' => $access_token);
                $this->session->set_userdata($userdata);
                $this->response->meta->access_token = $access_token;
                print_r(json_encode($this->response));
            } else {
                return;
            }
        }
    }

    /**
    * Process the supplied data and update an existing object
    *
    * @access public
    * @return NULL
    */
    public function update()
    {
        // JSON encode our roles
        if ( ! empty($this->response->meta->received_data->attributes->roles)) {
            $this->response->meta->received_data->attributes->roles = json_encode($this->response->meta->received_data->attributes->roles);
        }
        // JSON encode our orgs
        if ( ! empty($this->response->meta->received_data->attributes->orgs)) {
            $this->response->meta->received_data->attributes->orgs = json_encode(array_map('intval', $this->response->meta->received_data->attributes->orgs));
        }
        include 'include_update.php';
    }

    /**
    * Delete an existing object
    *
    * @access public
    * @return NULL
    */
    public function delete()
    {
        include 'include_delete.php';
    }

    /**
    * Collection of objects
    *
    * @access public
    * @return NULL
    */
    public function collection()
    {
        $this->{'m_'.$this->response->meta->collection}->collection(0, 1);
        output($this->response);
    }

    /**
    * Supply a HTML form for the user to create an object
    *
    * @access public
    * @return NULL
    */
    public function create_form()
    {
        $this->response->dictionary = $this->m_users->dictionary();
        $this->load->model('m_orgs');
        $this->response->included = array_merge($this->response->included, $this->m_orgs->collection($this->user->id));
        $this->load->model('m_roles');
        $this->response->included = array_merge($this->response->included, $this->m_roles->collection($this->user->id));
        $this->load->model('m_dashboards');
        $this->response->included = array_merge($this->response->included, $this->m_dashboards->collection($this->user->id));
        output($this->response);
    }

    /**
    * Supply a HTML form for the user to upload a collection of objects in CSV
    *
    * @access public
    * @return NULL
    */
    public function import_form()
    {
        $this->load->model('m_database');
        $this->response->data = $this->m_database->read('users');
        include 'include_import_form.php';
    }

    /**
    * Process the supplied data and create a new object
    *
    * @access public
    * @return NULL
    */
    public function import()
    {
        include 'include_import.php';
    }

}
// End of file users.php
// Location: ./controllers/users.php

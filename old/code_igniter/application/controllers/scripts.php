<?php
/**
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
*
* @category  Controller
* @package   Open-AudIT
* @author    Mark Unwin <mark.unwin@firstwave.com>
* @copyright 2022 Firstwave
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_4.4.2
* @link      http://www.open-audit.org
*/

/**
* Base Object Scripts
*
* @access   public
* @category Object
* @package  Open-AudIT
* @author   Mark Unwin <mark.unwin@firstwave.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class Scripts extends MY_Controller
{
    /**
    * Constructor
    *
    * @access    public
    */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_scripts');
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
        include 'include_scripts_options.php';
        $this->options = $options;
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
        include 'include_scripts_options.php';
        foreach ($options as $item) {
            $option = new stdClass();
            $option->id = $item->name;
            $option->type = 'option';
            $option->attributes = $item;
            $this->response->included[] = $option;
            unset($option);
        }
        foreach ($options_scripts as $key => $value) {
            $option = new stdClass();
            $option->id = $key;
            $option->type = 'script_option';
            $option->attributes = $value;
            $this->response->included[] = $option;
            unset($option);
        }
        $this->response->data = $this->{'m_'.$this->response->meta->collection}->read($this->response->meta->id);

        $this->load->model('m_files');
        $files = $this->m_files->collection($this->user->id);
        $filtered_files = array();
        foreach ($files as $file) {
            if ($this->response->data[0]->attributes->based_on === 'audit_windows.vbs' and strpos($file->attributes->path, '/') !== 0) {
                $filtered_files[] = $file;
            }
            if ($this->response->data[0]->attributes->based_on !== 'audit_windows.vbs' and strpos($file->attributes->path, '/') === 0) {
                $filtered_files[] = $file;
            }
        }
        #$this->response->included = array_merge($this->response->included, $this->m_files->collection($this->user->id));
        $this->response->included = array_merge($this->response->included, $filtered_files);

        if ( ! empty($this->response->data) && is_array($this->response->data)) {
            $this->response->meta->total = 1;
            $this->response->meta->filtered = 1;
            $this->load->model('m_orgs');
            $this->response->dictionary = $this->{'m_'.$this->response->meta->collection}->dictionary();
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
    }

    /**
    * Process the supplied data and update an existing object
    *
    * @access public
    * @return NULL
    */
    public function update()
    {
        include 'include_scripts_options.php';
        $this->options = $options;
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
    private function create_form()
    {
        $options = array();
        $options_scripts = array();
        // include our scripts options
        include 'include_scripts_options.php';
        foreach ($options as $item) {
            $option = new stdClass();
            $option->id = $item->name;
            $option->type = 'option';
            $option->attributes = $item;
            $this->response->included[] = $option;
            unset($option);
        }
        foreach ($options_scripts as $key => $value) {
            $option = new stdClass();
            $option->id = $key;
            $option->type = 'script_option';
            $option->attributes = $value;
            $this->response->included[] = $option;
            unset($option);
        }
        $this->response->dictionary = $this->m_scripts->dictionary();
        $this->load->model('m_orgs');
        $this->response->included = array_merge($this->response->included, $this->m_orgs->collection($this->user->id));
        $this->load->model('m_files');
        $this->response->included = array_merge($this->response->included, $this->m_files->collection($this->user->id));
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

    /**
    * Supply a file for download which is the script with the injected configuration
    *
    * @access public
    * @return NULL
    */
    public function download()
    {
        $this->response->meta->format = 'json';
        $script = $this->m_scripts->download($this->response->meta->id);
        $script_details = $this->m_scripts->read($this->response->meta->id);
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $script_details[0]->attributes->name);
        if ($script_details[0]->attributes->based_on == 'audit_windows.vbs') {
            header("Content-Type: text/vbscript");
        } else {
            header("Content-Type: application/x-sh");
        }
        
        header('Content-Transfer-Encoding: binary');
        echo $script;
    }
}
// End of file scripts.php
// Location: ./controllers/scripts.php

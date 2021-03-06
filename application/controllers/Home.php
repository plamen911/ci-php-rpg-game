<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Home
 * @author Plamen Markov <plamen@lynxlake.org>
 * @property Planet_model $planet_model
 * @property Message_model $message_model
 */
class Home extends CI_Controller
{
    private $data;

    public function __construct()
    {
        parent::__construct();

        $data = new stdClass();
        $data->resources = array();
        if ($this->session->userdata('logged_in')) {
            $this->load->model('planet_model');

            $data->planet_id = $this->session->userdata('planet_id');
            $data->resources = $this->planet_model->get_resources($data->planet_id);
        }
        $this->data = $data;
    }

    public function indexAction()
    {
        $data = $this->data;
        $this->load->view('header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('footer', $data);
    }

    public function resourcesAction()
    {
        $ret = array();
        $ret['code'] = 0;
        $ret['message'] = '';
        $ret['data'] = array();

        if (!$this->session->userdata('logged_in')) {
            $ret['code'] = 1;
            $ret['message'] = 'Not logged in!';
            die(json_encode($ret));
        }

        $this->load->model('message_model');
        $data = $this->data;

        $ret['code'] = 0;
        $ret['message'] = 'ok';
        $ret['data']['resources'] = $data->resources;
        $ret['data']['messages'] = $this->message_model->get_messages($data->planet_id);

        die(json_encode($ret));
    }
}

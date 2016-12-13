<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Galaxy extends CI_Controller
{
    private $data;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        $this->load->model('planet_model');
        $this->load->model('galaxy_model');

        $data = new stdClass();
        $data->player_id = $this->session->userdata('player_id');
        $data->planet_id = $this->session->userdata('planet_id');

        $data->resources = $this->planet_model->get_resources($data->planet_id);

        $this->data = $data;
    }

    public function indexAction()
    {
        redirect('/galaxy/players');
    }

    public function playersAction()
    {
        $data = $this->data;

        $data->players = $this->galaxy_model->get_players($data->planet_id);

        $this->load->view('header', $data);
        $this->load->view('galaxy/players', $data);
        $this->load->view('footer', $data);
    }
}

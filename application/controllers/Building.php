<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Building extends CI_Controller
{
    private $data;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        $this->load->model('planet_model');
        $this->load->model('building_model');

        $data = new stdClass();
        // $player_id = $this->session->userdata('player_id');
        $data->planet_id = $this->session->userdata('planet_id');

        $data->resources = $this->planet_model->get_resources($data->planet_id);

        $this->data = $data;
    }

    public function indexAction()
    {
        redirect('/building/list');
    }

    public function listAction()
    {
        $data = $this->data;

        $this->checkForBuildingInProgress($data->planet_id);

        $data->buildings = $this->planet_model->get_planet_buildings($data->planet_id);

        $this->load->view('header', $data);
        $this->load->view('building/list', $data);
        $this->load->view('footer', $data);
    }

    // Start to upgrade the building level - buildings_processes
    public function upgradeAction($building_id = 0)
    {
        $building_id = (int)$building_id;
        $data = $this->data;

        if (0 === $this->building_model->get_building($building_id)) {
            // show_404();
            redirect('/building/list');
        }

        $this->building_model->set_buildings_process($data->planet_id, $building_id);

        $ary = $this->building_model->get_building_in_process_data($data->planet_id);
        if (0 === $ary['building_id'] || $building_id !== $ary['building_id']) {
            redirect('/building/list');
        }

        $data->finishes_on = date('r', strtotime($ary['finishes_on']));

        // die('<pre>' . print_r($data, 1) . '</pre>');
        $this->load->view('header', $data);
        $this->load->view('building/upgrade', $data);
        $this->load->view('footer', $data);
    }

    // check for building in progress
    private function checkForBuildingInProgress($planet_id = 0)
    {
        $ary = $this->building_model->get_building_in_process_data($planet_id);
        if (0 < $ary['building_id']) {
            $this->session->set_flashdata('danger', 'Building in progress, please be patient...');
            redirect('/building/upgrade/' . $ary['building_id']);
        }
    }
}

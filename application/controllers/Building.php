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
            $this->session->set_flashdata('danger', 'Invalid Building ID!');
            redirect('/building/list');
        }

        $data->building_id = $building_id;
        $result = $this->building_model->set_building_process($data->planet_id, $building_id);
        $data->finishes_on = date('r', strtotime($result->finishes_on));

        $this->load->view('header', $data);
        $this->load->view('building/upgrade', $data);
        $this->load->view('footer', $data);
    }

    public function finishUpgradeAction($building_id = 0)
    {
        $building_id = (int)$building_id;
        $data = $this->data;

        if (0 === $this->building_model->get_building($building_id)) {
            $this->session->set_flashdata('danger', 'Invalid Building ID!');
            redirect('/building/list');
        }

        $this->building_model->finish_building_process($data->planet_id, $building_id);
        $this->session->set_flashdata('success', 'Building level was successfully upgraded.');
        redirect('/building/list');
    }
}

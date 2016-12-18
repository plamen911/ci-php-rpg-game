<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ship extends CI_Controller
{
    private $data;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('redirect_url', uri_string());
            redirect('login');
        }

        $this->load->model('planet_model');
        $this->load->model('ship_model');

        $data = new stdClass();
        // $player_id = $this->session->userdata('player_id');
        $data->planet_id = $this->session->userdata('planet_id');

        $data->resources = $this->planet_model->get_resources($data->planet_id);

        $this->data = $data;
    }

    public function indexAction()
    {
        redirect('/ship/list');
    }

    public function listAction()
    {
        $data = $this->data;

        $this->ship_model->finish_building_process2(33, 2);

        $data->ships = $this->ship_model->get_ships($data->planet_id);

        $this->load->view('header', $data);
        $this->load->view('ship/list', $data);
        $this->load->view('footer', $data);
    }

    // Start to upgrade the ship level - ships_processes
    public function upgradeAction($ship_id = 0)
    {
        $ship_id = (int)$ship_id;
        $data = $this->data;

        if (!$this->ship_model->get_ship($data->planet_id, $ship_id)) {
            $this->session->set_flashdata('danger', 'Invalid ship ID!');
            redirect('/ship/list');
        }

        $amount = (int)$this->input->get('amount');
        if (0 >= $amount) {
            $this->session->set_flashdata('danger', 'Please enter ship Qty.');
            redirect('/ship/list');
        }

        // Check for available resources and required building levels
        try {
            $this->ship_model->has_enough_resources($data->planet_id, $ship_id, $amount);
        } catch (Exception $ex) {
            $this->session->set_flashdata('danger', $ex->getMessage());
            redirect('/ship/list');
        }

        $data->ship_id = $ship_id;
        $result = $this->ship_model->set_building_process2($data->planet_id, $ship_id, $amount);
        $data->finishes_on = date('r', strtotime($result->finishes_on));

        $this->load->view('header', $data);
        $this->load->view('ship/upgrade', $data);
        $this->load->view('footer', $data);
    }

    public function finishUpgradeAction($ship_id = 0)
    {
        $ship_id = (int)$ship_id;
        $data = $this->data;

        if (!$this->ship_model->get_ship($data->planet_id, $ship_id)) {
            $this->session->set_flashdata('danger', 'Invalid ship ID!');
            redirect('/ship/list');
        }

        $this->ship_model->finish_building_process2($data->planet_id, $ship_id);
        $this->session->set_flashdata('success', 'Ship quantity was successfully updated.');
        redirect('/ship/list');
    }
}

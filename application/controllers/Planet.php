<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Planet extends CI_Controller
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
        $this->load->model('player_model');
        $this->load->model('building_model');

        $data = new stdClass();
        $data->player_id = (int)$this->session->userdata('player_id');
        $data->planet_id = (int)$this->session->userdata('planet_id');

        $data->resources = $this->planet_model->get_resources($data->planet_id);

        $this->data = $data;
    }

    public function indexAction()
    {
        redirect('/planet/list');
    }

    public function listAction()
    {
        $data = $this->data;

        $data->planets = $this->planet_model->get_planets($data->player_id);

        $this->load->view('header', $data);
        $this->load->view('planet/list', $data);
        $this->load->view('footer', $data);
    }

    public function createAction() {
        if (is_post_request()) {
            $data = $this->data;
            $planet_id = $this->player_model->create_planet($data->player_id, 'Planet of ' . $data->player_id);
            $planet = $this->planet_model->get_planet($planet_id);
            $this->session->set_flashdata('success', 'Planet ' . get_planet_name($planet) . ' successfully created.');
        }
        redirect('/planet/list');
    }

    public function activateAction($planet_id = 0)
    {
        $planet_id = (int)$planet_id;
        $planet = $this->planet_model->get_planet($planet_id);

        if (!$planet) {
            $this->session->set_flashdata('danger', 'Invalid Planet ID!');
        } else {
            $this->session->set_userdata('planet_id', $planet_id);
            $this->session->set_userdata('planet_name', get_planet_name($planet));
        }
        redirect('/planet/list');
    }

    public function deleteAction($planet_id = 0)
    {
        $planet_id = (int)$planet_id;

        $data = $this->data;

        if ($data->planet_id === $planet_id) {
            $this->session->set_flashdata('danger', 'You are not allowed to delete current active planet!');
            redirect('/planet/list');
        }

        $planet = $this->planet_model->get_planet($planet_id);
        if (!$planet) {
            $this->session->set_flashdata('danger', 'Invalid Planet ID!');
        } else {
            $this->session->set_flashdata('success', 'Planet ' . get_planet_name($planet) . ' successfully deleted.');
            $this->planet_model->delete_planet($planet_id);
        }

        redirect('/planet/list');
    }
}

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

        $this->load->model('player_model');
        $this->load->model('planet_model');
        $this->load->model('galaxy_model');
        $this->load->model('ship_model');

        $data = new stdClass();
        $data->player_id = $this->session->userdata('player_id');
        $data->planet_id = $this->session->userdata('planet_id');

        $data->resources = $this->planet_model->get_resources($data->planet_id);

        $this->data = $data;
    }

    public function indexAction()
    {
        redirect('/galaxy/map');
    }

    public function mapAction()
    {
        $data = $this->data;

        $data->players = $this->galaxy_model->get_players($data->planet_id);

        $this->load->view('header', $data);
        $this->load->view('galaxy/players', $data);
        $this->load->view('footer', $data);
    }

    public function flightAction($defender_planet_id = 0)
    {
        $data = $this->data;
        $data->defender_planet_id = (int)$defender_planet_id;

        $ships = $this->ship_model->get_my_ships($data->planet_id);

        if (empty($ships)) {
            $this->session->set_flashdata('danger', 'In order to attack another user you need to build some army ships.');
            redirect('/ship/list');
        }

        $player_id = $this->player_model->get_player_id_from_planet_id($data->defender_planet_id);
        $data->defender = $this->player_model->get_player($player_id);

        $data->ships = $ships;

        // die('<pre>' . print_r($data->ships, 1) . '</pre>');

        $this->load->view('header', $data);
        $this->load->view('galaxy/flight', $data);
        $this->load->view('footer', $data);
    }
}

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
        $data->player_id = (int)$this->session->userdata('player_id');
        $data->planet_id = (int)$this->session->userdata('planet_id');

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
        $this->load->view('galaxy/map', $data);
        $this->load->view('footer', $data);
    }

    public function flightAction($defender_player_id = 0)
    {
        $data = $this->data;
        $data->defender_player_id = (int)$defender_player_id;

        if ($data->player_id === $data->defender_player_id) {
            $this->session->set_flashdata('danger', 'You cannot attack yourself!');
            redirect('/ship/list');
        }

        $ships = $this->ship_model->get_my_ships($data->planet_id);

        if (empty($ships)) {
            $this->session->set_flashdata('danger', 'In order to attack another user you need to build some army ships.');
            redirect('/ship/list');
        }

        $data->ships = $ships;

        // defender_planet_id - these is already built active flight
        if (0 < $defender_pid = $this->galaxy_model->has_flights($data->planet_id)) {
            $this->session->set_flashdata('danger', 'In order to attack another user you need to build some army ships.');
            redirect('/galaxy/journey/' . $defender_pid);
        }

        $data->defender = $this->player_model->get_player($defender_player_id);

        $isPosted = 'POST' === strtoupper($_SERVER['REQUEST_METHOD']);

        $flight_ships = array();
        if ($isPosted) {
            foreach ($_POST as $key => $val) {
                if (preg_match('/^amount_(\d+)$/', $key, $matches)) {
                    $ship_id = $matches[1];
                    $amount = (int)$val;
                    $flight_ships[$ship_id] = $amount;
                }
            }
        }

        // check ship quantities
        $hasError = false;
        foreach ($data->ships as $ship) {
            if (isset($flight_ships[$ship->ship_id]) && $flight_ships[$ship->ship_id] > $ship->qty) {
                $hasError = true;
                $this->session->set_flashdata('danger', 'Max. qty for ' . $ship->name . ' is ' . $ship->qty . '.');
                break;
            }
        }

        if (!$hasError && $isPosted) {
            $this->galaxy_model->create_flight($data->planet_id, $defender_player_id, $flight_ships);
            redirect('/galaxy/journey/' . $defender_player_id);

        } else {
            $this->load->view('header', $data);
            $this->load->view('galaxy/flight', $data);
            $this->load->view('footer', $data);
        }
    }

    public function journeyAction($defender_player_id = 0)
    {

        die('Journey starts here: ' . $defender_player_id);

    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Galaxy extends CI_Controller
{
    private $data;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('redirect_url', uri_string());
            redirect('login');
        }

        $this->load->model('player_model');
        $this->load->model('planet_model');
        $this->load->model('galaxy_model');
        $this->load->model('ship_model');
        $this->load->model('message_model');

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

        $data->planets = $this->galaxy_model->get_planets_w_distance($data->planet_id);

        $this->load->view('header', $data);
        $this->load->view('galaxy/map', $data);
        $this->load->view('footer', $data);
    }

    public function flightAction($defender_planet_id = 0)
    {
        $data = $this->data;
        $defender_player_id = $this->player_model->get_player_id_from_planet_id($defender_planet_id);
        $data->defender_planet_id = (int)$defender_planet_id;

        if ($data->player_id === $defender_player_id) {
            $this->session->set_flashdata('danger', 'You cannot attack yourself!');
            redirect('/galaxy/map');
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

        $data->defender_planet = $this->planet_model->get_planet($defender_planet_id);

        $is_posted = is_post_request();

        $flight_ships = array();
        if ($is_posted) {
            foreach ($_POST as $key => $val) {
                if (preg_match('/^amount_(\d+)$/', $key, $matches)) {
                    $ship_id = $matches[1];
                    $amount = (int)$val;
                    $flight_ships[$ship_id] = $amount;
                }
            }
        }

        // check ship quantities
        $has_error = false;
        foreach ($data->ships as $ship) {
            if (isset($flight_ships[$ship->ship_id]) && $flight_ships[$ship->ship_id] > $ship->qty) {
                $has_error = true;
                $this->session->set_flashdata('danger', 'Max. qty for ' . $ship->name . ' is ' . $ship->qty . '.');
                break;
            }
        }

        if (!$has_error && $is_posted) {
            $flight_id = $this->galaxy_model->create_flight($data->planet_id, $data->defender_planet_id, $flight_ships);
            redirect('/galaxy/journey/' . $flight_id);

        } else {
            $this->load->view('header', $data);
            $this->load->view('galaxy/flight', $data);
            $this->load->view('footer', $data);
        }
    }

    public function journeyAction($flight_id = 0)
    {
        $flight_id = (int)$flight_id;
        $data = $this->data;

        $flight = $this->galaxy_model->get_flight($flight_id);
        if (empty($flight)) {
            $this->session->set_flashdata('danger', 'You must select planet to attack.');
            redirect('/galaxy/map');
        }

        $data->flight_id = $flight_id;
        $data->attacker_planet = $this->planet_model->get_planet($flight->attacker_planet_id);
        $data->defender_planet = $this->planet_model->get_planet($flight->defender_planet_id);
        $data->arriving_on = date('r', strtotime($flight->arriving_on));

        $this->load->view('header', $data);
        $this->load->view('galaxy/journey', $data);
        $this->load->view('footer', $data);
    }

    public function battleAction($flight_id = 0)
    {
        $data = $this->data;
        try {
            $data = $this->galaxy_model->get_battle_data($flight_id, $data);
        } catch (Exception $ex) {
            $this->session->set_flashdata('danger', $ex->getMessage());
            redirect('/galaxy/map');
        }

        //die('<pre>' . print_r($data, 1) . '</pre>');

        $battle_end_on = strtotime('+15 seconds');

        $data->battle_end_on = date('r', $battle_end_on);

        // set message that battle is started
        $this->message_model->set_message(array(
                'attacker_planet_id' => $data->attacker_planet->id,
                'defender_planet_id' => $data->defender_planet->id,
                'message_type' => 'battle-in-progress-and-time-remaining-until-end',
                'expires_on' => date('Y-m-d H:i:s', $battle_end_on)
            )
        );

        $data->html_body_attr = ' id="battle-canvas"';

        $this->load->view('header', $data);
        $this->load->view('galaxy/battle', $data);
        $this->load->view('footer', $data);
    }

    public function battleReportAction($flight_id = 0)
    {
        $data = $this->data;
        try {
            $this->data = $this->galaxy_model->battle_report($flight_id, $data);
        } catch (Exception $ex) {
            $this->session->set_flashdata('danger', $ex->getMessage());
            redirect('/galaxy/map');
        }

        $data->flight_id = $flight_id;

        $this->load->view('header', $data);
        $this->load->view('galaxy/battle-report', $data);
        $this->load->view('footer', $data);
    }

    public function journeyBackAction($flight_id = 0)
    {
        $flight_id = (int)$flight_id;
        $data = $this->data;

        $flight = $this->galaxy_model->get_flight($flight_id);
        if (empty($flight)) {
            $this->session->set_flashdata('danger', 'You must select planet to attack.');
            redirect('/galaxy/map');
        }

        $data->flight_id = $flight_id;
        $attacker_planet_id = $flight->attacker_planet_id;
        $defender_planet_id = $flight->defender_planet_id;

        $data->attacker_planet = $this->planet_model->get_planet($attacker_planet_id);
        $data->defender_planet = $this->planet_model->get_planet($defender_planet_id);

        $arriving_on = $this->galaxy_model->get_journey_time($attacker_planet_id, $defender_planet_id);
        $data->arriving_on = date('r', strtotime($arriving_on));

        $this->galaxy_model->delete_flight($flight_id);

        $this->load->view('header', $data);
        $this->load->view('galaxy/journey-back', $data);
        $this->load->view('footer', $data);
    }
}

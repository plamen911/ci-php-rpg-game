<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ship extends CI_Controller
{
    private $data;

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
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

        // $this->checkForShipInProgress($data->planet_id);

        $data->ships = $this->ship_model->get_ships($data->planet_id);
        // die('<pre>' . print_r($data->ships, 1) . '</pre>');

        $this->load->view('header', $data);
        $this->load->view('ship/list', $data);
        $this->load->view('footer', $data);
    }

    // Start to upgrade the ship level - ships_processes
    public function upgradeAction($ship_id = 0)
    {
        $ship_id = (int)$ship_id;
        $data = $this->data;

        // todo -- Check if there are available resources

        if (0 === $this->ship_model->get_ship($data->planet_id, $ship_id)) {
            // show_404();
            redirect('/ship/list');
        }

        $this->ship_model->set_ships_process($data->planet_id, $ship_id);

        $ary = $this->ship_model->get_ship_in_process_data($data->planet_id);
        if (0 === $ary['ship_id'] || $ship_id !== $ary['ship_id']) {
            redirect('/ship/list');
        }

        $data->finishes_on = date('r', strtotime($ary['finishes_on']));

        $this->load->view('header', $data);
        $this->load->view('ship/upgrade', $data);
        $this->load->view('footer', $data);
    }

    // check for ship in progress
    private function checkForShipInProgress($planet_id = 0)
    {
        $ary = $this->ship_model->get_ship_in_process_data($planet_id);
        if (0 < $ary['ship_id']) {
            $this->session->set_flashdata('danger', 'Building in progress, please be patient...');
            redirect('/ship/upgrade/' . $ary['ship_id']);
        }
    }
}

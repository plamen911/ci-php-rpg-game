<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Planet class.
 *
 * @extends CI_Controller
 */
class OLD_Planet extends CI_Controller
{
    /**
     * __construct function.
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        $this->load->model('planet_model');
    }

    public function indexAction()
    {
        redirect('/planet/list');
    }

    public function listAction()
    {
        $data = new stdClass();
        $player_id = $this->session->userdata('player_id');
        $data->planets = $this->planet_model->get_planets($player_id);

        $this->load->view('header');
        $this->load->view('planet/list', $data);
        $this->load->view('footer');
    }

    public function createAction()
    {
        $data = new stdClass();
        $player_id = $this->session->userdata('player_id');

        // set validation rules
        $this->form_validation->set_rules('name', 'Planet Name', 'trim|required');
        if ($this->form_validation->run() === false) {
            $this->load->view('header');
            $this->load->view('planet/create', $data);
            $this->load->view('footer');
        } else {
            $name = $this->input->post('name');
            $this->planet_model->create_planet($player_id, $name);
            $this->session->set_flashdata('success', 'Planet successfully created.');
            redirect('/planet/list');
        }
    }

    public function editAction($id = 0)
    {
        if (!$id) {
            show_404();
        }

        $data = new stdClass();

        $player_id = $this->session->userdata('player_id');
        $planet = $this->planet_model->get_planet($id, $player_id);

        if (!$planet) {
            show_404();
        }

        $data->planet = $planet;

        // set validation rules
        $this->form_validation->set_rules('name', 'Planet Name', 'trim|required');
        if ($this->form_validation->run() === false) {
            $this->load->view('header');
            $this->load->view('planet/edit', $data);
            $this->load->view('footer');
        } else {
            $fields = array(
                'name' => $this->input->post('name')
            );
            $this->planet_model->edit_planet($id, $player_id, $fields);
            $this->session->set_flashdata('success', 'Planet successfully updated.');
            redirect('/planet/list');
        }
    }

    public function deleteAction($id = 0)
    {
        $data = new stdClass();

        $this->load->view('header');
        $this->load->view('planet/delete', $data);
        $this->load->view('footer');
    }
}

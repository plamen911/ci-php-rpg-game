<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Planet class.
 *
 * @extends CI_Controller
 */
class OLD_Resource extends CI_Controller
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

        //$this->load->model('planet_model');
    }

    public function indexAction()
    {
        redirect('/resource/create');
    }

    public function createAction()
    {
        $data = new stdClass();
        $player_id = $this->session->userdata('player_id');

        $this->load->view('header');
        $this->load->view('resource/create', $data);
        $this->load->view('footer');
    }
}

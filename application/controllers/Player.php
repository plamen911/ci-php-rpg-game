<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Player class.
 * 
 * @extends CI_Controller
 */
class Player extends CI_Controller {

	/**
	 * __construct function.
	 * 
	 * @access public
	 */
	public function __construct() {
        parent::__construct();
		$this->load->model('player_model');
	}

	public function indexAction() {
        redirect('player/login');
	}
	
	/**
	 * register function.
	 * 
	 * @access public
	 * @return void
	 */
	public function registerAction() {
		// create the data object
		$data = new stdClass();

		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[4]|is_unique[players.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[players.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[1]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[1]|matches[password]');
		
		if ($this->form_validation->run() === false) {
			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('player/register', $data);
			$this->load->view('footer');
		} else {
			// set variables from the form
			$username = $this->input->post('username');
			$email    = $this->input->post('email');
			$password = $this->input->post('password');
			
			if ($this->player_model->create_player($username, $email, $password)) {
				redirect('/login');
			} else {
				// player creation failed, this should never happen
				$data->error = 'There was a problem creating your new account. Please try again.';
				
				// send error to the view
				$this->load->view('header');
				$this->load->view('player/register', $data);
				$this->load->view('footer');
			}
		}
	}

	/**
	 * profile function.
	 *
	 * @access public
	 * @return void
	 */
	public function profileAction() {
		$data = new stdClass();

        $player_id = $this->session->userdata('player_id');
		$data->player = $this->player_model->get_player($player_id);

		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

		if ($this->form_validation->run() === false) {
			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('player/profile', $data);
			$this->load->view('footer');
		} else {
			// set variables from the form
			$username = $this->input->post('username');
			$email    = $this->input->post('email');
			$password = $this->input->post('password');
			$password_confirm = $this->input->post('password_confirm');
			$full_name = $this->input->post('full_name');

			if (!empty($password) && $password !== $password_confirm) {
                $this->session->set_flashdata('danger', 'Passwords do not match.');
                redirect('/profile');
            }

            if (0 < $this->player_model->is_username_unique($player_id, $username)) {
                $this->session->set_flashdata('danger', 'This username already exists. Please choose another one.');
                redirect('/profile');
            }

            $fields = array(
                'username' => $username,
                'email' => $email,
                'full_name' => $full_name,
                'updated_at' => date('Y-m-d H:i:s')
            );
            if (!empty($password)) {
                $fields['password'] = $password;
            }

            $this->player_model->update_profile($player_id, $fields);
            $this->session->set_flashdata('success', 'Profile successfully updated.');
            redirect('/profile');
		}
	}
		
	/**
	 * login function.
	 * 
	 * @access public
	 * @return void
	 */
	public function loginAction() {
		
		// create the data object
		$data = new stdClass();
		
		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		
		if ($this->form_validation->run() == false) {
			
			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('player/login');
			$this->load->view('footer');
			
		} else {
			
			// set variables from the form
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			if ($this->player_model->resolve_player_login($username, $password)) {
				
				$player_id = $this->player_model->get_player_id_from_playername($username);
				$player    = $this->player_model->get_player($player_id);

                // set session user data
                $this->session->set_userdata(array(
                    'logged_in' => true,
                    'player_id' => (int)$player->id,
                    'username' => (string)$player->username,
                    'is_confirmed' => (bool)$player->is_confirmed,
                    'is_admin' => (bool)$player->is_admin
                ));

				// user login ok
				redirect('planet/list');
				
			} else {
				
				// login failed
				$data->error = 'Wrong username or password.';
				
				// send error to the view
				$this->load->view('header');
				$this->load->view('player/login', $data);
				$this->load->view('footer');
			}
		}
	}
	
	/**
	 * logout function.
	 * 
	 * @access public
	 * @return void
	 */
	public function logoutAction() {
		if (isset($_SESSION['logged_in']) && true === $_SESSION['logged_in']) {
			
			// remove session datas
			foreach ($_SESSION as $key => $value) {
				unset($_SESSION[$key]);
			}

            $this->session->set_flashdata('success', 'Successfully logged out.');
			
			// user logout ok
			redirect('/login');
		} else {
			
			// there user was not logged in, we cannot logged him out,
			// redirect him to site root
			redirect('/');
		}
	}
	
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Player_model class.
 * 
 * @extends CI_Model
 */
class Player_model extends CI_Model {

	/**
	 * __construct function.
	 * 
	 * @access public
	 */
	public function __construct() {
		
		parent::__construct();
		$this->load->database();
	}

	/**
	 * create_player function.
	 *
	 * @access public
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @return bool true on success, false on failure
	 */
	public function create_player($username = '', $email = '', $password = '') {
		$data = array(
			'username'   => $username,
			'email'      => $email,
			'password'   => $this->hash_password($password),
			'created_at' => date('Y-m-j H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
		);
		return $this->db->insert('players', $data);
	}

    /**
     * @param int $player_id
     * @param array $data
     */
	public function update_profile($player_id = 0, $data = array()) {
	    if (!empty($data['password'])) {
	        $data['password'] = $this->hash_password($data['password']);
        }
        $this->db->where('id', $player_id);
        $this->db->update('players', $data);
    }

    /**
     * @param int $player_id
     * @param string $username
     * @return int
     */
    public function is_username_unique($player_id = 0, $username = '') {
        return $this->db
            ->where('username', $username)
            ->where('id !=', $player_id)
            ->count_all_results('players');
    }

	/**
	 * resolve_player_login function.
	 *
	 * @access public
	 * @param string $username
	 * @param string $password
	 * @return bool true on success, false on failure
	 */
	public function resolve_player_login($username = '', $password = '') {
		$this->db->select('password');
		$this->db->from('players');
		$this->db->where('username', $username);
		$hash = $this->db->get()->row('password');
		return $this->verify_password_hash($password, $hash);
	}

	/**
	 * get_player_id_from_playername function.
	 *
	 * @access public
	 * @param string $username
	 * @return int the user id
	 */
	public function get_player_id_from_playername($username = '') {
		$this->db->select('id');
		$this->db->from('players');
		$this->db->where('username', $username);
		return (int)$this->db->get()->row('id');
	}

	/**
	 * get_player function.
	 *
	 * @access public
	 * @param int $player_id
	 * @return object the user object
	 */
	public function get_player($player_id = 0) {
		$this->db->from('players');
		$this->db->where('id', $player_id);
		return $this->db->get()->row();
	}

	/**
	 * hash_password function.
	 *
	 * @access private
	 * @param mixed $password
	 * @return string|bool could be a string on success, or bool false on failure
	 */
	private function hash_password($password = '') {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	/**
	 * verify_password_hash function.
	 *
	 * @access private
	 * @param string $password
	 * @param string $hash
	 * @return bool
	 */
	private function verify_password_hash($password = '', $hash = '') {
		return password_verify($password, $hash);
	}

}

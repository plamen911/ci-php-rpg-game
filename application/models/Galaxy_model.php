<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Galaxy_model class.
 * 
 * @extends CI_Model
 */
class Galaxy_model extends CI_Model {

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
     * @return mixed
     */
    public function get_players() {
        $this->db->from('players')
            ->join('planets', 'planets.player_id = players.id')
            ->order_by('players.username', 'asc');
        return $this->db->get()->result();
    }
}









<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Planet_model class.
 * 
 * @extends CI_Model
 */
class Planet_model extends CI_Model {

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
     * @param int $player_id
     * @return mixed
     */
    public function get_planets($player_id = 0) {
        $this->db->from('planets');
        $this->db->where('player_id', $player_id);
        $this->db->order_by('name', 'asc');
        return $this->db->get()->result();
    }

    /**
     * @param int $planet_id
     * @param int $player_id
     * @return object the planet object
     */
    public function get_planet($planet_id = 0, $player_id = 0) {
        $this->db->from('planets');
        $this->db->where(array(
            'id' => $planet_id,
            'player_id' => $player_id,
        ));
        return $this->db->get()->row();
    }

    /**
     * @param int $player_id
     * @param array $name
     */
    public function create_planet($player_id = 0, $name = array()) {
        // Assign random coordinates
        $data = array(
            'name' => $name,
            'x' => rand(1, 100),
            'y' => rand(1, 100),
            'player_id' => $player_id
        );
        $this->db->insert('planets', $data);
        $planet_id = $this->db->insert_id();

        // Set predefined amount of resources
        $query = $this->db->get('resources');
        foreach ($query->result() as $row) {
            $this->db->insert('planet_resources',
                array(
                    'planet_id' => $planet_id,
                    'resource_id' => $row->id,
                    'amount' => 0
                ));
        }
    }

    /**
     * @param int $planet_id
     * @param int $player_id
     * @param array $data
     */
    public function edit_planet($planet_id = 0, $player_id = 0, $data = array()) {
        $this->db->where('id', $planet_id);
        $this->db->where('player_id', $player_id);
        $this->db->update('planets', $data);
    }
}

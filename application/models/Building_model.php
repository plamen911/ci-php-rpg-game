<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Building_model class.
 * 
 * @extends CI_Model
 */
class Building_model extends CI_Model {

	/**
	 * __construct function.
	 * 
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

    public function get_building($building_id = 0) {
        $this->db->select('*')
            ->from('buildings')
            ->where('id', $building_id);
        return (int)$this->db->get()->row('id');
    }

    /**
     * @param int $planet_id
     * @param int $building_id
     * @return object
     */
	public function set_building_process($planet_id = 0, $building_id = 0) {
        $this->db->select('*')
            ->from('planet_buildings')
            ->where('planet_id', $planet_id)
            ->where('building_id', $building_id);
        $result = $this->db->get();
        $planet_building_id = (int)$result->row('id');

        // delete all active processes
        $this->db->delete('buildings_processes', array('planet_building_id' => $planet_building_id));

        // check if building in progress...
        $res = $this->get_building_process($planet_building_id);
        if (!$res) {
            $level = (int)$result->row('level');
            $building_time = 10 * $level;// in seconds

            $datetime = new DateTime();
            $datetime->add(new DateInterval('PT' . $building_time . 'S'));
            $finishes_on = $datetime->format('Y-m-d H:i:s');

            $data = array(
                'planet_building_id' => $planet_building_id,
                'finishes_on' => $finishes_on
            );
            $this->db->insert('buildings_processes', $data);
        }

        return $this->get_building_process($planet_building_id);
    }

    public function finish_building_process($planet_id = 0, $building_id = 0) {
        $this->db->select('*')
            ->from('planet_buildings')
            ->where('planet_id', $planet_id)
            ->where('building_id', $building_id);
        $result = $this->db->get();
        $planet_building_id = (int)$result->row('id');
        $level = (int)$result->row('level');

        $result = $this->get_building_process($planet_building_id);
        if ($result) {
            $level++;

            $this->db->where('id', $planet_building_id);
            $this->db->update('planet_buildings', array('level' => $level));

            // delete record from active process list
            $this->db->delete('buildings_processes', array(
                    'planet_building_id' => $planet_building_id
                )
            );
        }
    }

    /**
     * @param int $planet_building_id
     * @return object
     */
    private function get_building_process($planet_building_id = 0) {
        $this->db->select('*')
            ->from('buildings_processes')
            ->where('planet_building_id', $planet_building_id);
        return $this->db->get()->row();
    }
}









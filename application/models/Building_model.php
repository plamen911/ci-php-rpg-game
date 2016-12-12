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
     */
	public function set_buildings_process($planet_id = 0, $building_id = 0) {
	    $ary = $this->get_building_in_process_data($planet_id);
        if (0 === $ary['building_id']) {
            $this->db->select('*')
                ->from('planet_buildings')
                ->where('planet_id', $planet_id)
                ->where('building_id', $building_id);
            $result = $this->db->get();
            $planet_building_id = (int)$result->row('id');
            $level = (int)$result->row('level');

            $buildingTime = 10 * $level;// in seconds

            $datetime = new DateTime();
            $datetime->add(new DateInterval('PT' . $buildingTime . 'S'));
            $finishes_on = $datetime->format('Y-m-d H:i:s');

            $data = array(
                'planet_building_id' => $planet_building_id,
                'finishes_on' => $finishes_on
            );
            $this->db->insert('buildings_processes', $data);
        }
    }

    public function get_building_in_process_data($planet_id = 0) {
        $this->load->model('planet_model');
        $buildings = $this->planet_model->get_planet_buildings($planet_id);
        $IDs = array();
        foreach ($buildings as $building) {
            if (!empty($building_id) && (int)$building_id !== (int)$building->building_id) {
                continue;
            }
            $IDs[(int)$building->planet_building_id] = (int)$building->building_id;
        }
        $this->db->select('*')
            ->from('buildings_processes')
            ->where_in('planet_building_id', array_keys($IDs));

        $result = $this->db->get();
        $planet_building_id = ($result) ? (int)$result->row('planet_building_id') : 0;
        $finishes_on = ($result) ? $result->row('finishes_on') : '';
        $building_id = (isset($IDs[$planet_building_id])) ? $IDs[$planet_building_id] : 0;

        // check if already built
        if (0 < $planet_building_id) {
            $datetime1 = new DateTime($finishes_on);
            $datetime2 = new DateTime();
            // it is built - should add a building level
            if ($datetime1->format('U') <= $datetime2->format('U')) {
                $this->db->select('level')
                    ->from('planet_buildings')
                    ->where('id', $planet_building_id);
                $level = (int)$this->db->get()->row('level');
                $level++;

                $this->db->where('id', $planet_building_id);
                $this->db->update('planet_buildings', array('level' => $level));

                // delete record from active process list
                $this->db->delete('buildings_processes', array('planet_building_id' => $planet_building_id));

                $building_id = 0;
                $planet_building_id = 0;
                $finishes_on = '';

                /*return array(
                    'building_id' => $building_id,
                    'planet_building_id' => $planet_building_id,
                    'finishes_on' => $finishes_on,
                );*/
            }
        }

        return array(
            'building_id' => $building_id,
            'planet_building_id' => $planet_building_id,
            'finishes_on' => $finishes_on,
        );
    }
}









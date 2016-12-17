<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ship_model class.
 * 
 * @extends CI_Model
 */
class Ship_model extends CI_Model {

	/**
	 * __construct function.
	 * 
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

    public function get_ships($planet_id = 0, $ship_id = 0) {
        $this->db->select('*, planet_ships.amount AS qty, ships_cost_time.amount AS cost_time');
        $this->db->from('planet_ships');
        $this->db->join('ships', 'ships.id = planet_ships.ship_id');
        $this->db->join('ships_cost_time', 'ships_cost_time.ship_id = planet_ships.ship_id');
        $this->db->where('planet_ships.planet_id', $planet_id);
        if (!empty($ship_id)) {
            $this->db->where('planet_ships.ship_id', $ship_id);
        }
        $this->db->order_by('ships.name', 'asc');

        $ships = array();
        foreach ($this->db->get()->result() as $ship) {
            $ship->resources = $this->get_ships_cost_resources($ship->ship_id);
            $ship->buildings = $this->get_ships_cost_buildings($ship->ship_id);
            $ships[] = $ship;
        }

        return $ships;
    }

    public function get_my_ships($planet_id = 0) {
        $ships = array();
        foreach ($this->get_ships($planet_id) as $ship) {
            if (!$ship->qty) continue;
            $ships[] = $ship;
        }
        return $ships;
    }

    public function get_ship($planet_id = 0, $ship_id = 0) {
        $ships = $this->get_ships($planet_id, $ship_id);
        return (!empty($ships[0])) ? $ships[0] : array();
    }

    public function get_ships_cost_resources($ship_id = 0) {
        $this->db->select('*')
            ->from('ships_cost_resources')
            ->join('resources', 'resources.id = ships_cost_resources.resource_id')
            ->where('ships_cost_resources.ship_id', $ship_id)
            ->order_by('resources.name', 'asc');
        return $this->db->get()->result();
    }

    // ships_cost_buildings
    public function get_ships_cost_buildings($ship_id = 0) {
        $this->db->select('*')
            ->from('ships_cost_buildings')
            ->join('buildings', 'buildings.id = ships_cost_buildings.building_id')
            ->where('ships_cost_buildings.ship_id', $ship_id)
            ->order_by('buildings.name', 'asc');
        return $this->db->get()->result();
    }

    /**
     * @param int $planet_id
     * @param int $ship_id
     * @param int $qty
     * @return bool
     * @throws Exception
     */
    public function has_enough_resources($planet_id = 0, $ship_id = 0, $qty = 0) {
        $this->load->model('planet_model');

        // Get ship resources
        $requiredResources = array();
        $records = $this->get_ships_cost_resources($ship_id);
        foreach ($records as $record) {
            $resource_id = (int)$record->resource_id;
            $requiredResources[$resource_id] = (int)$record->amount * (int)$qty;
        }

	    // Get planet resources
        $records = $this->planet_model->get_resources($planet_id);
        foreach ($records as $record) {
            $resource_id = (int)$record->resource_id;
            if (isset($requiredResources[$resource_id]) && $requiredResources[$resource_id] > (int)$record->amount) {
                throw new Exception('Insufficient resource of ' . $record->name . '.');
            }
        }

        // Check required building levels
        $requiredLevels = array();
        $records = $this->get_ships_cost_buildings($ship_id);
        foreach ($records as $record) {
            $building_id = (int)$record->building_id;
            $requiredLevels[$building_id] = (int)$record->level;
        }

        // Get planet buildings
        $records = $this->planet_model->get_planet_buildings($planet_id);
        foreach ($records as $record) {
            $building_id = (int)$record->building_id;
            if (isset($requiredLevels[$building_id]) && $requiredLevels[$building_id] > (int)$record->level) {
                throw new Exception('Low ' . $record->name . ' level.');
            }
        }

	    return true;
    }

    /**
     * @param int $planet_id
     * @param int $ship_id
     * @param int $amount
     * @return object
     */
	public function set_building_process2($planet_id = 0, $ship_id = 0, $amount = 0) {
	    // delete old building processes
        $this->db->delete('ships_processes', array(
                'planet_id' => $planet_id,
                'ship_id' => $ship_id)
        );

        // check if building in progress...
        $res = $this->get_building_process2($planet_id, $ship_id);
        if (!$res) {
            $this->db->select('amount')
                ->from('ships_cost_time')
                ->where('ship_id', $ship_id);
            // Time in seconds per unit
            $buildingTime = (int)$this->db->get()->row('amount');
            // Time in seconds for all units
            $buildingTime *= $amount;

            $datetime = new DateTime();
            $datetime->add(new DateInterval('PT' . $buildingTime . 'S'));
            $finishes_on = $datetime->format('Y-m-d H:i:s');

            $data = array(
                'ship_id' => $ship_id,
                'planet_id' => $planet_id,
                'amount' => $amount,
                'finishes_on' => $finishes_on
            );
            $this->db->insert('ships_processes', $data);
        }

        return $this->get_building_process2($planet_id, $ship_id);
    }

    public function finish_building_process2($planet_id = 0, $ship_id = 0) {
        $result = $this->get_building_process2($planet_id, $ship_id);
        if ($result) {
            $amount = (int)$result->amount;

            // get old amount
            $this->db->select('amount')
                ->from('planet_ships')
                ->where('planet_id', $planet_id)
                ->where('ship_id', $ship_id);
            $amount += (int)$this->db->get()->row('amount');

            $this->db->where('planet_id', $planet_id);
            $this->db->where('ship_id', $ship_id);
            $this->db->update('planet_ships', array('amount' => $amount));

            // delete record from active process list
            $this->db->delete('ships_processes', array(
                    'planet_id' => $planet_id,
                    'ship_id' => $ship_id
                )
            );
        }
    }

    /**
     * @param int $planet_id
     * @param int $ship_id
     * @return object
     */
    public function get_building_process2($planet_id = 0, $ship_id = 0) {
        $this->db->select('*')
            ->from('ships_processes')
            ->where('planet_id', $planet_id)
            ->where('ship_id', $ship_id);
        return $this->db->get()->row();
    }
}









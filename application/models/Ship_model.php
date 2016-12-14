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
     */
	public function set_ships_process($planet_id = 0, $ship_id = 0, $amount = 0) {
	    $ary = $this->get_ship_in_process_data($planet_id);
        if (0 === $ary['ship_id']) {
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
    }

    public function get_ship_in_process_data($planet_id = 0) {
        $this->db->select('*')
            ->from('ships_processes')
            ->where_in('planet_id', $planet_id);

        $result = $this->db->get();
        $ship_id = ($result) ? (int)$result->row('ship_id') : 0;
        $amount = $qty = ($result) ? (int)$result->row('amount') : 0;
        $finishes_on = ($result) ? $result->row('finishes_on') : '';

        // check if already built
        if (0 < $ship_id) {
            $datetime1 = new DateTime($finishes_on);
            $datetime2 = new DateTime();
            // it is built - should add ship qty
            if ($datetime1->format('U') <= $datetime2->format('U')) {
                $this->db->select('amount')
                    ->from('planet_ships')
                    ->where('planet_id', $planet_id)
                    ->where('ship_id', $ship_id);
                $amount += (int)$this->db->get()->row('amount');

                $this->db->where('planet_id', $planet_id);
                $this->db->where('ship_id', $ship_id);
                $this->db->update('planet_ships', array('amount' => $amount));

                // delete record from active process list
                $this->db->delete('ships_processes', array('ship_id' => $ship_id, 'planet_id' => $planet_id));

                // decrease planet resources
                $usedResources = array();
                $records = $this->get_ships_cost_resources($ship_id);
                foreach ($records as $record) {
                    $resource_id = (int)$record->resource_id;
                    $usedResources[$resource_id] = (int)$record->amount * $qty;
                }

                foreach ($usedResources as $resource_id => $amount) {
                    if (empty($amount)) continue;

                    $this->db->select('amount')
                        ->from('planet_resources')
                        ->where('planet_id', $planet_id)
                        ->where('resource_id', $resource_id);
                    $amount = (int)$this->db->get()->row('amount') - $amount;

                    $this->db->where('planet_id', $planet_id);
                    $this->db->where('resource_id', $resource_id);
                    $this->db->update('planet_resources', array('amount' => $amount));
                }

                $ship_id = 0;
                $amount = 0;
                $finishes_on = '';
            }
        }

        return array(
            'ship_id' => $ship_id,
            'amount' => $amount,
            'finishes_on' => $finishes_on
        );
    }
}









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
     * @return mixed
     */
    public function get_planet($planet_id = 0) {
        $query = $this->db->get_where('planets', array('id' => $planet_id));
        return $query->row();
    }

    /**
     * @param int $planet_id
     * @return mixed
     */
    public function get_resources($planet_id = 0) {
        $this->add_income_per_hour($planet_id);

        $this->db->select('*');
        $this->db->from('planet_resources');
        $this->db->join('resources', 'resources.id = planet_resources.resource_id');
        $this->db->where('planet_resources.planet_id', $planet_id);
        $this->db->order_by('resources.name', 'asc');

        return $this->db->get()->result();
    }

    // Add income per hour to the planet's resources on each 2 minutes (1/30 of the income) e.g.
    // 3000 mineral/metal per hour
    private function add_income_per_hour($planet_id = 0) {
        $incomePerHour = 3000;

        // calc. time span between last update and now
        $this->db->select('updated_at');
        $this->db->from('planets');
        $this->db->where('id', $planet_id);
        $updated_at = $this->db->get()->row('updated_at');

        $datetime1 = new DateTime();
        $datetime2 = new DateTime($updated_at);
        $interval = $datetime1->diff($datetime2);
        $elapsedMinutes = (int)$interval->format('%i');

        if (2 > $elapsedMinutes) {
            return;
        }

        // get planet buildings levels
        $buildings = $this->get_planet_buildings($planet_id);
        foreach ($buildings as $building) {
            $building_id = (int)$building->building_id;
            $resource_id = $building_id;
            $level = (int)$building->level;

            // calc. new amount
            $amount = (int)($incomePerHour * $level / 60 * $elapsedMinutes);

            // get old amount
            $this->db->select('amount');
            $this->db->from('planet_resources');
            $this->db->where('planet_id', $planet_id);
            $this->db->where('resource_id', $resource_id);
            // sum new and old amount
            $amount += (int)$this->db->get()->row('amount');

            // set calculated amount
            $this->db->where('planet_id', $planet_id);
            $this->db->where('resource_id', $resource_id);
            $this->db->update('planet_resources', array('amount' => $amount));
        }

        // set updated time
        $this->db->where('id', $planet_id);
        $this->db->update('planets', array('updated_at' => date('Y-m-d H:i:s')));
    }

    public function get_planet_buildings($planet_id = 0) {
        $this->db->select('*, planet_buildings.id AS planet_building_id');
        $this->db->from('planet_buildings');
        $this->db->join('buildings', 'buildings.id = planet_buildings.building_id');
        $this->db->where('planet_buildings.planet_id', $planet_id);
        $this->db->order_by('buildings.name', 'asc');
        return $this->db->get()->result();
    }
}









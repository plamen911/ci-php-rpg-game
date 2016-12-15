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
     * @param int $planet_id
     * @return mixed
     */
    public function get_planets_w_distance($planet_id = 0) {
        // get my planet coordinates
        $this->load->model('planet_model');
        $planet = $this->planet_model->get_planet($planet_id);
        $x = $planet->x;
        $y = $planet->y;

        $data = array();
        $planets = $this->planet_model->get_planets();
        foreach ($planets as $planet) {
            $distance = $this->calc_distance($x, $y, $planet->x, $planet->y);
            $planet->distance = $distance;
            $data[] = $planet;
        }

        return $data;
    }

    private function calc_distance($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0) {
        $width = abs((int)$x2 - $x1);
        $height = abs((int)$y2 - $y1);
        return ceil(sqrt(pow($width, 2) + pow($height, 2)));
    }

    /**
     * @param int $attacker_planet_id
     * @param int $defender_planet_id
     * @param array $flight_ships
     * @return int
     */
    public function create_flight($attacker_planet_id = 0, $defender_planet_id = 0, $flight_ships = array()) {
        $this->delete_old_flights($attacker_planet_id);

        // calc. distance
        $this->load->model('planet_model');
        $attacker_planet = $this->planet_model->get_planet($attacker_planet_id);
        $defender_planet = $this->planet_model->get_planet($defender_planet_id);
        $distance = $this->calc_distance($attacker_planet->x, $attacker_planet->y, $defender_planet->x, $defender_planet->y);

        // fake speed = 4
        $journey_time = (int)ceil($distance / 4);

        $datetime = new DateTime();
        $datetime->add(new DateInterval('PT' . $journey_time . 'S'));
        $impact_on = $arriving_on = $datetime->format('Y-m-d H:i:s');

        $data = array(
            'attacker_planet_id' => $attacker_planet_id,
            'defender_planet_id' => $defender_planet_id,
            'impact_on' => $impact_on,
            'arriving_on' => $arriving_on
        );
        $this->db->insert('flights', $data);
        $flight_id = (int)$this->db->insert_id();

        foreach ($flight_ships as $ship_id => $amount) {
            $data = array(
                'flight_id' => $flight_id,
                'ship_id' => $ship_id,
                'amount' => $amount
            );
            $this->db->insert('flight_ships', $data);
        }

        // delete old messages
        $this->db->delete('messages', array(
            'attacker_planet_id' => $attacker_planet_id,
            'defender_planet_id' => $defender_planet_id
        ));

        // post new message
        $data = array(
            'attacker_planet_id' => $attacker_planet_id,
            'defender_planet_id' => $defender_planet_id,
            'message_type' => 'who-is-attacking-and-time-remaining-until-impact',
            'expires_on' => $impact_on,
        );
        $this->db->insert('messages', $data);

        return $flight_id;
    }

    public function has_flights($attacker_planet_id = 0) {
        $this->delete_old_flights($attacker_planet_id);

        $this->db->select('defender_planet_id')
            ->from('flights')
            ->where('attacker_planet_id', $attacker_planet_id);

        return (int)$this->db->get()->row('defender_planet_id');
    }

    private function delete_old_flights($attacker_planet_id = 0) {
        $this->db->select('*')
            ->from('flights')
            ->where('attacker_planet_id', $attacker_planet_id)
            ->where('impact_on <', date('Y-m-d H:i:s'));
        foreach ($this->db->get()->result() as $flight) {
            $this->db->delete('flight_ships', array('flight_id' => $flight->id));
        }
        $this->db->delete('flights', array(
            'attacker_planet_id' => $attacker_planet_id
        ));
    }

    public function get_flight($flight_id = 0)
    {
        $this->db->from('flights');
        $this->db->where('id', $flight_id);
        return $this->db->get()->row();
    }

}









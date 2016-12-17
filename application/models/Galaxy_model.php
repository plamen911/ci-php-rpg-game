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

        $this->load->model('message_model');
        $impact_on = $arriving_on = $this->get_journey_time($attacker_planet_id, $defender_planet_id);

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
        $this->message_model->gelete_old_messages($attacker_planet_id, $defender_planet_id);

        // post new message
        $data = array(
            'attacker_planet_id' => $attacker_planet_id,
            'defender_planet_id' => $defender_planet_id,
            'message_type' => 'who-is-attacking-and-time-remaining-until-impact',
            'expires_on' => $impact_on,
        );
        // $this->db->insert('messages', $data);
        $this->message_model->set_message($data);

        return $flight_id;
    }

    public function get_journey_time($attacker_planet_id, $defender_planet_id) {
        $this->load->model('planet_model');

        // calc. distance
        $attacker_planet = $this->planet_model->get_planet($attacker_planet_id);
        $defender_planet = $this->planet_model->get_planet($defender_planet_id);
        $distance = $this->calc_distance($attacker_planet->x, $attacker_planet->y, $defender_planet->x, $defender_planet->y);

        // fake speed = 4
        $journey_time = (int)ceil($distance / 4);

        $datetime = new DateTime();
        $datetime->add(new DateInterval('PT' . $journey_time . 'S'));

        return $arriving_on = $datetime->format('Y-m-d H:i:s');
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

    public function get_battle_data($flight_id = 0, $data = null) {
        $this->load->model('player_model');
        $this->load->model('planet_model');
        $this->load->model('ship_model');

        $flight_id = (int)$flight_id;
        if (!$data) {
            $data = new stdClass();
        }

        $flight = $this->get_flight($flight_id);
        if (empty($flight)) {
            throw new Exception('You must select planet to attack.');
        }

        $data->flight_id = $flight_id;
        $data->flight = $flight;

        $attacker_planet_id = $flight->attacker_planet_id;
        $defender_planet_id = $flight->defender_planet_id;

        $attacker_player_id = $this->player_model->get_player_id_from_planet_id($attacker_planet_id);
        $defender_player_id = $this->player_model->get_player_id_from_planet_id($defender_planet_id);

        $data->attacker_player_id = $attacker_player_id;
        $data->defender_player_id = $defender_player_id;

        $data->attacker_planet_id = $attacker_planet_id;
        $data->defender_planet_id = $defender_planet_id;

        $data->attacker_player = $this->player_model->get_player($attacker_player_id);
        $data->defender_player = $this->player_model->get_player($defender_player_id);

        $data->attacker_planet = $this->planet_model->get_planet($attacker_planet_id);
        $data->defender_planet = $this->planet_model->get_planet($defender_planet_id);

        $data->attacker_ships = $this->ship_model->get_my_ships($attacker_planet_id);
        $data->defender_ships = $this->ship_model->get_my_ships($defender_planet_id);

        $data->attacker_damage = $this->get_total_damage($data->attacker_ships);
        $data->defender_damage = $this->get_total_damage($data->defender_ships);

        return $data;
    }

    /**
     * @param int $flight_id
     * @param null $data
     * @return null|stdClass
     * @throws Exception
     */
    public function battle_report($flight_id = 0, $data = null)
    {
        $this->load->model('message_model');

        $data = $this->get_battle_data($flight_id, $data);

        $attacker_planet_id = $data->attacker_planet_id;
        $defender_planet_id = $data->defender_planet_id;

        // calc. damage factor
        $attacker_ships_left = 0;
        $defender_ships_left = 0;
        $stats = array();
        if ($data->attacker_damage > $data->defender_damage) {
            $stats[] = 'Attacker ' . $data->attacker_player->username . ' won the battle, defender ' . $data->defender_player->username . ' lost.';
            $attacker_ships_left = $this->get_damage_factor($data->attacker_damage, $data->defender_damage);
        } elseif ($data->attacker_damage < $data->defender_damage) {
            $stats[] = 'Defender ' . $data->defender_player->username . ' won the battle, attacker ' . $data->attacker_player->username . ' lost.';
            $defender_ships_left = $this->get_damage_factor($data->attacker_damage, $data->defender_damage);
        } else {
            $stats[] = 'The battle is draw! No winner between ' . $data->attacker_player->username . ' and ' . $data->defender_player->username . '.';
        }

        $this->update_ships_left($attacker_planet_id, $attacker_ships_left);
        $this->update_ships_left($defender_planet_id, $defender_ships_left);

        $attacker_ships_left = $this->get_ships_left($attacker_planet_id);
        $defender_ships_left = $this->get_ships_left($defender_planet_id);

        $stats[] = 'Units left after the battle: ';
        $stats[] = $data->attacker_player->username . ': ' . $attacker_ships_left;
        $stats[] = $data->defender_player->username . ': ' . $defender_ships_left;

        $data->stats = $stats;


        $this->session->set_userdata('battle_message_report', implode(' ', $stats));



        // delete old messages
        $this->message_model->gelete_old_messages($attacker_planet_id, $defender_planet_id);



        // post new message
        $message_data = array(
            'attacker_planet_id' => $attacker_planet_id,
            'defender_planet_id' => $defender_planet_id,
            'message_type' => implode(' ', $stats),
            'expires_on' => date('Y-m-d H:i:s', strtotime('+10 seconds')),
        );
        $this->message_model->set_message($message_data);

        return $data;
    }

    private function update_ships_left($planet_id = 0, $ships_left = 0) {
        $this->db
            ->select('*')
            ->from('planet_ships')
            ->where('planet_id', $planet_id);
        foreach ($this->db->get()->result() as $ship) {
            $amount = ceil($ship->amount * $ships_left);

            $this->db->where('planet_id', $planet_id);
            $this->db->where('ship_id', $ship->ship_id);
            $this->db->update('planet_ships', array('amount' => $amount));
        }
    }

    private function get_ships_left($planet_id = 0) {
        $this->load->model('ship_model');
        $message = '';
        $ships = $this->ship_model->get_my_ships($planet_id);
        if (!empty($ships)) {
            $ary = array();
            foreach ($ships as $ship) {
                $ary[] = $ship->name . ' (' . $ship->qty . ')';
            }
            $message .= implode(', ', $ary) . '.';
        } else {
            $message .= 'No ships left.';
        }

        return $message;
    }

    /**
     * @param array $ships
     * @return int
     */
    public function get_total_damage($ships = array()) {
        $damage = 0;
        if (!empty($ships)) {
            foreach ($ships as $ship) {
                $damage += (int)$ship->damage * (int)$ship->qty;
            }
        }
        return $damage;
    }

    private function get_damage_factor($attacker_damage = 0, $defender_damage = 0) {
        if ($attacker_damage > $defender_damage) {
            $bigger = $attacker_damage;
            $lower = $defender_damage;
        } else {
            $bigger = $defender_damage;
            $lower = $attacker_damage;
        }
        return (100 - floor($lower / $bigger * 100)) / 100;
    }

    public function delete_flight($flight_id = 0) {
        /*$this->db->delete('flights', array(
            'id' => (int)$flight_id
        ));*/
    }
}









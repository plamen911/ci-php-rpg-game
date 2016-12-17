<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Message_model class.
 * 
 * @extends CI_Model
 */
class Message_model extends CI_Model {

	/**
	 * __construct function.
	 * 
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

    public function get_messages($planet_id = 0)
    {
        $planet_id = (int)$planet_id;
        $this->load->model('player_model');
        $this->load->model('planet_model');

        $messages = array();
        $this->db
            ->select('*')
            ->from('messages')
            ->where('(attacker_planet_id = ' . $planet_id . ' OR defender_planet_id = ' . $planet_id . ')')
            ->where('expires_on >=', date('Y-m-d H:i:s'));
        foreach ($this->db->get()->result() as $message) {
            $attacker_player_id = $this->player_model->get_player_id_from_planet_id($message->attacker_planet_id);
            $defender_player_id = $this->player_model->get_player_id_from_planet_id($message->defender_planet_id);

            $message->attacker_player = $this->player_model->get_player($attacker_player_id);
            $message->defender_player = $this->player_model->get_player($defender_player_id);

            $message->attacker_planet = $this->planet_model->get_planet($message->attacker_planet_id);
            $message->defender_planet = $this->planet_model->get_planet($message->defender_planet_id);

            $datetime1 = new DateTime($message->expires_on);
            $datetime2 = new DateTime();
            $interval = $datetime1->diff($datetime2);
            $elapsedTime = $interval->format('%H:%I:%S');

            if ($planet_id === (int)$message->attacker_planet_id) {
                $attacker_abbr = '(me)';
                $defender_abbr = '';
            } else {
                $attacker_abbr = '';
                $defender_abbr = '(me)';
            }

            $message_text = '';
            switch ($message->message_type) {
                case 'who-is-attacking-and-time-remaining-until-impact':
                    $message_text .= $message->attacker_player->username . $attacker_abbr . ' is attacking ' .
                        $message->defender_player->username . $defender_abbr .
                        ' from ' . get_planet_name($message->attacker_planet) .
                        ' to ' . get_planet_name($message->defender_planet) . '.' .
                        ' Time remaining until the impact: ' . $elapsedTime . '.';
                    break;

                case 'battle-in-progress-and-time-remaining-until-end':
                    $message_text .= $message->attacker_player->username . '\'s ' . $attacker_abbr .
                        ' army reached ' . $message->defender_player->username . '\'s ' . $defender_abbr .
                        ' ' . get_planet_name($message->defender_planet) . ' and battle is happening right now. ' .
                        'Time remaining until the end: ' . $elapsedTime . '.';
                    break;

                default:
                    $message_text = $message->message_type;
                    break;
            }
            $message->message_text = $message_text;

            $messages[] = $message_text;
        }

        return $messages;
    }

    public function set_message($data = array()) {
        // check if message exists
        $this->db
            ->select('id')
            ->from('messages')
            ->where('attacker_planet_id', $data['attacker_planet_id'])
            ->where('defender_planet_id', $data['defender_planet_id'])
            ->where('message_type', $data['message_type']);
        $message_id = (int)$this->db->get()->row('id');
        // update time only
        if ($message_id) {
            $this->db->where('id', $message_id);
            $this->db->update('messages', array('expires_on' => $data['expires_on']));
        } else {
            // new message
            $this->db->insert('messages', $data);
            $message_id = $this->db->insert_id();
        }
        return $message_id;
    }

    // delete old messages
    public function gelete_old_messages($attacker_planet_id = 0, $defender_planet_id = 0) {
        $this->db->delete('messages', array(
            'attacker_planet_id' => $attacker_planet_id,
            'defender_planet_id' => $defender_planet_id
        ));
    }
}









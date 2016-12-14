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
    public function get_players($planet_id = 0) {
        // get my planet coordinates
        $this->db->select('x, y')
            ->from('planets')
            ->where('id', $planet_id);
        $result = $this->db->get();
        $x = (int)$result->row('x');
        $y = (int)$result->row('y');

        $data = array();
        $this->db->from('players')
            ->join('planets', 'planets.player_id = players.id')
            ->order_by('players.username', 'asc');
        foreach ($this->db->get()->result() as $result) {
            $distance = $this->calc_distance($x, $y, $result->x, $result->y);
            $result->distance = $distance;
            $data[] = $result;
        }

        return $data;
    }

    private function calc_distance($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0) {
        $width = abs((int)$x2 - $x1);
        $height = abs((int)$y2 - $y1);
        return ceil(sqrt(pow($width, 2) + pow($height, 2)));
    }

    //public function get

}









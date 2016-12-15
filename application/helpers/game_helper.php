<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_planet_name'))
{
    function get_planet_name($planet = null)
    {
        if (empty($planet)) return '';
        return 'Planet#' . sprintf('%06d', $planet->planet_id) . '@[' . $planet->x . ':' . $planet->y . ']';
    }
}
if ( ! function_exists('is_post_request'))
{
    function is_post_request()
    {
        return 'POST' === strtoupper($_SERVER['REQUEST_METHOD']);
    }
}

/* End of file game_helper.php */
/* Location: ./application/helpers/game_helper.php */
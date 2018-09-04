<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    function get_by_gps($pos){
        return $this->db->query("SELECT * FROM areas WHERE CONTAINS(bounds,GeomFromText('Point($pos->lng $pos->lat)'))
            ORDER BY level desc LIMIT 0,1")->row();
    }
}
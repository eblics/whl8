<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jokes_model extends CI_Model {

    public function get_joke(){
        $sql="SELECT * FROM `jokes` AS t1 JOIN (
                    SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `jokes`)-(SELECT MIN(id) FROM `jokes`))+(SELECT MIN(id) FROM `jokes`)) AS id
                ) AS t2   
                WHERE t1.id >= t2.id
                ORDER BY t1.id LIMIT 1";
        return $this->db->query($sql)->row();
    }
}

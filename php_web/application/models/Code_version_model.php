<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Code_version_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    function get_by_version($version){
        return $this->db->where('versionNum',$version)->get('code_version')->row();
    }
}

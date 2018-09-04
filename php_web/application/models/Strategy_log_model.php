<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Strategy_log_model extends CI_Model {

	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    function add($type,$opration,$data){
		$saveData=['type'=>$type,'opration'=>$opration,'data'=>$data,'theTime'=>time()];
		return $this->db->insert('strategy_log',$saveData);
    }
	
	
}

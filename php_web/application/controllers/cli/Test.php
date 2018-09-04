<?php
class Test extends CI_Controller {

	/**
	 * 测试php调用mysql存储过程
	 * @return void
	 */
	public function exec_procedure() {
		$params = [1, 2];
		$result = $this->db->query("CALL m_procedure(?, ?)", $params)->result();
		var_dump($result);
	}

}
<?php
class Version extends MerchantController {

	public function index() {
		print '<script>alert("当前版本：'. config_item('hls_version') .'")</script>';
	}
}
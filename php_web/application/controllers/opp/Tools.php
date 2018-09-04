<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends OppController {

	public function index() {
		$this->load->view('tools');
	}

	public function deploy() {
		if (isDev()) {
			$this->load->view('tools/deploy.html');
		} else {
			show_404();			
		}
	}

	public function api($apiName = NULL) {
		if (! isDev()) {
			$this->ajaxResponseFail('403 Forbidden', 403, 403);
			return;
		}
		if (! isset($apiName)) {
			$this->ajaxResponseFail('403 Forbidden', 403, 403);
		} else if ($apiName === 'deploy.test') {
			$resp = exec('./svn_hook.sh');
			$this->ajaxResponseSuccess($resp);
		} else {
			$this->ajaxResponseFail('404 Not Found', 404, 404);
		}
	}

}
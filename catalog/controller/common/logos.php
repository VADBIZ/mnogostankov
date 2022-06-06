<?php
class ControllerCommonLogos extends Controller {
	public function index() {
		$this->response->setOutput($this->load->view('common/logos'));
	}
}
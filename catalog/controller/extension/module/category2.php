<?php
class ControllerExtensionModuleCategory2 extends Controller {
	public function index() {
		$this->load->language('extension/module/category2');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}
      
        if (isset($parts[2])) {
			$data['child_id2'] = $parts[2];
		} else {
			$data['child_id2'] = 0;
		}

		

		return $this->load->view('extension/module/category2', $data);
	}
}
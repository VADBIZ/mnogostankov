<?php
class ControllerExtensionModuleTabpro extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/tabpro');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('tabpro', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->cache->delete('product');

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['width1'])) {
			$data['error_width1'] = $this->error['width1'];
		} else {
			$data['error_width1'] = '';
		}

		if (isset($this->error['height1'])) {
			$data['error_height1'] = $this->error['height1'];
		} else {
			$data['error_height1'] = '';
		}
      
        if (isset($this->error['width2'])) {
			$data['error_width2'] = $this->error['width2'];
		} else {
			$data['error_width2'] = '';
		}

		if (isset($this->error['height2'])) {
			$data['error_height2'] = $this->error['height2'];
		} else {
			$data['error_height2'] = '';
		}
      
        if (isset($this->error['width3'])) {
			$data['error_width3'] = $this->error['width3'];
		} else {
			$data['error_width3'] = '';
		}

		if (isset($this->error['height3'])) {
			$data['error_height3'] = $this->error['height3'];
		} else {
			$data['error_height3'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/tabpro', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/tabpro', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/tabpro', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/tabpro', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
      
        if (isset($this->request->post['top1'])) {
			$data['top1'] = $this->request->post['top1'];
		} elseif (!empty($module_info)) {
			$data['top1'] = $module_info['top1'];
		} else {
			$data['top1'] = '';
		}
      
        if (isset($this->request->post['top2'])) {
			$data['top2'] = $this->request->post['top2'];
		} elseif (!empty($module_info)) {
			$data['top2'] = $module_info['top2'];
		} else {
			$data['top2'] = '';
		}
      
        if (isset($this->request->post['top3'])) {
			$data['top3'] = $this->request->post['top3'];
		} elseif (!empty($module_info)) {
			$data['top3'] = $module_info['top3'];
		} else {
			$data['top3'] = '';
		}

		if (isset($this->request->post['limit1'])) {
			$data['limit1'] = $this->request->post['limit1'];
		} elseif (!empty($module_info)) {
			$data['limit1'] = $module_info['limit1'];
		} else {
			$data['limit1'] = 5;
		}

		if (isset($this->request->post['width1'])) {
			$data['width1'] = $this->request->post['width1'];
		} elseif (!empty($module_info)) {
			$data['width1'] = $module_info['width1'];
		} else {
			$data['width1'] = 200;
		}

		if (isset($this->request->post['height1'])) {
			$data['height1'] = $this->request->post['height1'];
		} elseif (!empty($module_info)) {
			$data['height1'] = $module_info['height1'];
		} else {
			$data['height1'] = 200;
		}
      
        // 2
        if (isset($this->request->post['limit2'])) {
			$data['limit2'] = $this->request->post['limit2'];
		} elseif (!empty($module_info)) {
			$data['limit2'] = $module_info['limit2'];
		} else {
			$data['limit2'] = 5;
		}

		if (isset($this->request->post['width2'])) {
			$data['width2'] = $this->request->post['width2'];
		} elseif (!empty($module_info)) {
			$data['width2'] = $module_info['width2'];
		} else {
			$data['width2'] = 200;
		}

		if (isset($this->request->post['height2'])) {
			$data['height2'] = $this->request->post['height2'];
		} elseif (!empty($module_info)) {
			$data['height2'] = $module_info['height2'];
		} else {
			$data['height2'] = 200;
		}
      
        // 3
        if (isset($this->request->post['limit3'])) {
			$data['limit3'] = $this->request->post['limit3'];
		} elseif (!empty($module_info)) {
			$data['limit3'] = $module_info['limit3'];
		} else {
			$data['limit3'] = 5;
		}

		if (isset($this->request->post['width3'])) {
			$data['width3'] = $this->request->post['width3'];
		} elseif (!empty($module_info)) {
			$data['width3'] = $module_info['width3'];
		} else {
			$data['width3'] = 200;
		}

		if (isset($this->request->post['height3'])) {
			$data['height3'] = $this->request->post['height3'];
		} elseif (!empty($module_info)) {
			$data['height3'] = $module_info['height3'];
		} else {
			$data['height3'] = 200;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tabpro', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tabpro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width1']) {
			$this->error['width1'] = $this->language->get('error_width1');
		}

		if (!$this->request->post['height1']) {
			$this->error['height1'] = $this->language->get('error_height1');
		}
      
        if (!$this->request->post['width2']) {
			$this->error['width2'] = $this->language->get('error_width2');
		}

		if (!$this->request->post['height2']) {
			$this->error['height2'] = $this->language->get('error_height2');
		}
      
        if (!$this->request->post['width3']) {
			$this->error['width3'] = $this->language->get('error_width3');
		}

		if (!$this->request->post['height3']) {
			$this->error['height3'] = $this->language->get('error_height3');
		}

		return !$this->error;
	}
}
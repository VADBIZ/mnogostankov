<?php
class ControllerExtensionDashboardTool extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/dashboard/tool');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_tool', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dashboard/tool', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/tool', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_tool_width'])) {
			$data['dashboard_tool_width'] = $this->request->post['dashboard_tool_width'];
		} else {
			$data['dashboard_tool_width'] = $this->config->get('dashboard_tool_width');
		}

		$data['columns'] = array();

		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}

		if (isset($this->request->post['dashboard_tool_status'])) {
			$data['dashboard_tool_status'] = $this->request->post['dashboard_tool_status'];
		} else {
			$data['dashboard_tool_status'] = $this->config->get('dashboard_tool_status');
		}

		if (isset($this->request->post['dashboard_tool_sort_order'])) {
			$data['dashboard_tool_sort_order'] = $this->request->post['dashboard_tool_sort_order'];
		} else {
			$data['dashboard_tool_sort_order'] = $this->config->get('dashboard_tool_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/tool_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/tool')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function dashboard() {
		$this->load->language('extension/dashboard/tool');

		$data['user_token'] = $this->session->data['user_token'];

		$data['links'] = array(
		    ['name' => 'Отображение брендов и товаров', 'href' => $this->url->link('extension/module/box', 'user_token=' . $this->session->data['user_token'], true)],
        );

		return $this->load->view('extension/dashboard/tool_info', $data);
	}
}

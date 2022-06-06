<?php
class ControllerExtensionModuleOptionsCategory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/options_category');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['version'] = '3.1.1';

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') and $this->validate()) {
			$this->model_setting_setting->editSetting('module_options_category', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			if ($this->request->post['module_options_category_apply']) {
				$this->response->redirect($this->url->link('extension/module/options_category', 'user_token=' . $this->session->data['user_token'], true));
			}

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
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

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/options_category', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/options_category', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		// General
		if (isset($this->request->post['module_options_category_autoselect'])) {
			$data['module_options_category_autoselect'] = $this->request->post['module_options_category_autoselect'];
		} else {
			$data['module_options_category_autoselect'] = $this->config->get('module_options_category_autoselect');
		}
		
		if (isset($this->request->post['module_options_category_show_no_stock'])) {
			$data['module_options_category_show_no_stock'] = $this->request->post['module_options_category_show_no_stock'];
		} else {
			$data['module_options_category_show_no_stock'] = $this->config->get('module_options_category_show_no_stock');
		}
		
		if (isset($this->request->post['module_options_category_no_stock_disabled'])) {
			$data['module_options_category_no_stock_disabled'] = $this->request->post['module_options_category_no_stock_disabled'];
		} else {
			$data['module_options_category_no_stock_disabled'] = $this->config->get('module_options_category_no_stock_disabled');
		}
		
		if (isset($this->request->post['module_options_category_datetimepicker'])) {
			$data['module_options_category_datetimepicker'] = $this->request->post['module_options_category_datetimepicker'];
		} else {
			$data['module_options_category_datetimepicker'] = $this->config->get('module_options_category_datetimepicker');
		}
		
		if (isset($this->request->post['module_options_category_special_mode'])) {
			$data['module_options_category_special_mode'] = $this->request->post['module_options_category_special_mode'];
		} else {
			$data['module_options_category_special_mode'] = $this->config->get('module_options_category_special_mode');
		}
		
		if (isset($this->request->post['module_options_category_image_select'])) {
			$data['module_options_category_image_select'] = $this->request->post['module_options_category_image_select'];
		} else {
			$data['module_options_category_image_select'] = $this->config->get('module_options_category_image_select');
		}
		
		// View
		if (isset($this->request->post['module_options_category_theme'])) {
			$data['module_options_category_theme'] = $this->request->post['module_options_category_theme'];
		} else {
			$data['module_options_category_theme'] = $this->config->get('module_options_category_theme');
		}
		
		if (isset($this->request->post['module_options_category_quantity'])) {
			$data['module_options_category_quantity'] = $this->request->post['module_options_category_quantity'];
		} else {
			$data['module_options_category_quantity'] = $this->config->get('module_options_category_quantity');
		}
		
		if (isset($this->request->post['module_options_category_quantity_product'])) {
			$data['module_options_category_quantity_product'] = $this->request->post['module_options_category_quantity_product'];
		} else {
			$data['module_options_category_quantity_product'] = $this->config->get('module_options_category_quantity_product');
		}
		
		if (isset($this->request->post['module_options_category_position'])) {
			$data['module_options_category_position'] = $this->request->post['module_options_category_position'];
		} else {
			$data['module_options_category_position'] = $this->config->get('module_options_category_position');
		}
		
		if (isset($this->request->post['module_options_category_name_tooltip'])) {
			$data['module_options_category_name_tooltip'] = $this->request->post['module_options_category_name_tooltip'];
		} else {
			$data['module_options_category_name_tooltip'] = $this->config->get('module_options_category_name_tooltip');
		}
		
		if (isset($this->request->post['module_options_category_image'])) {
			$data['module_options_category_image'] = $this->request->post['module_options_category_image'];
		} else {
			$data['module_options_category_image'] = $this->config->get('module_options_category_image');
		}
		
		if (isset($this->request->post['module_options_category_image_width'])) {
			$data['module_options_category_image_width'] = $this->request->post['module_options_category_image_width'];
		} elseif ($this->config->get('module_options_category_image_width')) {
			$data['module_options_category_image_width'] = $this->config->get('module_options_category_image_width');
		} else {
			$data['module_options_category_image_width'] = 20;
		}
		
		if (isset($this->request->post['module_options_category_image_height'])) {
			$data['module_options_category_image_height'] = $this->request->post['module_options_category_image_height'];
		} elseif ($this->config->get('module_options_category_image_height')) {
			$data['module_options_category_image_height'] = $this->config->get('module_options_category_image_height');
		} else {
			$data['module_options_category_image_height'] = 20;
		}
		
		if (isset($this->request->post['module_options_category_image_popup'])) {
			$data['module_options_category_image_popup'] = $this->request->post['module_options_category_image_popup'];
		} else {
			$data['module_options_category_image_popup'] = $this->config->get('module_options_category_image_popup');
		}
		
		if (isset($this->request->post['module_options_category_image_popup_width'])) {
			$data['module_options_category_image_popup_width'] = $this->request->post['module_options_category_image_popup_width'];
		} elseif ($this->config->get('module_options_category_image_popup_width')) {
			$data['module_options_category_image_popup_width'] = $this->config->get('module_options_category_image_popup_width');
		} else {
			$data['module_options_category_image_popup_width'] = 150;
		}
		
		if (isset($this->request->post['module_options_category_image_popup_height'])) {
			$data['module_options_category_image_popup_height'] = $this->request->post['module_options_category_image_popup_height'];
		} elseif ($this->config->get('module_options_category_image_popup_height')) {
			$data['module_options_category_image_popup_height'] = $this->config->get('module_options_category_image_popup_height');
		} else {
			$data['module_options_category_image_popup_height'] = 150;
		}
		
		if (isset($this->request->post['module_options_category_sku'])) {
			$data['module_options_category_sku'] = $this->request->post['module_options_category_sku'];
		} else {
			$data['module_options_category_sku'] = $this->config->get('module_options_category_sku');
		}
		
		if (isset($this->request->post['module_options_category_show_quantity'])) {
			$data['module_options_category_show_quantity'] = $this->request->post['module_options_category_show_quantity'];
		} else {
			$data['module_options_category_show_quantity'] = $this->config->get('module_options_category_show_quantity');
		}
		
		if (isset($this->request->post['module_options_category_show_price'])) {
			$data['module_options_category_show_price'] = $this->request->post['module_options_category_show_price'];
		} else {
			$data['module_options_category_show_price'] = $this->config->get('module_options_category_show_price');
		}
		
		if (isset($this->request->post['module_options_category_points'])) {
			$data['module_options_category_points'] = $this->request->post['module_options_category_points'];
		} else {
			$data['module_options_category_points'] = $this->config->get('module_options_category_points');
		}
		
		if (isset($this->request->post['module_options_category_weight'])) {
			$data['module_options_category_weight'] = $this->request->post['module_options_category_weight'];
		} else {
			$data['module_options_category_weight'] = $this->config->get('module_options_category_weight');
		}
		
		// Show
		$data['options'] = array();
		
		$this->load->model('extension/module/options_category');
		$this->load->model('catalog/option');

		$results = $this->model_extension_module_options_category->getOptions();

		foreach ($results as $result) {
			$data['options'][] = array(
				'option_id'     => $result['option_id'],
				'name'          => $result['name'],
				'type'          => $result['type'],
				'option_values' => $this->model_catalog_option->getOptionValues($result['option_id'])
			);
		}
		
		if (isset($this->request->post['module_options_category_show_mode'])) {
			$data['module_options_category_show_mode'] = $this->request->post['module_options_category_show_mode'];
		} else {
			$data['module_options_category_show_mode'] = $this->config->get('module_options_category_show_mode');
		}
		
		if (isset($this->request->post['module_options_category_show'])) {
			$data['module_options_category_show'] = $this->request->post['module_options_category_show'];
		} elseif ($this->config->get('module_options_category_show')) {
			$data['module_options_category_show'] = $this->config->get('module_options_category_show');
		} else {
			$data['module_options_category_show'] = array();
		}
		
		if (isset($this->request->post['module_options_category_value_show_mode'])) {
			$data['module_options_category_value_show_mode'] = $this->request->post['module_options_category_value_show_mode'];
		} else {
			$data['module_options_category_value_show_mode'] = $this->config->get('module_options_category_value_show_mode');
		}
		
		if (isset($this->request->post['module_options_category_value_show'])) {
			$data['module_options_category_value_show'] = $this->request->post['module_options_category_value_show'];
		} elseif ($this->config->get('module_options_category_value_show')) {
			$data['module_options_category_value_show'] = $this->config->get('module_options_category_value_show');
		} else {
			$data['module_options_category_value_show'] = array();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/options_category', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/options_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function install() {
		$this->load->model('extension/module/options_category');
		$this->model_extension_module_options_category->createColumns();
	}
}
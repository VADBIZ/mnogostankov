<?php

class ControllerExtensionPaymentSchet extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/payment/schet');
		$this->document->setTitle($this->language->get('page_title'));

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('payment_schet', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['error_warning'] = '';
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}

		$data['success'] = '';
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data['error_inn'] = '';
		if (isset($this->error['inn'])) {
			$data['error_inn'] = $this->error['inn'];
		}

		$data['error_account'] = '';
		if (isset($this->error['account'])) {
			$data['error_account'] = $this->error['account'];
		}

		$data['error_bik'] = '';
		if (isset($this->error['bik'])) {
			$data['error_bik'] = $this->error['bik'];
		}

		$data['error_corschet'] = '';
		if (isset($this->error['corschet'])) {
			$data['error_corschet'] = $this->error['corschet'];
		}

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		$data['breadcrumbs'][] = array('text' => $this->language->get('text_payment'), 'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		$data['breadcrumbs'][] = array('text' => $this->language->get('page_title'), 'href' => $this->url->link('extension/payment/schet', 'user_token=' . $this->session->data['user_token'], true));
		$data['action'] = $this->url->link('extension/payment/schet', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		foreach ($languages as $language) {
			$data['error_supplier' . $language['language_id']] = '';
			if (isset($this->error['supplier' . $language['language_id']])) {
				$data['error_supplier' . $language['language_id']] = $this->error['supplier' . $language['language_id']];
			}

			$data['error_director' . $language['language_id']] = '';
			if (isset($this->error['director' . $language['language_id']])) {
				$data['error_director' . $language['language_id']] = $this->error['director' . $language['language_id']];
			}

			$data['error_bank_supplier' . $language['language_id']] = '';
			if (isset($this->error['bank_supplier' . $language['language_id']])) {
				$data['error_bank_supplier' . $language['language_id']] = $this->error['bank_supplier' . $language['language_id']];
			}

			$data['payment_schet_supplier' . $language['language_id']] = $this->config->get('payment_schet_supplier' . $language['language_id']);
			$data['payment_schet_director' . $language['language_id']] = $this->config->get('payment_schet_director' . $language['language_id']);
			$data['payment_schet_accountant' . $language['language_id']] = $this->config->get('payment_schet_accountant' . $language['language_id']);
			$data['payment_schet_legal_address' . $language['language_id']] = $this->config->get('payment_schet_legal_address' . $language['language_id']);
			$data['payment_schet_actual_address' . $language['language_id']] = $this->config->get('payment_schet_actual_address' . $language['language_id']);
			$data['payment_schet_bank_supplier' . $language['language_id']] = $this->config->get('payment_schet_bank_supplier' . $language['language_id']);
			$data['payment_schet_description' . $language['language_id']] = $this->config->get('payment_schet_description' . $language['language_id']);
			$data['payment_schet_instruction' . $language['language_id']] = $this->config->get('payment_schet_instruction' . $language['language_id']);

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$data['payment_schet_supplier' . $language['language_id']] = $this->request->post['payment_schet_supplier' . $language['language_id']];
				$data['payment_schet_director' . $language['language_id']] = $this->request->post['payment_schet_director' . $language['language_id']];
				$data['payment_schet_accountant' . $language['language_id']] = $this->request->post['payment_schet_accountant' . $language['language_id']];
				$data['payment_schet_legal_address' . $language['language_id']] = $this->request->post['payment_schet_legal_address' . $language['language_id']];
				$data['payment_schet_actual_address' . $language['language_id']] = $this->request->post['payment_schet_actual_address' . $language['language_id']];
				$data['payment_schet_bank_supplier' . $language['language_id']] = $this->request->post['payment_schet_bank_supplier' . $language['language_id']];
				$data['payment_schet_description' . $language['language_id']] = $this->request->post['payment_schet_description' . $language['language_id']];
				$data['payment_schet_instruction' . $language['language_id']] = $this->request->post['payment_schet_instruction' . $language['language_id']];
			}
		}

		$data['payment_schet_inn'] = $this->config->get('payment_schet_inn');
		$data['payment_schet_kpp'] = $this->config->get('payment_schet_kpp');
		$data['payment_schet_account'] = $this->config->get('payment_schet_account');
		$data['payment_schet_bik'] = $this->config->get('payment_schet_bik');
		$data['payment_schet_corschet'] = $this->config->get('payment_schet_corschet');
		$data['payment_schet_telephone'] = $this->config->get('payment_schet_telephone');
		$data['payment_schet_mobilephone'] = isset($this->request->post['payment_schet_mobilephone']) ? $this->request->post['payment_schet_mobilephone'] : '';
		$data['payment_schet_mobilephone'] = $this->config->get('payment_schet_mobilephone');
		$data['payment_schet_image_signature'] = $this->config->get('payment_schet_image_signature');
		$data['payment_schet_image_signature_accountant'] = $this->config->get('payment_schet_image_signature_accountant');
		$data['payment_schet_image_stamp'] = $this->config->get('payment_schet_image_stamp');
		$data['payment_schet_signature_width'] = $this->config->get('payment_schet_signature_width');
		$data['payment_schet_signature_height'] = $this->config->get('payment_schet_signature_height');
		$data['payment_schet_signature_accountant_width'] = $this->config->get('payment_schet_signature_accountant_width');
		$data['payment_schet_signature_accountant_height'] = $this->config->get('payment_schet_signature_accountant_height');
		$data['payment_schet_stamp_width'] = $this->config->get('payment_schet_stamp_width');
		$data['payment_schet_stamp_height'] = $this->config->get('payment_schet_stamp_height');
		$data['payment_schet_total'] = $this->config->get('payment_schet_total');
		$data['languages'] = $languages;
		$data['payment_schet_order_status_id'] = $this->config->get('payment_schet_order_status_id');
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['payment_schet_geo_zone_id'] = $this->config->get('payment_schet_geo_zone_id');
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		$data['payment_schet_sort_order'] = $this->config->get('payment_schet_sort_order');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data['payment_schet_inn'] = $this->request->post['payment_schet_inn'];
			$data['payment_schet_kpp'] = $this->request->post['payment_schet_kpp'];
			$data['payment_schet_account'] = $this->request->post['payment_schet_account'];
			$data['payment_schet_bik'] = $this->request->post['payment_schet_bik'];
			$data['payment_schet_corschet'] = $this->request->post['payment_schet_corschet'];
			$data['payment_schet_telephone'] = $this->request->post['payment_schet_telephone'];
			$data['payment_schet_image_signature'] = $this->request->post['payment_schet_image_signature'];
			$data['payment_schet_image_signature_accountant'] = $this->request->post['payment_schet_image_signature_accountant'];
			$data['payment_schet_image_stamp'] = $this->request->post['payment_schet_image_stamp'];
			$data['payment_schet_signature_width'] = $this->request->post['payment_schet_signature_width'];
			$data['payment_schet_signature_height'] = $this->request->post['payment_schet_signature_height'];
			$data['payment_schet_signature_accountant_width'] = $this->request->post['payment_schet_signature_accountant_width'];
			$data['payment_schet_signature_accountant_height'] = $this->request->post['payment_schet_signature_accountant_height'];
			$data['payment_schet_stamp_width'] = $this->request->post['payment_schet_stamp_width'];
			$data['payment_schet_stamp_height'] = $this->request->post['payment_schet_stamp_height'];
			$data['payment_schet_total'] = $this->request->post['payment_schet_total'];
			$data['payment_schet_order_status_id'] = $this->request->post['payment_schet_order_status_id'];
			$data['payment_schet_geo_zone_id'] = $this->request->post['payment_schet_geo_zone_id'];
			$data['payment_schet_sort_order'] = $this->request->post['payment_schet_sort_order'];
			$data['payment_schet_status'] = $this->request->post['payment_schet_status'];
		}

		$data['payment_schet_status'] = $this->config->get('payment_schet_status');
		$this->load->model('tool/image');

		$data['thumb_signature'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		if ($this->config->get('payment_schet_image_signature')) {
			$data['thumb_signature'] = $this->model_tool_image->resize($this->config->get('payment_schet_image_signature'), 100, 100);
		}

		$data['thumb_signature_accountant'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		if ($this->config->get('payment_schet_image_signature_accountant')) {
			$data['thumb_signature_accountant'] = $this->model_tool_image->resize($this->config->get('payment_schet_image_signature_accountant'), 100, 100);
		}

		$data['thumb_stamp'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		if ($this->config->get('payment_schet_image_stamp')) {
			$data['thumb_stamp'] = $this->model_tool_image->resize($this->config->get('payment_schet_image_stamp'), 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/schet', $data));
	}

	public function uninstall()
	{
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('payment_schet');
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/payment/schet')) {
			$this->error['warning'] = $this->language->get('error_permission');
			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();

			$this->error['supplier' . $language['language_id']] = $this->language->get('error_supplier');
			$this->error['director' . $language['language_id']] = $this->language->get('error_director');
			$this->error['bank_supplier' . $language['language_id']] = $this->language->get('error_bank_supplier');
			$this->error['inn'] = $this->language->get('error_inn');
			$this->error['account'] = $this->language->get('error_account');
			$this->error['bik'] = $this->language->get('error_bik');
		} else {
			$this->error['corschet'] = $this->language->get('error_corschet');

			if (!isset($this->error['warning'])) {
			}

		}

		$this->error['warning'] = $this->language;
		return !$this->error;
	}
}

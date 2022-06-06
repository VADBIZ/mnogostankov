<?php
class ControllerSalePact extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/pact');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/pact');

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('sale/pact/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('sale/pact/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['pacts'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$pact_total = $this->model_sale_pact->getTotalPacts();

		$results = $this->model_sale_pact->getPacts($filter_data);

		foreach ($results as $result) {
			$data['pacts'][] = array(
				'pact_id'	  => $result['pact_id'],
				'name'        => $result['name'],
				'link'        => $result['link'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'        => $this->url->link('sale/pact/edit', 'user_token=' . $this->session->data['user_token'] . '&pact_id=' . $result['pact_id'] . $url, true)
			);
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_date_added'] = $this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . '&sort=p.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $pact_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pact_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($pact_total - $this->config->get('config_limit_admin'))) ? $pact_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $pact_total, ceil($pact_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/pact_list', $data));
	}

	public function add() {
		$this->load->language('sale/pact');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/pact');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_pact->addPact($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/pact')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['pact_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['pact_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['filename'])) {
			$data['error_filename'] = $this->error['filename'];
		} else {
			$data['error_filename'] = '';
		}

		if (isset($this->error['mask'])) {
			$data['error_mask'] = $this->error['mask'];
		} else {
			$data['error_mask'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['pact_id'])) {
			$data['action'] = $this->url->link('sale/pact/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('sale/pact/edit', 'user_token=' . $this->session->data['user_token'] . '&pact_id=' . $this->request->get['pact_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['pact_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$pact_info = $this->model_sale_pact->getPact($this->request->get['pact_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['pact_id'])) {
			$data['pact_id'] = (int)$this->request->get['pact_id'];
		} else {
			$data['pact_id'] = 0;
		}

		if (isset($this->request->post['pact_description'])) {
			$data['pact_description'] = $this->request->post['pact_description'];
		} elseif (isset($this->request->get['pact_id'])) {
			$data['pact_description'] = $this->model_sale_pact->getPactDescriptions($this->request->get['pact_id']);
		} else {
			$data['pact_description'] = array();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/pact_form', $data));
	}

	public function edit() {
		$this->load->language('sale/pact');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/pact');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_pact->editPact($this->request->get['pact_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('sale/pact');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/pact');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $pact_id) {
				$this->model_sale_pact->deletePact($pact_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/pact', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/pact')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('sale/pact');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => $this->config->get('config_limit_autocomplete')
			);

			$results = $this->model_sale_pact->getPacts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'pact_id' => $result['pact_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

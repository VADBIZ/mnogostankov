<?php

class ControllerCatalogParagraph3 extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/paragraph3');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/paragraph3');

		$this->model_catalog_paragraph3->fix();

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

	    if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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
			'href' => $this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/paragraph3/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/paragraph3/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('tool/image');

		$data['paragraphs'] = array();

		$filter_data = array(
			'filter_status'      => $filter_status,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'              => $this->config->get('config_limit_admin')
		);

		$provider_total = $this->model_catalog_paragraph3->getTotalParagraphs($filter_data);

		$results = $this->model_catalog_paragraph3->getParagraphs($filter_data);

		foreach ($results as $result) {
		    if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['paragraphs'][] = array(
				'paragraph3_id' => $result['paragraph3_id'],
				'image'         => $image,
				'status'        => $result['status'] ? $this->language->get('text_enabled_short') : $this->language->get('text_disabled_short'),
				'edit'          => $this->url->link('catalog/paragraph3/edit', 'user_token=' . $this->session->data['user_token'] . '&paragraph3_id=' . $result['paragraph3_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

	    if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_status'] = $this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);

		$url = '';

	    if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $provider_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($provider_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($provider_total - $this->config->get('config_limit_admin'))) ? $provider_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $provider_total, ceil($provider_total / $this->config->get('config_limit_admin')));

		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/paragraph3_list', $data));
	}

	public function add() {
		$this->load->language('catalog/paragraph3');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/paragraph3');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_paragraph3->addParagraph($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/paragraph3')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['paragraph3_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['paragraph3_id'])) {
			$data['action'] = $this->url->link('catalog/paragraph3/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/paragraph3/edit', 'user_token=' . $this->session->data['user_token'] . '&paragraph3_id=' . $this->request->get['paragraph3_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['paragraph3_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$provider_info = $this->model_catalog_paragraph3->getParagraph($this->request->get['paragraph3_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($provider_info)) {
			$data['image'] = $provider_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($provider_info) && is_file(DIR_IMAGE . $provider_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($provider_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($provider_info)) {
			$data['sort_order'] = $provider_info['sort_order'];
		} else {
			$data['sort_order'] = true;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($provider_info)) {
			$data['status'] = $provider_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/paragraph3_form', $data));
	}

	public function edit() {
		$this->load->language('catalog/paragraph3');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/paragraph3');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_paragraph3->editParagraph($this->request->get['paragraph3_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/paragraph3');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/paragraph3');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $paragraph3_id) {
				$this->model_catalog_paragraph3->deleteParagraph($paragraph3_id);
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

			$this->response->redirect($this->url->link('catalog/paragraph3', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/paragraph3')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}

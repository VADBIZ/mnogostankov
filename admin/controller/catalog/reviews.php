<?php

class ControllerCatalogReviews extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/reviews');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/review');

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/reviews', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['delete'] = $this->url->link('catalog/reviews/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['enabled'] = $this->url->link('catalog/reviews/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disabled'] = $this->url->link('catalog/reviews/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['reviews'] = array();

		$filter_data = array(
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$this->load->model('catalog/product');

		$review_total = $this->model_catalog_review->getTotalReviewz2($filter_data);

		$results = $this->model_catalog_review->getReviewz2($filter_data);

		foreach ($results as $result) {
			$product = false;
			if(isset($result['product_id']) && $result['product_id'] > 0){
			    $product_info = $this->model_catalog_product->getProduct($result['product_id']);
				if (!empty($product_info)) {
					$h = HTTP_CATALOG . 'index.php?route=product/product&product_id=' . $result['product_id'];
					$product = '<a href="'.$h.'" target="_blank">'.$product_info['name'].'</a>';
				}
			}

			$vid = 'Заявка из формы ЗАКАЗАТЬ ЗВОНОК';

			if($result['vid'] == 2) {$vid = 'Заявка из формы на странице товара';}
			if($result['vid'] == 3) {$vid = 'Заявка из страницы КОНТАКТОВ';}
			if($result['vid'] == 4) {$vid = 'Заявка на станок БУ';}

			$data['reviews'][] = array(
				'reviews_id'    => $result['reviews_id'],
				'name'          => $result['name'],
				'email'         => $result['email'],
				'telephone'     => $result['telephone'],
				'company'     	=> $result['company'],
				'whatsapp'     	=> $result['whatsapp'],
				'mailing'     	=> $result['mailing'],
				'tip'     		=> $result['tip'],
				'vid'     		=> $vid,
				'product'		=> $product,
				'text'          => $result['text'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
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

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/reviews', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($review_total - $this->config->get('config_limit_admin'))) ? $review_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $review_total, ceil($review_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/reviews', $data));
	}

	public function delete() {
		$this->load->language('catalog/reviews');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/review');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $reviews_id) {
				$this->model_catalog_review->deleteReviewz2($reviews_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

		}

		$this->response->redirect($this->url->link('catalog/reviews', 'user_token=' . $this->session->data['user_token'], true));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/reviews')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function enable() {
        $this->load->language('catalog/reviews');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/review');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $reviews_id) {
				$this->model_catalog_review->editReviewStatus($reviews_id, 1);
            }
            $this->session->data['success'] = $this->language->get('text_success');

        }
        $this->response->redirect($this->url->link('catalog/reviews', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function disable() {
        $this->load->language('catalog/reviews');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/review');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $reviews_id) {
				$this->model_catalog_review->editReviewStatus($reviews_id, 2);
            }
            $this->session->data['success'] = $this->language->get('text_success');

        }
        $this->response->redirect($this->url->link('catalog/reviews', 'user_token=' . $this->session->data['user_token'], true));
    }
}

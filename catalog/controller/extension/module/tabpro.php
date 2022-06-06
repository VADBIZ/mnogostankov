<?php
class ControllerExtensionModuleTabpro extends Controller {
	public function index($setting) {
        static $module = 0;
		$this->load->language('extension/module/tabpro');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products_latest'] = array();
      
        $data['top1'] = $setting['top1'];
        $data['top2'] = $setting['top2'];
        $data['top3'] = $setting['top3'];

		$results = $this->model_catalog_product->getLatestProducts($setting['limit1']);

		if (($results) && ($setting['top1'])) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width1'], $setting['height1']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width1'], $setting['height1']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
              
                if ($result['quantity'] <= 0) {
				    $stock = $result['stock_status'];
			    } else {
				    $stock = $this->language->get('text_instock');
			    }

				$data['products_latest'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
                    'compare'     => $this->model_catalog_product->getProductCompare($result['product_id']),
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
                    'ean'        => $result['ean'],
                    'stock'        => $stock,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}
      
        $data['products_bestseller'] = array();

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit2']);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width2'], $setting['height2']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width2'], $setting['height2']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
              
                if ($result['quantity'] <= 0) {
				    $stock = $result['stock_status'];
			    } else {
				    $stock = $this->language->get('text_instock');
			    }

				$data['products_bestseller'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
                    'compare'     => $this->model_catalog_product->getProductCompare($result['product_id']),
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
                    'ean'        => $result['ean'],
                    'stock'        => $stock,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}
      
        $data['products_special'] = array();

		$filter_data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit3']
		);

		$results = $this->model_catalog_product->getProductSpecials($filter_data);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width3'], $setting['height3']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width3'], $setting['height3']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if (!is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}
              
                if ($result['quantity'] <= 0) {
				    $stock = $result['stock_status'];
			    } else {
				    $stock = $this->language->get('text_instock');
			    }

				$data['products_special'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
                    'compare'     => $this->model_catalog_product->getProductCompare($result['product_id']),
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
                    'ean'        => $result['ean'],
                    'stock'        => $stock,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}
		}
      
        $data['module'] = $module++;
        
        if (($data['products_latest']) || ($data['products_bestseller']) || ($data['products_special'])) {
            return $this->load->view('extension/module/tabpro', $data);
        }
	}
}

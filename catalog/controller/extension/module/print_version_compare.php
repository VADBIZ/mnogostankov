<?php
class ControllerExtensionModulePrintVersionCompare extends Controller {
	public function index() {
		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		$data['base'] = $server;
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}
		
		$this->load->language('information/contact');
		$this->load->language('product/compare');
		$this->load->language('product/product');
		$this->load->language('extension/module/print_version');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (!isset($this->session->data['compare'])) {
			$this->session->data['compare'] = array();
		}

		$data['review_status'] = $this->config->get('config_review_status');
		$data['store'] = $this->config->get('config_name');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['telephone'] = $this->config->get('config_telephone');
		$data['fax'] = $this->config->get('config_fax');
		$data['email'] = $this->config->get('config_email');
		$data['store_domain'] = preg_replace("(^https?://)", "", $server );
		if(substr($data['store_domain'], -1) == '/') {
			$data['store_domain'] = substr($data['store_domain'], 0, -1);
		}

		$data['products'] = array();

		$data['attribute_groups'] = array();
		$data['related_display'] = false;
		$data['sku_display'] = false;

		foreach ($this->session->data['compare'] as $key => $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_compare_height'));
				} else {
					$image = false;
				}
				
				if ( !empty($product_info['sku']) ) {
					$data['sku_display'] = true;
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}
				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($product_info['quantity'] <= 0) {
					$availability = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$availability = $product_info['quantity'];
				} else {
					$availability = $this->language->get('text_instock');
				}

				$attribute_data = array();

				$attribute_groups = $this->model_catalog_product->getProductAttributes($product_id);

				foreach ($attribute_groups as $attribute_group) {
					foreach ($attribute_group['attribute'] as $attribute) {
						$attribute_data[$attribute['attribute_id']] = $attribute['text'];
					}
				}
				
				$products_related = array();
				$results = $this->model_catalog_product->getProductRelated($product_id);
				foreach ($results as $result) {
					$data['related_display'] = true;
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price_r = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price_r = false;
					}
					if ((float)$result['special']) {
						$special_r = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special_r = false;
					}
					if ($this->config->get('config_tax')) {
						$tax_r = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
					} else {
						$tax_r = false;
					}
					
					$products_related[] = array(
						'product_id'  => $result['product_id'],
						'name'        => $result['name'],
						'price'       => $price_r,
						'special'     => $special_r,
						'tax'         => $tax_r,
						'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
					);
				}
				
				$data['products'][$product_id] = array(
					'product_id'   => $product_info['product_id'],
					'name'         => $product_info['name'],
					'thumb'        => $image,
					'price'        => $price,
					'special'      => $special,
					'description'  => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
					'model'        => $product_info['model'],
					'sku'          => $product_info['sku'],
					'manufacturer' => $product_info['manufacturer'],
					'availability' => $availability,
					'minimum'      => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
					'rating'       => (int)$product_info['rating'],
					'reviews'      => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
					'weight'       => $this->weight->format($product_info['weight'], $product_info['weight_class_id']),
					'length'       => $this->length->format($product_info['length'], $product_info['length_class_id']),
					'width'        => $this->length->format($product_info['width'], $product_info['length_class_id']),
					'height'       => $this->length->format($product_info['height'], $product_info['length_class_id']),
					'attribute'    => $attribute_data,
					'related'      => $products_related,
					'href'         => $this->url->link('product/product', 'product_id=' . $product_id)
				);

				foreach ($attribute_groups as $attribute_group) {
					$data['attribute_groups'][$attribute_group['attribute_group_id']]['name'] = $attribute_group['name'];

					foreach ($attribute_group['attribute'] as $attribute) {
						$data['attribute_groups'][$attribute_group['attribute_group_id']]['attribute'][$attribute['attribute_id']]['name'] = $attribute['name'];
					}
				}
			} else {
				unset($this->session->data['compare'][$key]);
			}
		}

		$this->response->setOutput($this->load->view('extension/module/print_version_compare', $data));
	}
}

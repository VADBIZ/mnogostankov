<?php
use Dompdf\Dompdf;
class ControllerApiOrder extends Controller {
	public function add() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Customer
			if (!isset($this->session->data['customer'])) {
				$json['error'] = $this->language->get('error_customer');
			}
			
					//проверка способов оплаты
					if (empty($this->request->post['payment_method']) || empty($this->request->post['payment_code'])) {
						$json['error'] = $this->language->get('error_payment_method');
					} else {
						$this->session->data['payment_method'] = $this->request->post['payment_method'];
						$this->session->data['payment_code'] = $this->request->post['payment_code'];
					}

					//проверка способов доставки
					if (empty($this->request->post['shipping_method']) || empty($this->request->post['shipping_code'])) {
						$json['error'] = $this->language->get('error_shipping_method');
					} else {
						$this->session->data['shipping_method'] = $this->request->post['shipping_method'];
						$this->session->data['shipping_code'] = $this->request->post['shipping_code'];
					}

					//проверяем наличие менеджера
					if (isset($this->request->post['order_manager_id'])) {
						$this->session->data['order_manager_id'] = $this->request->post['order_manager_id'];
					} else {
						$this->session->data['order_manager_id'] = '';
					}

					//проверяем наличие вида договора
					if (isset($this->request->post['pact_type_id']) && !empty($this->request->post['pact_type_id'])) {
						$this->session->data['pact_type_id'] = $this->request->post['pact_type_id'];
					} else {
						$json['error'] = $this->language->get('error_pact_type');
					}

					//проверяем наличие заполненного договора
					if (isset($this->request->post['pact_sign'])) {
						$this->session->data['pact_sign'] = $this->request->post['pact_sign'];
					} else {
						$this->session->data['pact_sign'] = "";
					}

					if (isset($this->request->post['custom_field']) && !empty($this->request->post['custom_field'])) {
						$this->session->data['account_custom_field'] = $this->request->post['custom_field'];
					}

					//проверяем наличие условий оплаты
					if (isset($this->request->post['term_pay']) && !empty($this->request->post['term_pay'])) {
						$this->session->data['term_pay'] = $this->request->post['term_pay'];
					} else {
						$order_data['term_pay'] = '';
					}

					//проверяем наличие условий оплаты
					if (isset($this->request->post['time_delivery']) && !empty($this->request->post['time_delivery'])) {
						$this->session->data['time_delivery'] = $this->request->post['time_delivery'];
					} else {
						$json['error'] = $this->language->get('error_time_delivery');
					}

					//проверяем наличие ФИО руководителя с правом подписи
						if (isset($this->request->post['whosign']) && !empty($this->request->post['whosign'])) {
							$this->session->data['whosign'] = $this->request->post['whosign'];
						} else {
							$json['error'] = $this->language->get('error_whosign');
						}

					//проверяем наличие учредительного документа
						if (isset($this->request->post['foundoc']) && !empty($this->request->post['foundoc'])) {
							$this->session->data['foundoc'] = $this->request->post['foundoc'];
						} else {
							$json['error'] = $this->language->get('error_founddoc');
						}

					//проверяем наличие гарантийного срока
					if (isset($this->request->post['garantime']) && !empty($this->request->post['garantime'])) {
							$this->session->data['garantime'] = $this->request->post['garantime'];
						} else {
							$json['error'] = $this->language->get('error_garantime');
						}

					//проверяем наличие пусконаладочные работы
					if (isset($this->request->post['comwork']) && !empty($this->request->post['comwork'])) {
							$this->session->data['comwork'] = $this->request->post['comwork'];
						} else {
							$json['error'] = $this->language->get('error_cowork');
						}

					//проверяем наличие пусконаладочные работы
					if (isset($this->request->post['delivery']) && !empty($this->request->post['delivery'])) {
							$this->session->data['delivery'] = $this->request->post['delivery'];
						} else {
							$json['error'] = $this->language->get('error_delivery');
						}

					//проверяем наличие пунктов в доп списке
					if (isset($this->request->post['sublist']) && !empty($this->request->post['sublist'])) {
						$this->session->data['sublist'] = $this->request->post['sublist'];
					} else {
						$this->session->data['sublist'] = '';
					}

					if (isset($this->request->post['currency']) && !empty($this->request->post['currency'])) {
						$this->session->data['currency'] = $this->request->post['currency'];
					} else {
						$this->session->data['currency'] = "RUB";
					}
					
					if (isset($this->request->post['account_custom_field']) && !empty($this->request->post['account_custom_field'])) {
						$this->session->data['custom_field'] = $this->request->post['account_custom_field'];
					} else {
						$this->session->data['custom_field'] = "";
					}
			
			// Cart
			if ((!$this->cart->hasProducts()) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
				$json['error'] = $this->language->get('error_stock');
			}

			// Validate minimum quantity requirements.
			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

					break;
				}
			}
			
			if (!$json) {
				$json['success'] = $this->language->get('text_create');
				
				$order_data = array();

				// Store Details
				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id'] = $this->config->get('config_store_id');
				$order_data['store_name'] = $this->config->get('config_name');
				$order_data['store_url'] = $this->config->get('config_url');
				
				// Customer Details
				$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
				$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
				$order_data['firstname'] = $this->session->data['customer']['firstname'];
				$order_data['lastname'] = $this->session->data['customer']['lastname'];
				$order_data['email'] = $this->session->data['customer']['email'];
				$order_data['telephone'] = $this->session->data['customer']['telephone'];
				$order_data['custom_field'] = $this->session->data['customer']['custom_field'];
				
					$order_data['whosign'] = $this->session->data['whosign'];
					$order_data['currency'] = $this->session->data['currency'];
					$order_data['custom_field'] = $this->session->data['custom_field'];
					$order_data['payment_method'] = $this->session->data['payment_method'];
					$order_data['payment_code'] = $this->session->data['payment_code'];
					$order_data['foundoc'] = $this->session->data['foundoc'];
					$order_data['garantime'] = $this->session->data['garantime'];
					$order_data['comwork'] = $this->session->data['comwork'];
					$order_data['delivery'] = $this->session->data['delivery'];
					$order_data['shipping_method'] = $this->session->data['shipping_method'];
					$order_data['order_manager_id'] = $this->session->data['order_manager_id'];
					$order_data['pact_type_id'] = $this->session->data['pact_type_id'];
					$order_data['pact_sign'] = $this->session->data['pact_sign'];
					$order_data['term_pay'] = $this->session->data['term_pay'];
					$order_data['time_delivery'] = $this->session->data['time_delivery'];
					$order_data['shipping_code'] = $this->session->data['shipping_code'];
					$order_data['sublist'] = $this->session->data['sublist'];

				// Products
				$order_data['products'] = array();

				foreach ($this->cart->getProducts() as $product) {
					$option_data = array();

					foreach ($product['option'] as $option) {
						$option_data[] = array(
							'product_option_id'       => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'option_id'               => $option['option_id'],
							'option_value_id'         => $option['option_value_id'],
							'name'                    => $option['name'],
							'value'                   => $option['value'],
							'type'                    => $option['type']
						);
					}

					$order_data['products'][] = array(
						'product_id' => $product['product_id'],
						'name'       => $product['name'],
						'model'      => $product['model'],
						'option'     => $option_data,
						'download'   => $product['download'],
						'quantity'   => $product['quantity'],
						'subtract'   => $product['subtract'],
						'price'      => $product['price'],
						'total'      => $product['total'],
						'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     => $product['reward']
					);
				}

				// Order Totals
				$this->load->model('setting/extension');

				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				// Because __call can not keep var references so we put them into an array.
				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);
			
				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);
						
						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($total_data['totals'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data['totals']);

				$order_data = array_merge($order_data, $total_data);

				if (isset($this->request->post['comment'])) {
					$order_data['comment'] = $this->request->post['comment'];
				} else {
					$order_data['comment'] = '';
				}

				if (isset($this->request->post['affiliate_id'])) {
					$subtotal = $this->cart->getSubTotal();

					// Marketing
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				}

				$order_data['language_id'] = $this->config->get('config_language_id');
				$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
				$order_data['currency_code'] = $this->session->data['currency'];
				$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
				$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				} else {
					$order_data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$order_data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$order_data['accept_language'] = '';
				}

				$this->load->model('checkout/order');
				
				$json['order_id'] = $this->model_checkout_order->addOrder($order_data);

				// Set the order history
				if (isset($this->request->post['order_status_id'])) {
					$order_status_id = $this->request->post['order_status_id'];
				} else {
					$order_status_id = $this->config->get('config_order_status_id');
				}

				$this->model_checkout_order->addOrderHistory($json['order_id'], $order_status_id);
				
				// clear cart since the order has already been successfully stored.
//				$this->cart->clear();
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}
			
			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				
				// Customer
				if (!isset($this->session->data['customer'])) {
					$json['error'] = $this->language->get('error_customer');
				}
				
					//проверка способов оплаты
					if (empty($this->request->post['payment_method']) || empty($this->request->post['payment_code'])) {
						$json['error'] = $this->language->get('error_payment_method');
					} else {
						$this->session->data['payment_method'] = $this->request->post['payment_method'];
						$this->session->data['payment_code'] = $this->request->post['payment_code'];
					}

					//проверка способов доставки
					if (empty($this->request->post['shipping_method']) || empty($this->request->post['shipping_code'])) {
						$json['error'] = $this->language->get('error_shipping_method');
					} else {
						$this->session->data['shipping_method'] = $this->request->post['shipping_method'];
						$this->session->data['shipping_code'] = $this->request->post['shipping_code'];
					}

					//проверяем наличие менеджера
					if (isset($this->request->post['order_manager_id'])) {
						$this->session->data['order_manager_id'] = $this->request->post['order_manager_id'];
					} else {
						$this->session->data['order_manager_id'] = '';
					}

					//проверяем наличие вида договора
					if (isset($this->request->post['pact_type_id']) && !empty($this->request->post['pact_type_id'])) {
						$this->session->data['pact_type_id'] = $this->request->post['pact_type_id'];
					} else {
						$json['error'] = $this->language->get('error_pact_type');
					}

					//проверяем наличие заполненного договора
					if (isset($this->request->post['pact_sign'])) {
						$this->session->data['pact_sign'] = $this->request->post['pact_sign'];
					} else {
						$this->session->data['pact_sign'] = "";
					}

					if (isset($this->request->post['custom_field']) && !empty($this->request->post['custom_field'])) {
						$this->session->data['account_custom_field'] = $this->request->post['custom_field'];
					}

					//проверяем наличие условий оплаты
					if (isset($this->request->post['term_pay']) && !empty($this->request->post['term_pay'])) {
						$this->session->data['term_pay'] = $this->request->post['term_pay'];
					} else {
						$order_data['term_pay'] = '';
					}

					//проверяем наличие условий оплаты
					if (isset($this->request->post['time_delivery']) && !empty($this->request->post['time_delivery'])) {
						$this->session->data['time_delivery'] = $this->request->post['time_delivery'];
					} else {
						$json['error'] = $this->language->get('error_time_delivery');
					}

					//проверяем наличие ФИО руководителя с правом подписи
						if (isset($this->request->post['whosign']) && !empty($this->request->post['whosign'])) {
							$this->session->data['whosign'] = $this->request->post['whosign'];
						} else {
							$json['error'] = $this->language->get('error_whosign');
						}

					//проверяем наличие учредительного документа
						if (isset($this->request->post['foundoc']) && !empty($this->request->post['foundoc'])) {
							$this->session->data['foundoc'] = $this->request->post['foundoc'];
						} else {
							$json['error'] = $this->language->get('error_founddoc');
						}

					//проверяем наличие гарантийного срока
					if (isset($this->request->post['garantime']) && !empty($this->request->post['garantime'])) {
							$this->session->data['garantime'] = $this->request->post['garantime'];
						} else {
							$json['error'] = $this->language->get('error_garantime');
						}

					//проверяем наличие пусконаладочные работы
					if (isset($this->request->post['comwork']) && !empty($this->request->post['comwork'])) {
							$this->session->data['comwork'] = $this->request->post['comwork'];
						} else {
							$json['error'] = $this->language->get('error_cowork');
						}

					//проверяем наличие пусконаладочные работы
					if (isset($this->request->post['delivery']) && !empty($this->request->post['delivery'])) {
							$this->session->data['delivery'] = $this->request->post['delivery'];
						} else {
							$json['error'] = $this->language->get('error_delivery');
						}

					//проверяем наличие пунктов в доп списке
					if (isset($this->request->post['sublist']) && !empty($this->request->post['sublist'])) {
						$this->session->data['sublist'] = $this->request->post['sublist'];
					} else {
						$this->session->data['sublist'] = '';
					}

					if (isset($this->request->post['currency']) && !empty($this->request->post['currency'])) {
						$this->session->data['currency'] = $this->request->post['currency'];
					} else {
						$this->session->data['currency'] = "RUB";
					}
					
					if (isset($this->request->post['account_custom_field']) && !empty($this->request->post['account_custom_field'])) {
						$this->session->data['custom_field'] = $this->request->post['account_custom_field'];
					} else {
						$this->session->data['custom_field'] = "";
					}
				
//				var_dump($this->session->data);
				
//				var_dump($this->request->post);
//				var_dump($order_data);

//				// Cart
//				if ((!$this->cart->hasProducts()) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
//					$json['error'] = $this->language->get('error_stock');
//				}

				// Validate minimum quantity requirements.
				$products = $this->cart->getProducts();
//
//				foreach ($products as $product) {
//					$product_total = 0;
//
//					foreach ($products as $product_2) {
//						if ($product_2['product_id'] == $product['product_id']) {
//							$product_total += $product_2['quantity'];
//						}
//					}
//
//					if ($product['minimum'] > $product_total) {
//						$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
//
//						break;
//					}
//				}

				if (!$json) {
					$json['success'] = $this->language->get('text_success');
					
					$order_data = array();

					// Store Details
					$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
					$order_data['store_id'] = $this->config->get('config_store_id');
					$order_data['store_name'] = $this->config->get('config_name');
					$order_data['store_url'] = $this->config->get('config_url');

					// Customer Details
					$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
					$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
					$order_data['firstname'] = $this->session->data['customer']['firstname'];
					$order_data['lastname'] = $this->session->data['customer']['lastname'];
					$order_data['email'] = $this->session->data['customer']['email'];
					$order_data['telephone'] = $this->session->data['customer']['telephone'];
					$order_data['whosign'] = $this->session->data['whosign'];
					$order_data['currency'] = $this->session->data['currency'];
					$order_data['custom_field'] = $this->session->data['custom_field'];
					$order_data['payment_method'] = $this->session->data['payment_method'];
					$order_data['payment_code'] = $this->session->data['payment_code'];
					$order_data['foundoc'] = $this->session->data['foundoc'];
					$order_data['garantime'] = $this->session->data['garantime'];
					$order_data['comwork'] = $this->session->data['comwork'];
					$order_data['delivery'] = $this->session->data['delivery'];
					$order_data['shipping_method'] = $this->session->data['shipping_method'];
					$order_data['order_manager_id'] = $this->session->data['order_manager_id'];
					$order_data['pact_type_id'] = $this->session->data['pact_type_id'];
					$order_data['pact_sign'] = $this->session->data['pact_sign'];
					$order_data['term_pay'] = $this->session->data['term_pay'];
					$order_data['time_delivery'] = $this->session->data['time_delivery'];
					$order_data['shipping_code'] = $this->session->data['shipping_code'];
					$order_data['sublist'] = $this->session->data['sublist'];

					// Products
					$order_data['products'] = array();

					foreach ($this->cart->getProducts() as $product) {
						$option_data = array();

						foreach ($product['option'] as $option) {
							$option_data[] = array(
								'product_option_id'       => $option['product_option_id'],
								'product_option_value_id' => $option['product_option_value_id'],
								'option_id'               => $option['option_id'],
								'option_value_id'         => $option['option_value_id'],
								'name'                    => $option['name'],
								'value'                   => $option['value'],
								'type'                    => $option['type']
							);
						}

						$order_data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'model'      => $product['model'],
							'option'     => $option_data,
							'download'   => $product['download'],
							'quantity'   => $product['quantity'],
							'subtract'   => $product['subtract'],
							'price'      => $product['price'],
							'total'      => $product['total'],
							'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
							'reward'     => $product['reward']
						);
					}

					// Order Totals
					$this->load->model('setting/extension');

					$totals = array();
					$taxes = $this->cart->getTaxes();
					$total = 0;
					
					// Because __call can not keep var references so we put them into an array. 
					$total_data = array(
						'totals' => &$totals,
						'taxes'  => &$taxes,
						'total'  => &$total
					);
			
					$sort_order = array();

					$results = $this->model_setting_extension->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get('total_' . $result['code'] . '_status')) {
							$this->load->model('extension/total/' . $result['code']);
							
							// We have to put the totals in an array so that they pass by reference.
							$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
						}
					}

					$sort_order = array();

					foreach ($total_data['totals'] as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $total_data['totals']);

					$order_data = array_merge($order_data, $total_data);
//
					if (isset($this->request->post['comment'])) {
						$order_data['comment'] = $this->request->post['comment'];
					} else {
						$order_data['comment'] = '';
					}

					$this->model_checkout_order->editOrder($order_id, $order_data);

					// Set the order history
					if (isset($this->request->post['order_status_id'])) {
						$order_status_id = $this->request->post['order_status_id'];
					} else {
						$order_status_id = $this->config->get('config_order_status_id');
					}
					
					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);

					// When order editing is completed, delete added order status for Void the order first.
					if ($order_status_id) {
						$this->db->query("DELETE FROM `" . DB_PREFIX . "order_history` WHERE order_id = '" . (int)$order_id . "' AND order_status_id = '0'");
					}
				}
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	//проверка номера договора на уникальность
	public function checkUniquePact($pact_number){
		$this->load->model('sale/order');
		return $this->model_sale_order->checkPactUnique($pact_number);
	}

	public function delete() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$this->model_checkout_order->deleteOrder($order_id);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function info() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$json['order'] = $order_info;

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function history() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Add keys for missing post vars
			$keys = array(
				'order_status_id',
				'notify',
				'override',
				'comment'
			);

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$this->model_checkout_order->addOrderHistory($order_id, $this->request->post['order_status_id'], $this->request->post['comment'], $this->request->post['notify'], $this->request->post['override']);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
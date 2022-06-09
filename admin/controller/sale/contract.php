<?php
use Dompdf\Dompdf;
class ControllerSaleContract extends Controller {
	private $error = array();

	public function mb_ucfirst($text) {
		return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
	}

	public function get_currency($currency_code, $format) {
		$date = date('d/m/Y'); // Текущая дата
		$cache_time_out = '3600'; // Время жизни кэша в секундах

		$file_currency_cache = $_SERVER['DOCUMENT_ROOT'].'/currency.xml'; // Файл кэша

		if(strlen(file_get_contents($file_currency_cache)) == 0 || !is_file($file_currency_cache) || filemtime($file_currency_cache) < (time() - $cache_time_out)) {

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://www.cbr.ru/scripts/XML_daily.asp?date_req='.$date);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);

			$out = curl_exec($ch);

			curl_close($ch);

			file_put_contents($file_currency_cache, $out);

		}

		$content_currency = simplexml_load_file($file_currency_cache);

		return number_format(str_replace(',', '.', $content_currency->xpath('Valute[CharCode="'.$currency_code.'"]')[0]->Value), $format);

	}

	public function str_price($value) {
		$value = explode('.', number_format($value, 2, '.', ''));

		$f = new NumberFormatter('ru', NumberFormatter::SPELLOUT);
		$str = $f->format($value[0]);

		// Первую букву в верхний регистр.
		$str = mb_substr($str, 0, 1) . mb_substr($str, 1, mb_strlen($str));

		return $str . ' ';
	}

//	echo str_price(0);      // Ноль рублей 00 копеек.
//	echo str_price(150.50); // Сто пятьдесят рублей 50 копеек.
//	echo str_price(1203);   // Одна тысяча двести три рубля 00 копеек.
//	echo str_price(2541);   // Две тысячи пятьсот сорок один рубль 00 копеек.
//	echo str_price(100000); // Сто тысяч рублей 00 копеек.

	public function index() {

        require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');
		$this->load->language('sale/contract');
		$this->load->model('sale/order');
		$this->load->model('customer/customer');
		$this->load->model('sale/pact');
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/attribute');

		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		if (isset($this->request->get['order_id'])) {
			$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);

	//		var_dump($order_info);

			$order_id = $this->request->get['order_id'];
			$order_number = $order_info['order_number'];
			$getDate = new DateTime($order_info['date_added']);
			$order_date = $getDate->format('d.m.Y');
			if ($order_info['pact_type_id'] == 0)
				$order_info['pact_type_id'] = 6;
			$pact_info = $this->model_sale_pact->getPact($order_info['pact_type_id']);
			$pact_title = $pact_info['name'];
			$pact_link = $pact_info['link'];

			$phrase = urlencode($pact_title);
			$url_in = "http://pyphrasy.herokuapp.com/inflect?phrase={$phrase}&cases=datv";
			$fp = file_get_contents($url_in);
			mb_internal_encoding("UTF-8");
			if (!$fp) {
				$pact_title_datv = $pact_title;
			} else {
				$title_enc = json_decode($fp, true);
				$pact_title_datv = $this->mb_ucfirst($title_enc["datv"]);
			}

			$custom_field_info = json_decode($order_info['custom_field'], true);
			$company_name = $order_info['company'];
			$who_sign = $order_info['whosign'];
			$foundoc = $order_info['foundoc'];
			$termpay = $order_info['term_pay'];
			$timedelivery = $order_info['time_delivery'];
			$delivery = $order_info['delivery'];
			$garantime = $order_info['garantime'];
			$comwork = $order_info['comwork'];
			$sublist = $order_info['sublist'];

			$customer_info = $this->model_customer_customer->getCustomer($order_info['customer_id']);
			$inn = $custom_field_info["2"];
			$kpp = $custom_field_info["3"];
			$ogrn = $custom_field_info["4"];
			$urad = $custom_field_info["5"];
			$emaildir = $custom_field_info["8"];
			$phonedir = $customer_info["telephone"];

			$currency = $this->language->get('currency_rub_symbol');
			$currency_text = $this->language->get('currency_rub_text');
			$currency_usd_text = $this->language->get('currency_usd_text');
			$currency_usd_text2 = $this->language->get('currency_usd_text2');

			$currencyValue = $this->get_currency('USD', 4);
			$getCurrency = $this->get_currency('USD', 4);
			if ($order_info['currency_code'] == "USD") {
				$currency = $order_info['currency_code'];
				$currencyValue = 1;
				$currency_text = $order_info['currency_code'];
			}

			// Products
			$order_products = array();

			$products = $this->model_sale_order->getOrderProducts($order_id);

			foreach ($products as $product) {
				$order_products[] = array(
					'product_id' 	=> $product['product_id'],
					'quantity'   	=> $product['quantity'],
					'total'      	=> $product['total'],
					'option'		=> $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']),
					'product_info' 	=> $this->model_catalog_product->getProduct($product['product_id']),
					'product_discount' => $this->model_catalog_product->getProductDiscounts($product['product_id'])
				);
			}

			// НАЧАЛО PDF
			$test = false;
			if ($this->request->get['show']) {
				$test = $this->request->get['show'];
				$test = $test == "true" ? true: false;
			}

			$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';

				$html .= '<style type="text/css">';
					$html .= '
						* {padding:0;margin:0;font-family:arial;font-size: 14px;line-height: 16px}
						body{padding:0 50px;margin-top:2.3cm;margin-bottom:1cm;}
						header {position: fixed;top: 0cm;left: 0cm;right: 0cm;height: 2cm;padding: 15px 50px;}
						table{width:100%}
						.contract__title {text-align:center;font-size:16px;margin:20px 0}
						table {margin: 0 0 15px 0;width: 100%;border-collapse: collapse; border-spacing: 0;}		
						table td {padding: 2px 5px;}
						table th {padding: 5px;font-weight: bold;}
						h1 {font-size:20px}
						h3 {margin-top:20px;font-size:16px}
						.table-bordered td, .table-bordered th {border: 1px solid #000;}
						p.ti {text-indent: 30px}
						.attribute__groups-item {padding-left:30px}
						footer {position: fixed;bottom: -15px;left: 0px;right: 0px;text-align:center;height: 50px;padding-top:10px}
						footer .page span:after {content: counter(page, decimal);}
					';
				$html .= '</style>';

			$html .= '</head><body>';
				$html .= '<header>';
					$html .= '<table class="table"><tr>';
						$html .= '<td class="header__logo" style="text-align:left">';
							$html .= '<img src="'.$server.'image/contract__logo.png" alt="">';
						$html .= '</td>';
						$html .= '<td class="header__contact" style="text-align:right">';
							$html .= '<p class="header__contact-link">'.str_replace(array("https://","/"),"",$server).'</p>';
							$html .= '<p class="header__contact-title">Договор поставки №'.$order_info["order_number"].'<br> от '.$order_date;
						$html .= '</td>';
					$html .= '</tr></table>';
				$html .= '</header>';
				$html .= '<footer><p class="page">Страница <span></span> из 2</p></footer>';
				$html .= '<h1 class="contract__title">ДОГОВОР №'.$order_number.'<br>присоединения к '.$pact_title_datv.'</h1>';
				$html .= '<table style="width:100%;margin-bottom:10px"><tr><td style="text-align:left;padding-left:30px">г. Омск</td><td style="text-align:right">'.$order_date.'</td></tr></table>';
				$html .= '<p class="ti">Поставщик: ООО ГК «СТРУЧАЕВ», в лице Генерального директора Стручаева Виктора Васильевича, действующего на основании Устава и</p>';
				$html .= '<p class="ti">Покупатель: '.$company_name.' в лице '.$who_sign.' действующего на основании '.$foundoc.',</p>';
				$html .= '<p class="ti">настоящим Договором присоединяется к '.$pact_title_datv.', известных Покупателю и имеющих обязательную для Покупателя силу. Настоящим Покупатель подтверждает готовность оплатить и принять следующее Оборудование:</p>';
				if (count($order_products) > 0) {
					$html .= '<table class="table-bordered table" style="margin-top:20px">';
						$html .= '<thead>';
							$html .= '<tr>';
								$html .= '<th>№</th>';
								$html .= '<th>Наименование</th>';
								$html .= '<th>Кол-во</th>';
								$html .= '<th>Цена за единицу, '.$currency.'</th>';
								$html .= '<th>Скидка, '.$currency.'</th>';
								$html .= '<th>Стоимость, '.$currency.'</th>';
							$html .= '</tr>';
						$html .= '</thead>';
						$html .= '<tbody>';
							$i=1;
                            $sumTotal = 0;
                            $sumNDS = 0;
							foreach($order_products as $product){

								$brand = $this->model_catalog_manufacturer->getManufacturer($product['product_info']['manufacturer_id']);
								$price = $this->currency->format($this->tax->calculate($product['product_info']['price'], $product['product_info']['tax_class_id'], $this->config->get('config_tax')), "USD");

								$price = $price * $currencyValue;

								$price_discount = 0;
								$product_discount = "-";
								$priceTotal = $price;
								if (count($product['product_discount']) > 0) {
									$price_discount = (float)$product['product_discount'][0]['price'] * $currencyValue;
									$product_discount = number_format((float)$price - $price_discount, 0, ',', ' ');
									$priceTotal = $price_discount;
								}

								$html .= '<tr>';
									$html .= '<td>'.$i.'</td>';
									$html .= '<td>'.$product['product_info']['name'].'<br><strong>'. (isset($brand['name']) ? $brand['name'] : '') .'</strong>';

										if (count($product['option']) > 0) {
											$html .= "<br><br><strong>".$this->language->get('entry_option')."</strong>";
											foreach ($product['option'] as $product_option) {
												$product_options = $this->model_catalog_product->getProductOptions($product['product_id']);

												$product_option_id = $product_option['product_option_id'];
												$product_option_value_id = $product_option['product_option_value_id'];

												$priceOption = "";
												$priceOption_prefix = "";

												foreach($product_options as $po) {
													foreach($po['product_option_value'] as $popov) {
														if (($po['product_option_id'] == $product_option_id) && ($popov['product_option_value_id'] == $product_option_value_id)) {
															$priceOption = $popov['price'];
															$priceOption_prefix = $popov['price_prefix'];
														}
													}
												}

												if ($order_info['currency_code'] == "USD") {
													$priceOption = $priceOption / $getCurrency;
												}

												$priceTotal += $priceOption;
												$price += $priceOption;

												$priceOption = $this->currency->format($priceOption, $this->config->get('config_currency')) . $currency;

												$option_name = $product_option['name'];
												$option_value = $product_option['value'];
												$option_price = " (".$priceOption_prefix.$priceOption.")";
												$html .= "<br>".$option_name.": ".$option_value.$option_price;

											}
										}

									$totalPriceProductValue = $priceTotal * $product['quantity'];
									$totalPriceProduct = number_format($totalPriceProductValue, 0, ',', ' ');
									$totalPriceProductValueNDS = ($priceTotal * $product['quantity'] * 0.2) / 1.2;
									$totalPriceProductNDS = number_format($totalPriceProductValueNDS, 0, ',', ' ');
									$price_one = number_format($price, 0, ',', ' ');

									$sumTotal += $totalPriceProductValue;
                                    $sumNDS += $totalPriceProductValueNDS;

									$html .= '</td>';
									$html .= '<td>'.$product['quantity'].'</td>';
									$html .= '<td>'.$price_one.'</td>';
									$html .= '<td>'.$product_discount.'</td>';
									$html .= '<td>'.$totalPriceProduct.'</td>';
								$html .= '</tr>';
								$html .= '<tr>';
									$html .= '<td colspan="5" style="text-align:right">Итого</td>';
									$html .= '<td>'.$totalPriceProduct.'</td>';
								$html .= '</tr>';
								$html .= '<tr>';
									$html .= '<td colspan="5" style="text-align:right">В т.ч. НДС 20%</td>';
									$html .= '<td>'.$totalPriceProductNDS.'</td>';
								$html .= '</tr>';
							}
						$html .= '</tbody>';
					$html .= '</table>';
					if ($currency == "USD") {
						$html .= '<p>Итого: '.$this->str_price($sumTotal).' '.$currency_usd_text2.'., в т.ч. НДС (20%) '.$this->str_price($sumNDS).$currency_usd_text2.'.</p>';
					} else {
						$html .= '<p>Итого: '.$this->str_price($sumTotal).' '.mb_strtolower($currency).'., в т.ч. НДС (20%) '.$this->str_price($sumNDS).mb_strtolower($currency).'.</p>';
					}
					$attribute_groups = $this->model_catalog_product->getProductAttributes($product['product_id']);
					if (count($attribute_groups) > 0) {
						$html .= '<h3 style="text-align:center">Технические характеристики:</h3>';
						$html .= '<div class="attribute__groups">';
							foreach($attribute_groups as $attribute_group) {
								$attribute_id = $attribute_group["attribute_id"];
								foreach ($this->model_catalog_attribute->getAttributeDescriptions($attribute_id) as $attribute) {
									$attribute_name = $attribute['name'];
									$html .= '<div class="attribute__groups-item">'.$attribute_name.': ';
									foreach($attribute_group['product_attribute_description'] as $title) {
										$html .= $title['text'];
									}
									$html .= '</div>';
								}
							}
						$html .= '</div>';
					}
					$html .= '<h3 style="text-align:center">1. Общие условия поставки:</h3>';
						$html .= '<p class="ti">1.1. «'.$pact_title.'» опубликованы на сайте Поставщика <a href="'.$pact_link.'">'.$pact_link.'</a></p>';
						$html .= '<p class="ti">1.2. «'.$pact_title.'» и настоящий Договор, подписанный Покупателем и Поставщиком, в совокупности являются заключенным сторонами Договором поставки.</p>';
						$html .= '<p class="ti">1.3. Подписывая настоящий Договор без протокола разногласий, Покупатель подтверждает, что ознакомлен и добровольно соглашается с Общими условиями в полном объеме.</p>';
						$html .= '<p class="ti">1.4. При наличии замечаний к Общим условиям Покупатель до подписания настоящего Договора вправе направить протокол разногласий или предложить добавить изменения в Индивидуальные условия поставки.</p>';
						$html .= '<p class="ti">1.5. Согласованные в настоящем Договоре Индивидуальные условия поставки имеют приоритет над «'.$pact_title.'».</p>';
					$html .= '<h3 style="text-align:center">2. Индивидуальные условия поставки:</h3>';
						if ($currency == "USD") {
							$html .= '<p class="ti">2.1. Цена договора, установлена в '.$currency_usd_text.'. Платежи осуществляются в рублях по официальному курсу Банка России на день оплаты.</p>';
						} else {
							$html .= '<p class="ti">2.1. Цена договора, установлена в '.$currency_text.'.</p>';
						}
						$html .= '<p class="ti">2.2. Условия оплаты: '.$termpay.'</p>';
						$html .= '<p class="ti">2.3. Срок поставки: '.$timedelivery.'</p>';
						$html .= '<p class="ti">2.4. Адрес поставки: '.$delivery.'</p>';
						$html .= '<p class="ti">2.5. Гарантийный срок: '.$garantime.'</p>';
						$html .= '<p class="ti">2.6. Пусконаладочные работы: '.$comwork.'</p>';
						if ($sublist != '') {
							if (count($sublist) > 0) {
								$j = 7;
								foreach ($sublist as $sublistItem) {
									$html .= '<p class="ti">2.'.$j.'. '.$sublistItem.'</p>';
									$j++;
								}
							}
						}
					$html .= '<h3 style="text-align:center;margin-bottom:15px">Реквизиты</h3>';
						$html .= '<table class="table">';
							$html .= '<thead>';
								$html .= "<tr>";
									$html .= '<th></th>';
									$html .= '<th style="text-align:left">Поставщик</th>';
									$html .= '<th style="text-align:left">Покупатель</th>';
								$html .= "</tr>";
							$html .= '</thead>';
							$html .= '<tbody>';
                            foreach ($this->config->get('config_requisite') as $requisite) {
                                if ($requisite['code'] == 'name') {
                                    $name = 'Название';
                                    $value = $company_name;
                                } elseif ($requisite['code'] == 'iin') {
                                    $name = 'ИНН';
                                    $value = $inn;
                                } elseif ($requisite['code'] == 'kpp') {
                                    $name = 'КПП';
                                    $value = $kpp;
                                } elseif ($requisite['code'] == 'ogrn') {
                                    $name = 'ОГРН';
                                    $value = $ogrn;
                                } elseif ($requisite['code'] == 'urad') {
                                    $name = 'Юридический адрес';
                                    $value = $urad;
                                } elseif ($requisite['code'] == 'bank') {
                                    $name = 'Банк';
                                    $value = '';
                                } elseif ($requisite['code'] == 'raschet') {
                                    $name = 'Рас/счет';
                                    $value = '';
                                } elseif ($requisite['code'] == 'bik') {
                                    $name = 'БИК';
                                    $value = '';
                                } elseif ($requisite['code'] == 'korchet') {
                                    $name = 'Кор/счет';
                                    $value = '';
                                } elseif ($requisite['code'] == 'email') {
                                    $name = 'E-mail';
                                    $value = $emaildir;
                                } elseif ($requisite['code'] == 'phone') {
                                    $name = 'Телефон';
                                    $value = $phonedir;
                                }

                                $html .= '<tr>';
                                $html .= '<td>' . $name . '</td>';
                                $html .= '<td>' . $requisite['value'] . '</td>';
                                $html .= '<td>' . $value . '</td>';
                                $html .= '</tr>';
                            }
								$html .= '<tr>';
									$html .= '<td style="padding-top:30px">Подпись<br>МП</td>';
									$html .= '<td style="padding-top:30px">Стручаев В.В._________________</td>';
									$html .= '<td style="padding-top:30px">______________________________</td>';
								$html .= '</tr>';
							$html .= '</tbody>';
						$html .= '</table>';
				}
			$html .= '</body></html>';

				if (!$test) {
					$dompdf = new Dompdf(array('enable_remote' => true));
					$dompdf->loadHtml($html, 'UTF-8');
					$dompdf->setPaper('A4', 'portrait');
					$dompdf->render();
					$dompdf->stream('Mnogo-Stankov-Договор_'.str_replace("/","-",$order_info["order_number"]));
				} else {
					echo $html;
				}
		} else {
			$order_info = array();
		}
	}
}

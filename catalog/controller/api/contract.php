<?php
use Dompdf\Dompdf;
class ControllerSaleContract extends Controller {
	private $error = array();

	public function index() {
        require_once(DIR_SYSTEM . 'library/dompdf/autoload.inc.php');
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
		} else {
			$order_info = array();
		}

//		var_dump($order_info);

		$order_id = $this->request->get['order_id'];
		$order_number = $order_info['order_number'];
		$getDate = new DateTime($order_info['date_added']);
		$order_date = $getDate->format('d.m.Y');
		$pact_info = $this->model_sale_pact->getPact($order_info['pact_type_id']);
		$pact_title = $pact_info['name'];

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

		$currency = 'руб';
		$currency_text = "российских рублях";
		$currencyValue = $this->get_currency('USD', 0);
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
				'product_info' 	=> $this->model_catalog_product->getProduct($product['product_id']),
				'product_discount' => $this->model_catalog_product->getProductDiscounts($product['product_id'])
			);
		}

		// НАЧАЛО PDF
		$test = false;

		$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';

			$html .= '<style type="text/css">';
				$html .= '
					* {padding:0;margin:0;font-family:arial;font-size: 14px;line-height: 16px}
					body{padding:25px 50px}
					table{width:100%}
					.contract__title {text-align:center;font-size:16px;margin:20px 0}
					table {margin: 0 0 15px 0;width: 100%;border-collapse: collapse; border-spacing: 0;}		
					table td {padding: 2px 5px;}
					table th {padding: 5px;font-weight: bold;}
					h1 {font-size:20px}
					h3 {margin-top:20px;font-size:16px}
					.table-bordered td, .table-bordered th {border: 1px solid #000;}
				';
			$html .= '</style>';

		$html .= '</head><body>';
			$html .= '<table class="table"><tr>';
				$html .= '<td class="header__logo" style="text-align:left">';
					$html .= '<img src="'.$server.'image/contract__logo.png" alt="">';
				$html .= '</td>';
				$html .= '<td class="header__contact" style="text-align:right">';
					$html .= '<p class="header__contact-link">'.str_replace(array("https://","/"),"",$server).'</p>';
					$html .= '<p class="header__contact-title">Договор поставки №'.$order_info["order_number"].'<br> от '.$order_date;
				$html .= '</td>';
			$html .= '</tr></table>';
			$html .= '<h1 class="contract__title">ДОГОВОР №'.$order_number.'<br>присоединения к '.$pact_title_datv.'</h1>';
			$html .= '<table style="width:100%;margin-bottom:10px"><tr><td style="text-align:left">г. Омск</td><td style="text-align:right">№'.$order_info["order_number"].'</td></tr></table>';
			$html .= '<p>Поставщик: ООО ГК «СТРУЧАЕВ», в лице Генерального директора Стручаева Виктора Васильевича, действующего на основании Устава и<br>';
			$html .= 'Покупатель: '.$company_name.' в лице '.$who_sign.' действующего на основании '.$foundoc.',<br>';
			$html .= 'настоящим Договором присоединяется к '.$pact_title_datv.', известных Покупателю и имеющих обязательную для Покупателя силу. Настоящим Покупатель подтверждает готовность оплатить и принять следующее Оборудование:</p>';
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
						foreach($order_products as $product){

							$brand = $this->model_catalog_manufacturer->getManufacturer($product['product_info']['manufacturer_id']);
							$price = $this->currency->format($this->tax->calculate($product['product_info']['price'], $product['product_info']['tax_class_id'], $this->config->get('config_tax')), "USD");

							$price = $price * $currencyValue;
							$price_one = number_format($price, 0, ',', ' ');

							$price_discount = 0;
							$product_discount = "-";
							if (count($product['product_discount']) > 0) {
								$price_discount = (float)$product['product_discount'][0]['price'] * $currencyValue;
								$product_discount = number_format((float)$price - $price_discount, 0, ',', ' ');
								$priceTotal = $price_discount;
							}

							$totalPriceProductValue = $priceTotal * $product['quantity'];
							$totalPriceProduct = number_format($totalPriceProductValue, 0, ',', ' ');
							$totalPriceProductValueNDS = $priceTotal * $product['quantity'] * 0.2;
							$totalPriceProductNDS = number_format($totalPriceProductValueNDS, 0, ',', ' ');

							$html .= '<tr>';
								$html .= '<td>'.$i.'</td>';
								$html .= '<td>'.$product['product_info']['name'].'<br><strong>Бренд: '.$brand['name'].'</strong></td>';
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
				$html .= '<p>Итого: '.$this->str_price($totalPriceProductValue).' '.mb_strtolower($currency).'., в т.ч. НДС (20%) '.$this->str_price($totalPriceProductValueNDS).mb_strtolower($currency).'</p>';
				$html .= '<h3 style="text-align:center">Технические характеристики:</h3>';
				$attribute_groups = $this->model_catalog_product->getProductAttributes($product['product_id']);
				if (count($attribute_groups) > 0) {
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
					$html .= '<p>1.1. «'.$pact_title.'» опубликованы на сайте Поставщика https://mnogo-stankov.ru/documents</p>';
					$html .= '<p>1.2. «'.$pact_title.'» и настоящий Договор, подписанный Покупателем и Поставщиком, в совокупности являются заключенным сторонами Договором поставки.</p>';
					$html .= '<p>1.3. Подписывая настоящий Договор без протокола разногласий, Покупатель подтверждает, что ознакомлен и добровольно соглашается с Общими условиями в полном объеме.</p>';
					$html .= '<p>1.4. При наличии замечаний к Общим условиям Покупатель до подписания настоящего Договора вправе направить протокол разногласий или предложить добавить изменения в Индивидуальные условия поставки.</p>';
					$html .= '<p>1.5. Согласованные в настоящем Договоре Индивидуальные условия поставки имеют приоритет над «'.$pact_title.'».</p>';
				$html .= '<h3 style="text-align:center">2. Индивидуальные условия поставки:</h3>';
					$html .= '<p>2.1. Цена договора, установлена в '.$currency_text.'.</p>';
					$html .= '<p>2.2. Условия оплаты: '.$termpay.'</p>';
					$html .= '<p>2.3. Срок поставки: '.$timedelivery.'</p>';
					$html .= '<p>2.4. Адрес поставки: '.$delivery.'</p>';
					$html .= '<p>2.5. Гарантийный срок: '.$garantime.'</p>';
					$html .= '<p>2.6. Пусконаладочные работы: '.$comwork.'</p>';
					if (count($sublist) > 0) {
						$j = 7;
						foreach ($sublist as $sublistItem) {
							$html .= '<p>2.'.$j.'. '.$sublistItem.'</p>';
							$j++;
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
							$html .= '<tr>';
								$html .= '<td>Название</td>';
								$html .= '<td>ООО ГК «СТРУЧАЕВ»</td>';
								$html .= '<td>'.$company_name.'</td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>ИНН</td>';
								$html .= '<td>5528034021</td>';
								$html .= '<td>'.$inn.'</td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>КПП</td>';
								$html .= '<td>552801001</td>';
								$html .= '<td>'.$kpp.'</td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>ОГРН</td>';
								$html .= '<td>1175543000621</td>';
								$html .= '<td>'.$ogrn.'</td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>Юридический адрес:</td>';
								$html .= '<td>644514, Омская область, Омский район, с. Новотроицкое, ул. Гагарина, дом 67</td>';
								$html .= '<td>'.$urad.'</td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>Банк</td>';
								$html .= '<td>Омское отделение ПАО Сбербанк</td>';
								$html .= '<td></td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>Рас/счет</td>';
								$html .= '<td>40702810745000007517</td>';
								$html .= '<td></td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>БИК</td>';
								$html .= '<td>045209673</td>';
								$html .= '<td></td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>Кор/счет</td>';
								$html .= '<td>30101810900000000673</td>';
								$html .= '<td>'.'</td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>E-mail</td>';
								$html .= '<td>info@mnogo-stankov.ru</td>';
								$html .= '<td>'.$emaildir.'</td>';
							$html .= '</tr>';
							$html .= '<tr>';
								$html .= '<td>Телефон</td>';
								$html .= '<td>+7-923-046-17-68</td>';
								$html .= '<td>'.$phonedir.'</td>';
							$html .= '</tr>';
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
	}

	public function mb_ucfirst($text) {
		return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
	}

	public function get_currency($currency_code, $format) {
		$date = date('d/m/Y'); // Текущая дата
		$cache_time_out = '3600'; // Время жизни кэша в секундах

		$file_currency_cache = __DIR__.'/currency.xml'; // Файл кэша

		if(!is_file($file_currency_cache) || filemtime($file_currency_cache) < (time() - $cache_time_out)) {

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

//	echo str_price(0);      // Ноль рублей 00 копеек.
//	echo str_price(150.50); // Сто пятьдесят рублей 50 копеек.
//	echo str_price(1203);   // Одна тысяча двести три рубля 00 копеек.
//	echo str_price(2541);   // Две тысячи пятьсот сорок один рубль 00 копеек.
//	echo str_price(100000); // Сто тысяч рублей 00 копеек.

	public function str_price($value) {
		$value = explode('.', number_format($value, 2, '.', ''));

		$f = new NumberFormatter('ru', NumberFormatter::SPELLOUT);
		$str = $f->format($value[0]);

		// Первую букву в верхний регистр.
		$str = mb_substr($str, 0, 1) . mb_substr($str, 1, mb_strlen($str));

		return $str . ' ';
	}
}

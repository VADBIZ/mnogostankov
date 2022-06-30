<?php
namespace Cart;
class Currency {
	private $currencies = array();
	private $cache;

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->language = $registry->get('language');
		$this->cache = $registry->get('cache');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency");

		foreach ($query->rows as $result) {
			$this->currencies[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'symbol_left'   => $result['symbol_left'],
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value']
			);
		}
	}

	public function format($number, $currency, $value = '', $format = true, $zero_price = '') {
		$symbol_left = $this->currencies[$currency]['symbol_left'];
		$symbol_right = $this->currencies[$currency]['symbol_right'];
		$decimal_place = $this->currencies[$currency]['decimal_place'];

		if ($currency == "RUB")
			$value = $this->get_currency('USD', 4);
		else
			$value = 1;

		$amount = $value ? (float)$number * $value : (float)$number;

		$amount = round($amount, (int)$decimal_place);

		if (!$format) {
			return $amount;
		}

		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

//		var_dump($amount);

		$string .= number_format($amount, (int)$decimal_place, $this->language->get('decimal_point'), $this->language->get('thousand_point'));

		if ($symbol_right) {
			$string .= $symbol_right;
		}

		if ($amount == 0) {
			return $this->language->get('zero_price');
		} else {
			return $string;
		}
	}

    public function get_currency($currency_code, $format) {
        $date = date('d/m/Y');

        $file_currency_cache = $this->cache->get('cbr.currency');

        if (!$file_currency_cache) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $date);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $out = curl_exec($ch);
            curl_close($ch);

            $result = json_decode(json_encode((array)simplexml_load_string($out)), true);
            $this->cache->set('cbr.currency', $result);
        }

        foreach ($file_currency_cache['Valute'] as $item) {
            if ($item['CharCode'] == $currency_code) {
                return number_format(str_replace(',', '.', $item['Value']), $format);
            }
        }
    }

	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function getId($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['currency_id'];
		} else {
			return 0;
		}
	}

	public function getSymbolLeft($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getValue($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}

	public function has($currency) {
		return isset($this->currencies[$currency]);
	}
}

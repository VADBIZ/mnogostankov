<?php
class ModelCatalogReview extends Model {
	public function addReview($product_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', text = '" . $this->db->escape($data['text']) . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");

		$review_id = $this->db->getLastId();

		if (in_array('review', (array)$this->config->get('config_mail_alert'))) {
			$this->load->language('mail/review');
			$this->load->model('catalog/product');
			
			$product_info = $this->model_catalog_product->getProduct($product_id);

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message  = $this->language->get('text_waiting') . "\n";
			$message .= sprintf($this->language->get('text_product'), html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_reviewer'), html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_rating'), $data['rating']) . "\n";
			$message .= $this->language->get('text_review') . "\n";
			$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			$mail->send();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
	}
	
	
	
	public function addReviews($data) {
		if(!isset($data['dowload'])) {$data['dowload'] = '';}
		if(!isset($data['company'])) {$data['company'] = '';}
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "reviews SET name = '" . $this->db->escape($data['name']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', email = '" . $this->db->escape($data['email']) . "', company = '" . $this->db->escape($data['company']) . "', dowload = '" . $this->db->escape($data['dowload']) . "', text = '" . $this->db->escape($data['enquiry']) . "', `vid` = '" . (isset($data['vid']) ? (int)$data['vid'] : 1) . "', `whatsapp` = '" . (isset($data['whatsapp']) ? (int)$data['whatsapp'] : 0) . "', `mailing` = '" . (isset($data['mailing']) ? (int)$data['mailing'] : 0) . "', `product_id` = '" . (isset($data['product_id']) ? (int)$data['product_id'] : 0) . "', date_added = NOW()");

		$reviews_id = $this->db->getLastId();

		
			$this->load->language('mail/reviews');

			//$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$vid = 'Заявка из формы ЗАКАЗАТЬ ЗВОНОК';
			
			if((int)$data['vid'] == 2){$vid = 'Заявка из формы на странице товара';}
			if((int)$data['vid'] == 3){$vid = 'Заявка из страницы КОНТАКТОВ';}
			if((int)$data['vid'] == 4){$vid = 'Заявка на станок БУ';}
			
			if(isset($data['whatsapp']) && $data['whatsapp'] == 1) {
				$whatsapp = 'ДА';
			} else {
				$whatsapp = 'НЕТ';
			}
			
			if(isset($data['mailing']) && $data['mailing'] == 1) {
				$mailing = 'ДА';
			} else {
				$mailing = 'НЕТ';
			}
			
			if(isset($data['product_id']) && $data['product_id'] > 0){
				$this->load->model('catalog/product');

		        $product_info = $this->model_catalog_product->getProduct($data['product_id']);

		        if ($product_info && (int)$data['vid'] == 2) {
					$h = $this->url->link('product/product', 'product_id=' . $data['product_id']);
					$vid .= " " . $product_info['name'];
					$vid2 = 'Заявка из формы на странице товара <a href="'.$h.'">' . $product_info['name'].'</a>';
				}
			}

			$message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<div style="width: 680px;"><h3>'.$this->language->get('text_waiting') . '</h3>';
			
			
			if(isset($data['product_id']) && $data['product_id'] > 0 && $product_info && (int)$data['vid'] == 2){
				$message .= '<h4>'.$vid2 . '</h4>';
			} else {
				$message .= '<h4>'.$vid . '</h4>';
			}
			$message .= '<p style="margin-top: 0px; margin-bottom: 20px;"><b>Запрос оставил: </b> '.html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8').'</p>';
			$message .= '<p style="margin-top: 0px; margin-bottom: 20px;"><b>Телефон: </b> '.html_entity_decode($data['telephone'], ENT_QUOTES, 'UTF-8').'</p>';
			$message .= '<p style="margin-top: 0px; margin-bottom: 20px;"><b>Email: </b> '.html_entity_decode($data['email'], ENT_QUOTES, 'UTF-8').'</p>';

			if($data['company'] != '') {
				$message .= '<p style="margin-top: 0px; margin-bottom: 20px;"><b>Компания: </b> '.html_entity_decode($data['company'], ENT_QUOTES, 'UTF-8').'</p>';
			    
			}
			$message .= '<p style="margin-top: 0px; margin-bottom: 20px;"><b>Хочу получать ответ в WhatsApp - </b> ' . $whatsapp.'</p>';
			$message .= '<p style="margin-top: 0px; margin-bottom: 20px;"><b>Хочу получать рассылку на Email - </b> ' . $mailing.'</p>';
			$message .= '<p style="margin-top: 0px; margin-bottom: 10px;"><b>'.$this->language->get('text_review').'</b></p>';
			$message .= '<p>'.html_entity_decode($data['enquiry'], ENT_QUOTES, 'UTF-8') . '</p>';
			
			$message .= '</div></body></html>';

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($vid);
			//$mail->setText($message);
			$mail->setHtml($message);
			$mail->send();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		
	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReviewsByProductId($product_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
}
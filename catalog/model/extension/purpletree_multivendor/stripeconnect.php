<?php
class ModelExtensionPurpletreeMultivendorStripeconnect extends Controller {
	public function insertStripeAccount($data=array()) {

			$res= $this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_stripe_account SET seller_id = '" . (int)$data['seller_id'] . "', account_id = '" . $data['account_id'] . "',livemode = '" . $data['livemode'] . "',scope = '" . $data['scope'] . "',created_date = NOW()");
			return $res;
		}
		public function checkAccountExistwithsellerid($seller_id) {
			$result= $this->db->query("SELECT account_id FROM " . DB_PREFIX . "purpletree_stripe_account WHERE seller_id = '" . (int)$seller_id . "'");
			return $result->num_rows;
		}
		public function checkAccountExist($account_id='') {
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_stripe_account` (				
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) NOT NULL,
			`account_id` varchar(100) NOT NULL,
			`livemode` tinyint(1) NOT NULL DEFAULT '0',
			`scope` varchar(100) NOT NULL,
			`created_date` datetime NOT NULL,
			PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
			");	
			
			$result= $this->db->query("SELECT account_id FROM " . DB_PREFIX . "purpletree_stripe_account WHERE account_id = '" . $account_id . "'");
			return $result->num_rows;
		}
}
?>
<?php
class ModelCatalogParagraph3 extends Model {
    
    public function addParagraph($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "paragraph3 SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

		$paragraph3_id = $this->db->getLastId();
		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "paragraph3 SET image = '" . $this->db->escape($data['image']) . "' WHERE paragraph3_id = '" . (int)$paragraph3_id . "'");
		}

		return $paragraph3_id;
	}

	public function editParagraph($paragraph3_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "paragraph3 SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE paragraph3_id = '" . (int)$paragraph3_id . "'");
		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "paragraph3 SET image = '" . $this->db->escape($data['image']) . "' WHERE paragraph3_id = '" . (int)$paragraph3_id . "'");
		}
	}
    
    public function getParagraph($paragraph3_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "paragraph3 WHERE paragraph3_id = '" . (int)$paragraph3_id . "'");

		return $query->row;
	}
	
	public function deleteParagraph($paragraph3_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "paragraph3 WHERE paragraph3_id = '" . (int)$paragraph3_id . "'");
	}

	public function getParagraphs($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "paragraph3 ";
		
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
		    $sql .= " WHERE ";
		}
		
		$i = false;
		
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
		    if($i) {$sql .= " AND ";}
			$sql .= " status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY paragraph3_id";

		$sort_data = array(
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function fix() {
        $query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."paragraph3'");
        
        if(count($query->rows) <= 0){
            $sql = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."paragraph3` (
                `paragraph3_id` int(11) NOT NULL AUTO_INCREMENT,
                `image` varchar(255) DEFAULT NULL,
                `sort_order` int(3) NOT NULL DEFAULT '0',
                `status` tinyint(1) NOT NULL,
                PRIMARY KEY (`paragraph3_id`)
            )";
                
            $this->db->query($sql);
        }    
	} 
	
	public function getTotalParagraphs($data = array()) {
		$sql = "SELECT COUNT(DISTINCT paragraph3_id) AS total FROM " . DB_PREFIX . "paragraph3 ";
		
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
		    $sql .= " WHERE ";
		}
		
		$i = false;

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
		    if($i) {$sql .= " AND ";}
			$sql .= " status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	
	
	public function allParagraphs() {
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "paragraph3 WHERE status = '1'");
	    
	    return $query->rows;
	}
}
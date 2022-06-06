<?php
class ModelCatalogParagraph3 extends Model {
	public function getParagraphs($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "paragraph3 WHERE";
		
		$sql .= " status = '1' ORDER BY sort_order ASC";
		
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
    
    public function getTotalParagraphs($data = array()) {
        $query = $this->db->query("SELECT COUNT(DISTINCT paragraph3_id) AS total FROM " . DB_PREFIX . "paragraph3 WHERE status = '1'");

		return $query->row['total'];
    }
}
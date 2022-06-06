<?php
class ModelSalePact extends Model {
	public function addPact($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "pact SET date_added = NOW()");

		$pact_id = $this->db->getLastId();

		foreach ($data['pact_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "pact_description SET pact_id = '" . (int)$pact_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', link = '" . $this->db->escape($value['link']) . "'");
		}

		return $pact_id;
	}

	public function editPact($pact_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "pact_description WHERE pact_id = '" . (int)$pact_id . "'");

		foreach ($data['pact_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "pact_description SET pact_id = '" . (int)$pact_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', link = '" . $this->db->escape($value['link']) . "'");
		}
	}

	public function deletePact($pact_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "pact WHERE pact_id = '" . (int)$pact_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "pact_description WHERE pact_id = '" . (int)$pact_id . "'");
	}

	public function getPact($pact_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "pact p LEFT JOIN " . DB_PREFIX . "pact_description pd ON (p.pact_id = pd.pact_id) WHERE p.pact_id = '" . (int)$pact_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->row;
	}

	public function getPacts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "pact p LEFT JOIN " . DB_PREFIX . "pact_description pd ON (p.pact_id = pd.pact_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'pd.name',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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

	public function getPactDescriptions($pact_id) {
		$pact_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pact_description WHERE pact_id = '" . (int)$pact_id . "'");

		foreach ($query->rows as $result) {
			$pact_description_data[$result['language_id']] = array('name' => $result['name'], 'link' => $result['link']);
		}

		return $pact_description_data;
	}

	public function getTotalPacts() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pact");
		return $query->row['total'];
	}
}
<?php
class ModelLocalisationCity extends Model {
	public function addCity($data) {


//        $this->db->query("INSERT INTO " . DB_PREFIX . "city_desc SET name = 1");
//
//        $attribute_id = $this->db->getLastId();


        foreach ($data['city_name'] as $language_id => $value) {

            $this->db->query("INSERT INTO " . DB_PREFIX . "cites SET  status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($value['name']) . "', language_id = '" . (int)$language_id . "', zone = '" . $this->db->escape($data['zone']) . "', country_id = '" . (int)$data['country_id'] . "'");

        }

		$this->cache->delete('city');
	}

	public function editCity($city_id, $data) {


        $this->db->query("DELETE FROM " . DB_PREFIX . "cites WHERE city_id = '" . (int)$city_id . "'");

        foreach ($data['city_name'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "cites SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($value['name']) . "', language_id = '" . (int)$language_id . "', zone = '" . $this->db->escape($data['zone']) . "', country_id = '" . (int)$data['country_id'] . "'");
        }

		$this->cache->delete('city');
	}

	public function deleteCity($city_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cites WHERE id = '" . (int)$city_id . "'");

		$this->cache->delete('city');
	}

	public function getCity($city_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "cites WHERE id = '" . (int)$city_id . "'");

		return $query->row;
	}

	public function getCites($data = array()) {
		$sql = "SELECT *, c.name, co.name AS country  FROM " . DB_PREFIX . "cites AS c LEFT JOIN " . DB_PREFIX . "country AS co ON c.country_id = co.country_id WHERE c.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'c.name',
			'c.name',
			'c.zone'
		);
        if (!empty($data['filter_zone'])) {
            $sql .= " AND c.zone LIKE '" . $this->db->escape($data['filter_zone']) . "%'";
        }

        if (!empty($data['filter_country'])) {
            $sql .= " AND c.country LIKE '" . $this->db->escape($data['filter_country']) . "%'";
        }

        if (isset($data['filter_name'])) {
            $sql .= " AND c.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.name";
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



    public function getCityName($city_id) {
        $city_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cites WHERE id = '" . (int)$city_id . "'");

        foreach ($query->rows as $result) {
            $city_data[$result['language_id']] = array('name' => $result['name']);

        }
        return $city_data;
    }

//	public function getZonesByCountryId($country_id) {
//		$zone_data = $this->cache->get('zone.' . (int)$country_id);
//
//		if (!$zone_data) {
//			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");
//
//			$zone_data = $query->rows;
//
//			$this->cache->set('zone.' . (int)$country_id, $zone_data);
//		}
//
//		return $zone_data;
//	}

	public function getTotalCity() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cites");

		return $query->row['total'];
	}

//	public function getTotalZonesByCountryId($country_id) {
//		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "'");
//
//		return $query->row['total'];
//	}
}
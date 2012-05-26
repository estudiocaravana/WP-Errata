<?php 

require_once (WP_ERRATA_PATH . 'model/classes/Errata.php');

class ErrataModel{

	public function getTableName(){
		global $wpdb;

		return $wpdb->prefix."errata";
	}

	public function install(){
		global $wpdb;

		$sql = "CREATE TABLE IF NOT EXISTS `" .$this->getTableName()."` (
		    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		    `date` timestamp DEFAULT CURRENT_TIMESTAMP,
		    `errata` varchar(50) CHARACTER SET utf8 NOT NULL,
		    `correction` varchar(50) CHARACTER SET utf8 NOT NULL,
		    `url` varchar(200) CHARACTER SET utf8 NOT NULL,
		    `ip` varchar(20) CHARACTER SET utf8 NOT NULL,
		    `path` varchar(200) CHARACTER SET utf8 NOT NULL,
		    `html` varchar(30) CHARACTER SET utf8 NOT NULL,
		    `description` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
		    `email` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
		    `deleted` tinyint(1) NOT NULL DEFAULT '0',
		    `fixed` tinyint(1) NOT NULL DEFAULT '0',
		    PRIMARY KEY  (`id`)
		  );";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public function newErrata($errata) {
		global $wpdb;

		$data = array();

		$mandatoryProperties = Errata::getMandatoryProperties();
		foreach($mandatoryProperties as $mp){
			$data[$mp] = $errata ->__get($mp);
		}
		
		$optionalProperties = Errata::getOptionalProperties();
		foreach ($optionalProperties as $op) {
			$temp = $errata ->__get($op);
			if (isset($temp)) {
				$data[$op] = $errata ->__get($op);
			}
		}

		$res = $wpdb->insert($this->getTableName(),$data);
	}

	public function fixErrata($id,$unfix = false){
	
		return $this->updateAttribute($id, "fixed", $unfix ? 0 : 1);

	}

	public function deleteErrata($id, $undelete = false){
		
		return $this->updateAttribute($id, "deleted", $undelete ? 0 : 1);

	}

	private function updateAttribute($id, $attribute, $value){
		global $wpdb;

		$res = $wpdb->update(
			$this->getTableName(),
			array(
				$attribute => $value
			),
			array(
				'id' => $id
			)
		);

		return $res;
	}

	public function getErratas($view){
		global $wpdb;

		$fixedValue = ($view == "fixed") ? 1 : 0;
		$deletedValue = ($view == "deleted") ? 1 : 0;

		$erratas = array();

		$sql =  'SELECT * FROM '.$this->getTableName().
				' WHERE fixed = '.$fixedValue.' AND deleted = '.$deletedValue;

		$data = $wpdb->get_results($sql,'ARRAY_A');

		foreach ($data as $errata_row) {
			$errata = new Errata();
			foreach ($errata_row as $key => $value) {
				$errata->__set($key,$value);
			}

			$erratas[] = $errata;
		}

		return $erratas;
	}

}

?>
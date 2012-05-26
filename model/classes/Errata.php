<?php

class Errata {

	private $id;
	private $date;
	private $errata;
	private $correction;
	private $url;
	private $ip;
	private $path;
	private $html;
	private $description;
	private $email;
	private $deleted;
	private $fixed;

	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this -> $property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this -> $property = $value;
		}

		return $this;
	}
	
	public static function getMandatoryProperties(){
		return array("errata","correction","url","ip","path","html");
	}
	
	public static function getOptionalProperties(){
		return array("description","email");
	}

}
?>
<?php

//TODO Make it setup-independent
require_once('../../../../wp-blog-header.php');

define( 'WP_ERRATA_PATH', plugin_dir_path(__FILE__) );
require_once (WP_ERRATA_PATH . 'model/wp_model.php');
require_once (WP_ERRATA_PATH . 'model/classes/Errata.php');

$mandatoryProperties = Errata::getMandatoryProperties();
$errata = new Errata();

foreach ($mandatoryProperties as $property) {
	if (isset($_POST[$property])) {
		$errata -> __set($property, $_POST[$property]);
	} else {
		throw new ErrataException("", ERROR_INCORRECT_DATA);
	}
}

$optionalProperties = Errata::getOptionalProperties();
foreach ($optionalProperties as $property) {
	if (isset($_POST[$property])) {
		$errata -> __set($property, $_POST[$property]);
	}
}

$model = new ErrataModel();
$model->newErrata($errata);

?>
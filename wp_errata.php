<?php
/*
Plugin Name: WP Errata
Plugin URI: https://github.com/estudiocaravana/WP-Errata
Description: A WordPress plugin for reporting, recording and managing errors in web site content.
Version: 0.1
Author: estudiocaravana
Author URI: http://estudiocaravana.com
License: A "Slug" license name e.g. GPL2
*/

global $errata_db_version;
$errata_db_version = "0.1";

define( 'WP_ERRATA_PATH', plugin_dir_path(__FILE__) );

require_once (WP_ERRATA_PATH . 'model/wp_model.php');

function errata_install(){

	$model = new ErrataModel();
	$model->install();

	add_option("errata_db_version", $errata_db_version);
}

register_activation_hook(__FILE__,'errata_install');


function errata_header(){
	$scripts = '
		<link rel="stylesheet" type="text/css" href="'.plugins_url('view/elements.css', __FILE__ ).'"/>	
		<script type="text/javascript" src="'.plugins_url('view/js/jquery-1.7.1.min.js', __FILE__ ).'"></script>
		<script id="com-estudiocaravana-errata-script" type="text/javascript" src="'.plugins_url('view/js/errata.js', __FILE__ ).'"></script>
	';

	echo $scripts;
}

add_action('wp_head', 'errata_header');

function errata_footer(){
	require(WP_ERRATA_PATH . 'view/elements.php');
}

add_action('wp_footer', 'errata_footer');


function errata_admin_actions(){
	require_once (WP_ERRATA_PATH . 'admin/errata_list.php');

	$errata_list = new ErrataList();
	$errata_list->process_errata_list_action();
}

add_action('admin_init', 'errata_admin_actions' );


function errata_admin_page(){
	require_once (WP_ERRATA_PATH . 'admin/errata_list.php');

	$errata_list = new ErrataList();
	add_submenu_page('tools.php', __('Errata','ecerpl'), __('Errata','ecerpl'), 'edit_posts', 'errata_list', array($errata_list, 'showErrataList'));
}

add_action('admin_menu', 'errata_admin_page');

?>
<?php
/*
 * Plugin Name: WooCommerce Correios Rastreio Parser
 * Description: Atualize automaticamente o andamento do pedido com o rastreio dos Correios
 * Plugin URI: http://asaferamos.com
 * Author: Asafe Ramos
 * Author URI: http://asaferamos.com
 * Version: 1.0
 * Requires at least: 4.2
 * License: GPL3
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require 'vendor/autoload.php';

if ( ! class_exists( 'WC_Correios_Status_Parser' ) ) {
	include_once dirname( __FILE__ ) . '/class-wc-correios-status-parser.php';
	include_once dirname( __FILE__ ) . '/includes/class-correios-parser-table.php';

	new WC_Correios_Status_Parser();
}
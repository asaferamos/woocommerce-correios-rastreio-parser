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
	// exit; // Exit if accessed directly.
}

# Our new data
$data = array(
    'objetos' => 'PL443877462BR'
);
$url = 'http://www2.correios.com.br/sistemas/rastreamento/newprint.cfm';
$ch = curl_init($url);
$postString = http_build_query($data, '', '&');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$correiosPage = utf8_encode(curl_exec($ch));
curl_close($ch);
// var_dump($correiosPage);die;

// $correiosPage = file_get_contents('test.html', true);
// var_dump($correiosPage);die;
$re = [
    'table'   => '/(?s)(?<=\<table class=\"listEvent sro\"\>)(.*?)(?=\<\/table\>)/',
    'tr'      => '/(?s)(?<=\<tr\>)(.*?)(?=\<\/tr\>)/',
    'dtEvent' => '/(?s)(?<=\<td class=\"sroDtEvent\" valign=\"top\">)(.*?)(?=\<\/td\>)/',
    'lbEvent' => '/(?s)(?<=\<td class=\"sroLbEvent\">)(.*?)(?=\<\/td\>)/'
];
preg_match_all($re['table'], $correiosPage, $cTable, PREG_SET_ORDER,0);

preg_match_all($re['tr'], $cTable[0][0], $cTr);
// var_dump($cTr[0]);die;
foreach ($cTr[0] as $key => $vTr) {
    preg_match_all($re['dtEvent'], $vTr, $vTd);
    $new_str = str_replace("&nbsp;", ' ', strip_tags($vTd[0][0]));
    $lines = explode("\n", $new_str);
    
    foreach ($lines as $line) {
        $line = ltrim($line);
        if(!empty($line))
            echo $line, '<br>';
    }
    
    preg_match_all($re['lbEvent'], $vTr, $vTd);

    $new_str = str_replace("&nbsp;", ' ', strip_tags($vTd[0][0]));
    $lines = explode("\n", $new_str);
    
    foreach ($lines as $line) {
        $line = ltrim($line);
        if(!empty($line))
            echo $line, '<br>';
    }
    echo '<hr>';
    // die;
}
// Print the entire match result

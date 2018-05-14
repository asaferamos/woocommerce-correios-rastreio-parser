<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Correios_Status_Parser {
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    public function admin_menu() {
        $page = add_submenu_page(
            'woocommerce',
            __('Correios Status Parser', 'wc_correios_status_parser'),
            __('Correios Status Parser', 'wc_correios_status_parser'),
            apply_filters('woocommerce_csv_order_role', 'manage_woocommerce'),
            'wc_correios_status_parser',
            array($this, 'output')
        );
    }
    
    public function output() {
        $args = array(
            'limit' => -1,
            'status' => 'processing',
        );
        $orders = wc_get_orders( $args );
        include( 'views/orders.php' );
        
        foreach ($orders as $key => $value) {
            if($value->get_order_number() != 251)
                die;
            $correiosTrack = $value->get_meta('_correios_tracking_code');
            WC_Correios_Status_Parser::getListEvents($correiosTrack);
            echo '<hr>';
        }
        
        
    }
    
    public function getListEvents($correiosTrack){
        # Our new data
        $data = array(
            'objetos' => $correiosTrack
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

    }
}
<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Correios_Status_Parser {
    private $_conf;
    
    public function __construct($_conf) {
        $this->_conf = $_conf;
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
        $data = array(
            'objetos' => $correiosTrack
        );
        
        $correiosPage = self::getPage($data);
        
        preg_match_all($this->_conf['regexp']['table'], $correiosPage, $cTable, PREG_SET_ORDER,0);

        preg_match_all($this->_conf['regexp']['tr'], $cTable[0][0], $cTr);

        foreach ($cTr[0] as $key => $vTr) {
            preg_match_all($this->_conf['regexp']['dtEvent'], $vTr, $vTd);
            $new_str = str_replace("&nbsp;", ' ', strip_tags($vTd[0][0]));
            $lines = explode("\n", $new_str);
            
            foreach ($lines as $line) {
                $line = ltrim($line);
                if(!empty($line))
                    echo $line, '<br>';
            }
            
            preg_match_all($this->_conf['regexp']['lbEvent'], $vTr, $vTd);

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
        

    }
    
    private function getPage($data){
        $ch = curl_init($this->_conf['urlParser']);
        $postString = http_build_query($data, '', '&');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $correiosPage = utf8_encode(curl_exec($ch));
        curl_close($ch);
        
        return $correiosPage;
    }
}
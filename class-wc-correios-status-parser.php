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
        
        $data = array();
        $objectsCompleteds = 0;
        foreach ($orders as $key => $order) {
                
            $Correios = new \Baru\Correios\RastreioParser();
            $correiosTrack = $order->get_meta('_correios_tracking_code');
            if($correiosTrack == "") continue;
            $Correios->setCode($correiosTrack);
            $evento = $Correios->getEventLast();
                        
            $data[] = array(
                'pedido'    => "<a href='" . $order->get_view_order_url() . "'>" . $order->get_order_number() . "</a>",
                'nome'      => $order->get_formatted_billing_full_name(),
                'rastreio'  => $correiosTrack,
                'status'    => "<b>" . $evento->getLabel() . "</b>",
                'data'      => $evento->getDate() . " " . $evento->getHour(),
                'descricao' => $evento->getDescription()
            );
            
            
            if($evento->getLabel() == 'Objeto entregue ao destinatário '){
                $objectsCompleteds++;
                // $order->update_status( 'completed',  'Objeto entregue ao destinatário');
            }
            
        }
        
        $table = new WC_Correios_Status_Parser_Table();
        $table->setData($data);
        $table->prepare_items();
        
        ?>

        <h1>Correios Rastreio Parser</h1>
        <div class="wrap">
            <p>
                Objetos entregues atualizados: <?php echo $objectsCompleteds; ?>
            </p>
            <?php $table->display(); ?>
        </div>
        <?php
        
    }
}
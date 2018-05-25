<?php
/*
 * Plugin Name: Paulund WP List Table Example
 * Description: An example of how to use the WP_List_Table class to display data in your WordPress Admin area
 * Plugin URI: http://www.paulund.co.uk
 * Author: Paul Underwood
 * Author URI: http://www.paulund.co.uk
 * Version: 1.0
 * License: GPL2
 */


// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class WC_Correios_Status_Parser_Table extends WP_List_Table{
    private $data;
    
    public function prepare_items(){
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();

        $data = $this->data;

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'pedido'    => 'Pedido',
            'nome'      => 'Nome',
            'rastreio'  => 'Rastreio',
            'status'    => 'Status',
            'data'      => 'Data/Hora',
            'descricao' => 'Descrição'
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'pedido':
            case 'nome':
            case 'rastreio':
            case 'status':
            case 'data':
            case 'descricao':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }

}

 
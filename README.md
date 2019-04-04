# WooCommerce Correios Rastreio Parser
Plugin Wordpress para atualização de status dos pedidos do WooCommerce para _concluído_.

### Instalação
`composer install`

### Dependência
O plugin se utiliza desta [lib](https://github.com/asaferamos/correios-rastreio-api-parser) para parsear da página dos correios 

É necessário a utilização do plugin WooCommerce Correios, deste [fork](https://github.com/asaferamos/woocommerce-correios/), que possui uma modificação que adiciona um novo status as orders, _**order-dispatched**_

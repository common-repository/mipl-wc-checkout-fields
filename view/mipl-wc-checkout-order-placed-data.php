<?php
$custom_orders_enabled = get_option('woocommerce_custom_orders_table_enabled');
if($custom_orders_enabled == 'no'){
    $order_id = $order->ID;
}else{
    $order_id = $order->get_id();
}

echo wp_kses_post(mipl_wc_get_order_custom_fields_data($order_id));
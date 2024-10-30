<?php
$setting_position        = get_post_meta($post->ID, '_mipl_wc_cf_setting_position', true);
$cart_field_position     = get_post_meta($post->ID, '_mipl_wc_cf_cart_field_position', true);
$setting_quantity_repeat = get_post_meta($post->ID, '_mipl_wc_cf_setting_field_repeat', true);
$group_specilization     = get_post_meta($post->ID, '_mipl_wc_cf_field_group_specilization', true);
$order_item_specilization= get_post_meta($post->ID, '_mipl_wc_cf_order_item_specilization', true);
$setting_category        = get_post_meta($post->ID, '_mipl_wc_cf_setting_product_category', true);
$setting_products        = get_post_meta($post->ID, '_mipl_wc_cf_setting_products', true);
$setting_order_status    = get_post_meta($post->ID, '_mipl_wc_cf_email_to_list',true);
$setting_products        = !empty($setting_products) ? $setting_products : array();


if(empty($setting_quantity_repeat)){
    $setting_quantity_repeat = array();
}

?>
<div class="mipl_wc_cf_checkout_setting checkout">
    <div class="setting_item required">
        <label><span class="setting_label"><?php esc_html_e('Fields position ( Checkout page )') ?>:</span></label><br>
        <select class="mipl_cf_setting_select" name="_mipl_wc_cf_setting_position" id="">
        
            <?php 
                $field_position = array(
                    'woocommerce_before_checkout_billing_form',
                    'woocommerce_after_checkout_billing_form',
                    'woocommerce_before_checkout_shipping_form',
                    'woocommerce_after_checkout_shipping_form',
                    'woocommerce_before_order_notes',
                    'woocommerce_after_order_notes',
                    'woocommerce_checkout_before_order_review_heading',
                    'woocommerce_review_order_before_payment',
                    'woocommerce_review_order_after_payment',
                    'woocommerce_review_order_before_submit',
                    'woocommerce_review_order_after_submit'
                );
               
                foreach($field_position as $position){
                    $pattern          = array('/woocommerce_/', '/_/'); 
                    $replace          = array('',' ');                   ;
                    $display_position = ucwords(preg_replace($pattern,$replace, $position));
                    $select           = '';
                    if($setting_position == $position){
                        $select = 'selected';
                    } ?>
                    <option value="<?php echo esc_attr($position)?>" <?php echo esc_attr($select) ?>><?php esc_html_e($display_position) ?></option>
                    <?php
                }
            ?>
        </select>
        <span class="mipl_wc_cf_setting_note"><?php _e("[Note: 1) Please set shipping zone to show fields before/after shipping form.<br>2) Only for 'Classic Checkout'.]")?></span>
    </div>

    <div class="setting_item required">
        <label><span class="setting_label"><?php esc_html_e('Fields position ( Product single page )') ?>:</span></label><br>
        <select class="mipl_cf_setting_select" name="_mipl_wc_cf_cart_field_position" id="">
            <option value=""><?php esc_html_e('Select Position') ?></option>
            <?php 
                $field_position = array(
                    'woocommerce_before_add_to_cart_quantity',
                );
               
                foreach($field_position as $position){
                    $pattern          = array('/woocommerce_/', '/_/'); 
                    $replace          = array('',' ');                   ;
                    $display_position = ucwords(preg_replace($pattern,$replace, $position));
                    $select           = '';
                    if($cart_field_position == $position){
                        $select = 'selected';
                    } ?>
                    <option value="<?php echo esc_attr($position) ?>" <?php echo esc_attr($select) ?>><?php echo esc_html_e($display_position) ?></option>
                    <?php
                }
            ?>
        </select>
        <span class="mipl_wc_cf_setting_note"><?php _e('[Note: 1) reCAPTCHA v2 will not be display in any position of product single page.</br>2) Please add "MIPL Checkout Fields Group" block.If not added this block, product single page fields not considerable.(only for checkout block)]')?></span>
    </div>


    <div class="setting_item">
    
        <?php
        $field_group = "false";
        $checked = "";
        if(!empty($group_specilization) && $group_specilization == 'true'){
            $field_group = "true";
            $checked = "checked";
        }
        ?>
       <input type="checkbox" id="field_group_specilization" class="mipl_wc_ck_field_groups_specilization" name="_mipl_wc_cf_field_group_specilization" value="<?php echo esc_attr($field_group) ?>" <?php echo esc_attr($checked) ?>>
       <label for="field_group_specilization"><b><?php esc_html_e('Display Fields based on the Order Items/Products') ?></b></label><br>
       <span class="mipl_wc_cf_setting_note"><?php esc_html_e('[Note: Keep uncheck to Display Frields Group only once on Checkout Page.]'); ?></span>
        
    </div>

    <div class="mipl_product_subfields" style="margin:0px 15px;border-bottom:1px solid #ddd">
        <div class="sub_setting_item order_item_specilization" style="display:none">
            <?php
                $all_product = "checked";
                $particular_product ="";
                if($order_item_specilization == "all_product"){
                    $all_product = "checked";
                }elseif ($order_item_specilization == "particular_product") {
                    $particular_product ="checked";
                }
            ?>
            <input type="radio" name="_mipl_wc_cf_order_item_specilization" id="all_product" value="all_product" <?php echo esc_attr($all_product) ?>>
            <label for="all_product"><?php esc_html_e('For all products') ?></label><br>
            <input type="radio" name="_mipl_wc_cf_order_item_specilization" id="particular_product" value="particular_product" <?php echo esc_attr($particular_product) ?>>
            <label for="particular_product"><?php esc_html_e('For particular product') ?></label>
        </div>
        <div class="sub_setting_item mipl_wc_cf_product_category mipl_wc_cf_product_select2 required" style="display:none">
            <label><span class="setting_label"><?php esc_html_e('Product category') ?>:</span></label><br>
                <?php
                $args = array(
                    'taxonomy'     => 'product_cat',
                    'hierarchical' => true,
                    'hide_empty'   => false
                );
                $all_product_categories = get_categories( $args );
                $all_categories = array();
                foreach($all_product_categories as $parent){
                    if ($parent->category_parent == 0){
                        $all_categories[] = $parent->term_id;
                    }
                }
                
                ?>
                <select name="_mipl_wc_cf_setting_product_category[]" class="setting_category js-example-basic-multiple mipl_wc_ck_select2" placeholder=<?php echo esc_attr("Select product category") ?> id="setting_cat" data-allow-clear=true  multiple="multiple">

                    <?php
                    foreach($all_categories as $parent){
                        $select = "";
                        if (in_array($parent,(array)$setting_category)) {
                        $select = 'selected';
                        }
                        ?>
                        <option value="<?php echo esc_attr($parent) ?>" <?php echo esc_attr($select)?>><?php esc_html_e(get_the_category_by_ID($parent)) ?></option>
                    <?php
                    }
                    ?>
                </select>
            
        </div>
        <div class="sub_setting_item mipl_wc_cf_products mipl_wc_cf_product_select2 " style="display:none">
            <label for=""><span class="setting_label"><?php esc_html_e('Products') ?>:</span></label><br>
            <select id="setting_products12" name="_mipl_wc_cf_setting_products[]" data-allow-clear=true placeholder="<?php echo esc_attr('Select product') ?>" class="js-example-basic-multiple form-control setting_products mipl_wc_ck_select2" multiple="multiple">
                
                <?php
                $products = wc_get_products( array( 'status' => 'publish', 'limit' => -1 ) );
                foreach ( $products as $product ){
                    $select = "";
                    if(in_array($product->get_id(),$setting_products))
                    {
                        $select = "selected";
                        ?>
                        <option value="<?php echo esc_attr($product->get_id()) ?>" <?php echo esc_attr($select)?>><?php esc_html_e($product->get_title()) ?></option>
                        <?php
                    }else{
                        ?>
                        <option value="<?php echo esc_attr($product->get_id()) ?>" ><?php esc_html_e($product->get_title()) ?></option>
                        <?php
                    }
                ?> 
                <?php
                }
                ?>
            </select>
            <?php
            if(empty($products)){
                ?>
                <span><b><?php esc_html_e('[Note: Please add products in WooCommerce products]') ?></b></span>
                <?php
            }        
            ?>
        </div>
        <div class="sub_setting_item mipl_field_repeate" style="display:none;">
            <?php
                $checked = ""; 
                if($setting_quantity_repeat == "repeat"){
                    $checked = 'checked';
                }
            ?>
            <span class="setting_label"><?php esc_html_e('Fields repetition:') ?></span><br>
            <input type="checkbox" class="product_repeate" id="mipl_wc_setting_field_repeat" name="_mipl_wc_cf_setting_field_repeat" value="repeat" <?php echo esc_attr($checked); ?>>
            <label for="mipl_wc_setting_field_repeat"> <?php esc_html_e('Fields repeat by quantity') ?> </label><br>
            
        </div>
    </div>
    <div class="setting_item email">
        <label for=""><span class="setting_label"><?php esc_html_e(' Email Template:') ?> </span><br><span style="color:gray"><?php esc_html_e('Note: This group fields will show on selected email template.')?></span><br>
        </label>
        <?php
        $status_list = array('new_order','customer_note','customer_invoice','customer_completed_order','customer_processing_order','cancelled_order','failed_order','customer_refunded_order');
        foreach($status_list as $item){
            $pattern = array('/_/'); 
            $replace = array(' ');                   ;
            $display_status = ucwords(preg_replace($pattern,$replace, $item));
            
            $checked = "";
            $post_meta = get_post_meta($post->ID);
            $meta_keys = array_keys($post_meta);
                        
            if(empty($setting_order_status)){
                $checked = "";
            }else{
                if( is_array($setting_order_status) && in_array($item,$setting_order_status)){
                    $checked = "checked";
                }else{
                    $checked = "";
                }
            }

            if(!in_array('_mipl_wc_cf_email_to_list', $meta_keys)){
                if($item == 'new_order' || $item == 'failed_order'){
                    $checked = "checked";
                }
            }
            
            ?>
            <input type="checkbox" id="<?php echo esc_attr($item) ?>" name="_mipl_wc_cf_email_to_list[]" value="<?php echo esc_attr($item) ?>" <?php  echo esc_attr($checked) ?>><label for="<?php echo esc_attr($item) ?>"><?php esc_html_e(ucwords($display_status)) ?></label><br>
        <?php
        }
        ?>
        
    </div>
</div>

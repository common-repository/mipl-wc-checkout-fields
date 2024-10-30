<?php

class MIPL_WC_CF_Single_Page{

    public $mipl_wc_single_custom_validated_data = null;
    public $mipl_wc_single_valid_field_groups_data = null;

    function mipl_woocommerce_remove_cart_item( $cart_item_key, $cart ){

        if( !session_id() ){ session_start(); }
       
        $product_id     = $cart->cart_contents[ $cart_item_key ]['product_id'];
        $variation_id   = $cart->cart_contents[ $cart_item_key ]['variation_id'];
        $new_product_id = !empty($variation_id) ? $variation_id : $product_id;  

        if(!isset($_SESSION['mipl_single_product_post_data'])){
            return false;
        }

        foreach ($_SESSION['mipl_single_product_post_data'] as $group_id => $group_cf_data) {
            unset($_SESSION['mipl_single_product_post_data'][$group_id][$new_product_id]);
        }
        
    }

    function mipl_new_order_action(){
        unset($_SESSION['mipl_wc_cf_cart_data']);
    }

    function mipl_file_preview(){

        $file_name = isset($_GET['file']) && !(empty($_GET['file'])) ? sanitize_text_field($_GET['file']) : '';
        $file_name = base64_decode($file_name, true);
        $file_url = MIPL_WC_CF_UPLOAD_PATH.$file_name;
        if(!file_exists($file_url)){
            return false;
        }
        $mime_type = mime_content_type($file_url);
        header('Content-type: '.$mime_type);
        readfile($file_url);
        exit();

    }

    function saved_single_product(){

        if( !isset($_SESSION['mipl_single_product_post_data']) || empty($_SESSION['mipl_single_product_post_data'])){
            return '';
        }

        global $product;
        $valid_cart_data = array();
        $display_data_flag = false;
        $single_product_id = $product->get_id();
        $cart_data         = WC()->cart->get_cart();
        $product_type = $product->get_type();
        foreach($cart_data as $cart_data_key => $cart_item){

            $cart_product_id = $cart_item['product_id'];
            if($cart_product_id == $single_product_id){
                $display_data_flag = true;
                $valid_cart_data['product_id'][] = $single_product_id;
                $valid_cart_data['variation_id'][] = isset($cart_item['variation_id']) ? $cart_item['variation_id'] : '';  
            }
            if($product_type == 'grouped'){
                $child_product = $product->get_children();
                foreach ($child_product as $key=>$pro_id) {
                    $valid_cart_data['product_id'][$key] = $pro_id;
                }
                $display_data_flag = true;
            }
        }
     
        if(!$display_data_flag){
            return '';
        }

        $session_custom_fields = $_SESSION['mipl_single_product_post_data'];

        ob_start();

        foreach ($session_custom_fields as $group_id => $group_cf_data) {

            $admin_custom_fields = get_post_meta($group_id, '_mipl_wc_cf_custom_field', true);
            $group_specilization = get_post_meta($group_id, '_mipl_wc_cf_field_group_specilization', true);

            foreach ($valid_cart_data['product_id'] as $key => $pro_id) {
                $product_id = $valid_cart_data['product_id'][$key];
                $variation_id = isset($valid_cart_data['variation_id'][$key]) ? $valid_cart_data['variation_id'][$key] : '';
                $new_product_id = !empty($variation_id) ? $variation_id : $product_id;
                
                if($group_specilization == 'false'){
                    unset($_SESSION['mipl_single_product_post_data'][$group_id][$new_product_id]);
                }
    
                $old_post_data = isset($session_custom_fields[$group_id][$new_product_id]) ? $session_custom_fields[$group_id][$new_product_id] : array();
                $field_repeat = get_post_meta($group_id, '_mipl_wc_cf_setting_field_repeat', true);
                $group_title   = get_the_title($group_id);
                $product_title = get_the_title($new_product_id);

                foreach($old_post_data as $key => $post_data){

                    if( $field_repeat != 'repeat' && $key>0 ){
                        continue;
                    }

                    $qty = $key+1;
                    $suffix = mipl_wc_cf_english_ordinal_suffix($qty);
                    ?>
                    <table style="margin-bottom:10px;">    
                    <span style="font-size:17px;font-weight:700;">
                        <?php esc_html_e("$suffix $group_title"."(".$product_title.")"); ?>
                    </span>
                    <?php
                    foreach($admin_custom_fields['field_label'] as $fld_key=>$fld_data){
                    
                        $post_data_name = "_mipl_wc_cf_{$group_id}_{$new_product_id}_{$qty}_{$admin_custom_fields['field_name'][$fld_key]}";
                        $field_value = isset($old_post_data[$key][$post_data_name])?$old_post_data[$key][$post_data_name]:'';

                        $option_value = isset($admin_custom_fields['option_value'][$fld_key]) ? $admin_custom_fields['option_value'][$fld_key] : '';

                        $field_type = isset($admin_custom_fields['field_type'][$fld_key]) ? $admin_custom_fields['field_type'][$fld_key] : '';

                        $option_value_arr = array();
                        if(!empty($option_value)){
                            $options = explode("\n", $option_value);
                            foreach($options as $option){
                                $option_arr = explode(':', $option);
                                if(count($option_arr)>=2){
                                    $option_value_arr[trim($option_arr[0])] = trim($option_arr[1]);
                                }else{
                                    $option_value_arr[trim($option_arr[0])] = trim($option_arr[0]);
                                }
                            }
                        }

                        if(in_array($field_type, array('select', 'multicheckbox', 'radio'))){
                            if(is_array($field_value)){
                                $field_value_temp = array();
                                foreach($field_value as $field_val_item){
                                    $field_value_temp[] = isset($option_value_arr[$field_val_item]) ? $option_value_arr[$field_val_item] : '';
                                }
                                $field_value = implode(', ', $field_value_temp);
                            }else{
                                $field_value = isset($option_value_arr[$field_value]) ? $option_value_arr[$field_value] : '';
                            }
                        }
                        
                        if(is_array($field_value) && count($field_value)>0){
                            $field_value = implode(',', $field_value);
                        }

                        if($admin_custom_fields['field_type'][$fld_key] == 'recaptcha'){
                            continue;
                        }

                        if($admin_custom_fields['field_type'][$fld_key] == 'file'){
                            
                            $path = parse_url($field_value, PHP_URL_PATH);
                            $file_path = explode('mipl_checkout_files', $path);
                            $file = base64_encode($file_path[1]);
                            $file_name = basename($path);
                            ?>      
                                <tr>
                                    <td style="width:50%">
                                        <?php echo esc_html($admin_custom_fields['field_label'][$fld_key]) ?> : 
                                    </td> 
                                    <td>
                                        <a href="?mipl_action=file_preview&file=<?php echo esc_html($file) ?>" target="_blank"><?php echo esc_html($file_name); ?>
                                    </td>
                                </tr>
                            <?php

                        }else{

                            ?>   
                                <tr>
                                    <td style="width:50%">
                                        <?php echo esc_html($admin_custom_fields['field_label'][$fld_key]) ?> : 
                                    </td> 
                                    <td>
                                        <?php echo esc_html($field_value); ?>
                                    </td>
                                </tr>
                            <?php

                        }
                    }
                    ?>
                    </table>
                    <?php
                }
            }
        }

        $summary_content = ob_get_contents();
        ob_end_clean();
        return $summary_content;

    }


    function single_page_fields(){

        $_SESSION['temp_custom_fields_data'] = $_POST;
        $fields_summary = $this->saved_single_product();
        ?>
        <div class='mipl_wc_single_product_custom_fields_summary'>
            <?php echo $fields_summary; ?>
        </div>
        <div class="mipl-wc-single-product-page-field-group"></div>
        <input type="hidden" name="mipl-wc-single-page-addtocart" value="1" />
        <?php

    }


    function show_custom_fields_in_single_page(){

        global $post;
        $req_product_qty = isset($_POST['quantity']) ? (int)sanitize_text_field($_POST['quantity']) : 0;
        $product_id      = $post->ID;
        $product_qty     = 1;
        $variation_id = isset($_POST['variation_id']) ? sanitize_text_field($_POST['variation_id']) : '';
        $cart_product_info = array();   
        $product_cats_ids  = wc_get_product_term_ids( $product_id, 'product_cat' );

        $cart_product_info[] = array(
            'id' => $product_id,
            'category' => $product_cats_ids,
            'quantity' => $product_qty,
            'variation_id' => $variation_id
        );

        $product_info = wc_get_product($product_id);
        $product_type = $product_info->get_type();
        if($product_type == 'grouped') {
            $cart_product_info = array();   
            
            foreach ($_POST['quantity'] as $product_id => $product_qty) {
                $product_cats_ids  = wc_get_product_term_ids( $product_id, 'product_cat' );
                $cart_product_info[] = array(
                    'id' => $product_id,
                    'category' => $product_cats_ids,
                    'quantity' => $product_qty,
                    'variation_id' => $variation_id
                );
            }
                
        }
        
        if(!isset($_SESSION['temp_custom_fields_data'])){
            $temp_data = array();
        }else{
            $temp_data = $_SESSION['temp_custom_fields_data'];
        }
    
        $_SESSION['temp_custom_fields_data'] = array_merge($temp_data, $_POST);
        $current_wc_hook = current_filter();
        $validated_fields_groups = $this->get_valid_cart_groups($cart_product_info, 'woocommerce_before_add_to_cart_quantity');

        if(($product_type == 'grouped')){
            foreach ($_POST['quantity'] as $product_id => $req_product_qty) {
                $product_data = $this->get_product_data($product_id, $req_product_qty);
                $this->mipl_get_single_product_fields($validated_fields_groups, $product_id, 1, $product_data['req_product_qty']);
            }

        }elseif($product_type == 'variable'){
            $product_data = $this->get_product_data($variation_id, $req_product_qty);
            $product_id = isset($_POST['variation_id']) ? sanitize_text_field($_POST['variation_id']) : 0;
            $this->mipl_get_single_product_fields($validated_fields_groups, $product_id, $product_qty, $product_data['req_product_qty']);

        }else{
            $product_data = $this->get_product_data($product_id, $req_product_qty);
            $this->mipl_get_single_product_fields($validated_fields_groups, $product_id, $product_qty, $product_data['req_product_qty']);
        }
        
        die();
    }

    function get_product_qty_from_card($product_id, $product_type){

        $cart_items = WC()->cart->get_cart();
        $cart_product_qty = 0;
        if(!empty($cart_items)){

            foreach ($cart_items as $key => $cart_item) {
               
                if($product_type == 'variation'){
                    if( $product_id == $cart_item['variation_id'] ){
                        $cart_product_qty = $cart_item['quantity'];
                    }
                }else if($product_id == $cart_item['product_id']){
                    $cart_product_qty = $cart_item['quantity'];
                }
                
            }
        }
        return $cart_product_qty;

    }

    function get_product_data($product_id, $req_product_qty){
        $product_data = [];
        $product_info = wc_get_product($product_id);
        $product_data['stock_quantity'] = $stock_quantity = $product_info->get_stock_quantity();
        $product_data['backorder'] = $backorder = $product_info->get_backorders();
        $product_data['stock_manage'] = $stock_manage = $product_info->get_manage_stock();
        $product_data['product_type'] = $product_type = $product_info->get_type();
        $cart_product_qty = $this->get_product_qty_from_card($product_id,$product_type);
        $product_data['total_qty'] = $total_qty = $req_product_qty+$cart_product_qty;
        
        if($stock_manage && $backorder == 'no' && ($total_qty > $stock_quantity)){
            $req_product_qty = $stock_quantity-$cart_product_qty;
        }

        $product_data['req_product_qty'] = $req_product_qty;
        return $product_data;
    }


    function mipl_get_single_product_fields($validated_fields_groups, $product_id, $product_qty, $req_product_qty){

        foreach ($validated_fields_groups as $grp_id=>$fields_data) {

            if (isset($_SESSION['mipl_single_product_post_data'][$grp_id][$product_id])) {
                $qty = count($_SESSION['mipl_single_product_post_data'][$grp_id][$product_id]);
                $temp_qty[] = $qty+1;
                $product_qty = max($temp_qty);
            }
            
            $field_group_id = $fields_data['group_id'];
            if(empty($fields_data)){
                continue;
            }

            $all_custom_fields   = get_post_meta( $field_group_id, '_mipl_wc_cf_custom_field', true );
            $group_specilization = get_post_meta( $field_group_id, '_mipl_wc_cf_field_group_specilization', true );
            $field_group_setting = get_post_meta( $field_group_id, '_mipl_wc_cf_group_setting', true );
            $hide_title          = isset( $field_group_setting['hide_title'] ) ? $field_group_setting['hide_title'] : '';
            $product_setting_field_repeat = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);
            $custom_field        = !empty( $fields_data['fields'] ) ? $fields_data['fields'] : '';
            $error_field_error   = array('');

            if($group_specilization == 'false'){return false;}

            if( isset($all_custom_fields['errors']) ){
                foreach( $all_custom_fields['errors'] as $fld_key => $fld_value ) {
                    $error_field_error[] = count($fld_value);
                }
            }

            if( isset($custom_field['field_label']) && max($error_field_error) == count($custom_field['field_label']) ){
                continue;
            }

            if( !isset($custom_field) || empty($custom_field) ){ continue; }

            $group_title   = get_the_title($field_group_id);
            $product_title = get_the_title($product_id);
            if($product_setting_field_repeat == 'repeat'){
              
                for($qty = $product_qty; $qty <= $product_qty+$req_product_qty-1; $qty++){

                    $suffix = mipl_wc_cf_english_ordinal_suffix($qty);
                    ?>
                    <div class="mipl-wc-single-product-fields">
                        <h4 class='mipl_wc_cf_group_title'>
                            <?php esc_html_e("$suffix $group_title ($product_title)"); ?>
                        </h4>
                        <?php
                        $this->client_side_cart_fields($custom_field, $field_group_id, $qty, $product_id);
                        ?>
                    </div>
                    <?php
                }

            }elseif ($product_setting_field_repeat != 'repeat' && $product_qty == 1) {
                ?>
                    <div class="mipl-wc-single-product-fields">
                        <h4 class='mipl_wc_cf_group_title'>
                            <?php esc_html_e("$group_title ($product_title)"); ?>
                        </h4>
                        <?php
                            $this->client_side_cart_fields($custom_field, $field_group_id, 1, $product_id);
                        ?>
                    </div>
                <?php
            }
        }
    }


    function get_valid_cart_groups( $cart_product_info, $position = '' ){

        $valid_field_groups = array();
        $args_of_cf = array(
            'post_type'   => MIPL_WC_CF_POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1
        );

        if( trim($position) != '' ){
            $args_of_cf['meta_key']   = '_mipl_wc_cf_cart_field_position';
            $args_of_cf['meta_value'] = $position;
        }

        $total_fields_data = get_posts($args_of_cf);
        if( empty($total_fields_data) ){
            return array();
        }
        
        foreach( $total_fields_data as $field_data ){
            $field_group_id    = $field_data->ID;
            $field_group_title = $field_data->post_title;
            $custom_field  = get_post_meta($field_group_id, '_mipl_wc_cf_custom_field', true);
            $group_setting = get_post_meta($field_group_id, '_mipl_wc_cf_group_setting', true);
          
            if( (empty($custom_field)) || (!empty($group_setting['deactive_group'])) ){ 
                continue;
            }

            $group_specilization = get_post_meta( $field_group_id, '_mipl_wc_cf_field_group_specilization', true );
            $order_item_specilization = get_post_meta( $field_group_id, '_mipl_wc_cf_order_item_specilization', true );

            if($group_specilization == "false"){
                continue;
            }

            $setting_products = get_post_meta($field_group_id, '_mipl_wc_cf_setting_products', true);
            $setting_products = (!empty($setting_products)) ? $setting_products : array();

            $product_setting_category = get_post_meta($field_group_id, '_mipl_wc_cf_setting_product_category', true);
            $product_setting_category = (!empty($product_setting_category)) ? $product_setting_category : array();

            $product_setting_field_repeat = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);
            if(empty($product_setting_field_repeat)){
                $product_setting_field_repeat = array();
            }
            
            $products_data = array();
            foreach($cart_product_info as $cart_product_info_value){
                
                $product_id = $cart_product_info_value["id"];
                $term_id = $cart_product_info_value["category"];
                $product_quantity = ($product_setting_field_repeat == "repeat") ? $cart_product_info_value["quantity"] : 1;                
               
                if($order_item_specilization == "all_product" && $group_specilization == "true"){
                    if(!empty($cart_product_info_value['variation_id'])){
                        $product_id = $cart_product_info_value['variation_id'];
                    }
                    $products_data[] = array('id'=>$product_id, 'qty'=>$product_quantity);
                }

                if(($group_specilization == "true" && $order_item_specilization == "particular_product") && 
                (count(array_intersect($term_id, $product_setting_category)) <= 0) && 
                (!in_array($product_id, $setting_products))){
                    continue;
                }

                if(!isset($valid_field_groups[$field_group_id]) && ($group_specilization == "true" && $order_item_specilization == 'particular_product')){
                    if(!empty($cart_product_info_value['variation_id'])){
                        $product_id = $cart_product_info_value['variation_id'];
                    }
                    $products_data[] = array('id'=>$product_id, 'qty'=>$product_quantity);
                }

                $valid_field_groups[$field_group_id]['fields'] = $custom_field;
                
            }  

            $valid_field_groups[$field_group_id]['group_id'] = $field_group_id;
            $valid_field_groups[$field_group_id]['products'] = $products_data;

        }

        return $valid_field_groups;

    }

    function client_side_cart_fields( $custom_field, $field_group_id, $product_qty, $product_id = '' ){
        
        global $MIPL_WC_RECAPTCHA_FLAG;

        if(!isset($custom_field['field_label'])){
            return false;
        }
        
        $error_position = isset($custom_field['errors']) ? $custom_field['errors'] : array();
        
        $array_keys = array();
        if(isset($custom_field['errors'])){
            foreach($custom_field['errors'] as $key => $values){
                foreach($values as $key1 => $value){
                    $array_keys[$key1] = $value;
                }
            }
        }

        $custom_attributes = array();
        foreach($custom_field['field_label'] as $cf_key => $cf_value){
    
            if(in_array($cf_key, array_keys($array_keys))){
                continue;
            }
    
            $type  = isset($custom_field["field_type"][$cf_key]) ? $custom_field["field_type"][$cf_key] : "";
            $label = isset($custom_field["field_label"][$cf_key]) ? $custom_field["field_label"][$cf_key] : "";
            $default_value  = isset($custom_field["default_value"][$cf_key]) ? $custom_field["default_value"][$cf_key] : "";
            $option_values = isset($custom_field["option_value"][$cf_key]) ? $custom_field["option_value"][$cf_key] : "";
            $name = isset($custom_field["field_name"][$cf_key]) ? $custom_field["field_name"][$cf_key] : "";
            $check_placeholder = isset($custom_field['placeholder_checkbox'][$cf_key]) ? $custom_field['placeholder_checkbox'][$cf_key] : "";
            $file_size = isset($custom_field['file_size'][$cf_key]) ? $custom_field['file_size'][$cf_key] : "";
            $file_type = isset($custom_field['file_type'][$cf_key]) ? $custom_field['file_type'][$cf_key] : "";
            
            $current_position         = current_filter();
            $recaptcha_field_position = array("woocommerce_review_order_before_submit", "woocommerce_review_order_after_submit");

            $recaptcha_enable_checkbox = get_option('_mipl_wc_recaptcha_enable_checkbox');
            $recaptcha_type = get_option('_mipl_wc_recaptcha_recaptcha_type');

            if($type == 'recaptcha' && (isset($recaptcha_enable_checkbox) && $recaptcha_enable_checkbox == true)){
                continue;
            }
    
            if($type == 'recaptcha' && !in_array($current_position, $recaptcha_field_position) && (isset($recaptcha_type) && $recaptcha_type == 'reCAPTCHA_v2')){
                continue;
            }
    
            // $acceptance_field_position = array("woocommerce_review_order_before_payment", "woocommerce_review_order_after_payment", "woocommerce_review_order_before_submit", "woocommerce_review_order_after_submit");
            
            // if($type == 'checkbox' && !in_array($current_position, $acceptance_field_position)){
            //     continue;
            // }
            
            if($type == 'recaptcha' && $MIPL_WC_RECAPTCHA_FLAG == 1){
                $MIPL_WC_RECAPTCHA_FLAG++;
            }else if($type == 'recaptcha' && $MIPL_WC_RECAPTCHA_FLAG > 1){
                continue;
            }
            
            $custom_attributes['group_id'] = $field_group_id;
            $custom_attributes['cf_name']  = $name;
            
            if( !empty($file_type) ){
                $custom_attributes['file_type'] = implode(',', $file_type);
                $custom_attributes['file_size'] = $file_size;
            }

            $placeholder = "";

            if( $check_placeholder === "yes" ){
                $placeholder   = $default_value;
                $default_value = "";
            }elseif( $type == "select" ) {
                $placeholder = "Select $label";
            }

            $field_class = 'form-row-wide';
            $class       = 'mipl_wc_custom_fields';
            $span_class  = 'mipl_wc_custom_fields_span';
    
            $field_options = array();
            $required = false;
    
            if( !empty($custom_field["required_field"][$cf_key]) ){
                $required = true;
            }

            $class = ( $type == 'radio' ) ? "mipl_radio_input" : "";

            if($type=="checkbox"){
                if($default_value == 'yes'){
                    $default_value = 1;
                }
            }

            if( $type == 'select' || $type == 'radio' || $type == 'multicheckbox' ){
    
                $label_value   = preg_split("/[\n]+/", trim($option_values));
                $explode_array = array();

                foreach( $label_value as $option_key => $option_value ){

                    if( strpos($option_value, ":") ){

                        $tmp = explode(':', trim($option_value), 2);

                        if( !empty($tmp[0] && !empty($tmp[1])) ){

                            $explode_array[trim($tmp[0])] = trim($tmp[1]);
                        }

                    }else{

                        $tmp = explode('/n', trim($option_value));
                        
                        if( !empty($tmp[0]) ){
                            $explode_array[trim($tmp[0])] = trim($tmp[0]);
                        }

                    }
                }
              
                $field_options = $explode_array;
                
                if($type == 'select'){
                    $new_option[''] = "Select {$label}";
                    foreach($field_options as $opt_key => $opt_value){
                        $new_option[$opt_key] = $opt_value;
                    }
                    $field_class .= ' mipl-cf-select';
                    $field_options = $new_option;
                }
                
            }

            $default_options = array();

            if( $type == "multicheckbox" ){
                $default_values = preg_split("/[\n]+/", trim($default_value));
                $checkbox_array = array();
                
                foreach($default_values as $option_key => $option_value){
                
                    if( strpos($option_value, ":") ){
                        $tmp = explode(':', trim($option_value));

                        if( !empty($tmp[0] && !empty($tmp[1])) ){
                            $checkbox_array[trim($tmp[0])] = trim($tmp[1]);
                        }

                    }else{

                        $tmp = explode('/n', trim($option_value));

                        if(!empty($tmp[0])){
                            $checkbox_array[trim($tmp[0])] = trim($tmp[0]);
                        }

                    }
                }
    
                $default_options = $checkbox_array;
            }

            if( $type == 'color' ){
                $field_class .= ' mipl_colorpicker';
                $color_picker = "mipl_color_picker";
                $type         = 'text';
                $value        = "#f82525";
            }
    
            if( $type == 'date' ){
                $field_class .= ' mipl_date_picker';
                $type = 'text';
            }else if( $type == 'time' ){
                $field_class .= ' mipl_time_picker';
                $type = 'text';
            }else if( $type == 'datetime-local' ){
                $field_class .= ' mipl_datetime_picker';
                $type = 'text';
            }

            // $product_qty
            $wc_field_name = '_mipl_wc_cf_'.$field_group_id.'_'.$product_id.'_'.$product_qty.'_'.$name;
            if(isset($_SESSION['temp_custom_fields_data'][$wc_field_name])){
                $default_value = $_SESSION['temp_custom_fields_data'][$wc_field_name];
            }
            if( $type == 'multicheckbox' ){
                ?>
                <p class='mipl_custom_field'>
                <span><?php echo esc_attr($label) ?></span>
                <?php 
                if($required){?>
                <abbr class="required" title="required">*</abbr>
                <?php
                }else{?>
                <span class="optional"><?php esc_html_e("(optional)") ?></span>
                <?php
                }
                ?>
                <br>
                <?php

                    $temp_data = is_array($default_value) ? $default_value : array();
                    $default_field_keys = is_array($default_options) ? array_keys($default_options) : array();

                    foreach($field_options as $checkbox_key => $checkbox_value){
                        $check = "";
                        
                        if( in_array($checkbox_value, $default_options) || in_array($checkbox_key, $default_field_keys) || in_array($checkbox_key, $temp_data)){
                            $check = "checked";
                        }
                        ?>
                        
                        <label for="<?php echo esc_attr($field_group_id.$product_id.$name.$checkbox_key.$product_qty) ?>"> 
                        <input type="checkbox" id="<?php echo esc_attr($field_group_id.$product_id.$name.$checkbox_key.$product_qty) ?>"
                            name="<?php echo esc_attr($wc_field_name.'[]') ?>" value="<?php echo esc_attr($checkbox_key) ?>" class="required"
                            <?php echo esc_attr($check) ?>><?php echo esc_html($checkbox_value) ?>
                        </label>
                        <?php
                    }
                ?>
                    </p>
                <?php
    
            }else{
                ?>
                    <div class="mipl_wc_cf_form_field">
                <?php
                woocommerce_form_field($wc_field_name, array(
                    'type'        => $type,
                    'required'    => $required,
                    'label'       => $label,
                    'class'       => $class,
                    'input_class' => array($field_class),
                    'custom_attributes' => $custom_attributes,
                    'options'     => $field_options,    
                    'id'          => $wc_field_name,
                    'placeholder' => $placeholder
                ), $default_value);
                ?>
                    </div>
                <?php
            }
            
        }
    }


    // validation of single product page custom fields
    function validate_cart_fields( $passed_validation, $product_id, $quantity, $variation_id='', $variation='' ){

        if(!isset($_POST['mipl-wc-single-page-addtocart'])){
            return true;
        }

        $product_info = wc_get_product($product_id);
        $product_type = $product_info->get_type(); 
       
        if(!empty($variation_id)){

            $product_data= $this->get_product_data($variation_id, $quantity);
            $quantity = $product_data['req_product_qty'];
           
        }elseif($product_type == 'simple'){
           
            $product_data = $this->get_product_data($product_id, $quantity);
            $quantity = $product_data['req_product_qty'];

        }

        if( $product_data['stock_manage'] && 
        $product_data['backorder'] == 'no' && 
        $product_data['total_qty'] > $product_data['stock_quantity']){
            return true;
        }

        
        $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
        $cart_product_info[] = array(
            'id'       => $product_id,
            'category' => $product_cats_ids,
            'quantity' => $quantity,
            'variation_id' => $variation_id
        );

        $validated_fields_groups = $this->get_valid_cart_groups($cart_product_info, 'woocommerce_before_add_to_cart_quantity');
        $errors = array();
        $error_msg = array();
        $new_post = $_POST;
        $temp_qty = array();
        
        foreach ( $validated_fields_groups as $field_group_id => $fields_data ) {
            $product_id = !empty($variation_id) ? $variation_id : $product_id;  
            if (isset($_SESSION['mipl_single_product_post_data'][$field_group_id][$product_id])) {
                $session_data = $_SESSION['mipl_single_product_post_data'];
                $qty = count($_SESSION['mipl_single_product_post_data'][$field_group_id][$product_id]);
                $temp_qty[] = $qty+$quantity;
                $new_post = $this->prepared_post_data($session_data, $_POST);
                $quantity = max($temp_qty);
            }

            $custom_field = !empty($fields_data['fields']) ? $fields_data['fields'] : '';
            if( empty($custom_field) ){ continue; }
        
            $group_title  = !empty($field_group_id) ? get_the_title($field_group_id) : '';
            $group_specilization = get_post_meta($field_group_id, '_mipl_wc_cf_field_group_specilization', true);
            $product_setting_field_repeat = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);

            if($product_setting_field_repeat == 'no_repeat'){
                $quantity = 1;
            }

            $validated_array = mipl_wc_cf_get_order_fields($custom_field, $field_group_id, $product_id, $quantity);
            $return_data = $this->mipl_validated_post_data($custom_field, $field_group_id, $validated_array, $new_post, $quantity, $product_id);
            $post_data[] = $return_data['post_data'];
            $errors[] = $return_data['error'];
            $single_product_post_data[$field_group_id][$product_id] = $return_data['post_data'];
        }

        if(empty($errors)){ return true; }

        foreach ($errors as $key => $error_with_grp_id) {
            $error_msg[] = $this->mipl_get_error_message($error_with_grp_id, $single_product_post_data);
            
        }

        $message = array();
        foreach ($error_msg as $err_key => $message_data) {
            if(is_array($message_data) && !empty($message_data)){
                foreach($message_data as $message_str){
                $message[] = '<span class="mipl-validation-msg">'.$message_str.'</span>';
                }
            }
        }

        if(!empty($message)){
            $message_msg = implode('<br>',$message);
            wc_add_notice($message_msg , 'error');
            return false;
        }

        $this->set_single_product_page_cf_data($single_product_post_data);
        
        return true;

    }

    function set_single_product_page_cf_data($single_product_post_data){
        if(!isset($_SESSION['mipl_single_product_post_data'])){

            $_SESSION['mipl_single_product_post_data'] = $single_product_post_data;
        
        }else{

            foreach ($single_product_post_data as $grp_id => $group_data) {
                
                if(!isset($_SESSION['mipl_single_product_post_data'][$grp_id])){
                    $_SESSION['mipl_single_product_post_data'][$grp_id] = $single_product_post_data[$grp_id];
                }else{
                    foreach ($group_data as $product_id => $product_data) {
                        
                        $_SESSION['mipl_single_product_post_data'][$grp_id][$product_id] = $single_product_post_data[$grp_id][$product_id];
                        
                    }
                }
            
            }
        
        }
    }

    function mipl_get_error_message($error_with_grp_id, $single_product_post_data){
        $error_msg_array = array();
        foreach ($error_with_grp_id as $grp_id => $error_with_product_id) {
            foreach ($error_with_product_id as $product_id => $error_with_qty) {
                foreach ($error_with_qty as $qty => $error_msg) {
                    foreach($error_msg as $message){
                       
                        $number_with_suffix = mipl_wc_cf_english_ordinal_suffix($qty+1);
                        
                        $group_title = !empty(get_the_title($grp_id)) ? get_the_title($grp_id) : '';
                        $product_title = !empty(get_the_title($product_id)) ? "(".get_the_title($product_id).")" : '';
                        $errors_messages = $number_with_suffix." ".$group_title." ".$product_title. ": ".$message;
                        if(empty($group_title) && empty($number_with_suffix)){
                            $errors_messages = $message;
                        }
                        $error_msg_array[] = $errors_messages; 

                    }
                    
                }
               
            }
        }
        return $error_msg_array;
    }


    function prepared_post_data($session_data, $post){
        $new_post = $post;
        foreach ($session_data as $grp_id => $posted_data) {
            foreach ($posted_data as $pro_id => $key_value_data) {
                foreach ($key_value_data as $qty => $data) {
                    $new_post += $data;
                }
            }
        }
        return $new_post;
    }

    
    function mipl_validated_post_data($custom_field, $field_group_id, $validated_array, $post, $product_qty = "", $product_id = ""){

        $validated_post_data = array();
        foreach($custom_field['field_name'] as $fld_key => $fld_value){
            if($product_qty >= 1){
                for ($i=1; $i <= $product_qty; $i++) { 
                    $post_data_name = "_mipl_wc_cf_{$field_group_id}_{$product_id}_{$i}_{$custom_field['field_name'][$fld_key]}";
                    if(isset($post[$post_data_name]) && is_array($post[$post_data_name])){
                        $validated_post_data[$post_data_name] = array_map('sanitize_text_field', $post[$post_data_name]);
                    }else{
                        $validated_post_data[$post_data_name] = isset($post[$post_data_name]) && !empty($post[$post_data_name]) ? sanitize_text_field($post[$post_data_name]) : '';
                    }
                }
            }
        }
        foreach ($validated_array as $key => $validating_array) {
            $validated_data = new MIPL_WC_CF_Input_Validation($validating_array, $validated_post_data);
            $validated_data->validate();
            if( empty($error) ){
                $error = array();
            }
            $error[$field_group_id][$product_id][$key] = $validated_data->get_errors();
            $post_data[$key] = $validated_data->get_valid_data();
        }
        $return_data['post_data'] =  $post_data;
        $return_data['error'] =  $error;
        return $return_data;

    }

}
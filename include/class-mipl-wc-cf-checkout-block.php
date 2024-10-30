<?php

class MIPL_WC_CF_Checkout_Fields{

    function mipl_get_cart_product_info(){

        $cart_items = WC()->cart->get_cart();
        $cart_product_info = array();
    
        foreach ($cart_items as $key => $cart_item) {
            $variation_id = $cart_item['variation_id'];
            $product_cats_ids = wc_get_product_term_ids( $cart_item['product_id'], 'product_cat' );
            $cart_product_info[] = array(
                'id' => $cart_item['product_id'],
                'category' => $product_cats_ids ,
                'quantity' => $cart_item['quantity'],
                'variation_id' => $variation_id
            );
            
        }
        $_SESSION['mipl_wc_cf_product_data'] = $cart_product_info;

    }


    // Admin side valide fields
    function mipl_get_valid_groups(){

        $args_of_cf = array(
            'post_type'   => MIPL_WC_CF_POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1
        );

        $total_fields_data = get_posts( $args_of_cf );
       
        if( empty($total_fields_data) ){
            return array();
        }
        $valid_field_groups = array();
        foreach( $total_fields_data as $field_data ){

            $field_group_id = $field_data->ID;
            $field_group_title = $field_data->post_title;
           
            $custom_field = get_post_meta($field_group_id, '_mipl_wc_cf_custom_field', true);
            $group_setting = get_post_meta($field_group_id, '_mipl_wc_cf_group_setting', true);

            // group setting details
            $group_specilization = get_post_meta($field_group_id, '_mipl_wc_cf_field_group_specilization', true);
            $field_repeatation = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);
            $new_field_repeatation = 'No';
            if($field_repeatation == "repeat"){
                $new_field_repeatation = 'Yes';
            }
            $order_item_specilization = get_post_meta($field_group_id, '_mipl_wc_cf_order_item_specilization', true);
            $dep_product_data = array();
            if($order_item_specilization == 'particular_product'){
                $setting_products = get_post_meta($field_group_id, '_mipl_wc_cf_setting_products', true);
                $setting_product_category = get_post_meta($field_group_id, '_mipl_wc_cf_setting_product_category', true);
                $product_name = array();
                $category_name = array();
                if(is_array($setting_products) && count($setting_products)>0){
                    foreach($setting_products as $product_id){
                        $product_name[] = get_the_title($product_id);
                    }
                }
                if(is_array($setting_product_category) && count($setting_product_category)>0){
                    foreach($setting_product_category as $cat_id){
                        $category_name[] =  get_term( $cat_id )->name;
                    }
                }
                $dep_product_data['product'] = is_array($product_name) && count($product_name)>0 ? implode(', ', $product_name) : '';
                $dep_product_data['category'] = is_array($category_name) && count($category_name)>0 ? implode(', ', $category_name) : '';
            }
            $valid_field_groups[$field_group_id]['group_specilization'] = $group_specilization;
            $valid_field_groups[$field_group_id]['group_repetition'] = $new_field_repeatation;
            $valid_field_groups[$field_group_id]['product_info'] = $dep_product_data;
            $cf_data = $this->get_rearranged_cf($custom_field);
            $valid_field_groups[$field_group_id]['group_id'] = $field_group_id;
            $valid_field_groups[$field_group_id]['fields'] = $cf_data;
            $valid_field_groups[$field_group_id]['group_title'] = $field_group_title;
            if( (empty($custom_field)) || (!empty($group_setting['deactive_group']))){ 
                $valid_field_groups[$field_group_id]['deactive_group'] = $field_group_id;
                $valid_field_groups[$field_group_id]['fields'] = [];

            }
            
        }

        return $valid_field_groups;

    }


    function get_rearranged_cf($custom_field){

        if(empty($custom_field)){ return false; }
        
        $array_keys = array();
        if(isset($custom_field['errors'])){
            foreach($custom_field['errors'] as $key => $values){
                foreach($values as $key1 => $value){
                    $array_keys[$key1] = $value;
                }
            }
        }

        $cf_data = array();
        $fld_key1 = 0;
        foreach ($custom_field['field_label'] as $fld_key => $fld_data) {

            if(in_array($fld_key, array_keys($array_keys))){
                continue;
            }

            if(isset($custom_field['field_type'][$fld_key]) && $custom_field['field_type'][$fld_key] == 'recaptcha'){
                continue;
            }

            $cf_data[$fld_key1]['field_label'] =   isset($custom_field['field_label'][$fld_key])?$custom_field['field_label'][$fld_key]:"";
            $cf_data[$fld_key1]['field_name'] =    isset($custom_field['field_name'][$fld_key])?$custom_field['field_name'][$fld_key]:"";
            $cf_type = $cf_data[$fld_key1]['field_type'] =    isset($custom_field['field_type'][$fld_key])?$custom_field['field_type'][$fld_key]:"";
            $default_options = $cf_data[$fld_key1]['default_value'] = isset($custom_field['default_value'][$fld_key])?$custom_field['default_value'][$fld_key]:"";
            $option_values = $cf_data[$fld_key1]['option_value'] = isset($custom_field['option_value'][$fld_key])?$custom_field['option_value'][$fld_key]:"";
            $cf_data[$fld_key1]['required_field'] = isset($custom_field['required_field'][$fld_key])?$custom_field['required_field'][$fld_key]:"";
            $cf_data[$fld_key1]['placeholder_checkbox'] = isset($custom_field['placeholder_checkbox'][$fld_key])?$custom_field['placeholder_checkbox'][$fld_key]:"";
            $cf_data[$fld_key1]['file_size'] = isset($custom_field['file_size'][$fld_key])?$custom_field['file_size'][$fld_key]:"";
            $cf_data[$fld_key1]['file_type'] = isset($custom_field['file_type'][$fld_key])?$custom_field['file_type'][$fld_key]:"";


            if( $cf_type == 'select' || $cf_type == 'radio' || $cf_type == 'multicheckbox' ){

                $label_value = preg_split("/[\n]+/", trim($option_values));
                $explode_array = array();
                foreach($label_value as $option_key => $option_value){
                    if(strpos($option_value,":")){
                        $tmp = explode(':',trim($option_value),2);
                        if(!empty($tmp[0] && !empty($tmp[1]))){
                            $new_option = array('label'=>trim($tmp[1]),'value'=>trim($tmp[0]));
                            array_push($explode_array, $new_option);
                        }
                    }else{
                        $tmp = explode('/n',trim($option_value));
                        if(!empty($tmp[0])){
                            $new_option = array('label'=>trim($tmp[0]),'value'=>trim($tmp[0]));
                            array_push($explode_array, $new_option);
                        }
                    }
                }
    
                $field_options = $explode_array;

                if( $cf_type == 'select' ){
                    $empty_val[] = array(
                        'label' => __('Select').' '.$cf_data[$fld_key1]['field_label'],
                        'value' => ''
                    );
                    $field_options = array_merge($empty_val,$field_options);
                }

                $cf_data[$fld_key1]['option_value'] = $field_options;


                if($cf_type == 'multicheckbox'){
                    $de_label_value = preg_split("/[\n]+/", trim($default_options));
                    $de_explode_array = array();
                    foreach($de_label_value as $de_option_key => $de_option_value){
                        if(strpos($de_option_value,":")){
                            $tmp = explode(':',trim($de_option_value),2);
                            if(!empty($tmp[0] && !empty($tmp[1]))){
                                array_push($de_explode_array, $tmp[0]);
                            }
                        }else{
                            $tmp = explode('/n',trim($de_option_value));
                            if(!empty($tmp[0])){
                                array_push($de_explode_array, $tmp[0]);
                            }
                        }
                    }
        
                    $de_field_options = $de_explode_array;
                    $cf_data[$fld_key1]['default_value'] = $de_field_options;
                }
                
            }

            $fld_key1++;
            
        }
        return $cf_data;
    }

    function mipl_wc_custom_fields(){
        $cf_data = $this->mipl_get_valid_groups();
        echo json_encode($cf_data);
        die;
    }

    function mipl_client_side_valid_groups($field_group_id){

        $cart_product_info = isset($_SESSION['mipl_wc_cf_product_data']) ? $_SESSION['mipl_wc_cf_product_data'] : '';
          
        $field_group_title = get_the_title($field_group_id);
        
        $custom_field = get_post_meta($field_group_id, '_mipl_wc_cf_custom_field', true);
        $group_setting = get_post_meta($field_group_id, '_mipl_wc_cf_group_setting', true);
        
        if( (empty($custom_field)) || (!empty($group_setting['deactive_group']))){ 
            return false;
        } 

        $custom_field = $this->get_rearranged_cf($custom_field);


        $group_specilization = get_post_meta( $field_group_id, '_mipl_wc_cf_field_group_specilization', true );
        $order_item_specilization = get_post_meta( $field_group_id, '_mipl_wc_cf_order_item_specilization', true );
        $valid_field_groups = array();
        if($group_specilization == "false"){
            $products_data[] =  array('id' => 0, 'qty' => 1, 'product_name' => '');
            $valid_field_groups[$field_group_id]['group_id'] = $field_group_id;
            $valid_field_groups[$field_group_id]['fields'] = $custom_field;
            $valid_field_groups[$field_group_id]['group_title'] = $field_group_title;
            $valid_field_groups[$field_group_id]['products'] = $products_data;
            return $valid_field_groups;
        }

        $product_setting_field_repeat = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);

        $setting_products = get_post_meta($field_group_id, '_mipl_wc_cf_setting_products', true);
        $setting_products = (!empty($setting_products))?$setting_products:array();

        $product_setting_category = get_post_meta($field_group_id, '_mipl_wc_cf_setting_product_category', true);
        $product_setting_category = (!empty($product_setting_category))?$product_setting_category:array();
        
        $product_setting_field_repeat = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);
        if(empty($product_setting_field_repeat)){
            $product_setting_field_repeat = array();
        }

        $products_data = array();
        $flag = false;
        foreach($cart_product_info as $cart_product_info_value){
            
            $product_id = $cart_product_info_value["id"];
            $product_name = html_entity_decode(get_the_title($product_id)); 
            $term_id = $cart_product_info_value["category"];                
            $product_quantity = ($product_setting_field_repeat == "repeat")?$cart_product_info_value["quantity"]:1; 
            
            if($order_item_specilization == "all_product" && $group_specilization == "true"){
                if(isset($cart_product_info_value['variation_id']) && $cart_product_info_value['variation_id'] != 0){
                    $product_id = $cart_product_info_value["variation_id"];
                    $product_name = html_entity_decode(get_the_title($product_id)); 
                }

                $products_data[] = array('id' => $product_id, 'qty' => $product_quantity, 'product_name' => $product_name);
            }

            if(($group_specilization == "true" && $order_item_specilization == "particular_product") && 
            (count(array_intersect($term_id,$product_setting_category)) <= 0) && 
            (!in_array($product_id,$setting_products))){
                continue;
            }
                            
            if(($group_specilization == "true" && $order_item_specilization == 'particular_product')){
                if(isset($cart_product_info_value['variation_id']) && $cart_product_info_value['variation_id'] != 0){
                    $product_id = $cart_product_info_value["variation_id"];
                    $product_name = html_entity_decode(get_the_title($product_id)); 
                }
                $products_data[] = array('id' => $product_id, 'qty' => $product_quantity, 'product_name' => $product_name );

            }
            $valid_field_groups[$field_group_id]['group_id'] = $field_group_id;
            $valid_field_groups[$field_group_id]['fields'] = $custom_field;
            $valid_field_groups[$field_group_id]['group_title'] = $field_group_title;
            $valid_field_groups[$field_group_id]['single_product_data'] = isset($_SESSION['mipl_single_product_post_data'][$field_group_id])?$_SESSION['mipl_single_product_post_data'][$field_group_id]:array();
            $flag = true;
        }
        if($flag){
            $valid_field_groups[$field_group_id]['products'] = $products_data;
        }
        
        return $valid_field_groups;

    }


    function mipl_wc_client_custom_fields( $data ){
        $group_id = $data->get_param('id');
        $cf_data = $this->mipl_client_side_valid_groups($group_id);
        echo json_encode($cf_data);
        die;

    }


    function update_block_order_meta_custom_fields( \WC_Order $order, \WP_REST_Request $request ) {
        $checkout_custom_fields = json_decode( $request->get_body(), true );
        
        $checkout_custom_fields = $checkout_custom_fields['extensions'];

        if ( isset($checkout_custom_fields['mipl_ck_fields_group_json']) ) {

            $mipl_checkout_custom_fields_json = $checkout_custom_fields['mipl_ck_fields_group_json'];
            
            // JSON
            $order->update_meta_data('_mipl_checkout_custom_fields_json', $mipl_checkout_custom_fields_json);
            
        }

        // Store data from prepared key-value array
        if( isset( $checkout_custom_fields['mipl_ck_fields_group'] ) ){

            $checkout_custom_field_data = $checkout_custom_fields['mipl_ck_fields_group']; 
            $custom_fields_raw_data = array();
            foreach( $checkout_custom_field_data as $group_id => $fields_data ){
                if(!empty($checkout_custom_field_data[$group_id])){
                    $custom_fields_raw_data[$group_id] = $this->mipl_get_custom_fields_raw_data($group_id);                
                    foreach( $fields_data as $fld_key => $fld_value ){
                        $order->update_meta_data($fld_key, $fld_value);
                    
                    }
                }
                
            }
            $order->update_meta_data('_mipl_wc_cf_order_field_group_data', $custom_fields_raw_data);

        }

        $order->save();
        
    }

    function mipl_get_custom_fields_raw_data($group_id){
        $mipl_wc_valid_field_groups_data = [];
        $cf_data = $this->mipl_client_side_valid_groups($group_id);
        $custom_field = get_post_meta($group_id, '_mipl_wc_cf_custom_field', true);
        $array_keys = array();
        if(isset($custom_field['errors'])){
            foreach($custom_field['errors'] as $key => $values){
                foreach($values as $key1 => $value){
                    $array_keys[$key1] = $value;
                }
            }
        }

        foreach ($custom_field['field_label'] as $fld_key => $fld_data) {

            if(in_array($fld_key, array_keys($array_keys))){
              unset($custom_field['field_label'][$fld_key]);
              unset($custom_field['field_name'][$fld_key]);
              unset($custom_field['field_type'][$fld_key]);
              unset($custom_field['default_value'][$fld_key]);
              unset($custom_field['option_value'][$fld_key]);
              unset($custom_field['required_field'][$fld_key]);
              unset($custom_field['placeholder_checkbox'][$fld_key]);
              unset($custom_field['file_size'][$fld_key]);
              unset($custom_field['file_type'][$fld_key]);
            }

        }

        $mipl_wc_valid_field_groups_data['group']['name'] = $cf_data[$group_id]['group_title'];
        $mipl_wc_valid_field_groups_data['product'] = $cf_data[$group_id]['products'];
        $mipl_wc_valid_field_groups_data['fields'] = $custom_field;
        return $mipl_wc_valid_field_groups_data;
    }

    function mipl_wc_upload_file(){

        $response = array();
        
        $upload_dir = wp_upload_dir();
        if(!is_writable($upload_dir['basedir'])){
            $response = array('status'=>'error','message'=>__('File permission denied error!'));
            echo json_encode($response);
            die;
        }
       
        
        if ( !isset($_FILES['mipl_wc_file']) ){
            $response = array('status'=>'error','message'=>__("Invalid request!"));
            echo json_encode($response);
            die();
        }
        
        if ( $_FILES['mipl_wc_file']["error"] != UPLOAD_ERR_OK ) {
            $response = array('status'=>'error','message'=>$_FILES['mipl_wc_form']["error"]);
            echo json_encode($response);
            die();
        }
        
        if( !empty($response) ){
            echo json_encode($response);
            die();
        }

        $uploaded_folder = date("Y/m");   
        $random_prefix = mipl_wc_cf_rand(5); 
            
        $tmp_name = !empty($_FILES['mipl_wc_file']['tmp_name'])?sanitize_text_field($_FILES['mipl_wc_file']['tmp_name']):'';
        $uploaded_file = !empty($_FILES['mipl_wc_file']['name'])?basename($_FILES['mipl_wc_file']['name']):'';
        $file_extension = pathinfo($uploaded_file, PATHINFO_EXTENSION);
        $file_name = sanitize_title(pathinfo($uploaded_file, PATHINFO_FILENAME));
        $san_file_name = $random_prefix.'-'.$file_name.'.'.$file_extension;
        $base_dir_path = MIPL_WC_CF_UPLOAD_PATH.$uploaded_folder;
        $base_dir_url = MIPL_WC_CF_UPLOAD_URL.$uploaded_folder;
        $file_path = $base_dir_path.'/'.$san_file_name;
        $file_url = $base_dir_url.'/'.$san_file_name;

        if(!file_exists($base_dir_path)){
            mkdir($base_dir_path, 0775, true);
        }

        $upload = move_uploaded_file($tmp_name,$file_path);
        if($upload){
            $response = array('status'=>'success','message'=>'', 'file_url'=>$file_url);
        }else{
            $response = array('status'=>'error','message'=>__('File uploading error'));
        }
        
        echo json_encode($response);
   
        die();

    }

    function remove_single_product_cf_data_according_qty($cart_item_key, $quantity, $this_obj){

        if( !session_id() ){ session_start(); }
        
        $session_data = isset($_SESSION['mipl_single_product_post_data']) ? $_SESSION['mipl_single_product_post_data'] : '';
        
        if(!isset($session_data) || empty($session_data)){ return false; }

        $cart_items = $this_obj->get_cart();
        foreach($cart_items as $cart_item){
            $product_qty = $cart_item['quantity'];
            $product_id = isset($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']; 
            
            foreach ($session_data as $group_id => $data_with_product) {
                if(isset($data_with_product[$product_id])){
                    $session_data_count = count($data_with_product[$product_id]);
                    if($session_data_count>$product_qty){
                        for($i=$product_qty;$i<$session_data_count;$i++){
                            unset($_SESSION['mipl_single_product_post_data'][$group_id][$product_id][$i]);
                        }
                    }
                }
                
            }

        }

    }

}
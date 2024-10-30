<?php

//Generate random string
if(!function_exists('mipl_wc_cf_rand')){
    function mipl_wc_cf_rand($length = 5) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
}

// Number suffix.
if(!function_exists('mipl_wc_cf_english_ordinal_suffix')){
function mipl_wc_cf_english_ordinal_suffix($n){

    if (!in_array(($n % 100),array(11,12,13))){
        switch ($n % 10){
            
            case 1:  return $n .'st';
            case 2:  return $n .'nd';
            case 3:  return $n .'rd';
        }
    }

    return $n.'th';
}
}


// Session request checked.
if(!function_exists('mipl_wc_cf_json_request')){
function mipl_wc_cf_json_request(){
    if ( isset( $_SERVER['CONTENT_TYPE'] ) && wp_is_json_media_type( $_SERVER['CONTENT_TYPE'] ) ) {
        return true;
    }
    return false;
}
}


// Validating array of custom fields.
if(!function_exists('mipl_wc_custom_field_validate_data')){
function mipl_wc_custom_field_validate_data($custom_fields){
    
    $validation_array = array();
    $validation_data = array();
    $field_array = array();
    $duplicate_names  = array();
    
    $sub_field_keys = array('field_label', 'field_name', 'field_type', 'default_value', 'option_value', 'required_field', 'placeholder_checkbox', 'file_size', 'file_type');
    
    $required_field_values = array();
    if(isset($_POST['mipl_wc_custom_field']['field_name'])){

        $required_field_values = mipl_wc_sanitize_key_array($_POST['mipl_wc_custom_field']['field_name']);
       
    }

    $d_type = array('number'=>'numeric','color'=>'color','date'=>'date','time'=>'time','datetime-local'=>'datetime','email'=>'email','tel'=>'phone');

    //reindexing array values;
    if(isset($custom_fields['required_field'])){
        $custom_fields['required_field'] = array_values($custom_fields['required_field']);
    }

    foreach($custom_fields['field_name'] as $field_index => $field_name){
        $type = $custom_fields['field_type'][$field_index];
        $regex = "";
        if($custom_fields['placeholder_checkbox'][$field_index] == 'no'){
            $regex = isset($d_type[$type])?$d_type[$type]:'';
        }

        $temp_field_label = $field_index.'_field_label';
        $validation_array[$temp_field_label] = array(
            'label'      => 'field_label',
            'type'       => 'text',
            'validation' => array(
                'required' => __("Label should not blank!"),
                'limit' => '500',
                'limit_msg' => __('Label should be maximum 500 characters!')
            ),
            'sanitize'   => array('sanitize_text_field')
        );

        $temp_field_name = $field_index.'_field_name';
        $validation_array[$temp_field_name] = array(
            'label'      => 'field_name',
            'type'       => 'text',
            'validation' => array(
                'required' => __("Name should not blank!"),
                'regex'    => "/^[a-zA-Z0-9_-]*[a-zA-Z0-9-]$/",
                'regex_msg'=> __("Name is not valid"),
                'limit'    => '500',
                'limit_msg'=> __("Name should be maximum 500 characters!")
            ),
            'sanitize'   => array('sanitize_text_field')
        );

        $temp_field_type = $field_index.'_field_type';
        $validation_array[$temp_field_type] = array(
            'label'      =>'field_type',
            'type'       => 'select',
            'values'     => array('text', 'number', 'select', 'textarea', 'color', 'date', 'time', 'datetime-local', 'email', 'tel', 'checkbox', 'multicheckbox', 'radio','file', 'recaptcha'),
            'validation' => array(
                'in_values' => __("Type should be valid!"),
                'required'  => __("Type should not blank!")
            ),
            'sanitize'   => array('sanitize_text_field')
        );

        $temp_field_default_value = $field_index.'_default_value';
        $validation_array[$temp_field_default_value] = array(
            'label'      => 'default_value',
            'type'       => 'text',
            'validation' => array(
                'limit' => '500',
                'limit_msg' => __('Default value should be maximum 500 characters!'),
                $regex => __('Default value is not valid')
            ),
            'sanitize'   => array('sanitize_textarea_field')
        );

        $temp_field_options = $field_index.'_option_value';
        $validation_array[$temp_field_options] = array(
            'label'      => 'option_value',
            'type'       => 'textarea',
            'depend'     => array('field' => $temp_field_type,'value' => array('select','radio','multicheckbox')),
            'validation' => array(
                'required'  => __("Option value should not blank!")
            ),
            'sanitize'   => array('sanitize_textarea_field')
        );

        $temp_field_required = $field_index.'_required_field';
        $validation_array[$temp_field_required] = array(
            'label'      => 'required_field',
            'type'       => 'checkbox',
            'values'     => $required_field_values,
            'validation' => array('in_values'=>__('Required field should be valid!')),
            'sanitize'   => array('sanitize_text_field')
        );

        $temp_field_placeholder = $field_index.'_placeholder_checkbox';
        $validation_array[$temp_field_placeholder] = array(
            'label'      => 'placeholder_checkbox',
            'type'       => 'checkbox',
            'values'     => array('','no','yes'),
            'validation' => array('in_values'=>__('Placeholder field should be valid!')),
            'sanitize'   => array('sanitize_text_field')
        );

        $temp_field_file_size = $field_index.'_file_size';
        $validation_array[$temp_field_file_size] = array(
            'label'      => 'file_size',
            'type'       => 'number',
            'depend'     => array('field' => $temp_field_type,'value' => 'file'),
            'validation' => array(
                'required'  => __("File size should not blank!"),
                'numeric'=>__('File size should be valid!')
            ),
            'sanitize'   => array('sanitize_text_field')
        );

        $temp_field_file_type = $field_index.'_file_type';
        $validation_array[$temp_field_file_type] = array(
            'label'      => 'file_type',
            'type'       => 'select',
            'depend'     => array('field' => $temp_field_type,'value' => 'file'),
            'validation' => array(
                'required'  => __("File type should not blank!"),
            ),
            'sanitize'   => array('sanitize_text_field')
        );

        $validation_data[$temp_field_label] = isset($custom_fields['field_label'][$field_index])?$custom_fields['field_label'][$field_index]:'';
        $validation_data[$temp_field_name] = isset($custom_fields['field_name'][$field_index])?$custom_fields['field_name'][$field_index]:'';
        $validation_data[$temp_field_type] = isset($custom_fields['field_type'][$field_index])?$custom_fields['field_type'][$field_index]:'';
        $validation_data[$temp_field_default_value] = isset($custom_fields['default_value'][$field_index])?$custom_fields['default_value'][$field_index]:'';
        $validation_data[$temp_field_options] = isset($custom_fields['option_value'][$field_index])?$custom_fields['option_value'][$field_index]:'';

        
        $validation_data[$temp_field_required] = (isset($custom_fields['required_field']) && in_array($field_name,$custom_fields['required_field']))?$field_name:'';
        
        $validation_data[$temp_field_placeholder] = isset($custom_fields['placeholder_checkbox'][$field_index])?$custom_fields['placeholder_checkbox'][$field_index]:"";

        $validation_data[$temp_field_file_size] = isset($custom_fields['file_size'][$field_index])?$custom_fields['file_size'][$field_index]:"";

        $validation_data[$temp_field_file_type] = isset($custom_fields['file_type'][$field_index])?$custom_fields['file_type'][$field_index]:"";


        foreach($sub_field_keys as $sub_field_key){
            if( $sub_field_key == 'option_value' || $sub_field_key == 'default_value' ){
                $field_array[$sub_field_key][$field_index] =  isset($custom_fields[$sub_field_key][$field_index])?sanitize_textarea_field($custom_fields[$sub_field_key][$field_index]):'';
            }else if($sub_field_key == 'required_field'){
                $field_array[$sub_field_key][$field_index] = (isset($custom_fields['required_field']) && in_array($field_name,$custom_fields['required_field']))?sanitize_text_field($field_name):'';
            }else if($sub_field_key == 'file_type'){
                $field_array[$sub_field_key][$field_index] = (isset($custom_fields['file_type'][$field_index])) ? mipl_wc_sanitize_key_array($custom_fields['file_type'][$field_index]) : '';
            }else{
                if(isset($custom_fields[$sub_field_key][$field_index])){
                    $field_array[$sub_field_key][$field_index] =  sanitize_text_field($custom_fields[$sub_field_key][$field_index]);
                }
            }
        }

    }

    $counts = array_count_values(array_map('strtolower', $field_array['field_name']));
    
    $filtered = array_filter($field_array['field_name'], function ($value) use ($counts) {
        return $counts[strtolower($value)] > 1;
    });
    
    $array_first_key = array_key_first($filtered);

    if($array_first_key !== null){
        unset($filtered[$array_first_key]);
        foreach($filtered as  $filter_key => $filter_val){
            $duplicate_names[$filter_key.'_field_name'] = "Field name was duplicate";
        }
    }
  
    $val_obj = new MIPL_WC_CF_Input_Validation($validation_array, $validation_data);
    
    $rs = $val_obj->validate();
    $errors = $val_obj->get_errors();
    $post_data = $val_obj->get_valid_data();
    foreach($field_array as $store_field => $value_arr){
        foreach($value_arr as $v_index => $value){
            if( isset($errors[$v_index.'_field_label']) ){
                $field_array['errors']['field_label'][$v_index] = $errors[$v_index.'_field_label'];
            }
            if( isset($errors[$v_index.'_field_name']) ){
                $field_array['errors']['field_name'][$v_index] = $errors[$v_index.'_field_name'];
            }elseif( !isset($errors[$v_index.'_field_name'])  && isset($duplicate_names[$v_index.'_field_name'])){
                $field_array['errors']['field_name'][$v_index] = $duplicate_names[$v_index.'_field_name'];
            }
            if( isset($errors[$v_index.'_field_type']) ){
                $field_array['errors']['field_type'][$v_index] = $errors[$v_index.'_field_type'];
            }
            if( isset($errors[$v_index.'_default_value']) ){
                $field_array['errors']['default_value'][$v_index] = $errors[$v_index.'_default_value'];
            }
            if( isset($errors[$v_index.'_option_value']) ){
                $field_array['errors']['option_value'][$v_index] = $errors[$v_index.'_option_value'];
            }
            if( isset($errors[$v_index.'_required_field']) ){
                $field_array['errors']['required_field'][$v_index] = $errors[$v_index.'_required_field'];
            }
            if( isset($errors[$v_index.'_placeholder_checkbox']) ){
                $field_array['errors']['placeholder_checkbox'][$v_index] = $errors[$v_index.'_placeholder_checkbox'];
            }
            if( isset($errors[$v_index.'_file_size']) ){
                $field_array['errors']['file_size'][$v_index] = $errors[$v_index.'_file_size'];
            }
            if( isset($errors[$v_index.'_file_type']) ){
                $field_array['errors']['file_type'][$v_index] = $errors[$v_index.'_file_type'];
            }
        }
    }
    return $field_array;
    
}
}


//validation array of fields group setting
if(!function_exists('mipl_wc_cf_get_group_setting')){
function mipl_wc_cf_get_group_setting(){

    $group_setting = array(
        'deactive_group' => array(
            'label'      => 'active',
            'type'       => 'checkbox',
            'values'     => array('','1'),
            'validation' => array(
                'in_values' => __('Field Group setting should be valid!')
            ),
            'sanitize'   => array('sanitize_key')
        ),
        'hide_title' => array(
            'label'      => 'hide_title',
            'type'       => 'checkbox',
            'values'     => array('','1'),
            'validation' => array(
                'in_values' => __('Field Group setting should be valid!')
            ),
            'sanitize'   => array('sanitize_key')
        )
    );

    return $group_setting;
}
    
}

// Validation array for custom fields setting
if(!function_exists('mipl_wc_cf_get_custom_fields_setting')){
function mipl_wc_cf_get_custom_fields_setting(){
    
    $products = wc_get_products(array( 'status' => 'publish', 'limit' => -1 ));
    
    $mi_products = array();
    foreach($products as $product){
        $mi_products[] = $product->get_id();
    }

    $args = array(
        'taxonomy'     => 'product_cat',
        'hierarchical' => true,
        'hide_empty'   => false
    );
    $all_product_categories = get_categories($args);
    $all_categories = array();
    foreach($all_product_categories as $parent){
        if ($parent->category_parent == 0) {
            $all_categories[] = $parent->term_id;
        }
    }
   
    $custom_fields_setting = array(
        '_mipl_wc_cf_setting_position' => array(
            'label'      => 'field position',
            'type'       => 'select',
            'values'     => array(
                'woocommerce_after_checkout_billing_form',
                'woocommerce_before_checkout_billing_form',
                'woocommerce_before_checkout_shipping_form',
                'woocommerce_after_checkout_shipping_form',
                'woocommerce_before_order_notes',
                'woocommerce_after_order_notes',
                'woocommerce_checkout_before_order_review_heading',
                'woocommerce_review_order_before_payment',
                'woocommerce_review_order_after_payment',
                'woocommerce_review_order_before_submit',
                'woocommerce_review_order_after_submit'
            ),
            'validation' => array(
                'in_values'=>__('Field position should be valid!')
            ),
            'sanitize'   => array('sanitize_text_field')
        ),
        '_mipl_wc_cf_cart_field_position' => array(
            'label'      =>'field position',
            'type'       => 'select',
            'values'     => array(
                '',
                'woocommerce_before_add_to_cart_quantity',
            ),
            'validation' => array('in_values'=>__('Field position for product single view should be valid!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        '_mipl_wc_cf_field_group_specilization' => array(
            'label'      =>'choose field group',
            'type'       => 'checkbox',
            'values'     => array('true','false'),
            'validation' => array('in_values'=>__('Field group for particular product should be valid!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        '_mipl_wc_cf_order_item_specilization' => array(
            'label'      =>'Order item specilization',
            'type'       => 'radio',
            'values'     => array('all_product','particular_product'),
            'validation' => array('in_values'=>__('Field group for particular product should be valid!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        '_mipl_wc_cf_setting_field_repeat' => array(
            'label'      => 'field can repeat',
            'type'       => 'checkbox',
            'values'     => array('', 'repeat','no_repeat'),
            'validation' => array('in_values'=>__('Field repetition should be valid!')),
            'sanitize'   => array('sanitize_text_field')
        ),

        '_mipl_wc_cf_setting_product_category' => array(
            'label'      => 'product category',
            'type'       => 'select',
            'values'     => $all_categories,
            'validation' => array('in_values'=>__('Products category should be valid!')),
            'sanitize'   => array('sanitize_textarea_field')
        ),
        '_mipl_wc_cf_setting_products' => array(
            'label'      => 'products',
            'type'       => 'select',
            'values'     => $mi_products,
            'validation' => array('in_values'=>__('Products should be valid!')),
            'sanitize'   => array('sanitize_textarea_field')
        ),
        '_mipl_wc_cf_email_to_list' => array(
            'label'      => 'email',
            'type'       => 'checkbox',
            'values'     => array('','new_order','customer_note','customer_invoice','customer_completed_order','customer_processing_order','cancelled_order','failed_order','customer_refunded_order'),
            'validation' => array('in_values'=>__('Order type for email should be valid!')),
            'sanitize'   => array('sanitize_textarea_field')
        )
    );
    return $custom_fields_setting;  
}
}

//validation array for client side checkout custom fields
if(!function_exists('mipl_wc_cf_get_order_fields')){
function mipl_wc_cf_get_order_fields($meta_fields, $field_group_id='', $product_id='', $product_qty=''){
    
    $mipl_cf_form_fields = array();
    
    if(!empty($meta_fields)){
        if($product_qty >= 1){
            for($i=1; $i<=$product_qty; $i++){
                $mipl_cf_form_field_val[] = mipl_wc_get_client_side_validation_array($meta_fields, $field_group_id, $product_id,$i);
            }
        }else{
            $mipl_cf_form_field_val[] = mipl_wc_get_client_side_validation_array($meta_fields, $field_group_id);
        }
    }
    
    return $mipl_cf_form_field_val;
}
}

if(!function_exists('mipl_wc_get_client_side_validation_array')){
function mipl_wc_get_client_side_validation_array($meta_fields, $field_group_id='', $product_id='', $product_qty=''){
    
    if(empty($product_id) && empty($product_qty)){
       $new_field_name = "_mipl_wc_cf_{$field_group_id}";
    }else{
        $new_field_name = "_mipl_wc_cf_{$field_group_id}_{$product_id}_{$product_qty}";
    }

    $array_keys = array();
    if(isset($meta_fields['errors'])){
        foreach($meta_fields['errors'] as $key => $values){
            foreach($values as $key1 => $value){
                $array_keys[$key1] = $value;
            }
        }
    }
    // $acceptace_position = array('woocommerce_review_order_before_payment', 'woocommerce_review_order_after_payment', 'woocommerce_review_order_before_submit', 'woocommerce_review_order_after_submit');
    $fields_position = get_post_meta($field_group_id, '_mipl_wc_cf_setting_position', true);
    foreach($meta_fields as $field_key => $meta_field){
        foreach($meta_field as $key => $meta_val){

            if(in_array($key, array_keys($array_keys))){
                continue;
            }

            $file_size = isset($meta_fields['file_size'][$key])?$meta_fields['file_size'][$key]:'';

            $required_field = "";
            if(isset($meta_fields['required_field'])){
                if(isset($meta_fields['field_name'][$key])){
                    $field_required = $meta_fields['field_name'][$key];
                }

                if(isset($meta_fields['required_field'][$key]) && in_array($field_required, $meta_fields['required_field'])){
                    $required_field = 'required';
                }
                // if($meta_fields['field_type'][$key] == 'checkbox' && !in_array($fields_position,$acceptace_position)){
                //     $required_field = '';
                // }

            }
            if(isset($meta_fields['field_name'][$key])){

                if($meta_fields['field_type'][$key] == 'text'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field =>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'))
                    );
                }
                if($meta_fields['field_type'][$key] == 'email'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),'email'=>__('Please enter valid email!')
                        )
                    ); 
                }
                if($meta_fields['field_type'][$key] == 'textarea'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!')
                        ),
                    ); 
                }
                if($meta_fields['field_type'][$key] == 'tel'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),
                            'phone'=>ucfirst($meta_fields['field_label'][$key]).' '.__('should be valid!!')
                        ),
                    ); 
                }
                if($meta_fields['field_type'][$key] == 'number'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),
                            'natural'=>ucfirst($meta_fields['field_label'][$key]).' '.__('should be valid!')
                        ),
                    ); 
                }
                if($meta_fields['field_type'][$key] == 'radio'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>__('Error at').' '.strtolower($meta_fields['field_label'][$key]).'!'
                        ),
                    ); 
                }
                if($meta_fields['field_type'][$key] == 'checkbox'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('is required!')
                        ),
                    ); 
                }

                if($meta_fields['field_type'][$key] == 'multicheckbox'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>__('Please check at least one').' '.strtolower($meta_fields['field_label'][$key].'!')
                        ),
                    ); 
                }

                if($meta_fields['field_type'][$key] == 'select'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>__('Please select at least one').' '.strtolower($meta_fields['field_label'][$key].'!')
                        ),
                    ); 
                }

                if($meta_fields['field_type'][$key] == 'color'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),
                            'color'=>ucfirst($meta_fields['field_label'][$key]).' '.__('field should be valid!')
                        ),
                    ); 
                }

                if($meta_fields['field_type'][$key] == 'date'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),
                            'date'=>ucfirst($meta_fields['field_label'][$key]).' '.__('field should be valid!')
                        ),
                    ); 
                }

                if($meta_fields['field_type'][$key] == 'time'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),
                            'time'=>ucfirst($meta_fields['field_label'][$key]).' '.__('field should be valid!')
                        ),
                    ); 
                }

                if($meta_fields['field_type'][$key] == 'datetime-local'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),
                            'datetime'=>ucfirst($meta_fields['field_label'][$key]).' '.__('field should be valid!')
                        ),
                    ); 
                }

                if($meta_fields['field_type'][$key] == 'file'){
                    $mipl_cf_form_field_val[$new_field_name."_{$meta_fields['field_name'][$key]}"] = array(
                        'label'      => $meta_fields['field_name'][$key],
                        'type'       => $meta_fields['field_type'][$key], 
                        'sanitize'   => array('sanitize_text_field'),
                        'validation' => array(
                            $required_field=>ucfirst($meta_fields['field_label'][$key]).' '.__('field is required!'),
                            'url'=>ucfirst($meta_fields['field_label'][$key]).' '.__('field should be valid!')
                        ),
                    ); 
                }
            }
        }
    }
    return $mipl_cf_form_field_val;
}
}


//validation array for default fields
if(!function_exists('mipl_wc_checkout_default_field_valiadtion_array')){
function mipl_wc_checkout_default_field_valiadtion_array(){
    
    $default_fields = array(
        'priority' => array(
            'label'      => 'priority',
            'type'       => 'hidden',
            'validation' => array('limit'=>'50','limit_msg'=>__('Priority field should 50 Character only!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        'name' => array(
            'label'      => 'name',
            'type'       => 'hidden',
            'validation' => array('limit'=>'100','limit_msg'=>__('Name field should 100 Character only!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        'label' => array(
            'label'      => 'label',
            'type'       => 'text',
            'validation' => array('required'=>__("Label field should not blank!"),'limit'=>'100','limit_msg'=>__('Label field should 100 Character only!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        'placeholder' => array(
            'label'      => 'placeholder',
            'type'       => 'text',
            'validation' => array('limit'=>'500','limit_msg'=>__('Placeholder field should 500 Character only!')),
            'sanitize'   => array('sanitize_textarea_field')
        ),
        'class' => array(
            'label'      =>'class',
            'type'       => 'text',
            'validation' => array('limit'=>'100','limit_msg'=>__('Class field should 100 Character only!'),'regex'=>"/^[a-zA-Z-_ ]*$/",'regex_msg'=>__('Class field is not valid')),
            'sanitize'   => array('sanitize_text_field')
        ),
        'default' => array(
            'label'      => 'default',
            'type'       => 'text',
            'validation' => array('limit'=>'500','limit_msg'=>__('Default field should 500 Character only!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        'required' => array(
            'label'      => 'required',
            'type'       => 'checkbox',
            'validation' => array('limit'=>'100','limit_msg'=>__('Required field should 100 Character only!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        'hidden' => array(
            'label'      => 'hidden',
            'type'       => 'checkbox',
            'validation' => array('limit'=>'100','limit_msg'=>__('Hidden field should 100 Character only!')),
            'sanitize'   => array('sanitize_text_field')
        ),
        'changed_field' => array(
            'label'      => 'changed_field',
            'type'       => 'hidden',
            'validation' => array('limit'=>'50','limit_msg'=>__('Changed field should 50 Character only!')),
            'sanitize'   => array('sanitize_text_field')
        )
    );
    
    return $default_fields;
    
}
}


if(!function_exists('mipl_wc_cf_client_side_fields')){
function mipl_wc_cf_client_side_fields($custom_field, $field_group_id, $product_id='', $qty=0){

    global $MIPL_WC_RECAPTCHA_FLAG;

    if(!isset($custom_field['field_label'])){
        return false;
    }
    
    $error_position = isset($custom_field['errors'])?$custom_field['errors']:array();
    
    $array_keys = array();
    if(isset($custom_field['errors'])){
        foreach($custom_field['errors'] as $key => $values){
            foreach($values as $key1 => $value){
                $array_keys[$key1] = $value;
            }
        }
    }
    $custom_attributes = array();
    foreach($custom_field['field_label'] as $cf_key=>$cf_value){

        if(in_array($cf_key, array_keys($array_keys))){
            continue;
        }

        $type  = isset($custom_field["field_type"][$cf_key])?$custom_field["field_type"][$cf_key]:"";
        $label = isset($custom_field["field_label"][$cf_key])?$custom_field["field_label"][$cf_key]:"";
        $default_value  = isset($custom_field["default_value"][$cf_key])?$custom_field["default_value"][$cf_key]:"";
        $option_values = isset($custom_field["option_value"][$cf_key])?$custom_field["option_value"][$cf_key]:"";
        $name = isset($custom_field["field_name"][$cf_key])?$custom_field["field_name"][$cf_key]:"";
        $check_placeholder = isset($custom_field['placeholder_checkbox'][$cf_key])?$custom_field['placeholder_checkbox'][$cf_key]:"";
        $file_size = isset($custom_field['file_size'][$cf_key])?$custom_field['file_size'][$cf_key]:"";
        $file_type = isset($custom_field['file_type'][$cf_key])?$custom_field['file_type'][$cf_key]:"";
        
        $current_position = current_filter();
        $recaptcha_field_position = array("woocommerce_review_order_before_submit", "woocommerce_review_order_after_submit");
        // $recaptcha_data = get_option('_mipl_wc_cf_recaptcha_data');
        $recaptcha_enable_checkbox = get_option('_mipl_wc_recaptcha_enable_checkbox');
        $recaptcha_type = get_option('_mipl_wc_recaptcha_recaptcha_type');
        if($type == 'recaptcha' && !isset($recaptcha_enable_checkbox)){
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
        $custom_attributes['cf_name'] = $name;
        if(!empty($file_type)){
            $custom_attributes['file_type'] = implode(',', $file_type);
            $custom_attributes['file_size'] = $file_size;
        }
        $placeholder = "";
        if($check_placeholder==="yes"){
            $placeholder = $default_value;
            $default_value = "";
        }elseif($type == "select") {
            $placeholder = "Select $label";
        }
        $field_class = 'form-row-wide';
        $class = 'mipl_wc_custom_fields';
        $span_class = 'mipl_wc_custom_fields_span';

        $field_options = array();
        $required = false;

        if(!empty($custom_field["required_field"][$cf_key])){
            $required = true;
        }
        $class = ($type=='radio')?"mipl_radio_input":"";

        if($type=="checkbox"){
            if($default_value == 'yes'){
                $default_value = 1;
            }
        }

        if( $type == 'select' || $type == 'radio' || $type == 'multicheckbox' ){

            $label_value = preg_split("/[\n]+/", trim($option_values));
            $explode_array=array();
            foreach($label_value as $option_key => $option_value){
                if(strpos($option_value,":")){
                    $tmp = explode(':',trim($option_value),2);
                    if(!empty($tmp[0] && !empty($tmp[1]))){
                        $explode_array[trim($tmp[0])] = trim($tmp[1]);
                    }
                }else{
                    $tmp = explode('/n',trim($option_value));
                    if(!empty($tmp[0])){
                        $explode_array[trim($tmp[0])] = trim($tmp[0]);
                    }
                }
            }

            $field_options = $explode_array;
           
            if($type == 'select'){
                $field_class .= ' mipl-cf-select';
                foreach($field_options as $opt_key => $opt_value){
                    $new_option[$opt_key] = $opt_value;
                }
                $field_options = array_merge(array(''=>'Select'),$new_option);
            }
            
        }
        $default_options = array();
        if($type == "multicheckbox"){
            $default_values = preg_split("/[\n]+/", trim($default_value));
            $checkbox_array = array();
            foreach($default_values as $option_key => $option_value){
                if(strpos($option_value,":")){
                    $tmp = explode(':',trim($option_value));
                    if(!empty($tmp[0] && !empty($tmp[1]))){
                        $checkbox_array[trim($tmp[0])] = trim($tmp[1]);
                    }
                }else{
                    $tmp = explode('/n',trim($option_value));
                    if(!empty($tmp[0])){
                        $checkbox_array[trim($tmp[0])] = trim($tmp[0]);
                    }
                }
            }

            $default_options = $checkbox_array;
        }
        if($type == 'color'){
            $field_class .= ' mipl_colorpicker';
            $color_picker = "mipl_color_picker";
            $type         = 'text';
            $value        = "#f82525";
        }

        if($type == 'date'){
            $field_class .= ' mipl_date_picker';
            $type = 'text';
        }else if($type == 'time'){
            $field_class .= ' mipl_time_picker';
            $type = 'text';
        }else if($type == 'datetime-local'){
            $field_class .= ' mipl_datetime_picker';
            $type = 'text';
        }

        $setting_quantity_repeat = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);
        $wc_field_name = "_mipl_wc_cf_{$field_group_id}_$name";
        if( $product_id != ''){
            $wc_field_name = "_mipl_wc_cf_{$field_group_id}_{$product_id}_{$qty}_{$name}";
        }

        $cart_post_data = isset($_SESSION['mipl_single_product_post_data'][$field_group_id][$product_id][$qty-1][$wc_field_name]) ? $_SESSION['mipl_single_product_post_data'][$field_group_id][$product_id][$qty-1][$wc_field_name] : '';


        if( !empty($cart_post_data) ){
            $default_value = $cart_post_data;
        }

        if($type=='multicheckbox'){
            ?>
            <p class='mipl_custom_field'>
            <?php
            
            ?>
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
                $default_field_keys = array_keys($default_options);
                foreach($field_options as $checkbox_key=>$checkbox_value){
                    $check = "";
                    
                    $default_value = is_array($default_value) ? $default_value : array();
                    if(in_array($checkbox_value,$default_options) || in_array($checkbox_key,$default_field_keys) || in_array($checkbox_key, $default_value)){
                        $check = "checked";
                    }
                    ?>
                    
                    <label for="<?php echo esc_attr($name.$checkbox_key.$qty) ?>"> 
                    <input type="checkbox" id="<?php echo esc_attr($name.$checkbox_key.$qty) ?>"
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
}

//for admin notices
if(!function_exists('mipl_wc_cf_admin_notices')){
function mipl_wc_cf_admin_notices(){
    
    if((!empty($_SESSION['mipl_cf_ck_block_info']) && isset($_GET['post_type'])) && $_GET['post_type'] == 'mipl_wc_ck_fields'){
        ?>
        <div class="notice notice-info">
            <p><?php echo $_SESSION['mipl_cf_ck_block_info']['info'] ?>
            <a href="#TB_inline?&width=1024&height=530&padding=10px&inlineId=mipl_wc_cf_block_modal" class="thickbox mipl_wc_cf_block_modal" title="Adding Block In Checkout Page"><?php echo esc_html('Click Here') ?> <a>
            <p>
        </div>
        <?php
    }


    $message_type = array( 'error', 'success', 'warning', 'info' );
    foreach( $message_type as $type ){
        
        $class = 'notice is-dismissible ';

        if(!empty($_SESSION['field_position'])){
            printf( '<div class="notice notice-warning"><p>Warning: %s</p></div>', esc_html("Error fields are not shown in client side") );
            unset($_SESSION['field_position'] );

        }

        if( isset($_SESSION['mipl_cf_admin_notices'][ $type ]) && trim( $_SESSION['mipl_cf_admin_notices'][ $type ]) !='' ){
            $class = $class.' notice-'.$type;
            $message = wp_kses_post($_SESSION['mipl_cf_admin_notices'][ $type ]);
            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
            unset($_SESSION['mipl_cf_admin_notices'][$type]);
        }

    }
    
}   
}


// Sanitize fields name
if(!function_exists('mipl_wc_sanitize_key_array')){
function mipl_wc_sanitize_key_array( $data ){

    if( !is_array($data) ){ return false; }
    $new_data = array();
    foreach($data as $key=>$value){
        $new_data[$key] = !empty($value) ? sanitize_text_field($value) : $value;
    }
    return $new_data;
    
}
}


// Order and mail data
if(!function_exists('mipl_wc_get_order_custom_fields_data')){
function mipl_wc_get_order_custom_fields_data($order_id, $email_temp = ""){
    $custom_orders_enabled = get_option('woocommerce_custom_orders_table_enabled');
    
    $order_fields_data = get_post_meta($order_id, '_mipl_wc_cf_order_field_group_data', true);
    if($custom_orders_enabled == 'yes'){
        $order = wc_get_order($order_id);
        $order_fields_data = $order->get_meta('_mipl_wc_cf_order_field_group_data', true);
    }
    if( empty($order_fields_data) ){
        return false;
    }

    ob_start();
    $put_array_details = array();
    $custom_order_data = get_post_meta($order_id);
    foreach($order_fields_data as $group_id=>$field_data){
        $group_setting = get_post_meta($group_id, '_mipl_wc_cf_group_setting', true);
        if(!empty($group_setting['deactive_group'])){
            continue;
        }

        // email temp
        if(!empty($email_temp)){
            $setting_order_status = get_post_meta($group_id, '_mipl_wc_cf_email_to_list', true);
            if(!empty($setting_order_status) && !in_array($email_temp, $setting_order_status)){
              continue;
            }
        }
        
        if(!isset($field_data) || empty($field_data)){
            continue;
        }

        $array_keys = array();
        if(isset($field_data['fields']['errors'])){
            foreach($field_data['fields']['errors'] as $key => $values){
                foreach($values as $key1 => $value){
                    $array_keys[$key1] = $value;
                }
            }
        }
        $group_name = $field_data['group']['name'];
        $put_array_details['group_id'] = $group_id;
        $put_array_details['error_fields'] = $array_keys;
        if( isset($field_data['product']) ){
            foreach ($field_data['product'] as $key => $fld_data) {
                $product_name = isset($fld_data['product_name']) ? $fld_data['product_name'] : '';
                $qty = $fld_data['qty'];
                $prod_id = $fld_data['id'];
                $put_array_details['pro_id'] = $prod_id;
                
                for( $i=1; $i<=$qty; $i++ ){
                    $put_array_details['qty'] = $i;
                    $suffix = mipl_wc_cf_english_ordinal_suffix($i);

                    if($prod_id == 0){
                        ?>
                            <h3 style="font-size: 15px;margin: 0px;"><?php esc_html_e("$group_name") ?></h3>
                        <?php
                    }else{
                        ?>
                        <h3 style="font-size: 15px;margin: 0px;"><?php esc_html_e("$suffix $group_name ($product_name)") ?></h3>
                        <?php
                    }

                    ?>
                    <table style="margin-bottom:10px;" class="mipl_wc_order_table">
                    <?php mipl_wc_order_custom_fields($field_data, $put_array_details, $custom_order_data,$order_id);?>
                </table>
                <?php
                }
            }

        }else{
            
            $put_array_details['pro_id'] = '';
            $put_array_details['qty'] = '';
            if(!empty($field_data['group']['name']) && $field_data['group']['name'] != 'reCAPTCHA'){
                ?>
                <h3 style="font-size: 15px;margin: 0px;"><?php esc_html_e("$group_name") ?></h3>
                <?php
            }
            ?>
            <table style="margin-bottom:10px" class="mipl_wc_order_table">
            <?php mipl_wc_order_custom_fields($field_data, $put_array_details, $custom_order_data,$order_id);?>
            </table>
            <?php
            
        }
        
    }
    
    $order_custom_fields_data = ob_get_contents();
    ob_get_clean();
    return $order_custom_fields_data;
    
}
}


if(!function_exists('mipl_wc_order_custom_fields')){
function mipl_wc_order_custom_fields($field_data,$put_array_details,$custom_order_data,$order_id){
    $group_id = $put_array_details['group_id'];
    $prod_id = $put_array_details['pro_id'];
    $error_position = $put_array_details['error_fields'];
    $qty = $put_array_details['qty'];
    foreach($field_data['fields']['field_label'] as $field_index=>$field_label){
        if( in_array($field_index, array_keys($error_position))){ continue; }
        $field_name = isset($field_data['fields']['field_name'][$field_index])?$field_data['fields']['field_name'][$field_index]:'';
        $field_type = isset($field_data['fields']['field_type'][$field_index]) ? $field_data['fields']['field_type'][$field_index] : '';
        $file_type = isset($field_data['fields']['file_type'][$field_index]) ? $field_data['fields']['file_type'][$field_index] : '';
        $option_value = isset($field_data['fields']['option_value'][$field_index])?$field_data['fields']['option_value'][$field_index]:'';
        
        if($field_type == 'recaptcha'){
            continue;
        }

        $option_value_arr = array();
        if(!empty($option_value)){
            $options = explode("\n",$option_value);
            foreach($options as $option){
                $option_arr = explode(':',$option);
                if(count($option_arr)>=2){
                    $option_value_arr[trim($option_arr[0])] = trim($option_arr[1]);
                }else{
                    $option_value_arr[trim($option_arr[0])] = trim($option_arr[0]);
                }
            }
        }

        $post_fld_name = "_mipl_wc_cf_{$group_id}_{$prod_id}_{$qty}_{$field_name}"; 
        if(empty($prod_id) && empty($qty)){
            $post_fld_name = "_mipl_wc_cf_{$group_id}_{$field_name}"; 
        }
      
        $field_value = isset($custom_order_data[$post_fld_name])?$custom_order_data[$post_fld_name]:'';
        
        $custom_orders_enabled = get_option('woocommerce_custom_orders_table_enabled');
        if($custom_orders_enabled == 'yes'){
            $order = wc_get_order($order_id);
            $field_value = $order->get_meta($post_fld_name, true);
        }
        if(isset($field_value[0]) && is_serialized($field_value[0])){
            $field_value = unserialize($field_value[0]);
        }
        
        if(in_array($field_type, array('select','multicheckbox','radio'))){
            if(is_array($field_value)){
                $field_value_temp = array();
                foreach($field_value as $field_val_item){
                    $field_value_temp[] = isset($option_value_arr[$field_val_item])?$option_value_arr[$field_val_item]:'';
                }
                $field_value = implode(', ',$field_value_temp);
            }else{
                $field_value = isset($option_value_arr[$field_value])?$option_value_arr[$field_value]:'';
            }
        }

        if(is_array($field_value)){
            $field_value = implode(', ', $field_value);
        }

        if($field_type == 'checkbox'){
            if($field_value == 1){
                $field_value = 'yes';
            }else{
                $field_value = 'no';
            }
        }

        ?>
            <tr><th style='text-align:left;width:300px'><?php esc_html_e("$field_label") ?>:</th>
        <?php
        if($field_type == "file"){
            $path = parse_url($field_value, PHP_URL_PATH);
            $file_path = explode('mipl_checkout_files', $path);
            $file = isset($file_path[1]) ? base64_encode($file_path[1]) : '';
            $file_name = basename($path);
            $home_url = home_url().'/?mipl_action=file_preview&file='.$file;
            ?>
                <td><a href="<?php echo $home_url ?>" target="_blank"><?php esc_html_e("$file_name") ?></a></td></tr>
            <?php

        }else{
            ?>
            <td><?php esc_html_e("$field_value") ?><br></td></tr>
            <?php
        }
    }
    
}
}


//validation array of recaptcha fields
if(!function_exists('mipl_wc_recaptcha_validation')){
    function mipl_wc_recaptcha_validation($recaptcha_fields){
        
        $recaptcha_fields = array(
            '_mipl_wc_recaptcha_enable_checkbox' => array(
                'label'      =>'enable_checkbox',
                'type'       => 'checkbox',
                'values'     => array('','true','false'),
                'validation' => array(
                    'in_values'=>__('reCAPTCHA type should be valid!'),
                ),
                'sanitize'   => array('sanitize_text_field')
                ),
            '_mipl_wc_recaptcha_recaptcha_type' => array(
                'label'      =>'recaptcha_type',
                'type'       => 'select',
                'values'     => array('reCAPTCHA_v2','reCAPTCHA_v3'),
                'validation' => array(
                    'in_values'=>__('reCAPTCHA type should be valid!'),
                    'required'=>__('reCAPTCHA type should not blank!')
                ),
                'sanitize'   => array('sanitize_text_field')
                ),
            '_mipl_wc_recaptcha_v2_site_key' => array(
                'label'      =>'v2_site_key',
                'type'       => 'input',
                'depend'     => array('field' => '_mipl_wc_recaptcha_recaptcha_type','value' => 'reCAPTCHA_v2'),
                'validation' => array(
                    'required'=>__('RecaptchaV2 site key should not blank!'),
                    'regex'=>'/^[A-Za-z0-9_-]{40}+$/','regex_msg'=>__('RecaptchaV2 site key should be valid!')),
                'sanitize'   => array('sanitize_text_field')
                ),
            '_mipl_wc_recaptcha_v2_secret_key' => array(
                'label'      =>'v2_secret_key',
                'type'       => 'input',
                'depend'     => array('field' => '_mipl_wc_recaptcha_recaptcha_type','value' => 'reCAPTCHA_v2'),
                'validation' => array(
                    'required'=>__('RecaptchaV2 secrete key should not blank!'),
                    'regex'=>'/^[A-Za-z0-9_-]{40}+$/','regex_msg'=>__('RecaptchaV2 secrete key should be valid!')),
                'sanitize'   => array('sanitize_text_field')
                ),
            '_mipl_wc_recaptcha_v3_site_key' => array(
                'label'      =>'v2_site_key',
                'type'       => 'input',
                'depend'     => array('field' => '_mipl_wc_recaptcha_recaptcha_type','value' => 'reCAPTCHA_v3'),
                'validation' => array(
                    'required'=>__('RecaptchaV3 site key should not blank!'),
                    'regex'=>'/^[A-Za-z0-9_-]{40}+$/','regex_msg'=>__('RecaptchaV3 secrete key should be valid!')),
                'sanitize'   => array('sanitize_text_field')
                ),
            '_mipl_wc_recaptcha_v3_secret_key' => array(
                'label'      =>'v2_secret_key',
                'type'       => 'input',
                'depend'     => array('field' => '_mipl_wc_recaptcha_recaptcha_type','value' => 'reCAPTCHA_v3'),
                'validation' => array(
                    'required'=>__('RecaptchaV3 secrete key should not blank!'),
                    'regex'=>'/^[A-Za-z0-9_-]{40}+$/','regex_msg'=>__('RecaptchaV3 secrete key should be valid!')),
                'sanitize'   => array('sanitize_text_field')
                ),
         
        );
       
        $return_data = $recaptcha_fields;

        return $return_data;
    }
}


if(!function_exists('mipl_wc_cf_verify_recaptcha')){
function mipl_wc_cf_verify_recaptcha($post_recaptcha_data){

    // $recaptcha_data = get_option('_mipl_wc_cf_recaptcha_data');
    $recaptcha_type = get_option('_mipl_wc_recaptcha_recaptcha_type');
    if($recaptcha_type== 'reCAPTCHA_v2'){
        $v2_secret_key = get_option('_mipl_wc_recaptcha_v2_secret_key');
        $secret_key = $v2_secret_key;
    }else{
        $v3_secret_key = get_option('_mipl_wc_recaptcha_v3_secret_key');
        $secret_key = $v3_secret_key;
    }
    
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $args = array(
        'body' => http_build_query(array(
            'secret'   => $secret_key,
            "response" => $post_recaptcha_data,
            "remoteip" => $_SERVER['REMOTE_ADDR']
        )),
        'method' => 'POST',
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded')
    );
    $resp = wp_remote_post($url, $args);
    
    if(!empty($resp['body'])){
        $resp = json_decode( $resp['body'] );
        return $resp->success;
    }

    return false;

}
}

if(!function_exists('mipl_wc_cf_get_file_setting_data')){
function mipl_wc_cf_get_file_setting_data($group_id,$field_name){
    $group_data = get_post_meta($group_id,'_mipl_wc_cf_custom_field',true);
    $file_data = array();
    foreach ($group_data['field_name'] as $index => $fld_name) {
        if($field_name == $fld_name){
            $file_data['file_size'] = $group_data['file_size'][$index];
            $file_data['file_type'] = $group_data['file_type'][$index];
            
        }
    }
    return $file_data;
}
}
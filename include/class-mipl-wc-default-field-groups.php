<?php
class MIPL_WC_CF_Custom_Default_Fields{

    function mipl_default_field(){

        $old_posts_names = array();
        $existing_posts = get_posts(array(
            'post_type' => MIPL_WC_CF_POST_TYPE,
            'posts_per_page' => -1,
            'meta_key' => '_mipl_wc_cf_default_group',
            'meta_value' => 'yes'
        ));
        if( !empty($existing_posts) ){
            foreach($existing_posts as $existing_post){
                $old_posts_names[] = $existing_post->post_name;
            }
        }

            $default_field = array(
                "delivery_detail" => array(
                    "field_label" => array('Delivery Date'),
                    "field_name"  => array('delivery_date'),
                    "field_type"  => array('date'),
                    "default_value"  => array(''),
                    "option_value"   => array(''),
                    "required_field" => array(''),
                    "placeholder_checkbox" => array('no'),
                ),
                "players_detail"  => array(
                    "field_label" => array('Player Name', 'T-Shirt Size'),
                    "field_name"  => array('player_name', 't-shirt_size'),
                    "field_type"  => array('text', 'select'),
                    "default_value"  => array('', ''),
                    "option_value"   => array('', "XXXS\r\nXXS\r\nXS\r\nS\r\nM\r\nL\r\nXL\r\nXXL\r\nXXXL"),
                    "required_field" => array('', ''),
                    "placeholder_checkbox" => array('no', 'no'),
                ),
                "reCAPTCHA" => array(
                    "field_label" => array('reCAPTCHA'),
                    "field_name"  => array('reCAPTCHA'),
                    "field_type"  => array('recaptcha'),
                    "default_value"  => array(''),
                    "option_value"   => array(''),
                    "required_field" => array(''),
                    "placeholder_checkbox" => array('no'),
                )
            );
    
        
            $default_posts = array(
                "delivery_detail" => array(
                    'post_type'   => 'mipl_wc_ck_fields',
                    'post_title'  => 'Delivery info',
                    'post_name'   =>  'miplcf-delivery-info',
                    'post_status' => 'publish',
                    'post_author' => 1,
                ),
                "players_detail" => array(
                    'post_type'  => 'mipl_wc_ck_fields',
                    'post_title'  => 'Players',
                    'post_name'   =>  'miplcf-players',
                    'post_status' => 'publish',
                    'post_author' => 1,
                ),
                "reCAPTCHA" => array(
                    'post_type'  => 'mipl_wc_ck_fields',
                    'post_title'  => 'reCAPTCHA',
                    'post_name'   =>  'miplcf-recaptcha',
                    'post_status' => 'publish',
                    'post_author' => 1,
                )
            );
            
            // Insert the post into the database
            foreach ($default_posts as $key => $post_arr){

                if( in_array($post_arr['post_name'],$old_posts_names) ){
                    continue;
                }

                $post_id = wp_insert_post( $post_arr );
    
                $deactive_setting = array("deactive_group" => 1, "hide_title" => 1);
    
                if($post_id){

                    //Added default custom fields
                    $custom_default_fields = $default_field[$key];
                    add_post_meta($post_id, '_mipl_wc_cf_custom_field', $custom_default_fields);
                    
                    //Added default group setting
                   
                    add_post_meta($post_id, '_mipl_wc_cf_group_setting', $deactive_setting);
                    add_post_meta($post_id, '_mipl_wc_cf_default_group', 'yes');
    
                    //Added default fields setting
                    if($key == 'reCAPTCHA'){
                        add_post_meta($post_id, '_mipl_wc_cf_setting_position', 'woocommerce_review_order_before_submit');
                    }else{
                        add_post_meta($post_id, '_mipl_wc_cf_setting_position', 'woocommerce_before_checkout_billing_form');
                    }

                }

            }

        return true;

    }
    
}


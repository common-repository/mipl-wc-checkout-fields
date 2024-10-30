<?php
/**
* Plugin Name:       MIPL WC Checkout Fields
* Plugin URI:        https://wordpress.org/plugins/mipl-wc-checkout-fields
* Description:       Customize WooCommerce Checkout Fields, Create Group of custom fields & Update default checkout fields.
* Version:           1.2.0
* Requires at least: 5.1
* Requires PHP:      7.0
* Requires Plugins:  woocommerce
* Author:            Mulika Team
* Author URI:        https://www.mulikainfotech.com/
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/


/*
'MIPL WC Checkout Fields' is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

'MIPL WC Checkout Fields' is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with 'MIPL WC Checkout Fields'. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


defined( 'ABSPATH' ) || exit;

// Define MIPL-WC-CHECKOUT-FIELDS Version.
$plugin_data = get_file_data( __FILE__, array( 'version' => 'version' ) );
define( 'MIPL_WC_CHECKOUT_FIELDS_VERSION', $plugin_data['version'] );

// Define Const
define( 'MIPL_WC_CF_PLUGINS_URL', plugin_dir_url(__FILE__) );
define( 'MIPL_WC_CF_PLUGINS_DIR', plugin_dir_path(__FILE__) );
define( 'MIPL_WC_CF_POST_TYPE', 'mipl_wc_ck_fields' );
define( 'MIPL_WC_CF_UNIQUE_NAME', 'mipl-wc-checkout-fields' );
$mipl_uploads_dir = wp_upload_dir();
define( 'MIPL_WC_CF_UPLOAD_PATH', $mipl_uploads_dir['basedir']."/mipl-wc-checkout-fields/mipl_checkout_files" );
define( 'MIPL_WC_CF_UPLOAD_URL', $mipl_uploads_dir['baseurl']."/mipl-wc-checkout-fields/mipl_checkout_files" );

// Include Libs
include_once MIPL_WC_CF_PLUGINS_DIR.'include/lib-mipl-wc-cf-common.php';
include_once MIPL_WC_CF_PLUGINS_DIR.'include/class-mipl-input-validation.php';

// Include Classes
include_once MIPL_WC_CF_PLUGINS_DIR.'include/class-mipl-wc-cf-common.php';
include_once MIPL_WC_CF_PLUGINS_DIR.'include/class-mipl-wc-checkout.php';
include_once MIPL_WC_CF_PLUGINS_DIR.'include/class-mipl-wc-default-checkout-fields.php';
include_once MIPL_WC_CF_PLUGINS_DIR.'include/class-mipl-wc-default-field-groups.php';
include_once MIPL_WC_CF_PLUGINS_DIR.'include/class-mipl-wc-cf-product-single-page.php';
include_once MIPL_WC_CF_PLUGINS_DIR.'include/class-mipl-wc-cf-checkout-block.php';

// Create Class Objects
$mipl_custom_default_fields = new MIPL_WC_CF_Custom_Default_Fields();

//Global recaptcha flag
$GLOBALS['MIPL_WC_RECAPTCHA_FLAG'] = 1;

//Start session
if( !mipl_wc_cf_json_request() && !session_id() ){ session_start(); }

add_action( 'plugins_loaded', 'mipl_wc_checkout_init' );
function mipl_wc_checkout_init(){
    if ( class_exists( 'WooCommerce' ) ) {

        // Create Class Objects
        $mipl_default_checkout_obj = new MIPL_WC_Default_Checkout_Fields();
        $mipl_checkout_obj = new MIPL_WC_Checkout();
        $mipl_checkout_common = new MIPL_WC_CF_Common();
        $mipl_custom_default_fields = new MIPL_WC_CF_Custom_Default_Fields();
        $mipl_cf_single_page = new MIPL_WC_CF_Single_Page();

        // Common Hook
        add_action( 'init', array($mipl_checkout_obj, 'register_post_type'), 10);
        add_filter( 'woocommerce_email_styles', array( $mipl_checkout_common,'add_custom_styles_to_email' ) );
        
        if(isset($_REQUEST['mipl_action']) && $_REQUEST['mipl_action'] == 'file_preview'){
            add_action('init', array( $mipl_cf_single_page, 'mipl_file_preview' ),1);
        }
		// Admin side Hooks
        if(is_admin()){

            add_action( 'admin_enqueue_scripts',array($mipl_checkout_common,'admin_enqueue_scripts'),9);
            add_action( 'admin_menu',array($mipl_checkout_common,'register_checkout_default_fields'));
            add_action( 'admin_notices', 'mipl_wc_cf_admin_notices');
            add_action( 'add_meta_boxes',array($mipl_checkout_obj,'add_metaboxes'),10,2);
            add_action( 'manage_mipl_wc_ck_fields_posts_custom_column' ,array($mipl_checkout_obj,'show_fields_group_columns_data'), 10, 2);
            add_action( 'save_post',array($mipl_checkout_obj, 'save_custom_field'), 10, 3);
            add_action( 'admin_head', array($mipl_checkout_common, 'set_recaptcha_site_key'));
    
            add_filter( 'manage_mipl_wc_ck_fields_posts_columns', array($mipl_checkout_obj,'filter_fields_groups_columns' ));


            //Default fields
            if( isset($_POST['mipl_action']) && $_POST['mipl_action'] == 'save_wc_default_fields' ){
                add_action( 'init', array($mipl_default_checkout_obj, 'save_default_field_value'), 10);
            }
    
            if( isset($_REQUEST['mipl_action']) && $_REQUEST['mipl_action'] == 'mipl_reset_default_fields' ){
                add_action( 'init', array($mipl_default_checkout_obj, 'reset_default_field_value'), 10);
            }
    
            //save setting
            if( isset($_POST['mipl_action']) && $_POST['mipl_action'] == 'save_wc_cf_setting' ){
                add_action( 'init', array($mipl_checkout_common, 'save_wc_cf_setting'));
            }
           
            if( isset($_REQUEST['mipl_action']) && $_REQUEST['mipl_action'] == 'mipl_reset_recaptcha_fields' ){
                add_action( 'init', array($mipl_checkout_common, 'mipl_reset_recaptcha_fields'));
            }
    
    
            if((isset($_GET['post_type']) && isset($_GET['page'])) && $_GET['post_type'] == 'mipl_wc_ck_fields' && $_GET['page'] == 'mipl-wc-cf-default-fields'){

                $_SESSION['mipl_cf_admin_notices']['info'] = __("Edit default fields currently only functions with the 'Classic Checkout'.");
                
            }

            if(isset($_GET['post_type']) && $_GET['post_type'] == 'mipl_wc_ck_fields'){

                $_SESSION['mipl_cf_ck_block_info']['info'] = __("How to add 'MIPL Checkout Fields Group' block?");
                
            }

            // Plugin deactivation feedback.
            global $pagenow;
           
            if($pagenow == 'plugins.php'){
                add_action('admin_footer',  array($mipl_checkout_common, 'mipl_print_deactivate_feedback_dialog'));
            }
            add_action('admin_footer',  array($mipl_checkout_common, 'mipl_display_adding_block'));
            
            if ( isset($_REQUEST['mipl_action']) && $_REQUEST['mipl_action'] == 'mipl_cf_submit_and_deactivate') {
                add_action('init', array($mipl_checkout_common, 'mipl_cf_submit_and_deactivate'));
            }
           
        }

        // Client side Hooks
        if(!is_admin()){
            
            add_action( 'wp_enqueue_scripts',array($mipl_checkout_common, 'enqueue_scripts'));
            add_action( 'wp_head', array($mipl_checkout_common, 'set_recaptcha_site_key'));

            $checkout_field_positions = array(
                'woocommerce_before_order_notes',
                'woocommerce_after_order_notes',
                'woocommerce_checkout_before_order_review_heading',
                'woocommerce_review_order_before_payment',
                'woocommerce_review_order_after_payment',
                'woocommerce_review_order_before_submit',
                'woocommerce_review_order_after_submit'
            );
            foreach ($checkout_field_positions as $position_hook) {
                add_action($position_hook, array($mipl_checkout_obj, 'show_custom_field'),10);
            }
            add_action('wp_head',array($mipl_checkout_common, 'set_client_side_css'));
           
            add_action('woocommerce_checkout_billing', array($mipl_checkout_obj, 'before_billing_custom_fields'), 10);
            add_action('woocommerce_checkout_billing', array($mipl_checkout_obj, 'after_billing_custom_fields'), 99);
            add_action('woocommerce_checkout_shipping', array($mipl_checkout_obj, 'before_shipping_custom_fields'), 10);
            add_action('woocommerce_checkout_shipping', array($mipl_checkout_obj, 'after_shipping_custom_fields'), 99);
            

            //recaptcha v3
            add_action( 'woocommerce_review_order_after_submit', array($mipl_checkout_common, 'load_recaptcha_v3_script'));

            add_action( 'woocommerce_checkout_process',array($mipl_checkout_obj, 'validate_custom_fields'));
            add_action( 'woocommerce_checkout_update_order_meta', array($mipl_checkout_obj, 'update_order_meta'));
            add_action( 'woocommerce_email_order_meta', array($mipl_checkout_obj, 'display_fields_in_order_email'), 10, 4);

            add_filter( 'woocommerce_checkout_fields',array($mipl_default_checkout_obj, 'update_default_fields'));
            add_filter( 'woocommerce_form_field_file', array($mipl_checkout_common, 'mipl_form_file'),10,4);
            add_filter( 'woocommerce_form_field_recaptcha', array($mipl_checkout_common,'mipl_form_recaptcha_field'),10,4);
        
            if (isset($_REQUEST['mipl_action']) && $_REQUEST['mipl_action'] == 'mipl_wc_form_upload') {
                add_action( 'init', array($mipl_checkout_common, 'mipl_wc_upload_form' ));
            }

            // Product single page
            add_action( 'woocommerce_add_to_cart_validation', array($mipl_cf_single_page, 'validate_cart_fields'), 10, 5 );

            add_action( 'woocommerce_remove_cart_item', array($mipl_cf_single_page, 'mipl_woocommerce_remove_cart_item'), 10, 2 );
            add_action( 'woocommerce_new_order', array($mipl_cf_single_page, 'mipl_new_order_action'), 10, 2 );
        
            if(isset($_REQUEST['mipl_action']) && $_REQUEST['mipl_action'] == 'file_preview'){
                add_action( 'template_redirect', array( $mipl_cf_single_page, 'mipl_file_preview' ));
            }

            if (isset($_REQUEST['mipl_action']) && $_REQUEST['mipl_action'] == 'mipl_get_fld_of_single_product_page') {
                add_action( 'template_redirect', array( $mipl_cf_single_page, 'show_custom_fields_in_single_page' ));
            }
            
            add_action('woocommerce_before_add_to_cart_button', array($mipl_cf_single_page, 'single_page_fields' ));

            add_action('wp_footer', array($mipl_checkout_common, 'unset_session_data_after_order_received'));

        }

	}else{
        $_SESSION['mipl_cf_admin_notices']['warning'] = __("MIPL WC Checkout Fields: Please install and activate 'WooCommerce' plugin!");
        add_action( 'admin_notices','mipl_wc_cf_admin_notices');
    }
}

register_activation_hook( __FILE__, array($mipl_custom_default_fields, 'mipl_default_field') );


add_action('woocommerce_blocks_loaded', function() {
   
    $mipl_checkout_fields = new MIPL_WC_CF_Checkout_Fields();
    require_once __DIR__ . '/mipl-wc-checkout-fields-blocks-integration.php';
	add_action(
		'woocommerce_blocks_checkout_block_registration',
		function( $integration_registry ) {
			$integration_registry->register( new Custom_Fields_Blocks_Integration() );
		}
	);

    add_action( 'woocommerce_store_api_checkout_update_order_from_request',array($mipl_checkout_fields,'update_block_order_meta_custom_fields') , 10, 2 );
    
    add_action( 'woocommerce_cart_item_set_quantity',array($mipl_checkout_fields,'remove_single_product_cf_data_according_qty') , 10, 3 );

});


add_action( 'rest_api_init', function () {

    $mipl_checkout_fields = new MIPL_WC_CF_Checkout_Fields();

    register_rest_route( 'mipl-wc-cf/v1', '/cf_data', array(
        'methods'  => 'GET',
        'callback' => array($mipl_checkout_fields, 'mipl_wc_custom_fields'),
        'permission_callback' => '__return_true',
        ) 
    );

    register_rest_route( 'mipl-wc-cf/v1', '/client_cf_data/(?P<id>[a-zA-Z0-9-]+)', array(
        'methods'  => 'GET',
        'callback' => array($mipl_checkout_fields, 'mipl_wc_client_custom_fields'),
        'args' => array(
            'id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric( $param );
                }
            ),
        ),
        'permission_callback' => '__return_true',
        ) 
    );

    register_rest_route( 'mipl-wc-cf/v1', '/upload_file_data', array(
        'methods'  => 'POST',
        'callback' => array($mipl_checkout_fields, 'mipl_wc_upload_file'),
        'permission_callback' => '__return_true',
        ) 
    );

    
} );

if(!is_admin()){
    
    $mipl_checkout_fields = new MIPL_WC_CF_Checkout_Fields();
    add_action('woocommerce_checkout_init', array($mipl_checkout_fields, 'mipl_get_cart_product_info'));

}


<?php
class MIPL_WC_CF_Common{
    
    // Client side enqueue script.
    function enqueue_scripts(){
      
        // if ( is_product() && is_single() && is_checkout()){
            // Select2 Lib
           
        wp_enqueue_style('select2');
        wp_enqueue_script('select2');
        // }

        //Date picker
        wp_enqueue_script( 'mipl-wc-cf-flatpickr-js',MIPL_WC_CF_PLUGINS_URL.'assets/libs/flatpickr/flatpickr.min.js',null,true);
        wp_enqueue_style( 'mipl-wc-cf-flatpickr-css',MIPL_WC_CF_PLUGINS_URL.'assets/libs/flatpickr/flatpickr.min.css');
        
        //Color picker
        wp_enqueue_script( 'mipl-wc-cf-colorpicker-js',MIPL_WC_CF_PLUGINS_URL.'assets/libs/coloris/coloris.min.js',null,true);
        wp_enqueue_style( 'mipl-wc-cf-colorpicker-css',MIPL_WC_CF_PLUGINS_URL.'assets/libs/coloris/coloris.min.css');
        
        //Enqueue custom JS
        // wp_enqueue_script( 'mipl-wc-cf-scripts', MIPL_WC_CF_PLUGINS_URL.'assets/script/mipl_wc_cf_client_script.js', array( 'jquery' ), null, true );
        wp_enqueue_script( 'mipl-wc-cf-scripts', MIPL_WC_CF_PLUGINS_URL.'assets/script/mipl_wc_cf_client_script.min.js', array( 'jquery' ), null, true );
        
       
        $recaptcha_enable_checkbox = get_option('_mipl_wc_recaptcha_enable_checkbox');
        if(!isset($recaptcha_enable_checkbox) || empty($recaptcha_enable_checkbox)){return false;}
        
        $recaptcha_type = get_option('_mipl_wc_recaptcha_recaptcha_type');
        $v2_site_key = get_option('_mipl_wc_recaptcha_v2_site_key');
        if(!empty($recaptcha_type) && $recaptcha_type == 'reCAPTCHA_v2' && !empty($v2_site_key)){
            wp_enqueue_script("mipl-wc-cf-recaptchav2","https://www.google.com/recaptcha/api.js?onload=mipl_wc_cf_load_recaptcha&render=explicit");
        
        }
        $v3_site_key = get_option('_mipl_wc_recaptcha_v3_site_key');
        if(!empty($recaptcha_type) && $recaptcha_type == 'reCAPTCHA_v3' && !empty($v3_site_key)){
            wp_enqueue_script('mipl_wc_recaptchav3','https://www.google.com/recaptcha/api.js?render='.$v3_site_key);
            
        } 
        
    }

    //Style to email template
    function add_custom_styles_to_email( $css ) {
        $css .= 'table.mipl_wc_order_table { word-break: break-all; }';
        $css .= 'table.mipl_wc_order_table th, table.mipl_wc_order_table td{ padding: 6px!important; }';
        return $css;
    }
    
    function set_client_side_css(){
        ?>
        <style>
        .woocommerce-checkout.theme-storefront .mipl-wc-checkout-fields.before_order_review{ width:41%; float:right; }
        </style>
        <?php
    }

    // Admin side enqueue script.
    function admin_enqueue_scripts(){

        global $post;
        
        // Scripts
        wp_enqueue_media();
        wp_enqueue_script( 'jquery-ui-core');
        wp_enqueue_script( 'jquery-ui-sortable');
        
        //Enqueue custom JS
        wp_enqueue_script( 'mipl-wc-cf-admin-scripts', MIPL_WC_CF_PLUGINS_URL.'assets/script/mipl_wc_cf_admin_script.min.js', array( 'jquery' ), null, true );
        //wp_enqueue_script( 'mipl-wc-cf-admin-scripts', MIPL_WC_CF_PLUGINS_URL.'assets/script/mipl_wc_cf_admin_script.js', array( 'jquery', 'wp-i18n' ), null, true );
        
        //Enqueue custom CSS
        wp_enqueue_style( 'mipl-wc-cf-style',MIPL_WC_CF_PLUGINS_URL.'assets/css/mipl_wc_cf_style.min.css');
        
        if( !empty($post) && $post->post_type == MIPL_WC_CF_POST_TYPE ){
            //Date picker
            wp_enqueue_script( 'mipl-wc-cf-flatpickr-js',MIPL_WC_CF_PLUGINS_URL.'assets/libs/flatpickr/flatpickr.min.js',null,true);
            wp_enqueue_style( 'mipl-wc-cf-flatpickr-css',MIPL_WC_CF_PLUGINS_URL.'assets/libs/flatpickr/flatpickr.min.css');

            //Color picker
            wp_enqueue_script( 'mipl-wc-cf-colorpicker-js',MIPL_WC_CF_PLUGINS_URL.'assets/libs/coloris/coloris.min.js',null,true);
            wp_enqueue_style( 'mipl-wc-cf-colorpicker-css',MIPL_WC_CF_PLUGINS_URL.'assets/libs/coloris/coloris.min.css');

            // Select2 Lib
            wp_enqueue_script( 'mipl-wc-cf-select2-js', MIPL_WC_CF_PLUGINS_URL.'assets/libs/select2/select2.min.js');
            wp_enqueue_style( 'mipl-wc-cf-select2-css',MIPL_WC_CF_PLUGINS_URL.'assets/libs/select2/select2.min.css');
        }

    }


    function register_checkout_default_fields(){

        $mipl_default_checkout_obj = new MIPL_WC_Default_Checkout_Fields();
        add_submenu_page( 'edit.php?post_type='.MIPL_WC_CF_POST_TYPE, __('Default Fields'), __('Default Fields'), 'manage_options', 'mipl-wc-cf-default-fields',array($mipl_default_checkout_obj, 'checkout_default_fields'));
        add_submenu_page( 'edit.php?post_type='.MIPL_WC_CF_POST_TYPE, __('Settings'), __('Settings'), 'manage_options', 'mipl-wc-cf-setting',array($this,'mipl_wc_cf_fields_setting')); 
      
    }


    function mipl_wc_cf_fields_setting(){

        include_once MIPL_WC_CF_PLUGINS_DIR.'/view/mipl-wc-cf-setting-fields.php';

    }


    function save_wc_cf_setting(){
        $v2_site_key = get_option('_mipl_wc_recaptcha_v2_site_key');
        $v2_secret_key = get_option('_mipl_wc_recaptcha_v2_secret_key');
        
        $v3_site_key = get_option('_mipl_wc_recaptcha_v3_site_key');
        $v3_secret_key = get_option('_mipl_wc_recaptcha_v3_secret_key');

        if($_POST['_mipl_wc_recaptcha_recaptcha_type'] == 'reCAPTCHA_v3' && ((is_string($v3_site_key) && !empty($v3_site_key)) && (is_string($v3_secret_key) && !empty($v3_secret_key)) ) ){
            $_POST['_mipl_wc_recaptcha_v3_site_key'] = $v3_site_key;
            $_POST['_mipl_wc_recaptcha_v3_secret_key'] = $v3_secret_key;
        }

        if($_POST['_mipl_wc_recaptcha_recaptcha_type'] == 'reCAPTCHA_v2' && ((is_string($v2_site_key) && !empty($v2_site_key)) && (is_string($v2_secret_key) && !empty($v2_secret_key)) ) ){
            $_POST['_mipl_wc_recaptcha_v2_site_key'] = $v2_site_key;
            $_POST['_mipl_wc_recaptcha_v2_secret_key'] = $v2_secret_key;
        }

        $recaptcha_validational_array = mipl_wc_recaptcha_validation($_POST);
        $val_obj = new MIPL_WC_CF_Input_Validation($recaptcha_validational_array, $_POST);
        $val_obj->validate();
        $recaptcha_fields_errors = $val_obj->get_errors();
        $_SESSION['mipl_cf_admin_notices']['error'] = implode('<br>',$recaptcha_fields_errors);
        $recaptcha_post_data = $val_obj->get_valid_data();
        

        foreach ($recaptcha_post_data as $setting_key => $setting_value) {
            update_option($setting_key, $setting_value);
        }

    }

    function mipl_form_file($field, $key, $args, $value){
        $group_id = $args['custom_attributes']['group_id'];
        $mipl_file_nonce = wp_create_nonce('mipl_wc_cf_file_'.$group_id);
        $file_type = $args['custom_attributes']['file_type'];
        $file_size = isset($args['custom_attributes']['file_size']) ? $args['custom_attributes']['file_size'] : '';
        $file_url = isset($value) ? $value : '';
        $file_name = basename(parse_url($file_url, PHP_URL_PATH));

        if($args['required']){
            $validated_field = '<abbr class="required" title="required"> * </abbr>';
        }else{
            $validated_field = '<span class="optional">('.__('optional').')</span>';
        }

        $field .= '<p class="form-row " id="'.esc_attr( $key ).'_field" data-priority="">
            <label for="'.esc_attr( $key ).'" class="">'.esc_attr($args['label']). wp_kses_post($validated_field).' </label>';
        
        
        $selected_file = '';
        if(!empty($value)){
            $selected_file = '<p style="margin-top: -22px" class="mipl_selected_file_data"><label>'.__('Selected File').': </label><span class="mipl_file_label">"'.$file_name.'"</span></p>';
        }

        $field .= '<span class="woocommerce-input-wrapper"><input class="input-text file ' . esc_attr( implode( ' ', $args['input_class'] ) ) . ' mipl_wc_file_field" type="file" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" accept="' . esc_attr($file_type) . '" data-name=" ' . esc_attr($args['custom_attributes']['cf_name']) . ' " data-nonce=" '.$mipl_file_nonce.' " data-group="'.$group_id.'">'.$selected_file.'<input type="hidden" class="mipl_wc_hidden_forms_name" value="'.$value.'" name="' . esc_attr( $key ) . '" /><span style="color:red" class="mipl_wc_file_error"></span></span>';
       
    
        $field .= '</p>';

        return $field;

    }

    function mipl_form_recaptcha_field(){
    
        $enable_checkbox = get_option('_mipl_wc_recaptcha_enable_checkbox');
        $recaptcha_type = get_option('_mipl_wc_recaptcha_recaptcha_type');
        $field = '';
        if(!empty($recaptcha_type) && !empty($enable_checkbox) && $recaptcha_type == 'reCAPTCHA_v2'){

            $v2_site_key = get_option('_mipl_wc_recaptcha_v2_site_key');
            if(!empty($v2_site_key)){
                $field .= '<div id="mipl-cf-recaptcha" class="mipl-wc-cf-recaptcha mipl-wc-cf-recaptcha-v2" data-sitekey="'.$v2_site_key.'"></div>';

            }
    
        }
        return $field;

    }

    function load_recaptcha_v3_script(){

        $enable_checkbox = get_option('_mipl_wc_recaptcha_enable_checkbox');
        $site_key = get_option('_mipl_wc_recaptcha_v3_site_key');
        $recaptcha_type = get_option('_mipl_wc_recaptcha_recaptcha_type');
        if(!empty($site_key) && !empty($enable_checkbox) && $recaptcha_type=='reCAPTCHA_v3'){

            echo '<input type="hidden" class="mipl-wc-cf-recaptcha mipl-wc-cf-recaptcha-v3" name="g-recaptcha-response">';
        
        }
    }
    

    function mipl_wc_upload_form(){
                
        $group_id = filter_input(INPUT_POST, 'group_id', FILTER_DEFAULT);
        $field_name = filter_input(INPUT_POST, 'cf_name', FILTER_DEFAULT);
        $mipl_nonce = filter_input(INPUT_POST, 'file_nonce', FILTER_DEFAULT);
        $verify_nonce = wp_verify_nonce(trim($mipl_nonce), 'mipl_wc_cf_file_'.$group_id);
        
        if(!$verify_nonce){
            return false;
        }
        
        $upload_dir = wp_upload_dir();
        if(!is_writable($upload_dir['basedir'])){
            $response = array('status'=>'error','message'=>__('File permission denied error!'));
            echo json_encode($response);
            die;
        }
       
        if( empty($group_id) && empty($field_name) ){ return false; }

        $file_data = mipl_wc_cf_get_file_setting_data($group_id, trim($field_name));
        $file_size = $file_data['file_size'];
        $file_type = $file_data['file_type'];
        $response = array();
        if ( !isset($_FILES['mipl_wc_form']) ){
            $response = array('status'=>'error','message'=>__("Invalid request!"));
            echo json_encode($response);
            die();
        }
        
        if ( $_FILES['mipl_wc_form']["error"] != UPLOAD_ERR_OK ) {
            $response = array('status'=>'error','message'=>$_FILES['mipl_wc_form']["error"]);
            echo json_encode($response);
            die();
        }
        
        if( $_FILES['mipl_wc_form']['size'] <= 0 ){
            $response = array('status'=>'error','message'=>__('File size should not zero.'));
        }
        if( (1024*1024*$file_size) < $_FILES['mipl_wc_form']['size'] ){
            $response = array('status'=>'error','message'=>__('File size should less than').' '.$file_size.'mb.');
        }
        if( !in_array($_FILES['mipl_wc_form']['type'], $file_type ) ){
            $response = array('status'=>'error','message'=>__('File type is not valid'));
        }
        
        if( !empty($response) ){
            echo json_encode($response);
            die();
        }

        $uploaded_folder = date("Y/m");   
        $random_prefix = mipl_wc_cf_rand(5); 
            
        $tmp_name = !empty($_FILES['mipl_wc_form']['tmp_name'])?sanitize_text_field($_FILES['mipl_wc_form']['tmp_name']):'';
        $uploaded_file = !empty($_FILES['mipl_wc_form']['name'])?basename($_FILES['mipl_wc_form']['name']):'';
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
            $response = array('status'=>'success','message'=>__('File successfully uploaded'), 'data'=>$file_url);
        }else{
            $response = array('status'=>'error','message'=>__('File uploading error'));
        }
        
        echo json_encode($response);
   
        die();
    
    }
    

    function mipl_reset_recaptcha_fields(){
       
        if($_POST['_mipl_wc_recaptcha_recaptcha_type'] == 'reCAPTCHA_v2'){
            update_option('_mipl_wc_recaptcha_v2_site_key', '');
            update_option('_mipl_wc_recaptcha_v2_secret_key', '');
        }else{
            update_option('_mipl_wc_recaptcha_v3_site_key', '');
            update_option('_mipl_wc_recaptcha_v3_secret_key', '');
        }
        
        echo json_encode(array('status'=>'success','message'=>__('Successfully deleted')));
        die();
    }

    function set_recaptcha_site_key(){

        $v3_site_key = get_option('_mipl_wc_recaptcha_v3_site_key');
        $site_key = isset($v3_site_key) ? $v3_site_key : '';
        $rest_route = get_rest_url(null, 'mipl-wc-cf/v1');
        ?>
        <script>
            var MIPL_WC_CF_RECAPTCHA_V3 = '<?php echo $site_key ?>';
            var MIPL_WC_CF_HOME_URL = '<?php echo home_url('/')?>';
            var MIPL_WC_CF_REST_ROUTE = '<?php echo $rest_route ?>';
        </script>
        <?php

    }

    
    // Plugin deactivation popup
    function mipl_print_deactivate_feedback_dialog() {
        ?>

        <div id="mipl-wc-checkout-fields-deactivate-popup" style="display:none;">
        
            <?php
            $deactivate_reasons = [
                'no_longer_needed' => [
                    'title' => esc_html__('I no longer need the plugin'),
                    'input_placeholder' => '',
                ],
                'found_a_better_plugin' => [
                    'title' => esc_html__('I found a better plugin'),
                    'input_placeholder' => esc_html__('Please share which plugin'),
                ],
                'could_not_get_the_plugin_to_work' => [
                    'title' => esc_html__("I couldn't get the plugin to work"),
                    'input_placeholder' => '',
                ],
                'temporary_deactivation' => [
                    'title' => esc_html__("It's a temporary deactivation"),
                    'input_placeholder' => '',
                ],
                'other' => [
                    'title' => esc_html__('Other'),
                    'input_placeholder' => esc_html__('Please share the reason'),
                ],
            ];
            ?>

            <form id="mipl_cf_deactivation_form" method="post" style="margin-top:20px;margin-bottom:30px;">
                <div id="" style="font-weight: 700; font-size: 15px; line-height: 1.4;"><?php echo esc_html__('If you have a moment, please share why you are deactivating plugin:'); ?></div>
                <div id="" style="padding-block-start: 10px; padding-block-end: 0px;">
                    <?php foreach ($deactivate_reasons as $reason_key => $reason) { ?>
                        <div class="" style="display: flex; align-items: center; line-height: 2; overflow: hidden;">
                            <label>
                                <input id="plugin-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="" style="margin-block: 0; margin-inline: 0 15px; box-shadow: none;" type="radio" name="mipl_cf_deactivation_reason" value="<?php echo esc_attr($reason_key); ?>" required /><?php echo esc_html($reason['title']); ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <div id="mipl-cf-other-reason-textarea">
                <textarea style="vertical-align:top;margin-left: 30px;" id="other-reason" name="mipl_cf_deactivation_other_reason" rows="4" cols="50" placeholder=<?php echo esc_attr("Please share the reason") ?>></textarea>
                </div>

                <div class="" style="display: flex;  padding: 20px 0px;">
                    <button class="mipl_cf_submit_and_deactivate button button-primary button-large" type="submit" style="margin-right:10px;"><?php echo esc_html('Submit & Deactivate') ?></button>
                    <button class="mipl_cf_skip_and_deactivate button" type="button" ><?php echo esc_html('Skip & Deactivate') ?></button>
                </div>
                
            </form>

        </div>

        <script>
            jQuery(document).ready(function(){

                jQuery('#deactivate-mipl-wc-checkout-fields').click(function(){
                    var $deactivate_url = jQuery(this).attr('href');
                    tb_show("Quick Feedback", "#TB_inline?&amp;inlineId=mipl-wc-checkout-fields-deactivate-popup&amp;height=500;max-height: 330px; min-height: 330px;");
                    jQuery('#TB_window form').attr('data-deactivate_url',$deactivate_url);                    
                    return false;
                });

            });
        

            jQuery(document).ready(function(){

                jQuery('.mipl_cf_skip_and_deactivate').click(function(){
                    mipl_cf_deactivate_plugins();
                    return false;
                });
            
                jQuery('#mipl_cf_deactivation_form').submit(function(){
                    mipl_cf_deactivate_plugins();
                    return false;
                });

            });


            function mipl_cf_deactivate_plugins(){

                var $form_data = jQuery('#mipl_cf_deactivation_form').serializeArray();
                var $deactivate_url = jQuery('#mipl_cf_deactivation_form').attr('data-deactivate_url');
                jQuery('#mipl_cf_deactivation_form button').attr('disabled', 'disabled');
                jQuery.post('?mipl_action=mipl_cf_submit_and_deactivate', $form_data, function(response){
                    window.location = $deactivate_url;
                });
                
                return false;

            }
        

            jQuery(document).ready(function(){
                
                jQuery('#mipl_cf_deactivation_form').on( 'change', 'input[name="mipl_cf_deactivation_reason"]', function () {
                    $feedback_val = jQuery(this).val();
                    jQuery('#mipl-cf-other-reason-textarea textarea').removeAttr('required');
                    if($feedback_val == 'other'){
                        jQuery('#mipl-cf-other-reason-textarea textarea').attr('required','required');
                    }
                });

            });

        </script>
        <?php

    }

    function mipl_cf_submit_and_deactivate(){
        
        $feedback = "";
        if(isset($_POST['mipl_cf_deactivation_reason'])){
            $feedback = sanitize_text_field($_POST['mipl_cf_deactivation_reason']);
        }

        if($feedback == 'other' && isset($_POST['mipl_cf_deactivation_other_reason'])){
            $feedback = sanitize_textarea_field($_POST['mipl_cf_deactivation_other_reason']);
        }

        if(empty($feedback)){
            $feedback = __('Skipped feedback and plugin deactivated');
        }

        $deactivation_date = current_time('mysql');
        $home_url = home_url();
        $url = 'https://store.mulika.in/api/wp/v1/plugin/feedback/';        
        $args = array(
            'method'      => 'POST',
            'timeout'     => 2,
            'body'        => array(
                'home_url'     => $home_url,
                'plugin_name'     => MIPL_WC_CF_UNIQUE_NAME,
                'deactivation_date' => $deactivation_date,
                'feedback' => $feedback
            )
        );
        
        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
            // $error_message = $response->get_error_message();
        } else {
            // echo json_encode( $response );
        }

        die();

    }

    function unset_session_data_after_order_received(){
        if(!empty( is_wc_endpoint_url('order-received') )){
            unset($_SESSION['mipl_single_product_post_data']);
            unset($_SESSION['temp_custom_fields_data']);
            ?>
            <script id="mipl_session_destroy">
                sessionStorage.removeItem('mipl_ck_fields_group');
            </script>
            <?php
        }
        if ( is_product() || is_shop()) {
            ?>
            <script>
                sessionStorage.removeItem('mipl_ck_fields_group');
            </script>
            <?php
        }

    }

    function mipl_display_adding_block(){
        add_thickbox();
        ?>
            <div id="mipl_wc_cf_block_modal" class="mipl_cf7_popup_modal" style="display:none">
                <div class="mipl_wc_cf_popup_dialog mipl_wc_cf_popup_small">
                    <div class="mipl_wc_cf_popup_content">
                        <div>
                            <h4># <?php echo esc_html("Add block before or after checkout fields.") ?></h4>
                            <img src="<?php echo MIPL_WC_CF_PLUGINS_URL.'assets/images/add-after-before.png'?>" alt="" width="800px"/><hr>
                        </div>
                        <div>
                            <h4># <?php echo esc_html("Click on add(+) sign then 'MIPL Checkout Fields Group' block will be display.") ?> </h4>                            
                            <img src="<?php echo MIPL_WC_CF_PLUGINS_URL.'assets/images/display-block.png'?>" alt="" width="800px"/><hr>
                        </div>
                        <div>
                            <h4># <?php echo esc_html("Now 'MIPL Checkout Fields Group' block added then check fields group setting in right side.") ?></h4>                            
                            <img src="<?php echo MIPL_WC_CF_PLUGINS_URL.'assets/images/added-block.png'?>" alt="" width="800px"/>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php
    }
    
}
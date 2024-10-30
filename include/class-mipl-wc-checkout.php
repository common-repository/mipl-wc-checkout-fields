<?php
class MIPL_WC_Checkout{

    public $mipl_wc_custom_validated_data = null;
    public $mipl_wc_valid_field_groups_data = null;

    //register custom post type
    function register_post_type(){
        
        $labels = array(
            'name'          => __( "Fields Groups" ),
            'singular_name' => __( "Fields Groups" ),
            'menu_name'     => __( "Checkout Fields" ),
            'add_new'       => __( 'Add New' ),
            'add_new_item'  => __( 'Add New Fields Groups' ),
            'new_item'      => __( 'New Fields Groups' ),
            'edit_item'     => __( 'Edit Fields Groups' ),
            'view_item'     => __( 'View Fields Groups' ),
            'all_items'     => __( "All Field Groups" ),
            'search_items'  => __( 'Search Field Groups' ),
            'parent_item_colon' => __( 'Parent : Field Groups' )
        );
            
        $support = array('title');
            
        $args = array(
            'labels'        => $labels,
            'public'        => true,
            'publicly_queryable' => false,
            'hierarchical'       => false,
            'supports'           => $support,
            'menu_icon'          => "dashicons-forms"
        );

        register_post_type(MIPL_WC_CF_POST_TYPE, $args);

    } 


    function filter_fields_groups_columns( $columns ) {


        $new_columns = array(
            'cb' => $columns['cb'],            
            'title' => $columns['title'],
            '_mipl_wc_cf_setting_position' => __('Fields Position'),
            '_mipl_wc_cf_setting_field_repeat' => __('Repeat Fields'),
            '_mipl_wc_cf_setting_products' => __('Product Name'),
            '_mipl_wc_cf_setting_product_category' => __('Product Category'),
            '_mipl_wc_cf_count' => __('Fields Count'),
            '_mipl_wc_cf_active_group' => __('Status'),
            'date' => $columns['date'],
        );
        
        return $new_columns;
        
    }

    
    // Show setting of custom fields in admin columns
    function show_fields_group_columns_data($column_name, $id){
        
        $col_names = array(
            '_mipl_wc_cf_setting_position',
            '_mipl_wc_cf_setting_field_repeat',
            '_mipl_wc_cf_setting_products',
            '_mipl_wc_cf_setting_product_category',
            '_mipl_wc_cf_active_group',
            '_mipl_wc_cf_count',
        );
        
        if(in_array($column_name,$col_names)){
            $fld_grp_specilization = get_post_meta($id, '_mipl_wc_cf_field_group_specilization', true);
            $product_specilization = get_post_meta($id, '_mipl_wc_cf_order_item_specilization', true);

            $fld_grp_specilization = isset($fld_grp_specilization) ? $fld_grp_specilization : '';
            $product_specilization = isset($product_specilization) ? $product_specilization : '';

            if( $column_name == '_mipl_wc_cf_setting_products'){
                
                $product = get_post_meta($id, '_mipl_wc_cf_setting_products', true);

                if( empty($product) || !is_array($product) || ($fld_grp_specilization == 'false')){
                    echo __('&mdash;');
                    return true;
                }

                if($fld_grp_specilization == 'true' && $product_specilization == 'all_product'){
                    echo __("All");
                    return true;
                }
                
                $product_arr = array();
                foreach($product as $product_id){
                    $product_arr[] = get_the_title($product_id);
                }
                
                echo implode(', ', $product_arr);
                
            }else if( $column_name == '_mipl_wc_cf_setting_product_category'){
                
                $product_category = get_post_meta($id,'_mipl_wc_cf_setting_product_category', true);
                if( empty($product_category) || !is_array($product_category) ||  ($fld_grp_specilization=='false')){
                    echo __('&mdash;');
                    return true;
                }
                
                $product_cat_name = array();
                foreach($product_category as $product_cat_id){
                    $cat_obj = get_term($product_cat_id);
                    $product_cat_name[] = $cat_obj->name;
                }
                
                echo implode(', ', $product_cat_name);
                
            }else if( $column_name == '_mipl_wc_cf_count' ){
                
                $custom_fields = get_post_meta($id, '_mipl_wc_cf_custom_field', true);
                $field_count = 0;
                if(isset($custom_fields["field_label"])){
                    $field_count = count($custom_fields["field_label"]);
                }
                echo esc_html($field_count);
                
            }else if( $column_name == '_mipl_wc_cf_active_group' ){

                $group_setting = get_post_meta($id, '_mipl_wc_cf_group_setting', true);
            
                if(empty($group_setting['deactive_group'])){
                    echo esc_html("Activated");
                }else{
                    echo esc_html("Deactivated");
                }

            }else{
                
                $data = get_post_meta($id, $column_name, true);
                $pattern = array('/woocommerce_/', '/_/'); 
                $replace = array('',' ');                   ;
                $cf_column_data = ucwords(preg_replace($pattern, $replace, $data));
                if(!is_array($cf_column_data)){
                    echo esc_html($cf_column_data);
                }
                
            }
            
        }   
    }


    // Add meta boxes
    function add_metaboxes($post_type,$posts){ 
        add_meta_box('mipl_checkout_custom_field', __('Checkout Fields'),array($this,'checkout_custom_fields'), MIPL_WC_CF_POST_TYPE, 'normal', 'default');
        add_meta_box('mipl_checkout_custom_field_setting', __('Setting'),array($this,'checkout_custom_fields_setting'), MIPL_WC_CF_POST_TYPE, 'normal', 'default');
        
        add_meta_box('mipl_checkout_cf_group_setting', __('Group Setting'),array($this,'checkout_fields_group_setting'), MIPL_WC_CF_POST_TYPE,'side', 'default');

        // Show Orders Data
        if(get_current_screen()->id == 'woocommerce_page_wc-orders'){
            add_meta_box('mipl_checkout_cf_order_data', __('Order Details'), array($this,'checkout_fields_order_metabox'), 'woocommerce_page_wc-orders', 'normal', 'default');
        }else{
            add_meta_box('mipl_checkout_cf_order_data', __('Order Details'), array($this,'checkout_fields_order_metabox'), 'shop_order', 'normal', 'default');
        }
        
    }
    

    // Callback function of custom fields metabox
    function checkout_custom_fields($post){
        include_once MIPL_WC_CF_PLUGINS_DIR.'/view/mipl-wc-checkout-custom_field.php';
    }
    

    // Callback function of custom fields setting metabox
    function checkout_custom_fields_setting($post){
        include_once MIPL_WC_CF_PLUGINS_DIR.'/view/mipl-wc-cf-checkout-setting.php';
    }

    
    // Callback function of placed order data
    function checkout_fields_order_metabox($order){
        include_once MIPL_WC_CF_PLUGINS_DIR.'/view/mipl-wc-checkout-order-placed-data.php';
    }


    function checkout_fields_group_setting($post){
        include_once MIPL_WC_CF_PLUGINS_DIR.'/view/mipl-wc-checkout-group-setting.php';
    }


    // Save custom field and setting from admin side.
    function save_custom_field( $post_id, $post, $update ) {
        
        $_SESSION['mipl_cf_admin_notices'] = array(); 
        
        global $wpdb;

        if ( !$update ){
            return false;
        }

        $mipl_wc_post_status = array('publish');
        if( !in_array($post->post_status, $mipl_wc_post_status)){
            return false;
        }
        
        if ( $post->post_type != MIPL_WC_CF_POST_TYPE ){
            return false;
        }

        // Update group setting
        if(isset($_POST['_mipl_wc_cf_group_setting'])){

            $setting_array = mipl_wc_cf_get_group_setting();
            $val_obj = new MIPL_WC_CF_Input_Validation($setting_array, $_POST['_mipl_wc_cf_group_setting']);
            $val_obj->validate();
            $group_setting_errors = $val_obj->get_errors();
            $post_data = $val_obj->get_valid_data();
            update_post_meta($post_id, '_mipl_wc_cf_group_setting', $post_data);
            
        }

        if(!isset($_POST['_mipl_wc_cf_group_setting']) && $post->post_status == 'publish'){
            update_post_meta($post_id, '_mipl_wc_cf_group_setting', '');
        }

        // Update custom field.
        if(isset($_POST['mipl_wc_custom_field'])){
            $custom_fields =  mipl_wc_custom_field_validate_data($_POST['mipl_wc_custom_field']);
            update_post_meta($post_id, '_mipl_wc_cf_custom_field', $custom_fields);
            if(in_array('errors',array_keys($custom_fields))){
                $_SESSION['mipl_cf_admin_notices']['error'] = __('Fields should be valid!');
            }
        }
       
        if(!isset($_POST['mipl_wc_custom_field']) && $post->post_status == 'publish'){
            update_post_meta($post_id, '_mipl_wc_cf_custom_field', '');
        }

        //update custom field setting
        if(!isset($_POST['_mipl_wc_cf_setting_field_repeat'])){
            $_POST['_mipl_wc_cf_setting_field_repeat'] = "no_repeat";
        }

        if(!isset($_POST['_mipl_wc_cf_field_group_specilization'])){
            $_POST['_mipl_wc_cf_field_group_specilization'] = "false";
        }

        $settings_fields = mipl_wc_cf_get_custom_fields_setting();
        $setting_fields_data = array();

        
        $setting_fields = array(
            '_mipl_wc_cf_setting_position',
            '_mipl_wc_cf_cart_field_position',
            '_mipl_wc_cf_setting_field_repeat',
            '_mipl_wc_cf_field_group_specilization', 
            '_mipl_wc_cf_order_item_specilization', 
            '_mipl_wc_cf_setting_product_category',
            '_mipl_wc_cf_setting_products',
            '_mipl_wc_cf_email_to_list'
        );

        foreach($setting_fields as $key){
            if(isset($_POST[$key])){
                if(is_array($_POST[$key])){
                    $setting_fields_data[$key] = array_map('sanitize_text_field', $_POST[$key]);
                }else{
                    $setting_fields_data[$key] = sanitize_text_field($_POST[$key]);
                }
            }else{
                $setting_fields_data[$key] = '';
            }
        }
       
        $val_obj = new MIPL_WC_CF_Input_Validation($settings_fields,$setting_fields_data);
        $val_obj->validate();
        $errors = $val_obj->get_errors();
        $post_data = $val_obj->get_valid_data();
        foreach($post_data as $meta_key => $meta_val){
            update_post_meta( $post_id, $meta_key, $meta_val);
        }
       
        if(!empty($errors)){
            foreach($errors as $error_key => $error_val){
                if(empty($_SESSION['mipl_cf_admin_notices']['error'])){
                    $_SESSION['mipl_cf_admin_notices']['error'] = $error_val;
                }else{
                    $_SESSION['mipl_cf_admin_notices']['error'] .= "<br>".$error_val;
                }
            }
        } 

    }
    

    // Without post title post does't publish
    function disable_empty_title( $data, $postarr ){
        
        if( MIPL_WC_CF_POST_TYPE == $data['post_type'] ){
            
            if ( is_array( $data ) && 'publish' == $data['post_status'] && empty( $data['post_title'] )) {
                $data['post_status'] = 'draft';    
            }
                        
            if(isset($postarr['_mipl_wc_cf_setting_position'])){
                if( empty($data['post_title'])){
                    $data['post_status'] = 'draft';
                }else{
                    if( !empty( $data['post_title'] ) ){
                        $data['post_status'] = 'publish';
                    }
                }
            }

        }
        
        return $data;
        
    }


    // Show form field at client side
    function show_custom_field($checkout){

        $current_wc_hook = current_action();
        // if( $current_wc_hook == 'woocommerce_checkout_before_order_review' ){
        //     $current_wc_hook = 'woocommerce_checkout_before_order_review_heading';
        // }

        $cart_items = WC()->cart->get_cart();
        $validated_fields_groups = $this->get_valid_groups($cart_items, $current_wc_hook);

        $wrap_class = "";
        if( in_array($current_wc_hook, array('woocommerce_checkout_before_order_review_heading','woocommerce_checkout_before_order_review')) ){
            $wrap_class = "before_order_review";
        }
        ?>
        <div class='mipl-wc-checkout-fields <?php echo esc_attr($wrap_class) ?>'>
        <?php
        foreach ($validated_fields_groups as $grp_id=>$fields_data) {
            
            $field_group_id = $fields_data['group_id'];

            if(empty($fields_data)){
                continue;
            }
         
            $all_custom_fields = get_post_meta($field_group_id,'_mipl_wc_cf_custom_field',true);

            $group_specilization = get_post_meta($field_group_id, '_mipl_wc_cf_field_group_specilization', true);

            $field_group_setting = get_post_meta($field_group_id,'_mipl_wc_cf_group_setting',true);
            $hide_title = isset($field_group_setting['hide_title'])?$field_group_setting['hide_title']:'';

            $custom_field = !empty($fields_data['fields'])?$fields_data['fields']:'';
            $error_field_error = array('');
            if(isset($all_custom_fields['errors'])){
                foreach ($all_custom_fields['errors'] as $fld_key => $fld_value) {
                    $error_field_error[] = count($fld_value);
                }
            }
            if(isset($custom_field['field_label']) && max($error_field_error) == count($custom_field['field_label'])){
                continue;
            }
            
            if(!isset($custom_field) || empty($custom_field)){continue;}
            $group_title = get_the_title($field_group_id);
            
            if(!empty($fields_data['products'])){
                $products = $fields_data['products'];
                foreach ($products as $key => $pro_data) {
                    $product_id = $pro_data['id'];
                    $product_qty = $pro_data['qty'];
                    $product_title = get_the_title($product_id);
                    for($qty = 1; $qty <= $product_qty; $qty++){
                        $suffix = mipl_wc_cf_english_ordinal_suffix($qty);
                        
                        if(empty($hide_title)){
                            if($product_qty == 1){
                                ?>
                                <h4 class='mipl_wc_cf_group_title'>
                                    <?php esc_html_e("$group_title ($product_title)") ?>
                                </h4>  
                                <?php
                            }else{
                                ?>
                                <h4 class='mipl_wc_cf_group_title'>
                                    <?php esc_html_e("$suffix $group_title ($product_title)") ?>
                                </h4>  
                                <?php
                            }
                           
                        }
                        mipl_wc_cf_client_side_fields($custom_field, $field_group_id, $product_id, $qty);
                    }
                }
            }elseif ($group_specilization == 'false') {
                if(empty($hide_title) ){
                    ?>
                    <h4 class='mipl_wc_cf_group_title'>
                        <?php esc_html_e("$group_title") ?>
                    </h4>  
                    <?php
                }
                mipl_wc_cf_client_side_fields($custom_field, $field_group_id);
            }
        }
        ?>
        </div>
        <?php
       
    }
    
    
    //Client side custom field validation
    function validate_custom_fields(){
        // recaptcha validation
        $recaptcha_res = !empty($_POST['g-recaptcha-response']) ? mipl_wc_cf_verify_recaptcha($_POST['g-recaptcha-response']) : '';

        $cart_items = WC()->cart->get_cart();
        $validated_fields_groups = $this->get_valid_groups($cart_items);
        $error = array();
        $position = 1;

        foreach ($validated_fields_groups as $field_group_id => $fields_data) {

            $custom_field = !empty($fields_data['fields'])?$fields_data['fields']:'';
           
            $group_title = !empty($field_group_id)?get_the_title($field_group_id):'';
            $group_specilization = get_post_meta($field_group_id, '_mipl_wc_cf_field_group_specilization', true);
            
            $product_setting_field_repeat = get_post_meta($field_group_id, '_mipl_wc_cf_setting_field_repeat', true);


            if($group_specilization == "false"){

                if(!isset($this->mipl_wc_valid_field_groups_data[$field_group_id])){
                    $this->mipl_wc_valid_field_groups_data[$field_group_id]['group']['name'] = $group_title;
                    $this->mipl_wc_valid_field_groups_data[$field_group_id]['fields'] = $custom_field;
                }

                $validated_array = mipl_wc_cf_get_order_fields($custom_field,$field_group_id);
                $return_data = $this->mipl_validated_post_data($custom_field,$field_group_id,$validated_array,$_POST,"","");
                $post_data[] = $return_data['post_data'];
                $errors[] = $return_data['error'];

            }else{
             
                $product_data = array();
                $products = $fields_data['products'];
                foreach ($products as $key => $pro_data) {
                    $product_id = !empty($pro_data['id']) ? $pro_data['id'] : "" ;
                    $product_qty = !empty($pro_data['qty']) ? $pro_data['qty'] : "" ;
                    $product_title = !empty($product_id)?get_the_title($product_id):'';

                    $product_data[] = array('id'=>$product_id,'product_name'=>$product_title,'qty'=>$product_qty);

                    $validated_array = mipl_wc_cf_get_order_fields($custom_field,$field_group_id,$product_id,$product_qty);
                
                    if( empty($custom_field) ){ continue; }

                    $return_data = $this->mipl_validated_post_data($custom_field,$field_group_id,$validated_array,$_POST,$product_qty,$product_id);
                    $post_data[] = $return_data['post_data'];
                    $errors[] = $return_data['error'];
                }

                if(!isset($this->mipl_wc_valid_field_groups_data[$field_group_id])){
                    $this->mipl_wc_valid_field_groups_data[$field_group_id]['group']['name'] = $group_title;
                    $this->mipl_wc_valid_field_groups_data[$field_group_id]['fields'] = $custom_field;
                    $this->mipl_wc_valid_field_groups_data[$field_group_id]['product'] = $product_data;
                }
            }
            
        }
        
        $this->mipl_wc_custom_validated_data = $post_data;
        if($recaptcha_res === false || $_POST['g-recaptcha-response'] === ''){
            $errors[] = array("recaptcha_fail" => __("reCAPTCHA failed"));
        }
        foreach ($errors as $key => $error) {
            foreach($error as $field_group_id => $err_group_data){
                $group_title = $this->mipl_wc_valid_field_groups_data[$field_group_id]['group']['name'];
                if(empty($err_group_data)){
                    continue;
                }
                if(!is_array($err_group_data)){
                    wc_add_notice( $err_group_data, 'error' );
                    continue;
                }
                foreach($err_group_data as $error_field_name=>$error_msg){
                    $error_fld_array = explode('_', $error_field_name);

                    $number_with_suffix = '';
                    if( isset($error_fld_array[6]) ){
                        $qty = (int) $error_fld_array[6];
                        if($qty>0){
                            $number_with_suffix = mipl_wc_cf_english_ordinal_suffix($qty);
                        }

                    }
                    $product_title = !empty(get_the_title($error_fld_array[5])) ? "(".get_the_title($error_fld_array[5]).")" : '';
                    $errors_messages = $number_with_suffix." ".$group_title." ".$product_title. ": ".$error_msg;
                    if(empty($group_title) && empty($number_with_suffix)){
                        $errors_messages = $error_msg;
                    }
                    wc_add_notice($errors_messages , 'error');
                }
            }
        }
     
    }

    function mipl_validated_post_data($custom_field, $field_group_id, $validated_array, $post,  $product_qty = "", $product_id = ""){
        $validated_post_data = array();
        foreach($custom_field['field_name'] as $fld_key => $fld_value){
            if($product_qty >= 1){
                for ($i=1; $i <= $product_qty; $i++) { 
                    $post_data_name = "_mipl_wc_cf_{$field_group_id}_{$product_id}_{$i}_{$custom_field['field_name'][$fld_key]}";
                    if(is_array($post[$post_data_name])){
                        $validated_post_data[$post_data_name] = array_map('sanitize_text_field', $post[$post_data_name]);
                    }else{
                        $validated_post_data[$post_data_name] = sanitize_text_field($post[$post_data_name]);
                    }
                }
            }else{
                $post_data_name = "_mipl_wc_cf_{$field_group_id}_{$custom_field['field_name'][$fld_key]}";
                if(is_array($post[$post_data_name])){
                    $validated_post_data[$post_data_name] = array_map('sanitize_text_field', $post[$post_data_name]);
                }else{
                    $validated_post_data[$post_data_name] = sanitize_text_field($post[$post_data_name]);
                }
            }
        } 

        foreach ($validated_array as $key => $validating_array) {
            $validated_data = new MIPL_WC_CF_Input_Validation($validating_array, $validated_post_data);
            $validated_data->validate();
            if( empty($error[$field_group_id]) ){
                $error[$field_group_id] = array();
            }
            $error[$field_group_id] = array_merge($error[$field_group_id], $validated_data->get_errors());
            $post_data[] = $validated_data->get_valid_data();
        }
        $return_data['post_data'] =  $post_data;
        $return_data['error'] =  $error;
        return $return_data;

    }


    // Added or update custom field form client side 
    function update_order_meta( $order_id ) {
        
        if( $this->mipl_wc_custom_validated_data == null || $this->mipl_wc_valid_field_groups_data == null ){
            
            return false;

        }

        $order = wc_get_order($order_id);

        $all_cf_validated_data = $this->mipl_wc_custom_validated_data;
        $modified_data = array();
        foreach($all_cf_validated_data as $cf_validated_data){
            foreach($cf_validated_data as $position=>$post_data){
                foreach($post_data as $fld_name=>$fld_value){
                    $modified_data[$fld_name] = $fld_value;
                    $order->update_meta_data($fld_name, $fld_value);
                }
            }
        }

        $order_custom_fields = $this->mipl_wc_valid_field_groups_data;
        $order->update_meta_data('_mipl_wc_cf_order_field_group_data', $order_custom_fields);

        $order->save_meta_data();

    }
    
    
    // Display Checkout Fields in Email
    function display_fields_in_order_email($order, $sent_to_admin, $plain_text, $email){
        $order_id = $order->get_id();
        echo mipl_wc_get_order_custom_fields_data($order_id, $email->id);
        
    }
    
    
    // Collected all validated fields
    function get_valid_groups($cart_items, $position=''){

        $cart_product_info = array();
        $valid_field_groups = array();
        foreach( $cart_items as $cart_item ){
            
            $product_id = $cart_item['product_id'];
            $product_quantity = $cart_item['quantity'];
            $variation_id = isset($cart_item['variation_id'])?$cart_item['variation_id']:'';
            $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
 
            $cart_product_info[] = array(
                'id'=>$product_id,
                'category'=>$product_cats_ids,
                'quantity'=>$product_quantity,
                'variation_id'=>$variation_id
            );

        }
      
        $args_of_cf = array(
            'post_type'   => MIPL_WC_CF_POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1
        );


        if(trim($position) != ''){
          
            $args_of_cf['meta_key'] = '_mipl_wc_cf_setting_position';
            $args_of_cf['meta_value'] = $position;
        }

        $total_fields_data = get_posts($args_of_cf);
       
        if(empty($total_fields_data)){
            return array();
        }
        
        $shipping_zones = WC_Shipping_Zones::get_zones();
        foreach( $total_fields_data as $field_data ){

            $field_group_id = $field_data->ID;
            $field_group_title = $field_data->post_title;
            $setting_fields_position = get_post_meta($field_group_id, '_mipl_wc_cf_setting_position', true);
            $shipping_field_position = array(
                'woocommerce_before_checkout_shipping_form',
                'woocommerce_after_checkout_shipping_form'
            );
      
            if( in_array($setting_fields_position, $shipping_field_position) && empty($shipping_zones) ){
                continue;
            }
           
            $custom_field = get_post_meta($field_group_id, '_mipl_wc_cf_custom_field', true);
            $group_setting = get_post_meta($field_group_id, '_mipl_wc_cf_group_setting', true);
          
            if( (empty($custom_field)) || (!empty($group_setting['deactive_group']))){ 
                continue;
            }
          

            $group_specilization = get_post_meta( $field_group_id, '_mipl_wc_cf_field_group_specilization', true );
            $order_item_specilization = get_post_meta($field_group_id, '_mipl_wc_cf_order_item_specilization', true);

            if($group_specilization == "false"){
                $products_data[] = array();
                $valid_field_groups[$field_group_id]['group_id'] = $field_group_id;
                $valid_field_groups[$field_group_id]['fields'] = $custom_field;
                $valid_field_groups[$field_group_id]['position'] = $setting_fields_position;
                continue;
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
            foreach($cart_product_info as $cart_product_info_value){
                
                $product_id = $cart_product_info_value["id"];
                $term_id = $cart_product_info_value["category"];                
                $product_quantity = ($product_setting_field_repeat == "repeat")?$cart_product_info_value["quantity"]:1; 

                if($order_item_specilization == "all_product" && $group_specilization == "true"){
                  
                    if(!empty($cart_product_info_value['variation_id'])){
                        $product_id = $cart_product_info_value['variation_id'];
                    }
                    $products_data[] = array('id'=>$product_id,'qty'=>$product_quantity);
                }

                if(($group_specilization == "true" && $order_item_specilization == "particular_product") && 
                (count(array_intersect($term_id,$product_setting_category)) <= 0) && 
                (!in_array($product_id,$setting_products))){
                    continue;
                }
                                    
                if(!isset($valid_field_groups[$field_group_id]) && ($group_specilization == "true" && $order_item_specilization == 'particular_product')){
                    if(!empty($cart_product_info_value['variation_id'])){
                        $product_id = $cart_product_info_value['variation_id'];
                    }
                    $products_data[] = array('id'=>$product_id,'qty'=>$product_quantity);
                }
            }
                
            $valid_field_groups[$field_group_id]['group_id'] = $field_group_id;
            $valid_field_groups[$field_group_id]['fields'] = $custom_field;
            $valid_field_groups[$field_group_id]['position'] = $setting_fields_position;
            $valid_field_groups[$field_group_id]['products'] = $products_data;
            
        }
        return $valid_field_groups;

    }


    function before_billing_custom_fields(){
        $cart_items = WC()->cart->get_cart();
        $validated_fields_groups = $this->get_valid_groups($cart_items, 'woocommerce_before_checkout_billing_form');
        ?>
        <div class='mipl-wc-checkout-fields'>
        <?php

            $this->show_custom_fields_data($validated_fields_groups);
        
        ?>
        </div>
        <?php
    }

    function after_billing_custom_fields(){
        $cart_items = WC()->cart->get_cart();
        $validated_fields_groups = $this->get_valid_groups($cart_items, 'woocommerce_after_checkout_billing_form');
        ?>
        <div class='mipl-wc-checkout-fields'>
        <?php

            $this->show_custom_fields_data($validated_fields_groups);

        ?>
        </div>
        <?php
    }

    function before_shipping_custom_fields(){
        $cart_items = WC()->cart->get_cart();
        $validated_fields_groups = $this->get_valid_groups($cart_items, 'woocommerce_before_checkout_shipping_form');
        ?>
        <div class='mipl-wc-checkout-fields'>
        <?php

            $this->show_custom_fields_data($validated_fields_groups);

        ?>
        </div>
        <?php
    }

    function after_shipping_custom_fields(){
        $cart_items = WC()->cart->get_cart();
        $validated_fields_groups = $this->get_valid_groups($cart_items, 'woocommerce_after_checkout_shipping_form');
        ?>
        <div class='mipl-wc-checkout-fields'>
        <?php
       
            $this->show_custom_fields_data($validated_fields_groups);

        ?>
        </div>
        <?php
    }

    function show_custom_fields_data($validated_fields_groups){

        foreach ($validated_fields_groups as $grp_id=>$fields_data) {
            
            $field_group_id = $fields_data['group_id'];

            if(empty($fields_data)){
                continue;
            }
         
            $all_custom_fields = get_post_meta($field_group_id,'_mipl_wc_cf_custom_field',true);

            $group_specilization = get_post_meta($field_group_id, '_mipl_wc_cf_field_group_specilization', true);

            $field_group_setting = get_post_meta($field_group_id,'_mipl_wc_cf_group_setting',true);
            $hide_title = isset($field_group_setting['hide_title'])?$field_group_setting['hide_title']:'';

            $custom_field = !empty($fields_data['fields'])?$fields_data['fields']:'';
            $error_field_error = array('');
            if(isset($all_custom_fields['errors'])){
                foreach ($all_custom_fields['errors'] as $fld_key => $fld_value) {
                    $error_field_error[] = count($fld_value);
                }
            }
            if(isset($custom_field['field_label']) && max($error_field_error) == count($custom_field['field_label'])){
                continue;
            }
            
            if(!isset($custom_field) || empty($custom_field)){continue;}
            $group_title = get_the_title($field_group_id);
            
            if(!empty($fields_data['products'])){
                $products = $fields_data['products'];
                foreach ($products as $key => $pro_data) {
                    $product_id = $pro_data['id'];
                    $product_qty = $pro_data['qty'];
                    $product_title = get_the_title($product_id);
                    for($qty = 1; $qty <= $product_qty; $qty++){
                        $suffix = mipl_wc_cf_english_ordinal_suffix($qty);
                        
                        if(empty($hide_title)){
                            if($product_qty == 1){
                                ?>
                                    <h4 class='mipl_wc_cf_group_title'>
                                        <?php esc_html_e("$group_title ($product_title)") ?>
                                    </h4>
                                <?php
                            }else{
                                ?>
                                    <h4 class='mipl_wc_cf_group_title'>
                                        <?php esc_html_e("$suffix $group_title ($product_title)") ?>
                                    </h4>
                                <?php
                            }
                           
                        }
                        mipl_wc_cf_client_side_fields($custom_field, $field_group_id, $product_id, $qty);
                    }
                }
            }elseif ($group_specilization == 'false') {
                if(empty($hide_title) ){
                    ?>
                    <h4 class='mipl_wc_cf_group_title'>
                        <?php esc_html_e("$group_title") ?>
                    </h4>
                    <?php
                }
                mipl_wc_cf_client_side_fields($custom_field, $field_group_id);
            }
        }
    }

}

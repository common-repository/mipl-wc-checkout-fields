<?php
class MIPL_WC_Default_Checkout_Fields{

    // Updated WooCommerce default checkout fields.
    function update_default_fields($fields){
        
        $changed_default_values = get_option('_mipl_wc_ck_defualt_field');
        
        if(!empty($changed_default_values)){
            foreach($changed_default_values as $type => $field_data){
                foreach($field_data as $field_name=>$field){

                    if( isset($changed_default_values[$type][$field_name]['hidden']) && 
                        $changed_default_values[$type][$field_name]['hidden']=='true' ){
                        unset($changed_default_values[$type][$field_name]);
                        continue;
                    }
                    
                    if( isset($changed_default_values[$type][$field_name]['required']) && 
                        $changed_default_values[$type][$field_name]['required'] == "true"){
                        $changed_default_values[$type][$field_name]['required'] = (bool)true;
                    }else{
                        $changed_default_values[$type][$field_name]['required'] =  (bool)false;
                    }
                    
                }
            }
            
            $fields = $changed_default_values;
            
        }
        
        return $fields;
        
    }
    
    
    // Display and change the some value of default checkout fields.
    function checkout_default_fields(){
       
        $fields = WC()->checkout->get_checkout_fields();
        $changed_default_values = get_option('_mipl_wc_ck_defualt_field');
        if( !empty($changed_default_values) ){
            $fields = $changed_default_values;
        }

        ?>
        <div class="wrap mipl_wc_cf_ck_fields">
        <h2 class="wp-heading-inline"><?php esc_html_e('Checkout Default Fields')?></h2>
        <form action="" method="POST">
        <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="postbox-container-1" class="postbox-container">
            
                <div id="side-sortables" class="meta-box-sortables ui-sortable post_save_box">
                <div id="submitdiv" class="postbox ">
                    <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Save Changes') ?></h2>
                    </div>
                    <div class="inside">
                    <div class="submitbox" id="submitpost">
                    <div id="minor-publishing">
                    <div id="major-publishing-actions">
                        <div id="publishing-action">
                            <div class="mipl_wc_cf_default_reset_fields">
                                <button type="submit" name="mipl_action" id="publish" class="button button-primary button-large" value="save_wc_default_fields"><?php esc_html_e('Save changes') ?></button>
                                <button type="button" name="mipl_action" class="button button-primary button-large mipl_wc_reset_default_fields" value="reset_wc_default_fields"><?php esc_html_e('Reset') ?></button>
                            </div>
                        </div>
                        <div class="clear">
                        </div>
                    </div>
                    </div>
                    </div>
                    </div>
                </div>
                </div>
            
            </div> 

            <div id="postbox-container-2" class="postbox-container">
            <div id="submitdiv" class="postbox">
                
                <div class="postbox-header">
                    <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Default Fields') ?></h2>
                </div>
            
                <div class="mipl_wc_cf_default_checkout_fields">
                <div id="mipl_wc_cf_default_checkout_fields_wrapper" class="mipl_wc_cf_default_checkout_fields_wrapper">
                    
                    <ul class="mipl_wc_default_checkout_fields_tabs">
                        <li><a href="#billing-fields"><?php esc_html_e('Billing Fields') ?></a></li>
                        <li><a href="#shipping-fields"><?php esc_html_e('Shipping Fields') ?></a></li>
                        <li><a href="#order-fields"><?php esc_html_e('Order Fields') ?></a></li>
                    </ul>
                    <?php
                    $default_field = array(
                        'billing-fields'=>'billing',
                        'shipping-fields'=>'shipping',
                        'order-fields'=>'order'
                    );
                    foreach($default_field as $tab_id=>$default_type){
                    ?>
                    <div id="<?php echo esc_attr($tab_id) ?>" class="mipl_wc_default_cf_tab_content">
                    <div class="mipl_wc_cf_checkout_default_billing_field_table">
                        <div class="mipl_wc_cf_checkout_default_billing_field_table_row mipl_cf_ck_default_fields">
                            <div class="checkout_default_billing_field_table_data">
                            <div class="index_div"><strong style="margin-left:15px">#</strong></div>
                            <div class="default_field_table_column"><strong><?php esc_html_e('Name') ?></strong></div>
                            <div class="default_field_table_column"><strong><?php esc_html_e('Label') ?></strong></div>
                            <div class="default_field_table_column"><strong><?php esc_html_e('Placeholder') ?></strong></div>
                            <div class="default_field_table_column"><strong><?php esc_html_e('Required Field') ?></strong></div>
                            <div class="default_field_table_column"><strong><?php esc_html_e('Hide Field') ?></strong></div>
                            <div class="default_field_table_column btn"><strong><?php esc_html_e('Action') ?></strong></div>
                            </div>
                        </div>
                        <ul class="sortable">
                        <?php
                        $default_billing_fields_name = array(
                        'billing_first_name','billing_last_name','billing_company','billing_country','billing_address_1','billing_address_2','billing_city','billing_state','billing_postcode','billing_phone','billing_email',
                        'shipping_first_name','shipping_last_name','shipping_company','shipping_country','shipping_address_1','shipping_address_2','shipping_city','shipping_state','shipping_postcode',
                        'order_comments'
                        );
                        $number = 1;
                        foreach($fields[$default_type] as $field_names => $field_attr){

                            if(!in_array($field_names,$default_billing_fields_name)){
                                continue;
                            }

                            $required_checked = "";
                            $re_val = "false";
                            if(isset($field_attr['required']) && $field_attr['required'] == "true"){
                                $required_checked = "checked";
                                $re_val           = "true";
                            }


                            $hidden_field_checked = "";
                            $hidden_field_val     = "false";

                            if(isset($field_attr['hidden']) && $field_attr['hidden'] == "true"){
                                $hidden_field_checked = "checked";
                                $hidden_field_val     = "true";
                            }


                            if(isset($field_attr['class']) && is_array($field_attr['class'])){
                                $field_class = implode(" ",$field_attr['class']);
                            }else{
                                $field_class = isset($field_attr['class'])?$field_attr['class']:"";
                            }
                            $field_name        = !empty($field_names)?$field_names:"";
                            $field_label       = !empty($field_attr['label'])?$field_attr['label']:"";
                            $field_placeholder = !empty($field_attr['placeholder'])?$field_attr['placeholder']:"";
                            $priority_field    = !empty($field_attr['priority'])?$field_attr['priority']:"";
                            $default_field     = !empty($field_attr['default'])?$field_attr['default']:"";
                            $changed_fld       = !empty($field_attr['changed_field'])?$field_attr['changed_field']:"0";

                            ?>
                            <li class="ui-state-default handle">
                            <div class="mipl_wc_cf_checkout_default_billing_field_table_row">
                                <div class="checkout_default_billing_field_table_data <?php if($changed_fld==1){echo esc_attr("mipl_wc_cf_changed_field");} ?>">
                                    <div class="index_div" ><span class="index mipl_handle"><?php echo esc_html($number) ?></span></div>
                                    <div class="default_field_table_column"><?php echo esc_html($field_name) ?></div>
                                    <div class="default_field_table_column"><?php echo esc_html($field_label) ?></div>
                                    <div class="default_field_table_column"><?php echo esc_html($field_placeholder) ?></div>
                                    <div class="default_field_table_column"><?php if($re_val=="true"){echo esc_html("Required");}else{echo ("-");} ?></div>
                                    <div class="default_field_table_column"><?php if($hidden_field_val=="true"){echo esc_html("Hide");}else{echo ("-");} ?></div>
                                    <div class="default_field_table_column btn">
                                        <a href="#" class="edit_default_field button"><?php esc_html_e('Edit') ?></a>
                                    </div>
                                </div>
                                <div class="mipl_wc_default_field_field_edit_form" style="display:none">
                                <div class="changed_field">
                                    <input type="hidden" class="mipl_changed_input" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][changed_field]') ?>" value="<?php echo esc_attr($changed_fld) ?>">
                                </div>
                                <div class="form_input">
                                    <input type="hidden" class="default_field_priority" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][priority]') ?>" value="<?php echo esc_attr($priority_field) ?>">
                                </div>
                                <div class="form_input">
                                    <input type="hidden" class="default_field_name" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][name]') ?>" value="<?php echo esc_attr($field_name) ?>">
                                </div>
                                <div class="form_input">
                                    <label><?php esc_html_e('Label') ?><br><input type="text" class="default_field_label" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][label]') ?>" value="<?php echo esc_attr($field_label) ?>"></label>
                                </div>
                                <div class="form_input">

                                    <label><?php esc_html_e('Class') ?><br><input type="text" class="default_field_class" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][class]') ?>" value="<?php echo esc_attr($field_class) ?>"></label><br>
                                    <span><?php esc_html_e('[Note : Class name separate by space]') ?></span>
                                </div>
                                <?php
                                if($default_type=='order'){
                                    ?>
                                    <div class="form_input">
                                        <label><?php esc_html_e('Placeholder') ?><br><textarea  class="default_field_placeholder" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][placeholder]') ?>" ><?php echo esc_html($field_placeholder) ?></textarea></label>
                                    </div>
                                    <div class="form_input">
                                        <label><?php esc_html_e('Default value') ?><br><textarea class="default_field_value" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][default]') ?>" ><?php echo esc_html($default_field) ?></textarea></label>
                                    </div>
                                    <?php
                                }else{
                                    ?>
                                    <div class="form_input">
                                        <label><?php esc_html_e('Placeholder') ?><br><input type="text" class="default_field_placeholder" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][placeholder]') ?>" value="<?php echo esc_attr($field_placeholder) ?>"></label>
                                    </div>
                                    <div class="form_input">
                                        <label><?php esc_html_e('Default value') ?><br><input type="text" class="default_field_value" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][default]') ?>" value="<?php echo esc_attr($default_field) ?>"></label>
                                    </div>
                                    <?php
                                }?>

                                <div class="form_input">
                                    <label>
                                        <input type="checkbox" class="mipl_wc_ck_required_field" <?php echo esc_attr($required_checked) ?>>
                                        <input type="hidden" class="hidden_required_field" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][required]') ?>" value="<?php echo esc_attr($re_val) ?>">
                                        <?php esc_html_e('Required Field') ?>
                                    </label>
                                </div>
                                <div class="form_input">
                                    <label>
                                        <input type="checkbox" class="mipl_wc_ck_hiddden_field" <?php echo esc_attr($hidden_field_checked) ?>>
                                        <input type="hidden" class="hide_field" name="<?php echo esc_attr('mipl_wc_default_field['.$default_type.']['.$field_names.'][hidden]') ?>" value="<?php echo esc_attr($hidden_field_val) ?>">
                                        <?php esc_html_e('Hide Field') ?>

                                    </label>
                                </div>
                                <div class="mipl_wc_cf_default_field_button">
                                    <a class="mipl_wc_cf_close_default_field_button button"> <?php esc_html_e('Close') ?></a>
                                </div>
                                </div>
                            </div>
                            </li>
                            <?php
                            $number++;
                        }?>
                        </ul>
                        
                    </div>
                    </div>
                    <?php
                    }?>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        </form>
        </div>
    <?php
    }

    
    // Updated default checkout fields.
    function save_default_field_value(){

        if( !isset($_POST['mipl_wc_default_field']) || empty($_POST['mipl_wc_default_field']) ){
            return false;
        }
        
        $df_validated_data = array();
        if(isset($_POST['mipl_wc_default_field'])){
            $default_field = $this->validate_default_fields($_POST['mipl_wc_default_field']);
            $detail_array = mipl_wc_checkout_default_field_valiadtion_array();
            foreach($default_field as $df_key=>$df_data){
                foreach($df_data as $field_name=>$field_data){
                    $val_obj         = new MIPL_WC_CF_Input_Validation($detail_array,$field_data);
                    $rs              = $val_obj->validate();
                    $errors[]        = implode("",$val_obj->get_errors());
                    $post_data       = $val_obj->get_valid_data();
                    $df_validated_data[$df_key][$field_name] = $post_data;
                }

            }
            
            $_SESSION['mipl_cf_admin_notices']['error']=implode("",$errors) ;

            if(!empty($default_field)){
                update_option('_mipl_wc_ck_defualt_field',$default_field,true);
            }

        }
        if(isset($_POST['mipl_action']) && $_POST['mipl_action']=='mipl_reset_default_fields'){
            update_option('_mipl_wc_ck_defualt_field',"");

        }

    }

    
    function reset_default_field_value(){
        
        update_option('_mipl_wc_ck_defualt_field','');
        $resp=array('status'=>'success', 'message'=>__('successfully revoked'));
        echo json_encode($resp);
        die();
        
    }

    
    function validate_default_fields($data){
        
        $default_data = array();
        
        foreach ($data as $type => $fields) {
            foreach ($fields as $field_name => $fields_value) {
                foreach ($fields_value as $field => $value) {
                    if(is_string($value)){
                        $valide_data=sanitize_text_field($value);
                    }else{
                        foreach($value as $key1=>$value1){
                            if( !is_array($value1) ){
                                $value[$key1] = sanitize_text_field($value1);
                            }
                        }
                        $valide_data = $value;
                    }
                    $default_data[$type][$field_name][$field]= $valide_data;
                }
            }
        }
        
        return $default_data;
        
    }
    
      
}

<div class="wrap">
    <h1 class="wp-heading-inline"></h2>
    <hr class="wp-header-end">
    <form name="post" action="" method="post" id="post">
    <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables ui-sortable">
            <div id="submitdiv" class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Save Setting') ?></h2>
            </div>
            <div class="inside">
            <div class="submitbox" id="submitpost">
                    
                    <div id="minor-publishing">
                    <div id="major-publishing-actions">
                        <div id="publishing-action">
                            
                            <div class="mipl_wc_save_date_setting">
                                <button type="submit" name="mipl_action" id="publish" class="button button-primary button-large" value="save_wc_cf_setting"><?php esc_html_e('Save Setting') ?></button>
                              
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
            <div id="mipl_wc_checkout_details" class="postbox">
                <div class="postbox-header">
                    <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Settings') ?></h2>
                </div>
                <div class="inside mipl_cf_verification_wrapper">
                <div>
                <div id="mipl_wc_crm_tabs_wrapper" class="mipl_wc_crm_tabs_wrapper">
                    <ul class="mipl_wc_setting_tabs">
                        <li class="">
                            <a href="#recaptcha"><?php esc_html_e('reCAPTCHA') ?></a>
                        </li>
                    </ul>

                    <div id="recaptcha" class="mipl_wc_recaptcha_parent mipl_wc_tab_content">
                        <?php
                            
                            $enable_checkbox = get_option('_mipl_wc_recaptcha_enable_checkbox');
                            $recaptcha_type = get_option('_mipl_wc_recaptcha_recaptcha_type');


                            $v2_site_key = get_option('_mipl_wc_recaptcha_v2_site_key');
                            $v2_secret_key = get_option('_mipl_wc_recaptcha_v2_secret_key');
                            $v3_site_key = get_option('_mipl_wc_recaptcha_v3_site_key');
                            $v3_secret_key = get_option('_mipl_wc_recaptcha_v3_secret_key');

                        ?>
                        <div class="mipl_wc_recaptcha_info">
                            <div class="update-message notice inline notice-warning notice-alt form_input mipl_recaptcha_position_note"><span><?php esc_html_e('For reCAPTCHA v2, add recaptcha field in field groups.') ?></span></div>
                        </div>

                        <div class="mipl_wc_enable_checkbox">
                            <label>
                                <?php
                                $checked = ""; 
                                if($enable_checkbox == 'true'){
                                    $checked = "checked";
                                }
                                ?>
                            <input type="checkbox" class="" value="true" name="_mipl_wc_recaptcha_enable_checkbox" <?php echo esc_attr($checked) ?>>
                            <b><?php esc_html_e('Enable reCAPTCHA') ?></b>
                            </label>
                        </div>

                        <div class="mipl_wc_recaptcha_field">
                            <label><strong><?php esc_html_e('Select reCAPTCHA type:') ?></strong></label><br>
                            <select name="_mipl_wc_recaptcha_recaptcha_type" class="mipl_wc_recaptcha_type">
                                <?php
                                    $type = array('reCAPTCHA_v2'=>'reCAPTCHA v2','reCAPTCHA_v3'=>'reCAPTCHA v3');
                                    foreach ($type as $option_val => $option_label) {
                                        $selected = '';
                                        if($option_val == $recaptcha_type){
                                            $selected = 'selected';
                                        }
                                        ?>
                                        <option value="<?php echo esc_attr($option_val) ?>" <?php echo esc_attr($selected) ?>><?php esc_html_e($option_label) ?></option>
                                        <?php
                                    } 
                                ?>
                            </select>
                            
                        </div>
                        <div class="mipl_wc_recaptcha_v2">
                            <?php
                            
                            ?>
                            <div class="mipl_wc_recaptcha_field">
                                <label><strong> <?php esc_html_e('Site key:') ?></strong></label><br>
                                <?php
                                if(empty($v2_site_key)){
                                    ?>
                                        <input type="text" name="_mipl_wc_recaptcha_v2_site_key" class="mipl_wc_site_key mipl_recaptcha" value="<?php echo esc_attr($v2_site_key) ?>">
                                    <?php
                                }else{
                                    $len = strlen($v2_site_key);
                                    $new_v2_site_key = substr($v2_site_key, 0, 4).str_repeat('*', $len - 8).substr($v2_site_key, $len - 4);
                                    ?>
                                        <input type="text" class="mipl_wc_site_key mipl_recaptcha" value="<?php echo esc_attr($new_v2_site_key) ?>" disabled>
                                        
                                    <?php
                                }
                                ?>
                                
                            </div>
                            <div class="mipl_wc_recaptcha_field">
                                <label><strong><?php esc_html_e('Secret key:') ?></strong></label><br>
                                <?php
                                if(empty($v2_secret_key)){
                                    ?>
                                        <input type="text" name="_mipl_wc_recaptcha_v2_secret_key" class="mipl_wc_secret_key mipl_recaptcha" value="<?php echo esc_attr($v2_secret_key) ?>">
                                    <?php
                                }else{
                                    $len = strlen($v2_secret_key);
                                    $new_v2_secret_key = substr($v2_secret_key, 0, 4).str_repeat('*', $len - 8).substr($v2_secret_key, $len - 4);
                                    ?>
                                        <input type="text" class="mipl_wc_secret_key mipl_recaptcha" value="<?php echo esc_attr($new_v2_secret_key) ?>" disabled>
                                        
                                    <?php
                                }
                                ?>
                               
                            </div>
                            <div class="mipl_wc_save_date_setting">
                            <?php
                                if(!empty($v2_site_key) && !empty($v2_secret_key)){
                                    ?>
                                    <button type="button" name="miplaction" class="button button-primary button-large mipl_wc_reset_setting_fields" value="reset_wc_setting_fields"><?php esc_html_e('Reset') ?></button>
                                    <?php
                                }else{
                                    ?>
                                    <button type="submit" name="mipl_action" class="button button-primary button-large mipl_wc_save_setting" value="save_wc_cf_setting"><?php esc_html_e('Save Setting') ?></button>
                                    <?php
                                }
                            ?>
                            </div>
                                
                            
                        </div>
                        <div class="mipl_wc_recaptcha_v3">
                            <div class="mipl_wc_recaptcha_field">
                                <label><strong><?php esc_html_e('Site key:') ?></strong></label><br>
                                <?php
                                if(empty($v3_site_key)){
                                    ?>
                                        <input type="text" name="_mipl_wc_recaptcha_v3_site_key" class="mipl_wc_site_key mipl_recaptcha" value="<?php echo esc_attr($v3_site_key) ?>">
                                    <?php
                                }else{
                                    $len = strlen($v3_site_key);
                                    $new_v3_site_key = substr($v3_site_key, 0, 4).str_repeat('*', $len - 8).substr($v3_site_key, $len - 4);
                                    ?>
                                        <input type="text" class="mipl_wc_site_key mipl_recaptcha" value="<?php echo esc_attr($new_v3_site_key) ?>" disabled>
                                       
                                    <?php
                                }
                                ?>
                               
                            </div>
                            <div class="mipl_wc_recaptcha_field">
                                <label><strong><?php esc_html_e('Secret key:') ?></strong></label><br>
                                <?php
                                if(empty($v3_secret_key)){
                                    ?>
                                        <input type="text" name="_mipl_wc_recaptcha_v3_secret_key" class="mipl_wc_secret_key mipl_recaptcha" value="<?php echo esc_attr($v3_secret_key) ?>">
                                    <?php
                                }else{
                                    $len = strlen($v3_secret_key);
                                    $new_v3_secret_key = substr($v3_secret_key, 0, 4).str_repeat('*', $len - 8).substr($v3_secret_key, $len - 4);
                                    ?>
                                        <input type="text" class="mipl_wc_secret_key mipl_recaptcha" value="<?php echo esc_attr($new_v3_secret_key) ?>" disabled>
                                        
                                    <?php
                                }
                                ?>
                                
                            </div>

                            <div class="mipl_wc_save_date_setting">
                            <?php
                                if(!empty($v3_site_key) && !empty($v3_secret_key)){
                                    ?>
                                    <button type="button" name="miplaction" class="button button-primary button-large mipl_wc_reset_setting_fields" value="reset_wc_setting_fields"><?php esc_html_e('Reset') ?></button>
                                    <?php
                                }else{
                                    ?>
                                    <button type="submit" name="mipl_action" class="button button-primary button-large mipl_wc_save_setting" value="save_wc_cf_setting"><?php esc_html_e('Save Setting') ?></button>
                                    <?php
                                }
                            ?>
                            </div>

                        </div>
                       
                        
                    </div>

                </div> 
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </form>
</div>


<script>
jQuery(document).ready(function(){
var $active_tab = localStorage.getItem('mipl_wc_setting_last_active_tab');

if( $active_tab == null ){
    $active_tab = '#recaptcha';
}
mipl_wc_change_tab($active_tab);

jQuery('.mipl_wc_setting_tabs li a').click(function(){
    var $tab = jQuery(this).attr('href');
    return mipl_wc_change_tab($tab);
});

function mipl_wc_change_tab($tab){

    $tab = $tab.replaceAll('#',''); 

    if( jQuery('#'+$tab).length <= 0 ){ return false; }

    jQuery('.mipl_wc_setting_tabs li').removeClass('mipl_wc_active_tab');
    jQuery('.mipl_wc_setting_tabs li a[href=#'+$tab+']').parent('li').addClass('mipl_wc_active_tab');

    jQuery('.mipl_wc_crm_tabs_wrapper .mipl_wc_tab_content').hide(0);
    jQuery('.mipl_wc_crm_tabs_wrapper .mipl_wc_tab_content#'+$tab).show(0);

    localStorage.setItem('mipl_wc_setting_last_active_tab',$tab);

    return false;

}

});
</script>

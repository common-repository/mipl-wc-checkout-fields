<?php
$custom_field = get_post_meta($post->ID, '_mipl_wc_cf_custom_field', true);
$v2_site_key = get_option('_mipl_wc_recaptcha_v2_site_key');
$v2_secret_key = get_option('_mipl_wc_recaptcha_v2_secret_key');

$file_type = array(
    "Video" => array('video/mp4', 'video/mpeg', 'video/ogg', 'video/mp2t', 'video/webm'),
    "Audio" => array('audio/aac', 'audio/midi', 'audio/x-midi', 'audio/mpeg', 'audio/ogg', 'audio/opus', 'audio/webm
    ', 'audio/wav'),
    "Image" => array('image/jpeg', 'image/gif', 'image/png'),
    "Application" => array('application/pdf', 'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
    "Text" => array('text/plain')
);
?>
<div class="mipl_wc_cf_custom_field_table mipl_wc_cf_ck_fields">
    <?php
    if(is_string($custom_field)){
        ?>
        <div class="mipl_wc_cf_custom_field_table_row">
            <div class="custom_field_table_data">
                <div class="mipl_wc_cf_custom_field_table_column sort_icon"></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Label') ?></strong></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Name') ?></strong></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Type') ?></strong></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Default') ?></strong></div>
                <div class="mipl_wc_cf_custom_field_table_column btn"><strong><?php esc_html_e('Action') ?></strong></div>
            </div>
        </div>
        <div class="sortable mipl_add_custom_fields"></div>
        <?php
    }else{
    ?>   
        <div class="mipl_wc_cf_custom_field_table_row">
            <div class="custom_field_table_data">
                <div class="mipl_wc_cf_custom_field_table_column sort_icon"></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Label') ?> </strong></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Name') ?> </strong></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Type') ?> </strong></div>
                <div class="mipl_wc_cf_custom_field_table_column"><strong><?php esc_html_e('Default') ?> </strong></div>
                <div class="mipl_wc_cf_custom_field_table_column btn"><strong><?php esc_html_e('Action') ?></strong></div>
            </div>
        </div>
        <div class="sortable mipl_add_custom_fields">

            <?php
            if(isset($custom_field) && !empty($custom_field['field_label'])){
            foreach($custom_field['field_label'] as $key => $value){

                $field_label = !empty($custom_field['field_label'][$key])?$custom_field['field_label'][$key]:"";
               
                $field_name = isset($custom_field['field_name'][$key])?$custom_field['field_name'][$key]:"";
               
                $field_type = isset($custom_field['field_type'][$key])?$custom_field['field_type'][$key]:"";
               
                $default_value = isset($custom_field['default_value'][$key])?$custom_field['default_value'][$key]:"";
               
                $option_value =   isset($custom_field['option_value'][$key])?$custom_field['option_value'][$key]:"";

                $file_size =   isset($custom_field['file_size'][$key])?$custom_field['file_size'][$key]:"";

                $file_types =   isset($custom_field['file_type'][$key])?$custom_field['file_type'][$key]:"";
                
               
                //for outer error mark
                $error_mark = "";   
                
                //for error message
                $label_field_error_msg = isset($custom_field['errors']['field_label'][$key])?$custom_field['errors']['field_label'][$key]:"";

                $name_field_error_msg = isset($custom_field['errors']['field_name'][$key])?$custom_field['errors']['field_name'][$key]:"";

                $type_field_error_msg = isset($custom_field['errors']['field_type'][$key])?$custom_field['errors']['field_type'][$key]:"";


                $default_value_error_msg = isset($custom_field['errors']['default_value'][$key])?$custom_field['errors']['default_value'][$key]:"";

                $option_field_error_msg = isset($custom_field['errors']['option_value'][$key])?$custom_field['errors']['option_value'][$key]:"";

                $required_field_error_msg = isset($custom_field['errors']['required_field'][$key])?$custom_field['errors']['required_field'][$key]:"";

                $placeholder_checkbox_error_msg = isset($custom_field['errors']['placeholder_checkbox'][$key])?$custom_field['errors']['placeholder_checkbox'][$key]:"";

                $file_size_error_msg = isset($custom_field['errors']['file_size'][$key])?$custom_field['errors']['file_size'][$key]:"";

                $file_type_error_msg = isset($custom_field['errors']['file_type'][$key])?$custom_field['errors']['file_type'][$key]:"";

                //for inner error fields
                $label_error = $name_error = $type_error = $default_value_error = $option_error = $file_type_error = $file_size_error = "";
                if(isset($custom_field['errors']['field_label']) && in_array($key, array_keys($custom_field['errors']['field_label']))){
                    $label_error = "field_label_error";
                    $error_mark = "mipl_custom_field_error";
                }
                
                if(isset($custom_field['errors']['field_name']) && in_array($key, array_keys($custom_field['errors']['field_name']))){
                    $name_error = "field_name_error";
                    $error_mark = "mipl_custom_field_error";
                }

                if(isset($custom_field['errors']['field_type']) && in_array($key, array_keys($custom_field['errors']['field_type']))){
                    $type_error = "field_type_error";
                    $error_mark = "mipl_custom_field_error";
                }

                if(isset($custom_field['errors']['option_value']) && in_array($key, array_keys($custom_field['errors']['option_value']))){
                    $option_error = "field_option_error";
                    $error_mark = "mipl_custom_field_error";
                }

                if(isset($custom_field['errors']['default_value']) && in_array($key, array_keys($custom_field['errors']['default_value']))){
                    $default_value_error = "field_option_error";
                    $error_mark = "mipl_custom_field_error";
                }

                if(isset($custom_field['errors']['required_field']) && in_array($key, array_keys($custom_field['errors']['required_field']))){
                    $error_mark = "mipl_custom_field_error";
                }

                if(isset($custom_field['errors']['placeholder_checkbox']) && in_array($key, array_keys($custom_field['errors']['placeholder_checkbox']))){
                    $error_mark = "mipl_custom_field_error";
                }

                if(isset($custom_field['errors']['file_size']) && in_array($key, array_keys($custom_field['errors']['file_size']))){
                    $file_size_error = "field_option_error";
                    $error_mark = "mipl_custom_field_error";
                }

                if(isset($custom_field['errors']['file_type']) && in_array($key, array_keys($custom_field['errors']['file_type']))){
                    $file_type_error = "field_option_error";
                    $error_mark = "mipl_custom_field_error";
                }

            ?>
            <div class="mipl_wc_cf_custom_field_table_row <?php echo esc_attr($error_mark) ?>">
                <div class="custom_field_table_data">
                    <div class="mipl_wc_cf_custom_field_table_column sort_icon">
                        <span class="handle mipl_handle">:::</span>
                    </div>
                    <div class="mipl_wc_cf_custom_field_table_column">
                        <span class="tr_field_label"><?php if(empty($field_label)){echo esc_html("(no label)");}else{echo esc_html($field_label);} ?></span>
                    </div>
                    <div class="mipl_wc_cf_custom_field_table_column">
                        <span class="tr_field_name"><?php esc_html_e($field_name) ?></span>
                        
                    </div>
                    <div class="mipl_wc_cf_custom_field_table_column">
                        <span class="tr_field_type"><?php esc_html_e(ucfirst($field_type)) ?></span>
                        
                    </div>
                    <div class="mipl_wc_cf_custom_field_table_column">
                        <span class="tr_default_value"><?php esc_html_e($default_value) ?></span>
                    </div>
                    <div class="mipl_wc_cf_custom_field_table_column btn">
                        <a href="#" class="mipl_wc_edit_field"><?php esc_html_e('Edit') ?></a>
                        <a href="#" class="mipl_wc_remove_field"><?php esc_html_e('Remove') ?></a>
                    </div>
                </div>
                <div class="mipl_wc_cf_custom_field_edit_form mipl_custom_fields_form" style="display:none">
                    <div class="update-message notice inline notice-warning notice-alt form_input mipl_recaptcha_position_note" style="display:none;margin:10px">
                    </div>
                    <div class="form_input mipl_cf_required">
                        <label><?php esc_html_e('Field Label') ?></label><br>
                        <?php
                            $fields_error = '';
                            if(empty($field_label)){
                                $fields_error = "mipl_fields_error";
                            }
                        ?>
                        <input type="text" class="custom_field_label <?php echo esc_attr($label_error) ?>" name="mipl_wc_custom_field[field_label][]" value="<?php echo esc_attr($field_label) ?>"><br>
                        <span class="mipl_cf_error_msg" ><?php esc_html_e($label_field_error_msg) ?></span>
                        
                    </div>
                    <div class="form_input mipl_cf_required">
                        <label><?php esc_html_e('Field Name') ?></label>
                        <br><input type="text" class="mipl_wc_ck_custom_field_name <?php echo esc_attr($name_error) ?>" name="mipl_wc_custom_field[field_name][]" value="<?php echo esc_attr($field_name) ?>"><br>
                        <span class="mipl_cf_error_msg"><?php echo esc_html($name_field_error_msg) ?></span>
                    </div>
                    <div class="form_input mipl_cf_required">
                        <label><?php esc_html_e('Field Type') ?></label><br>
                        <select name="mipl_wc_custom_field[field_type][]" class="mipl_wc_ck_custom_field_type <?php echo esc_attr($type_error) ?>" id="row-selector1" >
                            <?php 
                            $field_options = array('Text'=>'text','Number'=>'number','Select'=>'select','Textarea'=>'textarea','Color'=>'color','Date'=>'date','Time'=>'time','Datetime'=>'datetime-local','Email'=>'email','Telephone'=>'tel','Checkbox (yes/no)'=>'checkbox','Checkbox (multiple checkbox)'=>'multicheckbox','Radio'=>'radio', 'File'=>'file', 'reCAPTCHA'=>'recaptcha');
                            foreach($field_options as $option_label=>$option_val){
                                $select = '';
                                $options_value = $option_val;
                                
                                if($field_type == $option_val){
                                    $select = 'selected';
                                }
                                
                                ?>
                                <option value = "<?php echo esc_attr(strtolower($option_val)) ?>" <?php  echo esc_attr($select) ?> ><?php esc_html_e(ucfirst($option_label)) ?></option>
                            <?php } ?>
                        </select><br>
                        <span class="mipl_cf_error_msg"><?php echo esc_html($type_field_error_msg) ?></span>
                    </div>

                    <div class="form_input mipl_wc_recaptcha_note" style="display:none">
                        <span class="mipl_recaptcha_note">
                            <b><?php esc_html_e('NOTE:Please save reCAPTCHA setting (Checkout Fields -> Setting)') ?></b>
                        </span>
                    </div>
                    
                    <?php
                        if(($field_type == "select" || $field_type == "radio" || $field_type == "multicheckbox"))
                        {
                            ?>
                            <div id='option_value' class="form_input mipl_cf_required" >
                                <label for=""><?php esc_html_e('Option value') ?> </label><br>
                                <textarea class="mipl_option_data <?php echo esc_attr($option_error) ?>" name="mipl_wc_custom_field[option_value][]"><?php echo esc_html($option_value)  ?></textarea>
                                <br>
                                <span class="mipl_cf_error_msg">
                                <?php echo esc_html($option_field_error_msg) ?>
                                </span>
                                <br><span><?php esc_html_e('Note: Enter each choice on a new line.For more control, you may specify both a value and label like red:Red') ?> </span>
                            </div>
                            <?php
                        }else{
                            ?>
                            <div style="display:none;" id='option_value' class="form_input mipl_cf_required">
                                <label for=""><?php esc_html_e('Option value') ?></label><br>
                                <textarea class="mipl_option_data <?php echo esc_attr($option_error) ?>" name="mipl_wc_custom_field[option_value][]"></textarea>
                                <br><span><?php esc_html_e('Enter each choice on a new line.For more control, you may specify both a value and label like red:Red') ?></span>
                            </div>  
                            <?php
                        }
                    ?>

                    <div id="default" class="form_input mipl_cf_textarea_default_class" style="display:none">
                        <label for=""><?php esc_html_e('Default value') ?></label><br>
                        <textarea class="mipl_cf_textarea_default_value <?php echo esc_attr($default_value_error) ?>" name=""><?php echo esc_attr($default_value) ?></textarea>
                        <span class="mipl_cf_error_msg"><?php echo esc_html($default_value_error_msg) ?></span>
                    </div>
                    
                    
                    <div id="default" class="form_input mipl_cf_default_class">
                        <label for=""><?php esc_html_e('Default value') ?></label><br>
                        <input type="text" name="mipl_wc_custom_field[default_value][]" class="mipl_custom_field_default_value edit_custom_field_default_value <?php echo esc_attr($default_value_error) ?>" value="<?php echo esc_attr($default_value) ?>" style="width:100%;">
                        <span class="mipl_cf_error_msg"><?php echo esc_html($default_value_error_msg) ?></span>
                    </div>

                    <div class="form_input mipl_cf_file_size_class mipl_cf_required">
                        <label><?php esc_html_e('Max File size (MB)') ?></label><br>
                        <input type="number" class="file_size <?php echo esc_attr($file_size_error) ?>" name="mipl_wc_custom_field[file_size][]" value="<?php echo esc_attr($file_size) ?>" min="1">
                        <span class="mipl_cf_error_msg"><?php echo esc_html($file_size_error_msg) ?></span>
                    </div>

                    <div class="form_input mipl_cf_file_type_class mipl_cf_required">
                        <label><?php esc_html_e('File type') ?></label><br>
                        <select name="mipl_wc_custom_field[file_type][<?php echo esc_attr($key) ?>][]" class="file_type mipl_wc_ck_select2 <?php echo esc_attr($file_type_error) ?>" multiple data-index="<?php echo esc_attr($key) ?>">
                            <?php
                            foreach($file_type as $label=>$types){
                            
                            ?>
                            <optgroup label="<?php echo esc_attr($label) ?>">
                                <?php
                                foreach($types as $type){
                                    $selected = "";
                                    if(!empty($file_types) && in_array($type, $file_types)){
                                        $selected = "selected";
                                    }
                                ?>
                                <option value="<?php echo esc_attr($type) ?>" <?php echo esc_attr($selected) ?>><?php echo esc_html($type) ?></option>
                                <?php
                                }
                                ?>
                            
                            </optgroup>
                            <?php
                                
                            }
                            ?>
                        </select>
                        <span class="mipl_cf_error_msg"><?php echo esc_html($file_type_error_msg) ?></span>
                        
                    </div>
                    
                    
                    <div class="form_input mipl_placeholder_checkbox">
                        <?php
                        $checked = "" ;       
                        $placeholder_checkbox = "no" ;              
                        if(isset($custom_field['placeholder_checkbox'][$key]) && $custom_field['placeholder_checkbox'][$key] == "yes")
                        {
                        $checked = "checked";
                        $placeholder_checkbox = "yes";
                        }
                        
                        ?>
                        
                        <label>
                            <input type="checkbox" class="placeholder_checkbox" <?php echo esc_attr($checked) ?>>
                            <input type="hidden" class="hidden_placeholder_checkbox" name="mipl_wc_custom_field[placeholder_checkbox][]" value="<?php echo esc_attr($placeholder_checkbox) ?>">
                            <?php esc_html_e('Default field as a placeholder') ?><br>
                            <span class="mipl_cf_error_msg"><?php echo esc_html($placeholder_checkbox_error_msg) ?></span>

                        </label>
                    </div>

                    <div class="form_input">
                        <?php
                        if(empty($custom_field['required_field'])){
                            $custom_field['required_field'] = array();
                        }
                        $checked = ""; 
                        if(!empty($custom_field['required_field'][$key]) && in_array($field_name,$custom_field['required_field'])){
                            $checked = 'checked';
                        }
                        ?>
                        <label><input type="checkbox" class="mipl_wc_ck_required_field" name="mipl_wc_custom_field[required_field][<?php echo esc_attr($key) ?>]" value="<?php echo esc_attr($field_name) ?>" <?php echo esc_attr($checked) ?> data-index="<?php echo esc_attr($key) ?>">
                        <?php esc_html_e('Required Field') ?></label><br>
                        <span class="mipl_cf_error_msg"><?php echo esc_html($required_field_error_msg) ?></span>

                    </div>
                    <div class="form_input mipl_field_group_button">
                        <a class="mipl_done_field_group_button button"><?php esc_html_e('Close') ?></a>
                    </div>
                    
                </div>
            </div>
            <?php
            }
            }
               
            ?>
        </div>
        <?php
            unset($_SESSION['mipl_cf_admin_error']);
            }
        ?>
</div>
<div class="mipl_wc_cf_add_custom_field_button">
    <a class="mipl_wc_add_field_button button"><?php esc_html_e('+Add Field') ?></a>
</div>

<script id="mipl_wc_add_custom_fields_form" type="text/template">
    <div class="mipl_wc_cf_custom_field_table_row ">    
        <div class="custom_field_table_data">
            <div class="mipl_wc_cf_custom_field_table_column sort_icon">
                <span class="handle mipl_handle">:::</span>
            </div>
            <div class="mipl_wc_cf_custom_field_table_column">
                <span class="tr_field_label"><?php esc_html_e('(no label)') ?></span>
            </div>
            <div class="mipl_wc_cf_custom_field_table_column">
                <span class="tr_field_name"><?php esc_html_e('(no name)') ?></span>
            </div>
            <div class="mipl_wc_cf_custom_field_table_column">
                <span class="tr_field_type"><?php esc_html_e('(Text)') ?></span>
            </div>
            <div class="mipl_wc_cf_custom_field_table_column">
                <span class="tr_default_value"><?php esc_html_e('(no default value)') ?></span>
            </div>
            <div class="mipl_wc_cf_custom_field_table_column btn">
                    <a href="#" class="mipl_wc_edit_field"><?php esc_html_e('Edit') ?></a>
                    <a href="#" class="mipl_wc_remove_field"><?php esc_html_e('Remove') ?></a>
            </div>
        </div>

        <div class="mipl_wc_cf_custom_field_add_form mipl_custom_fields_form">
            <div class="update-message notice inline notice-warning notice-alt form_input mipl_recaptcha_position_note" style="display:none">
               
            </div>
            <div class="form_input mipl_cf_required">
                <label><?php esc_html_e('Field Label') ?></label><br>
                <input type="text" class="custom_field_label" name="mipl_wc_custom_field[field_label][]" value="">
            </div>
            <div class="form_input mipl_cf_required">
                <label><?php esc_html_e('Field Name') ?></label><br>
                <input type="text" class="mipl_wc_ck_custom_field_name" name="mipl_wc_custom_field[field_name][]" value="">
            </div>
            <div class="form_input mipl_cf_required">
                <label><?php esc_html_e('Field Type') ?></label><br>
                    <select name="mipl_wc_custom_field[field_type][]" class="mipl_wc_ck_custom_field_type" id="">
                        <option value="text"><?php esc_html_e('Text') ?></option>
                        <option value="number"><?php esc_html_e('Number') ?></option>
                        <option value="select"><?php esc_html_e('Select') ?></option>
                        <option value="textarea"><?php esc_html_e('Textarea') ?></option>
                        <option value="color"><?php esc_html_e('Color') ?></option>
                        <option value="date"><?php esc_html_e('Date') ?></option>
                        <option value="time"><?php esc_html_e('Time') ?></option>
                        <option value="datetime-local"><?php esc_html_e('Datetime') ?></option>
                        <option value="tel"><?php esc_html_e('Telephone') ?></option>
                        <option value="email"><?php esc_html_e('Email') ?></option>
                        <option value="checkbox"><?php esc_html_e('Checkbox (yes/no)') ?></option>
                        <option value="multicheckbox"><?php esc_html_e('Checkbox (multiple checkbox)') ?></option>
                        <option value="radio"><?php esc_html_e('Radio') ?></option>
                        <option value="file"><?php esc_html_e('File') ?></option>
                        <option value="recaptcha"><?php esc_html_e('reCAPTCHA') ?></option>
                    </select>

            </div>
            <div class="form_input mipl_wc_recaptcha_note" style="display:none">
                <span class="mipl_recaptcha_note" style="color:gray">
                    <?php esc_html_e('Note: Please save reCAPTCHA setting [Checkout Fields -> Setting]') ?>
                </span>
            </div>

            <div class="form_input mipl_cf_required" style="display:none" id="mipl_cf_option_value">
                <label><?php esc_html_e('Option value') ?></label><br>
                <textarea class="mipl_option_data" name="mipl_wc_custom_field[option_value][]"></textarea><br>
                <b><?php esc_html_e('NOTE') ?>:</b><span><?php esc_html_e('Enter each choice on a new line.For more control, you may specify both a value and label like red:Red') ?></span>

            </div>

            <div class="form_input mipl_cf_default_class">
                <label for=""><?php esc_html_e('Default value') ?></label><br>
                <input type="text" name="mipl_wc_custom_field[default_value][]" class="mipl_custom_field_default_value" value="" style="width:100%;">

            </div>
            <div id="default" class="form_input mipl_cf_textarea_default_class" style="display:none">
                <label><?php esc_html_e('Default value') ?></label><br>
                <textarea id="" class="mipl_cf_textarea_default_value" name=""></textarea>

            </div>

            <div class="form_input mipl_cf_file_size_class mipl_cf_required" style="display:none">
                <label><?php esc_html_e('Max File size (MB)') ?> </label><br>
                <input type="number" class="file_size" name="mipl_wc_custom_field[file_size][]" value="5" min="1">
            </div>

            <div class="form_input mipl_cf_file_type_class mipl_cf_required" style="display:none">
                <label><?php esc_html_e('File type') ?></label><br>
                <select name="mipl_wc_custom_field[file_type][][]" class="file_type mipl_wc_ck_select2" multiple>
                    <?php
                    foreach($file_type as $label=>$types){
                       
                    ?>
                    <optgroup label="<?php echo esc_attr($label) ?>">
                        <?php
                        foreach($types as $type){
                        ?>
                        <option value="<?php echo esc_attr($type) ?>"><?php echo esc_html($type) ?></option>
                        <?php
                        }
                        ?>
                       
                    </optgroup>
                    <?php
                        
                    }
                    ?>
                </select>
                <!-- <span class="mipl_recaptcha_note">
                    <?php
                    if(empty($v2_site_key) && empty($v2_secret_key)){
                        ?>
                        <b><?php esc_html_e('NOTE') ?>:</b><span><?php esc_html_e('Please save reCAPTCHA setting [Checkout Fields -> Setting]') ?>
                        <?php
                    }
                    ?>
                </span> -->
            </div>

            <div class="form_input mipl_placeholder_checkbox">
                <label><input type="checkbox" class="placeholder_checkbox" name="mipl_wc_custom_field[placeholder_checkbox][]" value="">
                <?php esc_html_e('Default field as a placeholder') ?></label>
            </div>

            <div class="form_input">
                <label><input type="checkbox" class="mipl_wc_ck_required_field" name="mipl_wc_custom_field[required_field][]" value="">
                <?php esc_html_e('Required Field') ?></label>
            </div>
            <div class="form_input mipl_field_group_button">
                <a class="mipl_done_field_group_button button"><?php esc_html_e('Close') ?></a>
            </div>

        </div>
    </div>
</script>





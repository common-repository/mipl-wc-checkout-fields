<?php

$group_setting_data = get_post_meta($post->ID, '_mipl_wc_cf_group_setting', true);

$checked = "";
if(isset($group_setting_data) && !empty($group_setting_data['deactive_group'])){
    $checked = "checked";
}

$title_checked = "";
if(isset($group_setting_data) && !empty($group_setting_data['hide_title'])){
    $title_checked = "checked";
}
?>

<div class="mipl_wc_cf_group_setting">
    <label for="active_group"><input type="checkbox" id="active_group" name="_mipl_wc_cf_group_setting[deactive_group]" value="1" <?php echo esc_attr($checked) ?>> <b><?php esc_html_e('Deactivate') ?></b> </label>
</div>
<div class="mipl_wc_cf_group_setting">
    <label for="hide_title"><input type="checkbox" id="hide_title" name="_mipl_wc_cf_group_setting[hide_title]" value="1" <?php echo esc_attr($title_checked) ?>> <b><?php esc_html_e('Hide Group Title') ?></b> </label>
    <br><span class="mipl_wc_cf_setting_note"><?php esc_html_e("[Note: * Only for 'Classic Checkout']")?></span>
</div>
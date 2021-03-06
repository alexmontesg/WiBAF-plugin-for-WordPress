<?php

wp_enqueue_script("jquery");
add_action('wp_head','hook_adaptation_files');

function file_exists_url($url) {
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $status = $code == 200;
    curl_close($ch);
    return $status;
}

function get_path_to_adaptation_file($option_name, $default_path) {
    $path = get_option($option_name);
    if($path == '') {
        $path = $default_path;
    }
    $path = WIBAF__THEME_URL . $path;
    return $path;
}

function hook_adaptation_files() {
    $adaptation_file = get_path_to_adaptation_file('adaptation_file', WIBAF__DEFAULT_ADAPTATION);
    $modelling_file = get_path_to_adaptation_file('modelling_file', WIBAF__DEFAULT_MODELLING);
    if(!isset($_GET['logged_in'])):
        if (file_exists_url($adaptation_file)):
?>
            <link href='<?php echo $adaptation_file; ?>' rel='prefetch' type='text/amf' title='AM' />
<?php
        endif;
        if (file_exists_url($modelling_file)):
?>
            <link href='<?php echo $modelling_file; ?>' rel='prefetch' type='text/umf' title='UM' />
<?php
        endif;
   endif;
}
?>

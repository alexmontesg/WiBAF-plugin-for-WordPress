<?php
/*
Plugin Name: WiBAF
Plugin URI: http://wibaf.win.tue.nl/
Version: 0.1
Author: Alejandro Montes Garcia
Description: WiBAF is an adaptation library that works on the client
*/
define('WIBAF__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WIBAF__PLUGIN_URL', plugin_dir_url(__FILE__));
define('WIBAF__THEME_URL', get_template_directory_uri());
define('WIBAF__DEFAULT_ADAPTATION', '/adaptation/adaptation.amf');
define('WIBAF__DEFAULT_MODELLING', '/adaptation/modelling.umf');
define('WIBAF__DEFAULT_LEVELS', '3');
define('WIBAF__DEFAULT_USER_LEVEL', '1');
define('WIBAF__SETTINGS_TITLE', 'What have we learned about you?');
define('WIBAF__SETTINGS_EXPLANATION', 'These variables are used for adaptation of the content provided.');
define('WIBAF__PRIVACY_TITLE', 'Where your information is stored');
define('WIBAF__PRIVACY_EXPLANATION', 'You can choose where we store your information, in your computer it will be more private, but in our servers we can offer you a better service.');

include WIBAF__PLUGIN_DIR . '_inc/util.php';
include WIBAF__PLUGIN_DIR . '_inc/wp_head.php';
include WIBAF__PLUGIN_DIR . '_inc/wp_footer.php';
include WIBAF__PLUGIN_DIR . '_inc/admin_menu.php';
include WIBAF__PLUGIN_DIR . '_inc/user_profile.php';
include WIBAF__PLUGIN_DIR . '_inc/publish_post.php';
include WIBAF__PLUGIN_DIR . '_inc/data_handler/user_data.php';
include WIBAF__PLUGIN_DIR . '_inc/server_techniques/history.php';

?>

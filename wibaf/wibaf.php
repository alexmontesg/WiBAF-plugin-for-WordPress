<?php
/*
Plugin Name: WiBAF
Plugin URI: http://wibaf.win.tue.nl/
Version: 0.1
Author: Alejandro Montes Garcia
Description: The world wide web is an enormous hyperspace where users face the problem of information overload. Adaptive web based systems try to tackle this problem by displaying only the information that is really meaningful for the user. These systems need to collect data from the user in order to personalize the information. The set of information that the system has collected about a user is called the User Model. User models in adaptive web base systems are typically stored on the server. However, this has some issues such as lack of privacy, server overload, band-width usage, limitation of events that can be tracked, lack of context awareness, etc... To solve this problem, some client side approaches have also been proposed. Still, client based user modeling has some other drawbacks. Typically the user has to install some piece of software, like a desktop application or a browser plugin, and techniques that rely on the comparison of several user profiles cannot be applied. P2P networks allow the analysis of several client user profiles at a time, but in that case the result will depend on the peers connected at the moment when the comparison is being performed. WiBAF aims to balance these two approaches in a way that the advantages of both are maximized and the drawbacks minimized.
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
?>

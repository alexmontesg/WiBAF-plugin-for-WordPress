<?php
add_action('wp_footer', 'link_wibaf');
add_filter('login_redirect', 'my_login_redirect', 10, 3);

function my_login_redirect($redirect_to) {
  return home_url() . "?logged_in";
}

function link_wibaf() { ?>
    <script type='text/javascript' src='<?php echo WIBAF__PLUGIN_URL; ?>js/wibaf.min.js'></script>
    <script type='text/javascript'>
    var $ = jQuery;
        $(function() {
	    wibaf.getInstance({
                visual: {
                    url: "url-to-be-filled",
                    feedback: "This is used to indicate whether the user is visual (values bigger than 0) or verbal (values lower than 0)",
                    domain: "learning-style"
                },
                global: {
                    url: "url-to-be-filled",
                    feedback: "This is used to indicate whether the user is global (values bigger than 0) or analytic (values lower than 0)",
                    domain: "learning-style"
                },
                active: {
                    url: "url-to-be-filled",
                    feedback: "This is used to indicate whether the user is active (values bigger than 0) or reflective (values lower than 0)",
                    domain: "learning-style"
                }                
            }).init(function() {
                <?php checkPrivacySettings(); ?>
            });
        });
    </script>
<?php }

function sync() {
    if (isset($_GET['logged_in'])) {
        download_data();
    } else {
        upload_data();
    }
}

function checkPrivacySettings() {
?>
    rules.getInstance().getRule("default", function(rule) {
        if(!rule) {
            rule = new Rule("default", <?php echo init_setting('default_slider_level', WIBAF__DEFAULT_USER_LEVEL); ?>, "default");
            rules.getInstance().addRule(rule);
        }
        if(rule.value + 1 == <?php echo init_setting('slider_levels', WIBAF__DEFAULT_LEVELS); ?>) {
            userModel.getInstance().setAllServer(true);
        } else if (rule.value < 2) {
            userModel.getInstance().setAllServer(false);
        } else {
            var domains = <?php echo json_encode(get_downloadable_groups()); ?>;
        }
<?php
        sync();
?>
    });
<?php
}

function store_data_on_browser($user_variable) {
?>
    var data = <?php echo $user_variable; ?>;
    for(var key in data) {
    var type = "string";
        if(!isNaN(data[key])) {
            data[key] = parseFloat(data[key]);
            type = "numeric";
        }
        var url = "";
        var feedback = "Times you have accessed this page";
        var domain = "access";
        if(!key.endsWith("_accessed")) {
            var dictVar = wibaf.getInstance().getDictionary().get(key);
            url = dictVar ? dictVar.url : "";
            feedback = dictVar ? dictVar.feedback : "";
            domain = dictVar ? dictVar.domain : "";
        }
        userModel.getInstance().init_update(key, data[key], type, feedback, feedback, domain);
    }
<?php
}

function download_slider($sliderValue) {
?>
    rules.getInstance().getRule("default", function(rule) {
        if(rule) {
          rules.getInstance().updateRule("default", <?php echo $sliderValue; ?>);
        } else {
          rules.getInstance().addRule(new Rule("default", <?php echo $sliderValue; ?>, "default"));
        }
    });
<?php
}

function get_downloadable_groups() {
    $default_levels = init_setting('slider_levels', WIBAF__DEFAULT_LEVELS);
    $groups = array();
    for ($i = 2; $i < $default_levels - 1; $i++) {
        if(get_option('fields_' . $i) != '') array_push($groups, array_unique(str_getcsv(strtolower(get_option('fields_' . $i)))));
    }
    return $groups;
}

function download_data() {
    $user_id = get_current_user_id();
    if ($user_id == 0) return;
    $data = array_filter(array_map(function($a) {
        return $a[0];
    }, get_user_meta($user_id)));
    $obj = "{";
    foreach($data as $key => $value) {
        if(startsWith($key, "wibaf_")) {
            $key = substr($key, strlen("wibaf_"));
            $obj = $obj . ("'" . $key . "':'" . $value . "',");
        }
    }
    $obj = $obj . "}";
    store_data_on_browser($obj);
}

function upload_data() {
    $user_id = get_current_user_id();
    if ($user_id == 0) return;
    if(isset($_COOKIE['wibaf_upload'])) {
        $to_upload = json_decode(stripslashes($_COOKIE['wibaf_upload']));
        foreach($to_upload as $item) {
            update_user_meta($user_id, "wibaf_" . $item -> name, $item -> value);
        }
        $to_delete = json_decode(stripslashes($_COOKIE['wibaf_delete']));
        foreach($to_delete as $item) {
            delete_user_meta($user_id, "wibaf_" . $item);
        }
        $to_delete = json_decode($_COOKIE['wibaf_privacy']);
        update_user_meta($user_id, 'privacyvspersonalization', $_COOKIE['wibaf_privacy']);
    }
?>
    userModel.getInstance().getAll(function(items) {
        var toUpload = [];
        var toDelete = [];
        for(var i = 0; i < items.length; i++) {
            if(items[i].server.send && items[i].changed) {
                var item = {
                    name: items[i].name,
                    value: items[i].value
                }
                toUpload.push(item);
                userModel.getInstance().setUnchanged(items[i].name);
            } else if(items[i].changed) {
                toDelete.push(items[i].name);
                userModel.getInstance().setUnchanged(items[i].name);
            }
        }
        document.cookie = "wibaf_upload=" + JSON.stringify(toUpload) + ";";
        document.cookie = "wibaf_delete=" + JSON.stringify(toDelete) + ";";
    });
    rules.getInstance().getRule("default", function(rule) {
        document.cookie = "wibaf_privacy=" + rule.value + ";";
    });
<?php
}
?>

<?php
add_action('show_user_profile', 'my_show_extra_profile_fields');
add_action('edit_user_profile', 'my_show_extra_profile_fields');

function get_privacy_levels() {
    $privacy_levels = array("No data is tracked", "Data is kept private on the client");
    for($i = 2; $i < get_option('slider_levels') - 1; $i++) {
        array_push($privacy_levels, get_option("feedback_" . $i));
    }

    array_push($privacy_levels, "Data is stored on the server");
    return $privacy_levels;
}

function display_user_profile() { ?>
    <table class="form-table" id="user_profile">
        <thead>
            <tr>
                <th>Variable Name</th>
                <th>Explanation</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
<?}

function my_show_extra_profile_fields($user) {
?>
        <h3><?php echo get_option("settings_title"); ?></h3>
        <p><?php echo get_option("settings_explanation"); ?></p>
        <?php display_user_profile(); ?>

	<h3><?php echo get_option("privacy_title"); ?></h3>
        <p><?php echo get_option("privacy_explanation"); ?></p>

	<table class="form-table" style="width: 50%;">
	  <tr>
	    <td>
	      <div class="text-right" style="width: 20%; float: left;"><p><strong>More privacy</strong></p></div>
	      <div class="range-slider" style="width: 50%; float: left;">
		<input style="width: 100%;" type="range" id="privacyvspersonalization" name="privacyvspersonalization" min="0" max="<?php echo get_option('slider_levels') - 1;?>"/>
              </div>
              <div class="col-sm-3" style="width: 20%; float: right;">
		<p><strong>More personalisation</strong></p>
              </div>    
	      <div style="float: left; width: 100%;">
		<p id="privacyExplanation" class="privacyExplanation"></p>
	      </div>
	    <td>
	  </tr>
	</table>
        <script type='text/javascript' src='<?php echo WIBAF__PLUGIN_URL; ?>js/wibaf.min.js'></script>
        <script type="text/javascript">
            var $ = jQuery;
            var privacyLevels = <?php echo json_encode(get_privacy_levels());?>;
            function displayPrivacyExplanation() {
                var userLevel = $("#privacyvspersonalization").val();
                $("#privacyExplanation").text(privacyLevels[userLevel]);
            }

            function getInputField(name, type, value) {
                return $('<input>', {
                    name : "wibaf_" + name,
                    type : type,
                    value : value
                });
            }

            function getInputDomain(name, domain) {
                return $('<input>', {
                    name : "domain_wibaf_" + name,
                    type : "hidden",
                    value : domain
                });
            }

            $(function() {
                displayPrivacyExplanation();
                $("#privacyvspersonalization").on("change", displayPrivacyExplanation);
                wibaf.getInstance().init(function() {
                    <?php download_data();?>
                    var table = $("#user_profile");
                    userModel.getInstance().getAll(function(items) {
                        items.forEach(function(item) {
                            table.find('tbody').append($('<tr>').
                                append($('<td>').text(item.name)).
                                append($('<td>').text(item.feedback ? item.feedback : "")).
                                append($('<td>').append(item.type == "numeric" ? getInputField(item.name, "number", item.value) : getInputField(item.name, "text", item.value))).
                                append(getInputDomain(item.name, item.domain)));
                        });
                    });
                });
            });
        </script>
<?php }

add_action('personal_options_update', 'save_user_model' );
add_action('edit_user_profile_update', 'save_user_model' );

function save_user_model($user_id) {
    if (!current_user_can('edit_user', $user_id )) return false;
    update_usermeta($user_id, 'privacyvspersonalization', $_POST['privacyvspersonalization']);
    $user_meta_keys = array_filter(array_keys($_POST), function($key) {
        return strpos($key, 'wibaf_') === 0 || strpos($key, 'domain_wibaf_') === 0;
    });
    if($_POST['privacyvspersonalization'] == init_setting('slider_levels', WIBAF__DEFAULT_LEVELS) - 1) {
        foreach($user_meta_keys as $key) {
            update_usermeta($user_id, $key, $_POST[$key]);
        }
    } else if($_POST['privacyvspersonalization'] > 1) {
        $all_groups = get_downloadable_groups();
        $groups = array();
        for($i = 2; $i <= $_POST['privacyvspersonalization']; $i++) {
            $groups = array_merge($groups, $all_groups[$i - 2]);
        }
        foreach($user_meta_keys as $key) {
            if(in_array($_POST['domain_' . $key], $groups)) {
                update_usermeta($user_id, $key, $_POST[$key]);
            }
        }
    } else {
        $user_profile = get_user_meta($user_id);
        foreach ($user_profile as $key => $value) {
            if (startsWith($key, "wibaf_") || startsWith($key, "domain_wibaf_")) {
                delete_user_meta($user_id, $key);
            }
        }
    }
    return true;
}
?>

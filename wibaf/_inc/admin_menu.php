<?php
wp_enqueue_script("jquery");
add_action('admin_menu', 'wibaf_settings');

function wibaf_settings() {
    add_menu_page('WiBAF Settings', 'WiBAF Settings', 'administrator', 'wibaf_settings', 'wibaf_display_settings');
}

function init_setting($option_name, $default_value) {
    $setting = get_option($option_name);
    $setting = $setting == '' ? $default_value : $setting;
    return $setting;
}

function wibaf_display_settings() {
    $adaptation_file = init_setting('adaptation_file', WIBAF__DEFAULT_ADAPTATION);
    $modelling_file = init_setting('modelling_file', WIBAF__DEFAULT_MODELLING);
    $slider_levels = init_setting('slider_levels', WIBAF__DEFAULT_LEVELS);
    $default_slider_level = init_setting('default_slider_level', WIBAF__DEFAULT_USER_LEVEL);
    $settings_title = init_setting('settings_title', WIBAF__SETTINGS_TITLE);
    $settings_explanation = init_setting('settings_explanation', WIBAF__SETTINGS_EXPLANATION);
    $privacy_title = init_setting('privacy_title', WIBAF__PRIVACY_TITLE);
    $privacy_explanation = init_setting('privacy_explanation', WIBAF__PRIVACY_EXPLANATION);
    $levels_array = array();
    for ($i = 2; $i < $slider_levels - 1; $i++) {
        $levels_array["fields_" . $i] = get_option("fields_" . $i);
        $levels_array["feedback_" . $i] = get_option("feedback_" . $i);
    }
?>
<div class='wrap'>
    <form action='options.php' method='post' name='options'>
        <h2>Select your settings</h2>
        <h3>Adaptation and modelling files</h3>
        <p>Please provide the path to the adaptation and modelling files from your current theme directory</p>
    <?php echo wp_nonce_field('update-options'); ?>
        <table class='form-table' width='100%' cellpadding='10'>
            <tbody>
                <tr>
                    <td scope='row' align='left'>
                        <label>Adaptation file</label>
                        <input type='text' required name='adaptation_file' value='<?php echo $adaptation_file; ?>' />
                    </td>
                </tr>
                <tr>
                    <td scope='row' align='left'>
                        <label>Modelling file</label>
                        <input type='text' required name='modelling_file' value='<?php echo $modelling_file; ?>' />
                    </td>
                </tr>
            </tbody>
        </table>
        <h3>Slider</h3>
        <p>Please enter information about the slider. Levels 0, 1 and the highest are handeled automatically</p>
        <table class='form-table' width='100%' cellpadding='10'>
            <tbody>
                <tr id='slider_levels'>
                    <td scope='row' align='left'>
                        <label>Number of levels in the slider</label>
                        <input type='number' min='3' required name='slider_levels' value='<?php echo $slider_levels; ?>' />
                    </td>
                </tr>
                <tr>
                    <td scope='row' align='left'>
                        <label>Default slider level for a new user</label>
                        <input type='number' min='0' required name='default_slider_level' value='<?php echo $default_slider_level; ?>' />
                    </td>
                </tr>
            </tbody>
        </table>
        <h3>User settings screen</h3>
        <p>Please enter information about the user settings screen</p>
        <table class='form-table' width='100%' cellpadding='10'>
            <tbody>
                <tr>
                    <td scope='row' align='left'>
                        <label>Title for WiBAF settings</label>
                        <input type='text' required name='settings_title' value='<?php echo $settings_title; ?>' />
                    </td>
                </tr>
                <tr>
                    <td scope='row' align='left'>
                        <label>Explanation for WiBAF settings</label>
                        <input type='text' required name='settings_explanation' value='<?php echo $settings_explanation; ?>' />
                    </td>
                </tr>
                <tr>
                    <td scope='row' align='left'>
                        <label>Title for WiBAF privacy</label>
                        <input type='text' required name='privacy_title' value='<?php echo $privacy_title; ?>' />
                    </td>
                </tr>
                <tr>
                    <td scope='row' align='left'>
                        <label>Explanation for WiBAF privacy</label>
                        <input type='text' required name='privacy_explanation' value='<?php echo $privacy_explanation; ?>' />
                    </td>
                </tr>
            </tbody>
        </table>
        <input type='hidden' name='action' value='update' />
        <input type='hidden' name='page_options' />
        <input type='submit' name='submit' value='Update' />
    </form>
</div>
<script>
    var levels_array = <?php echo json_encode($levels_array);?>;
    function removeFieldGroups() {
        var nodes = document.getElementsByClassName('field_group');
        while(nodes.length > 0) {
            nodes[0].parentNode.removeChild(nodes[0]);
        }
    }

    function createElementWithAttributes(tag, attrNames, attrValues) {
        var element = document.createElement(tag);
        if(attrNames.length != attrValues.length) return element;
        for(var i = 0; i < attrNames.length; i++) {
            element.setAttribute(attrNames[i], attrValues[i]);
        }
        return element;
    }
    
    function addSliderField(node, i, labelText1, labelText2, name) {
        name = name + '_' + i;
        var tr = createElementWithAttributes('tr', ['class'], ['field_group']);
        var td = createElementWithAttributes('td', ['scope', 'align'], ['row', 'left']);
        var label = document.createElement('label');
        label.innerText = labelText1 + i + labelText2;
        td.appendChild(label);
        var input = createElementWithAttributes('input', ['type', 'name'], ['text', name]);
        input.value = levels_array[name];
        td.appendChild(input);
        tr.appendChild(td);
        node.parentNode.appendChild(tr);
        setPageOptionsValue(name, true);
    }

    function setPageOptionsValue(value, append) {
        var pageOptions = document.getElementsByName('page_options')[0];
        if(append) pageOptions.setAttribute('value', pageOptions.value + ',' + value);
        else pageOptions.setAttribute('value', value);
    }

    function setMaxDefault() {
        var currentLevels = document.getElementsByName('slider_levels')[0].value;
        var defaultSlider = document.getElementsByName('default_slider_level')[0]
        defaultSlider.setAttribute("max", currentLevels - 1);
        if(defaultSlider.value > currentLevels - 1) defaultSlider.value = currentLevels -1;
    }

    function loadSliderFields() {
        setMaxDefault();
        var sliderLevelsRow = document.getElementById('slider_levels');
        setPageOptionsValue('adaptation_file,modelling_file,slider_levels,default_slider_level,settings_title,settings_explanation,privacy_title,privacy_explanation', false);
        removeFieldGroups();
        var sliderLevels = parseInt(document.getElementsByName('slider_levels')[0].value);
        for(var i = 2; i < sliderLevels - 1; i++) {
            addSliderField(sliderLevelsRow, i, 'Field groups to send to the server in the level ', ' (separated by commas)', 'fields');
            addSliderField(sliderLevelsRow, i, 'User-friendly explanation for level ', '', 'feedback');
        }
    }

    window.onload = loadSliderFields;
    document.getElementById('slider_levels').addEventListener('change', loadSliderFields);
</script>
<?php
}
?>

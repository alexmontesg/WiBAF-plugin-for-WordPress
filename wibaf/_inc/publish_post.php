<?php
add_action('publish_post', 'edit_adaptation_file', 10, 2);

function edit_adaptation_file($ID, $post) {
    $file = get_path_to_adaptation_file('adaptation_file', WIBAF__DEFAULT_ADAPTATION);
    $title = preg_replace("/\s+/", "-", strtolower($post -> post_title));
    $title = preg_replace("/[^a-z0-9-]/mi", "", $title);
    $blogname = preg_replace("/\s+/", "-", strtolower(get_option('blogname')));
    $blogname = preg_replace("/[^a-z0-9-]/mi", "", $blogname);
    $title = $title . "--" . $blogname;
    $content = "\n@user(" . $title . "-accessed-gt: 0) {";
    if(!(strpos(file_get_contents($file), $content) !== false)) {
        $content = $content . "\n    #post-" . $ID . " {";
        $content = $content . "\n        add_class: already-visited;";
        $content = $content . "\n    }";
        $content = $content . "\n}";
        file_put_contents($file, $content, FILE_APPEND);
    }
}
?>

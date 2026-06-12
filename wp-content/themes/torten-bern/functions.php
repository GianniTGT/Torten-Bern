/* Theme functions: register support */
<?php
add_action('after_setup_theme', function(){
    add_theme_support('woocommerce');
    add_theme_support('title-tag');
});

// Simple menu
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('torten-bern-style', get_stylesheet_uri());
});

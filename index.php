<?php
/*
Plugin Name: Default theme settings
*/

remove_action('wp_head', 'wp_generator');

add_theme_support( 'post-thumbnails' );

function new_excerpt_more( $more ) {
  return '&hellip;';
}
add_filter('excerpt_more', 'new_excerpt_more');

//verwijder emoji shizzle
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

//verwijder yoast shizzle
add_filter( 'wpseo_use_page_analysis', '__return_false' );

//verijwder admin bar
add_filter('show_admin_bar', '__return_false');

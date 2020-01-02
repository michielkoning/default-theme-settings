<?php
/*
Plugin Name: Default theme settings
*/

function mk_default_theme_settings_load_textdomain()
{
    load_plugin_textdomain(
        'mk_default_theme_settings',
        false,
        basename(dirname(__FILE__)) . '/languages'
    );
}
add_action('plugins_loaded', 'mk_default_theme_settings_load_textdomain');

remove_action('wp_head', 'wp_generator');

function setup_theme()
{
    add_theme_support('post-thumbnails');

    /* HTML5 */
    add_theme_support('html5');

    /* automatic feed links */
    add_theme_support('automatic-feed-links');
}

add_action('after_setup_theme', 'setup_theme');

//verwijder emoji shizzle
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

//verwijder yoast shizzle
add_filter('wpseo_use_page_analysis', '__return_false');

//verijwder admin bar
add_filter('show_admin_bar', '__return_false');

function header_pre()
{
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <title><?php wp_title('|', true, 'right'); ?></title>
  <?php
}

function header_post()
{
    ?>
</head>
<body <?php body_class(); ?>>

  <a href="#main" class="skip-link"><?php _e(
      'To maincontent',
      'mk_default_theme_settings'
  ); ?></a>
  <a href="#menu" class="skip-link"><?php _e(
      'To mainmenu',
      'mk_default_theme_settings'
  ); ?></a>

  <?php
}

function footer_post()
{
    ?>

</body>
</html>
  <?php
}

add_action('wp_head', 'header_pre', -1);
add_action('wp_head', 'header_post', 99);
add_action('wp_footer', 'footer_post', 99);

function new_excerpt_more($more)
{
    return '&hellip;';
}
add_filter('excerpt_more', 'new_excerpt_more');

//set wrapper around video
add_filter('embed_oembed_html', 'tdd_oembed_filter', 10, 4);
function tdd_oembed_filter($html, $url, $attr, $post_ID)
{
    return "<figure class=\"video-container\">{$html}</figure>";
}

function add_edit_link_after_content($content)
{
    $edit_post_title = is_single()
        ? "Bewerk dit bericht"
        : "Bewerk deze pagina";
    $edit_post_url = get_edit_post_link();
    $edit_link = null;
    if ($edit_post_url) {
        $edit_link = "<p><a href=\"{$edit_post_url}\" class=\"post-edit-link\" title=\"{$edit_post_title}\">{$edit_post_title}</a></p>";
    }
    return $content . $edit_link;
}
add_filter('the_content', 'add_edit_link_after_content');


function access_menu_for_editors()
{
    // get the the role object
    $role_object = get_role('editor');

    // add $cap capability to this role object
    $role_object->add_cap('edit_theme_options');
}

add_filter('admin_init', 'access_menu_for_editors');

function remove_yoast_ads()
{
    echo '<style>
    .yoast_premium_upsell_admin_block,
    #sidebar-container {
      display: none;
    }
  </style>';
}
add_action('admin_head', 'remove_yoast_ads');

add_filter('img_caption_shortcode', 'cleaner_caption', 10, 3);

function cleaner_caption($output, $attr, $content)
{
    /* We're not worried abut captions in feeds, so just return the output here. */
    if (is_feed()) {
        return $output;
    }

    /* Set up the default arguments. */
    $defaults = array(
        'id' => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => ''
    );

    /* Merge the defaults with user input. */
    $attr = shortcode_atts($defaults, $attr);

    /* If the width is less than 1 or there is no caption, return the content wrapped between the < tags. */
    if (1 > $attr['width'] || empty($attr['caption'])) {
        return $content;
    }

    /* Set up the attributes for the caption <div>. */
    $attributes = !empty($attr['id'])
        ? ' id="' . esc_attr($attr['id']) . '"'
        : '';
    $attributes .= ' class="wp-caption ' . esc_attr($attr['align']) . '"';
    /*  $attributes .= ' style="width: ' . esc_attr( $attr['width'] ) . 'px"';   */

    /* Open the caption <div>. */
    $output = '<div' . $attributes . '>';

    /* Allow shortcodes for the content the caption was created for. */
    $output .= do_shortcode($content);

    /* Append the caption text. */
    $output .= '' . $attr['caption'] . '';

    /* Close the caption </div>. */
    $output .= '</div>';

    /* Return the formatted, clean caption. */
    return $output;
}

<?php
/**
 * Plugin Name: Demo Plugin
 * Description: ACF Local json feature to sync fieldgroups, custom post type which is private.
 * Version: 1.0.0
 * Requires PHP: 7.4
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: demo
 * Domain Path: /languages
 * 
 * @link              https://juvo-design.de
 * @since             1.0.0
 * @package           demo-plugin
 */

namespace Demo_Plugin;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Plugin absolute path
 */
define('DEMO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DEMO_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_demo() {
    // Ensure ACF is available
    if (function_exists('acf_add_local_field_group')) {
        // Include your ACF field group definitions
        include_once DEMO_PLUGIN_PATH . 'acf-field-groups.php';
        // Sync field groups
        acf()->json->sync();
    }
}

//Change ACF Local JSON save location to /acf folder inside this plugin
add_filter('acf/settings/save_json', function() {
    return dirname(__FILE__) . '/acf-json';
});

//Include the /acf folder in the places to look for ACF Local JSON files
add_filter('acf/settings/load_json', function() {
    $paths[] = dirname(__FILE__) . '/acf-json';
    return $paths;
});

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_demo() {
    // Ensure ACF is available
    if (function_exists('acf_add_local_field_group')) {
        // Remove local JSON folder
        acf()->json->remove_local();
    }
}

register_activation_hook(__FILE__, 'Demo_Plugin\activate_demo');
register_deactivation_hook(__FILE__, 'Demo_Plugin\deactivate_demo');

/**
 * Register Custom Post Type
 */
function custom_private_post_type() {

    $labels = array(
        'name'                  => _x( 'Private Posts', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Private Post', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Private Posts', 'text_domain' ),
        'name_admin_bar'        => __( 'Private Post', 'text_domain' ),
        'archives'              => __( 'Post Archives', 'text_domain' ),
        'attributes'            => __( 'Post Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Post:', 'text_domain' ),
        'all_items'             => __( 'All Posts', 'text_domain' ),
        'add_new_item'          => __( 'Add New Post', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Post', 'text_domain' ),
        'edit_item'             => __( 'Edit Post', 'text_domain' ),
        'update_item'           => __( 'Update Post', 'text_domain' ),
        'view_item'             => __( 'View Post', 'text_domain' ),
        'view_items'            => __( 'View Posts', 'text_domain' ),
        'search_items'          => __( 'Search Post', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into post', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this post', 'text_domain' ),
        'items_list'            => __( 'Posts list', 'text_domain' ),
        'items_list_navigation' => __( 'Posts list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter posts list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Private Post', 'text_domain' ),
        'description'           => __( 'Private Posts', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => false, // Set to false to make it private
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-lock', // You can change the icon as needed
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
    );
    register_post_type( 'private_post', $args );

}
add_action( 'init', 'Demo_Plugin\custom_private_post_type', 0 );

/**
 * Register ACF Gutenberg Block
 */
function register_hello_world_block() {
    if( function_exists('acf_register_block_type') ) {
        acf_register_block_type(array(
            'name'              => 'hello-world',
            'title'             => __('Hello World'),
            'description'       => __('A basic Gutenberg block that displays "Hello World".'),
            'render_callback'   => 'render_hello_world_block',
            'category'          => 'common',
            'icon'              => 'admin-comments',
            'keywords'          => array( 'hello', 'world' ),
            'mode'              => 'edit',
            'supports'          => array(
                'mode'  => false,
                'align' => false,
            ),
        ));
    }
}
add_action('acf/init', 'Demo_Plugin\register_hello_world_block');

/**
 * Render callback function for Hello World block
 */
function render_hello_world_block() {
    echo '<div class="hello-world-block">Hello World</div>';
}

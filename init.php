<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 17.12.19
 * Time: 20:09
 */
/*
Plugin Name: Champion Helper
Plugin URI: https://prototypedev.net/
Description:
Version:
Author:
Author URI:
License:
Text Domain: chmp
*/

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'CHMP_VERSION', '4.1.2' );
define( 'CHMP__MINIMUM_WP_VERSION', '4.8' );
define( 'CHMP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
//define( 'CHMP_DELETE_LIMIT', 100000 );

register_activation_hook( __FILE__, array( 'helpers', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'helpers', 'plugin_deactivation' ) );

//require_once( CHMP__PLUGIN_DIR . 'class/.php' );
require_once( CHMP__PLUGIN_DIR . 'helpers.php' );
require_once( CHMP__PLUGIN_DIR . 'rest.php' );

//add_action( 'init', array( 'Akismet', 'init' ) );
//
//add_action( 'rest_api_init', array( 'Akismet_REST_API', 'init' ) );
//
//if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
//    require_once( AKISMET__PLUGIN_DIR . 'class.akismet-admin.php' );
//    add_action( 'init', array( 'Akismet_Admin', 'init' ) );
//}
//
////add wrapper class around deprecated akismet functions that are referenced elsewhere
//require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );
//
//if ( defined( 'WP_CLI' ) && WP_CLI ) {
//    require_once( AKISMET__PLUGIN_DIR . 'class.akismet-cli.php' );
//}

add_action( 'admin_menu', '_admin_menu' );
function _admin_menu(){
    add_menu_page(
        'Manage Competitions',
        'Champion Helper',
        'manage_options',
        CHMP__PLUGIN_DIR,
        '',
        '',
        6 );
//    add_submenu_page(
//        CHMP__PLUGIN_DIR,
//        'Genre',
//        'Genre',
//        'manage_options',
//        CHMP__PLUGIN_DIR.'pages/index-admin.php');
}

function init_taxonomy()
{
//====================Clients
    $labels = array(
        'name' => 'bz',
        'singular_name' => 'bz',
        'add_new' => 'Додати',
        'add_new_item' => 'Додати новий',
        'edit_item' => 'Редагувати',
        'new_item' => 'Додати',
        'all_items' => 'Все',
        'view_item' => 'Переглянути',
        'search_items' => 'Шукаємо',
        'not_found' => 'Не знайдено',
        'not_found_in_trash' => 'Не знайдено у смітнику',
        'menu_name' => 'Клієнти',
    );

    $supports = array('title');

    $slug = get_theme_mod('clients_permalink');
    $slug = (empty($slug)) ? 'champion' : $slug;

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => CHMP__PLUGIN_DIR,
        'query_var' => true,
        'rewrite' => array('slug' => $slug),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 5,
        'supports' => $supports,
        'register_meta_box_cb' => 'wpt_add_event_metaboxes',
    );

    register_post_type('champion', $args);
    register_taxonomy('champion', 'champion', array(
        'label' => 'Clients Categories',
        'labels' => array(
            'menu_name' => __('Clients Categories', 'chmp')
        ),
        'rewrite' => array(
            'slug' => 'champion',//'clients',
            'hierarchical' => false
        ),
        'hierarchical' => false,
        'show_in_menu' => false,// adding to custom menu manually
        'query_var' => true,
        'public' => true
    ));
    add_rewrite_tag("%champion%", '([^/]+)', "post_type=champion&name=");
    add_permastruct('champion', 'champion/%champion%', array(
        'with_front' => true,
        'paged' => true,
        'ep_mask' => EP_NONE,
        'feed' => false,
        'forcomments' => false,
        'walk_dirs' => false,
        'endpoints' => false,
    ));



}
add_action( 'init', 'init_taxonomy');


/**
 * Adds a metabox to the right side of the screen under the â€œPublishâ€ box
 */
function wpt_add_event_metaboxes() {
    add_meta_box(
        'wpt_events_location',
        'Event Location',
        'wpt_events_location',
        'champion',
        'side',
        'default'
    );
}

/**
 * Output the HTML for the metabox.
 */
function wpt_events_location() {
    global $post;
    // Nonce field to validate form request came from current site
    wp_nonce_field( basename( __FILE__ ), 'event_fields' );
    // Get the location data if it's already been entered
    $location = get_post_meta( $post->ID, 'location', true );
    // Output the field
    echo '<input type="text" name="location" value="' . esc_textarea( $location )  . '" class="widefat">';
}

/**
 * Save the metabox data
 */
function wpt_save_events_meta( $post_id, $post ) {
    // Return if the user doesn't have edit permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }
    // Verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times.
    if ( ! isset( $_POST['location'] ) || ! wp_verify_nonce( $_POST['event_fields'], basename(__FILE__) ) ) {
        return $post_id;
    }
    // Now that we're authenticated, time to save the data.
    // This sanitizes the data from the field and saves it into an array $events_meta.
    $events_meta['location'] = esc_textarea( $_POST['location'] );
    // Cycle through the $events_meta array.
    // Note, in this example we just have one item, but this is helpful if you have multiple.
    foreach ( $events_meta as $key => $value ) :
        // Don't store custom data twice
        if ( 'revision' === $post->post_type ) {
            return;
        }
        if ( get_post_meta( $post_id, $key, false ) ) {
            // If the custom field already has a value, update it.
            update_post_meta( $post_id, $key, $value );
        } else {
            // If the custom field doesn't have a value, add it.
            add_post_meta( $post_id, $key, $value);
        }
        if ( ! $value ) {
            // Delete the meta key if there's no value
            delete_post_meta( $post_id, $key );
        }
    endforeach;
}
add_action( 'save_post', 'wpt_save_events_meta', 1, 2 );
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
define( 'CHMP__DAYS_BEFORE_COMPETITION', 40 );

register_activation_hook( __FILE__, array( 'helpers', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'helpers', 'plugin_deactivation' ) );

//require_once( CHMP__PLUGIN_DIR . 'class/.php' );
require_once( CHMP__PLUGIN_DIR . 'helpers.php' );
require_once( CHMP__PLUGIN_DIR . 'class/rest.php' );

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
        'name' => 'Змагання',
        'singular_name' => 'competition',
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
        'public' => false,
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
    global $post;
    if ($post->post_status=='publish')
    add_meta_box(
        'wpt_events_location2',
        'Shortcode',
        'wpt_events_location2',
        'champion',
        'normal',
        'high'
    );
    add_meta_box(
        'wpt_events_location',
        'Налаштування потоків',
        'wpt_events_location',
        'champion',
        'normal',
        'default'
    );
    if ($post->post_status=='publish')
    add_meta_box(
        'wpt_events_location1',
        'Графік змагань',
        'wpt_events_location1',
        'champion',
        'normal',
        'low'
    );
}

/**
 * Output the HTML for the metabox.
 */
function wpt_events_location() {
   wp_nonce_field( basename( __FILE__ ), 'event_fields' );
   require_once ('pages/index-admin.php');
}

function wpt_events_location1() {
    require_once ('pages/table.php');
}

function wpt_events_location2() {
    global $post;
    echo '[FPU id="'.$post->ID.'"]';
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

    $data = prepare_input_data();

    if ( ! wp_verify_nonce( $_POST['event_fields'], basename(__FILE__) ) ) {
        return $post_id;
    }
    // Now that we're authenticated, time to save the data.

    // Cycle through the $events_meta array.
    // Note, in this example we just have one item, but this is helpful if you have multiple.
    foreach ( $data as $key => $value ) :
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

function prepare_input_data(){
    # get description of sections
    $sectionDSCR=array();
    foreach ($_POST as $key=>$value){
        if (strpos($key,'sectionDSCR')===0){
            $tmp=explode('_',$key);
            $sectionDSCR[$tmp[1]][$tmp[2]]=filter_var($value,FILTER_SANITIZE_STRING);
        }
    }

    # filter data values
    $filter_args=array(
        "date_start"=>FILTER_SANITIZE_STRING,
        "date_end"=>FILTER_SANITIZE_STRING
    );
    $data=filter_input_array(INPUT_POST,$filter_args,true);
    $data['sectionDSCR'] = json_encode($sectionDSCR,JSON_UNESCAPED_UNICODE);
    return $data;
}


add_shortcode( 'FPU', 'fpu_shortcode' );
/**
 * [FPU id="100500"]
 * @param $atts
 * @return string
 */
function fpu_shortcode( $atts ){
    $atts = shortcode_atts( array('id' => false,), $atts );
    // check if id exist and this is int >0
    if (intval($atts['id'])!==0)
    {
        $id = intval($atts['id']);
        $post = get_post( $id);
        // post not exist
        if ((!$post) || ($post->post_type!="champion")) return;

        //check how match days before competition
        $metadata=array(
            'date_end'=>get_post_meta($post->ID,'date_start',true),
            'date_start'=>date('Y-m-d'));
        if (strtotime($metadata['date_start']) < strtotime($metadata['date_end'])){
            $days=plugin_get_date_diff($metadata);
            $dt = array(
                'start' => date('Y-m-d',strtotime(get_post_meta($post->ID,'date_start',true)."-7 days")),
                'end' => date('Y-m-d',strtotime(get_post_meta($post->ID,'date_end',true)."+7 days")),
            );
            if ($days>=CHMP__DAYS_BEFORE_COMPETITION)
                require ('pages/register_form.php');
        }
    }
    return;
}

// Add the custom columns to the book post type:
add_filter( 'manage_champion_posts_columns', 'set_custom_edit_champion_columns' );
function set_custom_edit_champion_columns($columns) {
    unset( $columns['author'],$columns['date'],$columns['title'] );
    $columns['title'] = __( 'Змагання', 'chmp' );
    $columns['total_judge'] = __( 'Кількість суддів що заявилась', 'chmp' );
    $columns['end_date'] = __( 'Дата закінчення прийому заявок','chmp' );
    $columns['days_left'] = __( 'Днів залишилось','chmp' );
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_champion_posts_custom_column' , 'custom_champion_column', 10, 2 );
function custom_champion_column( $column, $post_id ) {
    $metadata=array(
        'date_end'=>
            date('Y-m-d',strtotime(get_post_meta($post_id ,'date_start',true)."-".CHMP__DAYS_BEFORE_COMPETITION." days")),
        'date_start'=>date('Y-m-d'));
    switch ( $column ) {
        case 'title': {
            echo get_post($post_id)->post_title;
            break;}
        case 'total_judge' : {
            echo judgeClass::calculateJudges($post_id);
            break;
        }
        case 'end_date' :
            echo $metadata['date_end'];
            break;
        case 'days_left':{
            //check how match days before competition
            if (strtotime($metadata['date_start']) < strtotime($metadata['date_end'])){
               echo plugin_get_date_diff($metadata);
            }
            else echo '0';
            break;
        }

    }
}
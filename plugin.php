<?php

/**
 * Plugin Name: Lucky Spinner Wheel Discount
 * Plugin URI: https://github.com/mahmudhaisan/
 * Description: Lucky Spinner Wheel Discount
 * Author: Mahmud haisan
 * Author URI: https://github.com/mahmudhaisan
 * Developer: Mahmud Haisan
 * Developer URI: https://github.com/mahmudhaisan
 * Text Domain: lswd
 * Domain Path: /languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


if (!defined('ABSPATH')) {
    die('are you cheating');
}

define("LSWD_PLUGINS_PATH", plugin_dir_path(__FILE__));
define("LSWD_PLUGINS_DIR_URL", plugin_dir_url(__FILE__));


add_action('wp_enqueue_scripts', 'lswd_custom_enqueue_assets');

add_action('admin_enqueue_scripts', 'lswd_custom_register_and_enqueue_assets');
function lswd_custom_register_and_enqueue_assets()
{

    wp_enqueue_style('bootstrap-min', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css');
    wp_enqueue_style('fontawesome-css-min', plugin_dir_url(__FILE__) . 'assets/css/fontawesome.min.css');
    wp_enqueue_style('select2-css-min', plugin_dir_url(__FILE__) . 'assets/css/select2.min.css');
    wp_enqueue_style('style-css', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');


    wp_enqueue_script('bootstrap-min', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('select2-min-js', plugin_dir_url(__FILE__) . 'assets/js/select2.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('admin-script-js', plugin_dir_url(__FILE__) . 'assets/js/admin-script.js', array('jquery'), '1.0.0', true);
}

// Enqueue CSS and JavaScript
function lswd_custom_enqueue_assets()
{

    // Your PHP arrays
    $custom_form_data_group_1 = json_encode(get_option('custom_form_data_group_1'));
    $custom_form_data_group_2 = json_encode(get_option('custom_form_data_group_2'));
    $custom_form_data_group_3 = json_encode(get_option('custom_form_data_group_3'));
    $custom_form_data_group_4 = json_encode(get_option('custom_form_data_group_4'));
    $custom_form_data_group_5 = json_encode(get_option('custom_form_data_group_5'));
    $custom_form_data_group_6 = json_encode(get_option('custom_form_data_group_6'));
   
  

    wp_enqueue_style('bootstrap-min', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css');
    wp_enqueue_style('fontawesome-css-min', plugin_dir_url(__FILE__) . 'assets/css/fontawesome.min.css');
   
    wp_enqueue_style('style-css', plugin_dir_url(__FILE__) . 'assets/css/style.css');



    wp_enqueue_script('bootstrap-min', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js', array('jquery'), '1.0.0', true);
  
    wp_enqueue_script('script-js', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '1.0.0', true);
    wp_localize_script(
        'script-js',
        'lucky_spin_wheel',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'group1' => $custom_form_data_group_1,
            'group2' => $custom_form_data_group_2,
            'group3' => $custom_form_data_group_3,
            'group4' => $custom_form_data_group_4,
            'group5' => $custom_form_data_group_5,
            'group6' => $custom_form_data_group_6,
        )
    );
}

include_once LSWD_PLUGINS_PATH . '/includes/admin/admin.php';
include_once LSWD_PLUGINS_PATH . '/includes/frontend/frontend.php';

if (is_admin() && defined('DOING_AJAX') && DOING_AJAX) {
    include_once LSWD_PLUGINS_PATH . '/ajax.php';
}

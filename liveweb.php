<?php
/*
Plugin Name: Liveweb
Plugin URI: https://liveweb.io
Description: WP Plugin for liveweb
Version: 1.1.0
Author: liveweb
Author URI: https://liveweb.io
*/

//Exit if accessed directly
defined('ABSPATH') or die("Restricted access!");

//Load  Scripts
require(plugin_dir_path(__FILE__) . '/includes/liveweb-scripts.php' );

function lwcw_register_head($hook) {
    //Load only on ?page=lwcw-plugin
    if ($hook !== 'toplevel_page_lwcw-plugin') {
        return;
    }
    wp_enqueue_style('bootstrap-css', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js');
    wp_enqueue_style('lwcw-css', plugin_dir_url(__FILE__) . 'css/lv-style.css');
}

add_action('admin_enqueue_scripts', 'lwcw_register_head');

//Register Widget
function lwcw_register_widget(){
    register_widget("LWCW_Admin");
}

//Hook in function
add_action('admin_menu','lwcw_plugin_setup_menu','widgets_init','lwcw_register_widget');

function  lwcw_plugin_setup_menu(){
    add_menu_page( 'LiveWeb Chat Widget Plugin', 'Live Web', 'manage_options', 'lwcw-plugin', 'lwcw_init' );
}

function lwcw_init() {

    $write = file_get_contents(plugin_dir_path(__FILE__) . '/js/settings.json' );
    $json = json_decode($write, true);
    $key = $json['api_key'];
    $api_key = esc_html($key);
    if($api_key === '') {
        echo "
            <div class='alert alert-danger alert-dismissible fade show my-1 mr-4'> Api key not found
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
        ";
    }
    else {
        echo " 
            <div class='api-added p-4 my-1 mr-4'> 
                <h6>Your API key is already added.</h6>
                <small>If you desire to change it, enter new value and submit.</small>
            </div>
        ";
    }

    if( isset($_GET['status']) && $api_key !== '') {

        echo "
        <div class='alert alert-info alert-dismissible fade show my-1 mr-4'> Api key is successfully added
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
               <span aria-hidden='true'>&times;</span>
            </button>
        </div>
        ";
    }

    echo "
    <div class='wrapper p-4 mr-4 mt-4'>
        <section class='section-1'>
            <div class='container'>
                <div class='row'>
                    <div class='col-12'>
                        <h3 class='title' style='color: #ffffff;'>Enter your API key</h3>
                        <p>If you can not find it, contact <strong>liveweb</strong> support channel.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class='section-2'>
            <div class='container'>
                <div class='row'>
                    <div class='col-12'>
                        <form class='form-group' action='./admin.php?page=lwcw-plugin&status=success' method='post' name='myForm' >
                            <div class='row'>
                                <div class='col-6'>
                                    <input class='form-control form-control-sm key-input' id='key' type='text' name='key' placeholder='Paste your API key here' value='". $api_key."' />
                                </div>
                                <div class='col-6'>
                                    <input class='btn btn-primary' type='submit' value='Submit'/>
                                    <a href='https://app.liveweb.io/settings/chat-widget' class='btn btn-primary text-white'>Go To Widget Settings</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    ";
}

//get the form elements and store them in variables
//Simple wordpress validation
if( isset($_POST['key'] ) ){
    if (empty($_POST['key'])) {
        $key_field = "";
        $error = "API key is required";

        $get_json = file_get_contents(plugin_dir_path(__FILE__) . '/js/settings.json' );

        $json = json_decode($get_json, true);
        $json['api_key'] = $key_field;
        $newJson = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents(plugin_dir_path(__FILE__) . '/js/settings.json', $newJson);

        $file = fopen(plugin_dir_path(__FILE__) . '/js/main.js', 'w');
        fwrite($file, 'window.livewebSettings = '. $newJson);
        fclose($file);
    }
    else {
        // Sanitize input for api_key
        $new_key = sanitize_text_field($_POST['key']);

        // Write api_key to settings.json file
        $write = file_get_contents(plugin_dir_path(__FILE__) . '/js/settings.json' );
        $json = json_decode($write, true);
        $json['api_key'] = $new_key;
        $newJson = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);
        file_put_contents(plugin_dir_path(__FILE__) . '/js/settings.json', $newJson);

        // Write settings.json to main.js file
        $file = fopen(plugin_dir_path(__FILE__) . '/js/main.js', 'w');
        fwrite($file, 'window.livewebSettings = '. $newJson);
        fclose($file);
    }
}
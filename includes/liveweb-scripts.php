<?php

//Add Scripts
function lwcw_add_scripts(){


    //Add Main CSS
    wp_enqueue_style('lwcw_main_style', plugin_dir_url(__FILE__). '../css/lv-style.css');

    //Add Main JS
    wp_enqueue_script('lwcw_main_script', plugin_dir_url(__FILE__). '../js/main.js', '', '', true);
    //Add scripts
    wp_register_script('liveweb-generator', 'https://proxy.liveweb.io/generator', '', '', true);
    wp_enqueue_script('liveweb-generator');

}

add_action('wp_enqueue_scripts', 'lwcw_add_scripts');


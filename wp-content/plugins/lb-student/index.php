<?php
/**
 * Plugin Name: Student addon
 * Description: Käsitööturg custom extension for student private posts
 * Version: 1.0
 */


class lbStudent{

    public static $errors = [];

	function __construct(){

        add_action('init', [$this, 'register_post_type']);

    }

    function register_post_type(){
    
        register_post_type('student_product',
            [
               'labels'      => [
                   'name'          => __('Student products'),
                   'singular_name' => __('Student product'),
               ],
               'public'      => true,
               'has_archive' => false,
            ]
        );

    }

}

new lbStudent();
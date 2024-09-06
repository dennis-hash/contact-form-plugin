<?php
    /**
     * Plugin Name: Contact Plugin
     * Description: Contact Plugin form
     * Author: Dennis
     * Text Domain: Simple-contact-form
     */


    if(!defined('ABSPATH')) {
        exit;
    }

if(!class_exists('SimpleContactForm')) {
    class SimpleContactForm
    {
        public function __construct()
        {
            define ('MY_PLUGIN_PATH',plugin_dir_path(__FILE__) );
            require_once (MY_PLUGIN_PATH . 'vendor/autoload.php');

        }

        public function initialize(){
            include_once MY_PLUGIN_PATH . 'includes/utilities.php';
            include_once MY_PLUGIN_PATH . 'includes/optionsPage.php';
            include_once MY_PLUGIN_PATH . 'includes/contactForm.php';
        }


    }
    $contactPlugin = new SimpleContactForm;
    $contactPlugin->initialize();

}



<?php

use Carbon_Fields\Field;
use Carbon_Fields\Container;


add_action('after_setup_theme', 'load_carbon_fields');
add_action('carbon_fields_register_fields', 'create_options_page');

function load_carbon_fields()
{
    Carbon_Fields\Carbon_Fields::boot();
}

function create_options_page(){
    Container::make( 'theme_options', __( 'Theme Options' ) )
        ->set_icon( 'dashicons-media-text' )
        ->add_fields( array(
            Field::make( 'checkbox', 'contact_plugin_inactive', __( 'Plugin is Active' ) )
                ->set_option_value( 'yes' ),

            Field::make( 'text', 'contact_plugin', __( 'Recipient Email' ) ),

        ) );
}

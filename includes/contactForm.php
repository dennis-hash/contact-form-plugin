<?php
add_shortcode('contact', 'showContactForm');
add_action('rest_api_init', 'create_rest_endpoint');
add_action('init', 'create_submissions_page');

function showContactForm() {
    include MY_PLUGIN_PATH . '/includes/templates/contact.php';
}

function create_rest_endpoint() {
    register_rest_route( 'v1/contact', 'submit', array(
        'methods' => 'POST',
        'callback' => 'create_contact_form',
    ));
}

function create_submissions_page()
{

    // Create the submissions post type to store form submissions

    $args = [

        'public' => true,
        'has_archive' => true,
        'menu_position' => 30,
        'publicly_queryable' => false,
        'labels' => [

            'name' => 'Submissions',
            'singular_name' => 'Submission',
            'edit_item' => 'View Submission'

        ],
        'supports' => false,
        'capability_type' => 'post',
        'capabilities' => array(
            'create_posts' => false,
        ),
        'map_meta_cap' => true
    ];

    register_post_type('submission', $args);
}

function create_contact_form($data) {
    $params = $data->get_params();

    if(!wp_verify_nonce($params['nonce'], 'wp-rest')) {
        return new WP_Rest_Response('Message not sent', 422);
    }

    // Remove unneeded data from paramaters
    unset($params['_wpnonce']);
    unset($params['_wp_http_referer']);


    //send email
    // Send the email message
    $headers = [];

    $admin_email = get_bloginfo('admin_email');
    $admin_name = get_bloginfo('name');

    // Set recipient email
    $recipient_email = get_plugin_options('contact_plugin_recipients');

    if (!$recipient_email) {
        // Make all lower case and trim out white space
        $recipient_email = strtolower(trim($recipient_email));
    } else {

        // Set admin email as recipient email if no option has been set
        $recipient_email = $admin_email;
    }


    $headers[] = "From: {$admin_name} <{$admin_email}>";
    $headers[] = "Reply-to: {$params['name']} <{$params['email']}>";
    $headers[] = "Content-Type: text/html";

    $subject = "New enquiry from {$params['name']}";

    $message = '';
    $message = "<h1>Message has been sent from {$params['name']}</h1>";


    $postarr = [

        'post_title' => $params['name'],
        'post_type' => 'submission',
        'post_status' => 'publish'

    ];

    $post_id = wp_insert_post($postarr);

    // Loop through each field posted and sanitize it
    foreach ($params as $label => $value) {

        switch ($label) {

            case 'message':

                $value = sanitize_textarea_field($value);
                break;

            case 'email':

                $value = sanitize_email($value);
                break;

            default:

                $value = sanitize_text_field($value);
        }

        add_post_meta($post_id, sanitize_text_field($label), $value);

        $message .= '<strong>' . sanitize_text_field(ucfirst($label)) . ':</strong> ' . $value . '<br />';
    }
    wp_mail($recipient_email, $subject, $message, $headers);

    $confirmation_message = "The message was sent successfully!!";

    if (get_plugin_options('contact_plugin_message')) {

        $confirmation_message = get_plugin_options('contact_plugin_message');

        $confirmation_message = str_replace('{name}', $params['name'], $confirmation_message);
    }

    return new WP_Rest_Response($confirmation_message, 200);

}
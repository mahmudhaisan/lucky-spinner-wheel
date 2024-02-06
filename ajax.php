<?php


// Add this action to handle the AJAX request
add_action('wp_ajax_save_winning_segment', 'save_winning_segment');
add_action('wp_ajax_nopriv_save_winning_segment', 'save_winning_segment'); // For non-logged-in users

function save_winning_segment() {
    if (isset($_POST['winningSegment'])) {
        $winning_segment = sanitize_text_field($_POST['winningSegment']);
        // For example, save it as user meta
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'winning_segment', $winning_segment);
        wp_send_json_success('Winning segment saved successfully.');
    } else {
        wp_send_json_error('Missing winning segment data.');
    }
    wp_die();
}

<?php


// Add this action to handle the AJAX request
add_action('wp_ajax_save_winning_segment', 'save_winning_segment');
add_action('wp_ajax_nopriv_save_winning_segment', 'save_winning_segment'); // For non-logged-in users

function save_winning_segment()
{
    if (isset($_POST['winningSegment'])) {
        $winning_segment = $_POST['winningSegment'];

        // json_encode($winning_segment);
        // For example, save it as user meta
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'winning_segment', $winning_segment);
        wp_send_json_success('Winning segment saved successfully.');
    } else {
        wp_send_json_error('Missing winning segment data.');
    }
    wp_die();
}




add_action('wp_ajax_get_cart_info', 'get_cart_info');

function get_cart_info()
{
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        wp_send_json_error('WooCommerce is not active.');
    }

    // Get current user ID
    $current_user_id = get_current_user_id();

    // Get user's winning segment
    $winning_segment = get_user_meta($current_user_id, 'winning_segment', true);
    $winning_segment_type = isset($winning_segment['type']) ? $winning_segment['type'] : null;
    $winning_segment_amount = isset($winning_segment['amount']) ? intval($winning_segment['amount']) : 0;

    // Get conditions
    $min_cart_amount = intval(get_option('min_cart_amount'));
    $selected_categories_options = get_option('selected_categories_options');
    $min_product_count = intval(get_option('min_product_count'));
    $spin_expiry_day = intval(get_option('spin_expiry'));

    // Get cart contents
    $cart = WC()->cart->get_cart();

    // Calculate total product price in cart
    $total_product_price = 0;
    foreach ($cart as $item) {
        $total_product_price += floatval($item['data']->get_price()) * $item['quantity'];
    }

    // Check if any product from selected categories is in cart
    $selected_category_product_in_cart = false;
    foreach ($cart as $item) {
        $product_id = $item['product_id'];
        $product_categories = get_the_terms($product_id, 'product_cat');
        foreach ($product_categories as $category) {
            if (in_array($category->term_id, $selected_categories_options)) {
                $selected_category_product_in_cart = true;
                break 2; // Exit both loops
            }
        }
    }

    // Get total products in cart from selected categories
    $total_products_in_selected_categories = 0;
    foreach ($cart as $item) {
        $product_id = $item['product_id'];
        $product_categories = get_the_terms($product_id, 'product_cat');
        foreach ($product_categories as $category) {
            if (in_array($category->term_id, $selected_categories_options)) {
                $total_products_in_selected_categories += $item['quantity'];
                break; // Exit inner loop
            }
        }
    }

    // Prepare data to be sent in the AJAX response
    $data = array(
        'cart' => $cart,
        'winning_segment_type' => $winning_segment_type,
        'min_cart_amount' => $min_cart_amount,
        'selected_categories_options' => $selected_categories_options,
        'min_product_count' => $min_product_count,
        'spin_expiry_day' => $spin_expiry_day,
        'total_product_price' => $total_product_price,
        'selected_category_product_in_cart' => $selected_category_product_in_cart,
        'total_products_in_selected_categories' => $total_products_in_selected_categories
    );

    // Return cart data
    wp_send_json_success($data);
    wp_die();
}


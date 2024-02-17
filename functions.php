<?php 


function wheel_cart_conditions_values(){
     // Check if WooCommerce is active
     if (!class_exists('WooCommerce')) {
        wp_send_json_error('WooCommerce is not active.');
    }

    // Get current user ID
    $current_user_id = get_current_user_id();
    // Get user's winning segment
    $winning_segment = get_user_meta($current_user_id, 'winning_segment', true);
    $winning_segment_type = isset($winning_segment['type']) ? $winning_segment['type'] : null;

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

    $cart_empty = WC()->cart->is_empty();


    $check_category_condition = check_category_condition();


    // Prepare data to be sent in the AJAX response
    $data = array(
        'winning_segment_type' => $winning_segment_type,
        'min_cart_amount' => $min_cart_amount,
        'selected_categories_options' => $selected_categories_options,
        'min_product_count' => $min_product_count,
        'spin_expiry_day' => $spin_expiry_day,
        'total_product_price' => $total_product_price,
        'selected_category_product_in_cart' => $selected_category_product_in_cart,
        'total_products_in_selected_categories' => $total_products_in_selected_categories,
        'cart_empty' => $cart_empty,
        'check_category_condition' => $check_category_condition,
    );


    return $data;
}




function get_cart_product_categories_info() {
    // Initialize an empty array to store category info
    $category_info = array();

    // Get WooCommerce cart instance
    $cart = WC()->cart;

    // Loop through cart items
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        // Get product ID
        $product_id = $cart_item['product_id'];

        // Get product categories
        $product_categories = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

        // Loop through product categories
        foreach ( $product_categories as $category_id ) {
            // Add product count to category info
            if ( isset( $category_info[$category_id] ) ) {
                $category_info[$category_id] += $cart_item['quantity'];
            } else {
                $category_info[$category_id] = $cart_item['quantity'];
            }
        }
    }

    // Return category info
    return $category_info;
}






function check_category_condition() {

    $category_options_info = category_options_data();
    $cart_category_info = get_cart_product_categories_info();


    // Check if any category option value is less than the corresponding cart value
    $category_condition_found = false;

    foreach ($category_options_info as $category_id => $option_value) {
        if (isset($cart_category_info[$category_id]) && $option_value < $cart_category_info[$category_id]) {
            $category_condition_found = true;
            break; // Found a match, no need to continue looping
        }
    }

    return $category_condition_found;
}






function category_options_data() {
    $category_option_condition = get_option('cat_amount_condition');
    $category_totals = array();

    // Calculate total amounts for each category from $category_option_condition
    foreach ($category_option_condition as $item) {
        $category_id = $item['category'];
        $amount = $item['amount'];

        if (isset($category_totals[$category_id])) {
            $category_totals[$category_id] += $amount;
        } else {
            $category_totals[$category_id] = $amount;
        }
    }

    // Remove duplicate category IDs
    $category_options_info = array_unique($category_totals);

    return $category_options_info;
}




function apply_winning_segment_discount() {
    // Get current user ID
    $current_user_id = get_current_user_id();

    // Get user's winning segment
    $winning_segment = get_user_meta($current_user_id, 'winning_segment', true);

    // Extract winning segment type and amount
    $winning_segment_type = isset($winning_segment['type']) ? $winning_segment['type'] : null;
    $winning_segment_amount = isset($winning_segment['amount']) ? intval($winning_segment['amount']) : 0;

    // Check if the winning segment type is 'fixed_discount'
    if ($winning_segment_type == 'fixed_discount') {
        // Apply the percentage discount based on the winning segment amount
        $percentage_discount = $winning_segment_amount / 100; // Convert the amount to a percentage
        $cart_total = WC()->cart->subtotal; // Get the cart subtotal

        // Calculate the discount amount
        $discount_amount = $cart_total * $percentage_discount;

        // Apply the discount
        WC()->cart->add_fee('Discount', -$discount_amount);
    }
}
// Define a function to get all product IDs and names, including uncategorized products
function get_all_product_ids_and_names() {
    // Initialize an empty array to store product data
    $products_data = array();

    // Query WooCommerce for all products, including uncategorized products
    $args = array(
        'post_type' => 'product', // Specify post type as product
        'posts_per_page' => -1, // Retrieve all products
        
    );

    $products_query = new WP_Query($args);

    // Loop through each product to retrieve ID and name
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $product_data = array(
                'id' => get_the_ID(),
                'name' => get_the_title(),
            );
            $products_data[] = $product_data;
        }
        wp_reset_postdata(); // Reset the post data
    }

    // Return the array containing product IDs and names
    return $products_data;
}

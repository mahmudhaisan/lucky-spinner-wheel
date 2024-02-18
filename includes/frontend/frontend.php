<?php

// Function to add the gift button with spin result
function add_gift_button()
{


    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

        // conditions part
        $cart_conditions_data = wheel_cart_conditions_values();
        $cart_category_info = get_cart_product_categories_info();


        $winning_segment_option = $cart_conditions_data['winning_segment_type'];
        $min_cart_amount = isset($cart_conditions_data['min_cart_amount']) ? intval($cart_conditions_data['min_cart_amount']) : 0;
        $selected_categories_options = $cart_conditions_data['selected_categories_options'];
        $min_product_count = isset($cart_conditions_data['min_product_count']) ? intval($cart_conditions_data['min_product_count']) : 0;
        $spin_expiry_day = $cart_conditions_data['spin_expiry_day'];
        $total_product_price = isset($cart_conditions_data['total_product_price']) ? intval($cart_conditions_data['total_product_price']) : 0;
        // Check if cart amount or product count meets the conditions
        $winning_segment_value = get_user_meta($user_id, 'winning_segment', true);

        $category_option_condition = get_option('cat_amount_condition');
        $category_condition_result = check_category_condition();



?>

        <div id="popup-overlay" class="spin-wheel-row" style="display: none;">
            <div id="popup-container">
                <div id="popup-content">
                    <button id="popup-close-btn"><i class="fa fa-times" aria-hidden="true"></i></button>

                    <?php if ($winning_segment_value) {
                        $winning_segment_display_text = isset($winning_segment_value['displayText']) ? $winning_segment_value['displayText'] : '';
                        $winning_segment_type = isset($winning_segment_value['type']) ? $winning_segment_value['type'] : '';
                        $winning_segment_amount = isset($winning_segment_value['amount']) ? $winning_segment_value['amount'] : '';
                        $winning_segment_text = isset($winning_segment_value['text']) ? $winning_segment_value['text'] : '';

                        switch ($winning_segment_type) {
                            case 'fixed_discount':
                                $selected_spin_segment_number = $winning_segment_amount;
                                break;
                            case 'percentage_discount':
                                $selected_spin_segment_number = $winning_segment_amount . '%';
                                break;
                            case 'free_product':
                                $selected_spin_segment_number = $winning_segment_amount;
                                break;
                            case 'no_luck':
                            case 'add_another_spin':
                                $selected_spin_segment_number = '';
                                break;
                            default:
                                // Handle unexpected segment types
                                break;
                        }

                        // Assign common text value for all segment types
                        $selected_spin_segmant_text = $winning_segment_text;

                    ?>
                        <div id="spin-wheel-content" style="display: none;">
                            <div id="wheel">
                                <div class="indicator">
                                    <img src="<?php echo LSWD_PLUGINS_DIR_URL . 'assets/icons/indicator.png'; ?>" alt="Example Image">
                                </div>
                                <canvas id="canvas" width="500" height="500"></canvas>
                                <button id="spin" class="spin-wheel-btn">Spin</button>
                            </div>

                            <div class="add_new_spin_chance">

                            </div>

                            <!-- Updated Bootstrap 5 "Spin Now" button -->
                            <button id="spin-now-btn" class="btn btn-primary rounded-pill py-2 px-4 spin-wheel-btn w-100 mt-3">
                                Spin Now
                            </button>
                        </div>
                        <div class="container">
                            <div id="spin-result-content" class="bg-dark text-white p-5 rounded mt-3">
                                <h1 class="display-6 text-dark">You just hit the jackpot!!</h1>
                                <div id="show-spin-result" class=" mb-3">
                                    <div class="container mt-2 p-3 text-center">
                                        <p class="display-4 mb-0 fw-bold"><?php echo  $selected_spin_segment_number; ?></p>
                                        <p class="display-5 mb-0"><?php echo   $selected_spin_segmant_text; ?></p>
                                    </div>

                                </div>
                                <p class="h4 text-dark">Ends on:
                                    <span class="text-warning" id="countdown">
                                    </span>
                                </p>

                                <button class="btn btn-warning rounded-pill py-2 px-4 w-100 mt-3">
                                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>">Get It Now</a>
                                </button>
                            </div>
                        </div>


                    <?php }; ?>




                    <!-- show on first -->
                    <?php if (!$winning_segment_value) : ?>
                        <div id="spin-wheel-content">
                            <div id="wheel">
                                <div class="indicator">
                                    <img src="<?php echo LSWD_PLUGINS_DIR_URL . 'assets/icons/indicator.png'; ?>" alt="Example Image">
                                </div>
                                <canvas id="canvas" width="500" height="500"></canvas>
                                <button id="spin" class="spin-wheel-btn">Spin</button>
                            </div>

                            <div class="add_new_spin_chance">

                            </div>

                            <!-- Updated Bootstrap 5 "Spin Now" button -->
                            <button id="spin-now-btn" class="btn btn-primary rounded-pill py-2 px-4 spin-wheel-btn w-100 mt-3">
                                Spin Now
                            </button>
                        </div>


                        <div class="container updated-segmant-data">

                        </div>

                        <div class="show-result-content">

                        </div>


                    <?php endif; ?>
                </div>
            </div>
        </div>








        <div class="gift-button-wrapper">




            <?php



            if ($total_product_price >= $min_cart_amount && $category_condition_result)

                echo '<div id="gift-button" style="display: block;">You have a gift 🎁</div>';


            ?>

        </div>


<?php }
}


add_action('wp_footer', 'add_gift_button');


// Update user meta value to empty when an order is placed
function update_user_meta_on_order_placement($order_id)
{
    $user_id = get_current_user_id();

    // Check if the user ID is valid and the order ID is valid
    if ($user_id && $order_id) {


        // Update the user meta value to empty
        update_user_meta($user_id, 'winning_segment', '');
        update_user_meta($user_id, 'winning_segment_updated', '');
    }
}
add_action('woocommerce_new_order', 'update_user_meta_on_order_placement', 10, 1);










add_action('woocommerce_cart_calculate_fees', 'apply_discount_fee');
function apply_discount_fee()
{
    // Your code to calculate and apply the fee

    // Get current user ID
    $current_user_id = get_current_user_id();
    // Get user's winning segment
    $winning_segment = get_user_meta($current_user_id, 'winning_segment', true);

    // Extract winning segment type and amount
    $winning_segment_type = isset($winning_segment['type']) ? $winning_segment['type'] : null;
    $winning_segment_amount = isset($winning_segment['amount']) ? intval($winning_segment['amount']) : 0;
    $winning_segment_free_product_id = isset($winning_segment['free_product_id']) ? intval($winning_segment['free_product_id']) : 0;

    $cart_conditions_data = wheel_cart_conditions_values();
    $category_condition_result = check_category_condition();


    $total_product_price = isset($cart_conditions_data['total_product_price']) ? intval($cart_conditions_data['total_product_price']) : 0;
    $min_cart_amount = isset($cart_conditions_data['min_cart_amount']) ? intval($cart_conditions_data['min_cart_amount']) : 0;


    if ($category_condition_result && $total_product_price >= $min_cart_amount) {

        if ($winning_segment_type == 'fixed_discount') {
            $fixed_discount_amount = $winning_segment_amount; // Convert the amount to a percentage 
            // Apply the discount
            WC()->cart->add_fee('Discount', -$fixed_discount_amount);
            $cart_total_with_discount = WC()->cart->total; // Get the cart total with discount
        }

        if ($winning_segment_type == 'percentage_discount') {
            $discount_percentage = $winning_segment_amount / 100; // Convert the amount to a percentage 
            // Calculate the discount amount
            $discount_amount = WC()->cart->get_subtotal() * $discount_percentage;
            // Apply the discount
            WC()->cart->add_fee('Discount', -$discount_amount);
        }
    }
}







add_action('woocommerce_before_calculate_totals', 'my_custom_cart_items_raw_output');

function my_custom_cart_items_raw_output($cart_object)
{

    foreach ($cart_object->get_cart() as $item) {

        if (array_key_exists('custom_price', $item)) {
            $item['data']->set_price($item['custom_price']);
        }
    }
}










add_action('woocommerce_before_calculate_totals', 'check_and_update_cart_item', 10, 1);

function check_and_update_cart_item($cart)
{



    // Get current user ID
    $current_user_id = get_current_user_id();
    // Get user's winning segment
    $winning_segment = get_user_meta($current_user_id, 'winning_segment', true);

    $found = false;
    // Extract winning segment type and amount
    $winning_segment_type = isset($winning_segment['type']) ? $winning_segment['type'] : null;
    $winning_segment_amount = isset($winning_segment['amount']) ? intval($winning_segment['amount']) : 0;
    $winning_segment_free_product_id = isset($winning_segment['free_product_id']) ? intval($winning_segment['free_product_id']) : 0;
    $cart_conditions_data = wheel_cart_conditions_values();
    $category_condition_result = check_category_condition();
    $total_product_price = isset($cart_conditions_data['total_product_price']) ? intval($cart_conditions_data['total_product_price']) : 0;
    $min_cart_amount = isset($cart_conditions_data['min_cart_amount']) ? intval($cart_conditions_data['min_cart_amount']) : 0;


    if ($category_condition_result && $total_product_price >= $min_cart_amount) {
        if ($winning_segment_type == 'free_product') {

            // Check if the product with ID 23 and zero price is already in the cart
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                if ($cart_item['product_id'] == $winning_segment_free_product_id && $cart_item['data']->get_price() == 0) {
                    $found = true;

                    $cart->cart_contents[$cart_item_key]['quantity'] = 1;

                    break; // Stop the loop
                }
            }

            // If the product was not found, add it to the cart
            if (!$found) {
                wc()->cart->add_to_cart($winning_segment_free_product_id, 1, 0, array(), array('custom_price' => 0));
            }
        }

        if ($winning_segment_type == 'add_another_spin') {
            update_user_meta($current_user_id, 'winning_segment', '');
            update_user_meta($current_user_id, 'winning_segment_updated', '');
        }
    } else {

        // Check if the product with ID 23 and zero price is already in the cart
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $winning_segment_free_product_id && $cart_item['data']->get_price() == 0) {
                $found = true;

                unset($cart->cart_contents[$cart_item_key]);

                break; // Stop the loop
            }
        }
    }
}





// Function to retrieve event types from Paddle API using wp_remote_get
function get_paddle_event_types()
{
    $api_url = 'https://sandbox-api.paddle.com/event-types';
    $post_api_url = 'https://sandbox-api.paddle.com/transactions/preview';
    $product_data_url = 'https://sandbox-api.paddle.com/products';


    $access_token = '27b507c8a7fc7b146cea2c706fe3299c14ad770c341e9c134e';

    // Setup GET request arguments
    $get_args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $access_token
        )
    );

    // Corrected $post_data array
    $post_data = array(
        'items' => array(
            array(
                'quantity' => 20,
                'price' => array(
                    'description' => 'Digital',
                    'unit_price' => array(
                        'amount' => '10',
                        'currency_code' => 'USD',
                    ),
                    'product' => array(
                        'name' => 'digital good',
                        'tax_category' => 'digital-goods'
                    )
                )
            )
        ),
    );





    // Setup POST request arguments
    $post_args = array(
        'body' => json_encode($post_data),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token
        )
    );



    // Setup POST request arguments
    $post_args = array(
        'body' => json_encode($post_data),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $access_token
        )
    );



    // Make the GET request
    $get_response = wp_remote_get($api_url, $get_args);

    // Make the POST request
    $post_response = wp_remote_post($post_api_url, $post_args);

    // Check for errors in GET request
    if (is_wp_error($get_response)) {
        return $get_response;
    }

    // Check for errors in POST request
    if (is_wp_error($post_response)) {
        return $post_response;
    }

    // Parse JSON response of GET request
    $get_body = wp_remote_retrieve_body($get_response);
    $get_data = json_decode($get_body, true);

    // Parse JSON response of POST request
    $post_body = wp_remote_retrieve_body($post_response);
    $post_data = json_decode($post_body, true);

    // Return data
    return array(
        'get_response' => $get_data,
        'post_response' => $post_data
    );
}









// Shortcode to display event types
function display_paddle_event_types()
{
    $data = get_paddle_event_types();

    if (is_wp_error($data)) {
        return 'Error: ' . $data->get_error_message();
    }

    echo '<h2>GET Response</h2>';
    echo '<pre>';
    print_r($data['get_response']);
    echo '</pre>';

    echo '<h2>POST Response</h2>';
    echo '<pre>';
    print_r($data['post_response']);
    echo '</pre>';
}
add_shortcode('paddle_event_types', 'display_paddle_event_types');

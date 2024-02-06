<?php

// Function to add the gift button with spin result
function add_gift_button()
{
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $cart_amount_threshold = 0; // Set your cart amount threshold
        $required_product_count = 5; // Set the required number of products
        $required_categories = array('category1', 'category2'); // Add your required category slugs

        // Check if cart amount or product count meets the conditions
        if (is_cart_conditions_met($cart_amount_threshold, $required_product_count, $required_categories)) {
            $winning_segment_value = get_user_meta($user_id, 'winning_segment', true);
            ?>

            <div id="popup-overlay" class="spin-wheel-row" style="display: none;">
                <div id="popup-container">
                    <div id="popup-content">
                        <button id="popup-close-btn"><i class="fa fa-times" aria-hidden="true"></i></button>

                        <?php if ($winning_segment_value) : ?>

                            <div id="spin-result-content" class="bg-dark text-white p-4 rounded mt-3">
                                <h1 class="display-4">You just hit the jackpot!</h1>
                                <div id="show-spin-result" class="mb-3 display-4"><?php echo  $winning_segment_value; ?></div>
                                <p class="h4">Ends on: <span class="text-warning">[Your Date and Time]</span></p>
                                <button class="btn btn-warning rounded-pill py-2 px-4 w-100 mt-3">
                                    Get It Now
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (!$winning_segment_value) : ?>
                            <div id="spin-wheel-content">
                                <div id="wheel">
                                    <div class="indicator">
                                        <img src="<?php echo LSWD_PLUGINS_DIR_URL . 'assets/icons/indicator.png'; ?>" alt="Example Image">
                                    </div>
                                    <canvas id="canvas" width="500" height="500"></canvas>
                                    <button id="spin" class="spin-wheel-btn">Spin</button>
                                </div>

                                <!-- Updated Bootstrap 5 "Spin Now" button -->
                                <button id="spin-now-btn" class="btn btn-primary rounded-pill py-2 px-4 spin-wheel-btn w-100 mt-3">
                                    Spin Now
                                </button>
                            </div>

                            <div id="spin-result-content" class="bg-dark text-white p-4 rounded mt-3" style="display: none;">
                                <h1 class="display-4">You just hit the jackpot!</h1>
                                <div id="show-spin-result" class="mb-3 display-4"></div>
                                <p class="h4">Ends on: <span class="text-warning">[Your Date and Time]</span></p>
                                <button class="btn btn-warning rounded-pill py-2 px-4 w-100 mt-3">
                                    Get It Now
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="gift-button" style="display: block;">You have a gift 🎁</div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var giftButton = document.getElementById('gift-button');
                    var spinNowBtn = document.getElementById('spin-now-btn');

                    // Show/hide gift button based on cart conditions
                    if (is_cart_conditions_met(<?php echo $cart_amount_threshold; ?>, <?php echo $required_product_count; ?>, <?php echo json_encode($required_categories); ?>)) {
                        giftButton.style.display = 'block';
                    } else {
                        giftButton.style.display = 'none';
                    }

                    // Add event listener to spin now button
                    spinNowBtn.addEventListener('click', function () {
                        // Show the spin wheel popup
                        document.getElementById('popup-overlay').style.display = 'block';
                    });
                });
            </script>

        <?php }
    }
}
add_action('wp_footer', 'add_gift_button');

// Function to check cart conditions
function is_cart_conditions_met($cart_amount_threshold, $required_product_count, $required_categories)
{
    // Check if cart amount is greater than or equal to the threshold
    if (WC()->cart->total >= $cart_amount_threshold) {
        return true;
    }

    // Check if the required number of products from specific categories are in the cart
    $product_count_in_categories = 0;

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];
        $product_categories = get_the_terms($product_id, 'product_cat');

        if ($product_categories) {
            foreach ($product_categories as $category) {
                if (in_array($category->slug, $required_categories)) {
                    $product_count_in_categories++;
                    break; // Break out of the inner loop once a match is found for the current product
                }
            }
        }
    }

    return $product_count_in_categories >= $required_product_count;
}

// Update user meta value to empty when an order is placed
function update_user_meta_on_order_placement($order_id) {
    $user_id = get_current_user_id();

    // Check if the user ID is valid and the order ID is valid
    if ($user_id && $order_id) {
        // Update the user meta value to empty
        update_user_meta($user_id, 'winning_segment', '');
    }
}
add_action('woocommerce_new_order', 'update_user_meta_on_order_placement', 10, 1);
?>

<?php


add_action('wp_ajax_add_single_carpet_product_to_cart', 'add_single_carpet_product_to_cart');
add_action('wp_ajax_nopriv_add_single_carpet_product_to_cart', 'add_single_carpet_product_to_cart');

function add_single_carpet_product_to_cart()
{
    // Get product ID and quantity from the AJAX request
    $product_id = intval($_POST['carpetSingleProductId']);
    $product_qty = intval($_POST['productItemQty']);



    if (is_in_cart($product_id)) {


        remove_cart_item_by_product_id($product_id);

        // Product is not in the cart, add it with the specified quantity
        WC()->cart->add_to_cart($product_id, $product_qty);
    } else {
     
        // Product is not in the cart, add it with the specified quantity
        WC()->cart->add_to_cart($product_id, 1);
    }



    echo WC()->cart->get_subtotal();
    

    wp_die();
}



function is_in_cart( $ids ) {
    // Initialise
    $found = false;

    // Loop through cart items
    foreach( WC()->cart->get_cart() as $cart_item ) {
        // For an array of product IDs
        if( is_array($ids) && ( in_array( $cart_item['product_id'], $ids ) || in_array( $cart_item['variation_id'], $ids ) ) ){
            $found = true;
            break;
        }
        // For a unique product ID (integer or string value)
        elseif( ! is_array($ids) && ( $ids == $cart_item['product_id'] || $ids == $cart_item['variation_id'] ) ){
            $found = true;
            break;
        }
    }

    return $found;
}


function remove_cart_item_by_product_id( $product_id ) {
    // Get cart contents
    $cart = WC()->cart->get_cart();

    // Loop through cart items
    foreach ( $cart as $cart_item_key => $cart_item ) {
        // Check if the product ID matches
        if ( $product_id == $cart_item['product_id'] ) {
            // Remove the cart item
            WC()->cart->remove_cart_item( $cart_item_key );
            break; // Exit the loop after removing the item
        }
    }
}



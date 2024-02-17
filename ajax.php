<?php


// Add this action to handle the AJAX request
add_action('wp_ajax_save_winning_segment', 'save_winning_segment');
add_action('wp_ajax_nopriv_save_winning_segment', 'save_winning_segment'); // For non-logged-in users

function save_winning_segment()
{
    if (isset($_POST['winningSegment'])) {
        $winning_segment = $_POST['winningSegment'];
        $updated_expiry_time = $_POST['updatedExpiryTime'];
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'winning_segment', $winning_segment);
        update_user_meta($user_id, 'winning_segment_updated', $updated_expiry_time);
    }

    $user_id = get_current_user_id();

    // Retrieve winning segment value
    $winning_segment_value = get_user_meta($user_id, 'winning_segment', true);

    if ($winning_segment_value) {
        $winning_segment_display_text = $winning_segment_value['displayText'];
        $winning_segment_type = isset($winning_segment['type']) ? $winning_segment['type'] : null;
        $winning_segment_amount = isset($winning_segment['amount']) ? intval($winning_segment['amount']) : 0;
        $winning_segment_text = $winning_segment_value['text'];

        switch ($winning_segment_type) {
            case 'fixed_discount':
                $selected_spin_segment_number = $winning_segment_amount;

                apply_winning_segment_discount();

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

        // Construct HTML markup
        $html = '<div id="spin-result-content" class="bg-dark text-white p-5 rounded mt-3">';
        $html .= '<h1 class="display-6 text-dark">You just hit the jackpot!!</h1>';
        $html .= '<div id="show-spin-result" class=" mb-3">';
        $html .= '<div class="container mt-2 p-3 text-center">';
        $html .= '<p class="display-4 mb-0 fw-bold">' . $selected_spin_segment_number . '</p>';
        $html .= '<p class="display-5 mb-0">' . $selected_spin_segmant_text . '</p>';
        $html .= '</div></div>';
        $html .= '<p class="h4 text-dark">Ends on:';
        $html .= '<span class="text-warning" id="countdown"></span></p>';
        $html .= '<button class="btn btn-warning rounded-pill py-2 px-4 w-100 mt-3">Get It Now</button>';
        $html .= '</div>';

        // Echo HTML markup
        echo $html;
    }

    // Terminate WordPress execution
    wp_die();
}




add_action('wp_ajax_get_cart_info', 'get_cart_info');



function get_cart_info()
{

    $data = wheel_cart_conditions_values();

   

    // Return cart data
    wp_send_json_success($data);
    wp_die();
}




// Add AJAX action to retrieve countdown time from the database
add_action('wp_ajax_get_countdown_time', 'get_countdown_time');


function get_countdown_time()
{


    $current_user_id = get_current_user_id();


    $winning_segment_time = get_user_meta($current_user_id, 'winning_segment_updated', true);

    if (isset($winning_segment_time)) {
        $winning_segment_time = intval($winning_segment_time);
    } else {
        $winning_segment_time = 0;
    }

    // Send countdown time as JSON response
    wp_send_json_success(array('winning_segment_time' => $winning_segment_time));
}








function add_category_select()
{
    // Retrieve product categories
    $categories = get_terms('product_cat', array('hide_empty' => false));
    $cat_amount = get_option('cat_amount_condition');

    foreach ($cat_amount as $cat) {
        echo '<div class="row mb-3">';
        echo '<div class="col-2">';
        echo '<label for="categoryInput" class="col-form-label">Product Category:</label>';
        echo '</div>';
        echo '<div class="col-2">';

        // Output select options
        echo '<select class="form-control" name="categories[]">';
        foreach ($categories as $category) {
            $selected = $cat['category'] == $category->term_id ? 'selected="selected"' : '';
            echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '<div class="col-2">';
        echo '<input type="number" class="form-control cat_amount" name="cat_amount[]" placeholder="Amount" value="' . $cat['amount'] . '">';
        echo '</div>';
        echo '<div class="col-2">';
        echo '<button class="add-row"><i class="fas fa-plus"></i></button>';
        echo '<button class="remove-row"><i class="fas fa-minus"></i></button>';
        echo '</div>';
        echo '</div>'; // Remove the redundant closing </div>
    }

    wp_die();
}
add_action('wp_ajax_add_category_select', 'add_category_select');

function remove_category_row()
{
    if (isset($_POST['row_id'])) {
        // Code to remove the row from the database
        echo 'success';
    }

    wp_die();
}
add_action('wp_ajax_remove_category_row', 'remove_category_row');

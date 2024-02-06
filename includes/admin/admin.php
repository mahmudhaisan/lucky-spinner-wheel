<?php
// Add a custom menu page to the admin menu
function custom_admin_menu()
{
    add_menu_page(
        'Spin Wheel Parts',
        'Spin Wheel Parts',
        'manage_options',
        'spin-wheel-settings',
        'render_spin_wheel_settings_page',
        '',
        20
    );

    // Add submenus
    add_submenu_page(
        'spin-wheel-settings',
        'Spinner General Settings',
        'Spinner General Settings',
        'manage_options',
        'spin-wheel-general-settings',
        'render_spin_wheel_general_settings'
    );

    add_submenu_page(
        'spin-wheel-settings',
        'Submenu Page 2',
        'Submenu Page 2',
        'manage_options',
        'spin-wheel-submenu-page-2',
        'render_spin_wheel_submenu_page_2'
    );
}
add_action('admin_menu', 'custom_admin_menu');

// Render the settings page using Bootstrap 5
function render_spin_wheel_settings_page()
{

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Loop through each group
        for ($i = 1; $i <= 6; $i++) {
            $selectKey = 'group' . $i . '_select';
            $amountKey = 'group' . $i . '_amount';
            $textKey = 'group' . $i . '_text';
            $colorKey = 'group' . $i . '_color';

            // Get values from the form
            $selectValue = sanitize_text_field($_POST[$selectKey]);


            $amountValue = sanitize_text_field($_POST[$amountKey]);
            $textValue = sanitize_text_field($_POST[$textKey]);
            $colorValue = sanitize_text_field($_POST[$colorKey]);


            $display_text = $amountValue . ' ' .  $textValue;

            // Save to WordPress options
            $option_name = 'custom_form_data_group_' . $i;

            $updated = update_option($option_name, array(
                'displayText' => $display_text,
                'type' => $selectValue,
                'amount' => $amountValue,
                'text' => $textValue,
                'color' => $colorValue
            ));
        }



        echo '<div class="container mt-5">';
        echo '<div class="alert alert-success" role="alert">Options updated successfully!</div>';
        echo '</div>';
    }


?>

    <div class="container mt-5">
        <form method="post" action="">
            <h3 class=" mb-3">Setup Wheel Parts</h3>
            <?php
            $options = array(
                'fixed_discount' => 'Fixed Discount',
                'percentage_discount' => 'Percentage Discount',
                'free_product' => 'Free Product',
                'no_luck' => 'No Luck',
                'add_another_spin' => 'Add Another Spin',
            );

            // Loop through each group
            for ($i = 1; $i <= 6; $i++) {
                $get_option_val = get_option('custom_form_data_group_' . $i);

                // print_r($get_option_val);

            ?>


                <div class="row mb-3">
                    <label for="group<?php echo $i; ?>_select" class="col-sm-2 col-form-label">Spin <?php echo $i; ?>:</label>

                    <div class="col">

                        <select class="form-select" id="group<?php echo $i; ?>_select" name="group<?php echo $i; ?>_select" onchange="toggleAmountField('group<?php echo $i; ?>')">
                            <?php
                            foreach ($options as $key => $value) {
                                $selected = ($get_option_val && $get_option_val['select'] === $key) ? 'selected' : '';
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="group<?php echo $i; ?>_text" name="group<?php echo $i; ?>_text" placeholder="Text" value="<?php echo esc_attr($get_option_val['text']); ?>">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="group<?php echo $i; ?>_amount" name="group<?php echo $i; ?>_amount" placeholder="Amount" value="<?php echo esc_attr($get_option_val['amount']); ?>">
                    </div>

                    <div class="col">
                        <input type="Color" class="form-control form-control-color" id="group<?php echo $i; ?>_color" name="group<?php echo $i; ?>_color" placeholder="Color" value="<?php echo esc_attr(empty($get_option_val['color']) ? '#000' : $get_option_val['color']); ?>">

                    </div>


                </div>
            <?php
            }
            ?>
            <!-- Submit button -->
            <div class="row">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>

<?php }


// Callback functions for submenus
function render_spin_wheel_general_settings()
{ ?>
    <div class="container">
        <h3 class="mt-5 mb-5">Spinner General Conditions</h3>
        
        <?php
        if (isset($_POST['save_conditions_settings'])) {
            $cart_amount = isset($_POST['cartAmount']) ? sanitize_text_field($_POST['cartAmount']) : '';
            $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array();
            $product_count = isset($_POST['productCount']) ? sanitize_text_field($_POST['productCount']) : '';

            // Save to options
            update_option('min_cart_amount', $cart_amount);
            update_option('selected_categories_options', $categories);
            update_option('min_product_count', $product_count);

            if ($cart_amount !== '' && !empty($categories) && $product_count !== '') {
                echo '<div class="alert alert-success" role="alert">Conditions updated successfully!</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Failed to update conditions. Please make sure all fields are filled.</div>';
            }
        }
        ?>
        
        <!-- Cart amount and product category form -->
        <form id="cartGiftForm" method="post" >
            <div class="row mb-3">
                <label for="cartAmountInput" class="col-sm-2 col-form-label">Cart Amount:</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="cartAmountInput" name="cartAmount" placeholder="Enter cart amount" value="<?php echo esc_attr(get_option('min_cart_amount')); ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label for="categoryInput" class="col-sm-2 col-form-label">Product Category:</label>
                <div class="col-sm-4">
                    <select id="categoryInput" name="categories[]" multiple="multiple">
                        <?php
                        $product_categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => false,
                        ));

                        $selected_categories = get_option('selected_categories_options', array());

                        foreach ($product_categories as $category) {
                            $selected = in_array($category->term_id, $selected_categories) ? 'selected' : '';
                            echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
                        }
                        ?>
                    </select>

                    <!-- <select class="form-select" id="categoryInput" name="category"></select> -->
                </div>
            </div>
            <div class="row mb-3">
                <label for="" class="col-sm-2 col-form-label">Product Count:</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="productCountInput" name="productCount" placeholder="Enter product count" value="<?php echo esc_attr(get_option('min_product_count')); ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-8">
                    <button type="submit" name="save_conditions_settings" class="btn btn-primary">Add Rules</button>
                </div>
            </div>
        </form>

    </div>
    <?php
}




function render_spin_wheel_submenu_page_2()
{
    echo '<div class="wrap">';
    echo '<h1>Submenu Page 2</h1>';
    // Add content for Submenu Page 2
    echo '</div>';
}

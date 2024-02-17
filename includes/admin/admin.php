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
            $free_product = 'group' . $i . '_free_product';

            // Get values from the form
            $selectValue = sanitize_text_field($_POST[$selectKey]);


            $amountValue = sanitize_text_field($_POST[$amountKey]);
            $textValue = sanitize_text_field($_POST[$textKey]);
            $colorValue = sanitize_text_field($_POST[$colorKey]);


            $free_product_value = sanitize_text_field($_POST[$free_product]);



            $display_text = $amountValue . ' ' .  $textValue;

            // Save to WordPress options
            $option_name = 'custom_form_data_group_' . $i;


            if ($selectValue  == 'no_luck' || $selectValue  == 'add_another_spin') {
                $updated = update_option($option_name, array(
                    'displayText' => $display_text,
                    'type' => $selectValue,
                    'text' => $textValue,
                    'color' => $colorValue
                ));
            } elseif ($selectValue  == 'free_product') {
                $updated = update_option($option_name, array(
                    'displayText' => $display_text,
                    'type' => $selectValue,
                    'text' => $textValue,
                    'free_product_id' => $free_product_value,
                    'color' => $colorValue
                ));
            } else {
                $updated = update_option($option_name, array(
                    'displayText' => $display_text,
                    'type' => $selectValue,
                    'amount' => $amountValue,
                    'text' => $textValue,
                    'color' => $colorValue
                ));
            }
        }

        // Your PHP arrays
        $custom_form_data_group_1 = json_encode(get_option('custom_form_data_group_1'));
        $custom_form_data_group_2 = json_encode(get_option('custom_form_data_group_2'));
        $custom_form_data_group_3 = json_encode(get_option('custom_form_data_group_3'));
        $custom_form_data_group_4 = json_encode(get_option('custom_form_data_group_4'));
        $custom_form_data_group_5 = json_encode(get_option('custom_form_data_group_5'));
        $custom_form_data_group_6 = json_encode(get_option('custom_form_data_group_6'));






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


            $product_data = get_all_product_ids_and_names();


            // echo '<pre>';
            // print_r($product_data);
            // echo '</pre>';


            // Loop through each group
            for ($i = 1; $i <= 6; $i++) {
                $get_option_val = get_option('custom_form_data_group_' . $i);


            ?>


                <div class="row mb-3">
                    <label for="group<?php echo $i; ?>_select" class="col-sm-2 col-form-label">Spin <?php echo $i; ?>:</label>

                    <div class="col">
                        <select class="form-select spin_select_type" id="group<?php echo $i; ?>_select" name="group<?php echo $i; ?>_select" onchange="toggleAmountField('group<?php echo $i; ?>')">
                            <?php
                            foreach ($options as $key => $value) {

                                $selected = ($get_option_val && $get_option_val['type'] === $key) ? 'selected' : '';

                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="group<?php echo $i; ?>_text" name="group<?php echo $i; ?>_text" placeholder="Text" value="<?php echo esc_attr($get_option_val['text']); ?>">
                    </div>
                    <div class="col">
                        <div class="select_product_id" >
                            <select class="form-select" id="group<?php echo $i; ?>_free_product" name="group<?php echo $i; ?>_free_product" style="display: <?php echo isset($get_option_val['free_product_id']) ? 'block' : 'none'; ?>;">
                                <?php foreach ($product_data as $product) :
                                    $selected = ($get_option_val && isset($get_option_val['free_product_id']) && $get_option_val['free_product_id'] == $product['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $product['id']; ?>" <?php echo $selected; ?>><?php echo $product['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <input type="text" style="display: <?php echo isset($get_option_val['amount']) ? 'block' : 'none'; ?>;" class="form-control" id="group<?php echo $i; ?>_amount" name="group<?php echo $i; ?>_amount" placeholder="Amount" value="<?php echo isset($get_option_val['amount']) ? esc_attr($get_option_val['amount']) : ''; ?>">
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



        $cat_amount = get_option('cat_amount_condition');



        if (isset($_POST['save_conditions_settings'])) {
            $cart_amount = isset($_POST['cartAmount']) ? sanitize_text_field($_POST['cartAmount']) : '';
            $product_count = isset($_POST['productCount']) ? sanitize_text_field($_POST['productCount']) : '';
            $spin_expiry = isset($_POST['spinExpiry']) ? sanitize_text_field($_POST['spinExpiry']) : '';

            $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array();
            $cat_amount = isset($_POST['cat_amount']) ? array_map('intval', $_POST['cat_amount']) : array();


            // Check if both arrays have the same length
            if (count($categories) == count($cat_amount)) {
                $count = count($categories);
                for ($i = 0; $i < $count; $i++) {
                    $combinedArray[] = array(
                        'category' => $categories[$i],
                        'amount' => $cat_amount[$i]
                    );
                }
            } else {
                // Handle arrays of different lengths, if needed
                echo "Arrays have different lengths.";
            }

            // Save to options
            update_option('cat_amount_condition', $combinedArray);
            update_option('min_cart_amount', $cart_amount);
            update_option('selected_categories_options', $categories);
            update_option('min_product_count', $product_count);
            update_option('spin_expiry', $spin_expiry);

            if ($cart_amount !== '') {
                echo '<div class="alert alert-success" role="alert">Conditions updated successfully!</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Failed to update conditions. Please make sure all fields are filled.</div>';
            }
        }
        ?>

        <!-- Cart amount and product category form -->
        <form id="cartGiftForm" method="post">
            <div class="row mb-3">
                <label for="cartAmountInput" class="col-sm-2 col-form-label">Cart Amount:</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="cartAmountInput" name="cartAmount" placeholder="Enter cart amount" value="<?php echo esc_attr(get_option('min_cart_amount')); ?>">
                </div>
            </div>

            <div class="select-product-category-wrapper">

            </div>

            <div class="row mb-3">
                <label for="" class="col-sm-2 col-form-label">Spin Expiry: (Days)</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="spinExpiryInput" name="spinExpiry" placeholder="Enter spin expiry date" value="<?php echo esc_attr(get_option('spin_expiry')); ?>">
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

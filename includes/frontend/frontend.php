<?php


add_shortcode('carpet_clean_service_shortcode', 'cyc_carpet_clean_service_shortcode');

function cyc_carpet_clean_service_shortcode()
{
    ob_start(); // Start output buffering

    WC()->cart->empty_cart();

?>

    <div class="row bg-dark text-white pt-5 pb-5">
        <div class="col-10">
            <!-- Your site logo or other content goes here -->
        </div>
        <div class="col-2"> <!-- Add ml-auto class here -->
            <?php
            // Get the cart URL
            $cart_url = wc_get_cart_url();

            // Get the cart count
            $cart_count = WC()->cart->get_cart_contents_count();
            ?>
            <a href="<?php echo esc_url($cart_url); ?>" class="cart-icon">
                <i class="fas fa-shopping-cart fa-lg"></i>
                
                    <span class="cart-count"><?php echo esc_html($cart_count); ?></span>
                
            </a>
        </div>
    </div>


    <div class="container mt-5 mb-5">

        <div class="row">
            <!-- Left Side - 3 Columns -->
            <div class="col-md-3">
                <p>(303) 857-5016</p>
                <p>(720) 605-9002</p>
                <p><strong>Hours</strong></p>
                <!-- Days and Hours Table -->
                <table class="table">

                    <tbody>
                        <tr>
                            <td>Sunday</td>
                            <td>8:00am - 7:00pm</td>
                        </tr>
                        <tr>
                            <td>Monday</td>
                            <td>8:00am - 7:00pm</td>
                        </tr>
                        <tr>
                            <td>Tuesday</td>
                            <td>8:00am - 7:00pm</td>
                        </tr>
                        <tr>
                            <td>Wednesday</td>
                            <td>8:00am - 7:00pm</td>
                        </tr>
                        <tr>
                            <td>Thursday</td>
                            <td>8:00am - 7:00pm</td>
                        </tr>
                        <tr>
                            <td>Friday</td>
                            <td>8:00am - 7:00pm</td>
                        </tr>
                        <tr>
                            <td>Saturday</td>
                            <td>8:00am - 7:00pm</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Right Side - 9 Columns -->
            <div class="col-md-9">

                <div class="row mb-5">
                    <div class="col-md-6">
                        <!-- Text on the left -->
                        <h3>Our services</h3>
                    </div>
                    <div class="col-md-6">
                        <!-- Search input with icon on the right -->
                        <!-- <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">
                                    <i class="fa fa-search"></i> Replace with your preferred search icon class
                                </span>
                            </div>
                        </div> -->
                    </div>
                    <p>Price is an accurate estimate based on the standard scope of work</p>
                </div>



                <div class="container">
                    <?php
                    // Get all subcategories of the 'carpet' category
                    $carpet_subcategories = get_terms('product_cat', array(
                        'parent'     => get_term_by('slug', 'carpet', 'product_cat')->term_id,
                        'hide_empty' => false,
                    ));

                    foreach ($carpet_subcategories as $subcategory) :
                    ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4><?php echo esc_html($subcategory->name); ?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            $args = array(
                                'post_type'      => 'product',
                                'posts_per_page' => -1,
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => 'product_cat',
                                        'field'    => 'term_id',
                                        'terms'    => $subcategory->term_id,
                                    ),
                                ),
                            );

                            $loop = new WP_Query($args);

                            while ($loop->have_posts()) : $loop->the_post();
                                global $product;

                                $product_id = $product->id;
                            ?>
                                <div class="col-md-4">
                                    <div class="card mt-5">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            echo '<img src="' . get_the_post_thumbnail_url($product->ID, 'medium') . '" class="card-img-top" alt="Product Image">';
                                        }
                                        ?>



                                        <div class="card-body product-card-item">



                                            <div class="product-input-qty-options">
                                                <div class="input-group product-input-qty-row" style="display: none;">
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-danger btn-number product-quantity-change" data-type="minus" data-field="quant">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </span>

                                                    <input type="hidden" class="carpet_single_product_info" product-id="<?php echo $product_id ?>">
                                                    <input type="text" name="quant" class="form-control input-number-qty" value="0" min="0" max="999999">

                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-success btn-number product-quantity-change" data-type="plus" data-field="quant">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </span>
                                                </div>

                                                <div class="input-group d-flex justify-content-end product-plus-btn-show" >
                                                    <button type="button" class="btn btn-success product-quantity-change" data-type="plus" data-field="quant" >
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            
                                            
                                            </div>

                                            <div class="text-center mx-auto mt-5">
                                                <h5 class="card-title"><?php the_title(); ?></h5>
                                                <p class="card-price"><?php echo $product->get_price_html() . '/Each'; ?></p>
                                                <a href="" class="product-card-details">Details</a>
                                            </div>




                                        </div>





                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php wp_reset_postdata(); ?>
                </div>


                <!-- Sticky bar at the bottom -->
                <div class="d-none d-md-block  bg-dark text-white p-3 checkout-footer-sticky mt-5">
                    This is your sticky bar content.
                </div>


            </div>
        </div>
    </div>


<?php

    $output = ob_get_clean(); // Get the output and clean the buffer
    return $output;
}

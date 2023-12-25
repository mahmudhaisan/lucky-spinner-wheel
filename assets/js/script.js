jQuery(document).ready(function ($) {


    $('.product-quantity-change').click(function () {
        var inputGroup = $(this).closest('.product-input-qty-options').find('.product-input-qty-row');
        var inputGroupSingle = $(this).closest('.product-input-qty-options').find('.product-plus-btn-show');
        var inputField = inputGroup.find('.input-number-qty');
        var currentValue = parseInt(inputField.val());
        var updatedValue;

        if ($(this).data('type') === 'plus') {
            updatedValue = currentValue + 1;
            inputGroup.show();


            if (updatedValue > 0) {

                inputGroupSingle.removeClass('show-important');
                inputGroupSingle.addClass('hidden-important');
                inputGroup.closest('.product-card-item').addClass('bg-info');
            }
        } else {
            updatedValue = currentValue > 0 ? currentValue - 1 : 0;
            // inputGroup.hide();
            // inputGroup.siblings('.input-group').show();


            if (updatedValue == 0) {
                inputGroup.closest('.product-card-item').removeClass('bg-info');
                inputGroup.hide();
                inputGroupSingle.addClass('show-important');
            }
        }

        inputField.val(updatedValue);

        // AJAX request
        updateCartItem(inputField);
    });

    function updateCartItem(inputField) {
        var carpetSingleProductId = inputField.siblings('.carpet_single_product_info').attr('product-id');
        var updatedValue = parseInt(inputField.val());



        $.ajax({
            type: 'POST',
            url: carpet_checkout.ajaxurl,
            data: {
                action: 'add_single_carpet_product_to_cart',
                carpetSingleProductId: carpetSingleProductId,
                productItemQty: updatedValue,
            },
            success: function (response) {
                // Get the cart count element
                var $cartCount = $('.cart-count');

                // Get the current cart total
                var currentTotal = parseFloat($cartCount.text());

                // Get the updated cart total from the response
                var updatedTotal = parseFloat(response);

                // Animate the counting effect with decimal support
                $cartCount.prop('number', currentTotal).animateNumber({
                    number: updatedTotal,
                    numberStep: function (now, tween) {
                        var target = $(tween.elem);

                        // Display the number with two decimal places
                        target.text(now.toFixed(2));
                    }
                }, 500); // Adjust the duration as needed
            },
        });
    }


});


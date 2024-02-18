
jQuery(document).ready(function ($) {


    $('#categoryInput').select2();



    // Function to add category select options
    function addCategorySelect() {
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'add_category_select'
            },
            success: function (response) {
                $('.select-product-category-wrapper').html(response);

                $('.select-product-category-wrapper .row:first').addClass('default-row');

                $('.default-row').find('button.remove-row').prop('disabled', true); // Enable minus button for cloned row



            }
        });
    }

    // Add initial category select
    addCategorySelect();

    // Add row on plus button click
    $('.select-product-category-wrapper').on('click', '.add-row', function (e) {

        e.preventDefault();
        var defaultRow = $('.default-row').clone();
        defaultRow.removeClass('default-row');
        // defaultRow.find('button.add-row').remove(); // Remove plus button from cloned row
        defaultRow.closest('.row').find('.cat_amount').val('');
        defaultRow.find('button.remove-row').prop('disabled', false); // Enable minus button for cloned row
        defaultRow.insertAfter($(this).closest('.row'));
    });

    // Remove row on minus button click
    $('.select-product-category-wrapper').on('click', '.remove-row', function () {
        var row = $(this).closest('.row');
        // Prevent removing the first row
        if (!row.hasClass('default-row')) {
            row.remove();
        }
    });

});



function toggleAmountField(group) {

    // alert(12);
    var selectValue = document.getElementById(group + '_select').value;
    var amountField = document.getElementById(group + '_amount');
    var textField = document.getElementById(group + '_text');
    var free_product = document.getElementById(group + '_free_product');



    textField.value = '';

    
    // Check if the selected option is 'No Luck' or 'Add Another Spin'
    if (selectValue === 'no_luck' || selectValue === 'add_another_spin') {
        amountField.style.display = 'none';
        amountField.value = '';
        free_product.style.display = 'none';
    } else if (selectValue == 'free_product') {
        amountField.style.display = 'none';
        free_product.style.display = 'block';
    } else {
        free_product.style.display = 'none';
        amountField.style.display = 'block';
    }
}





jQuery(document).ready(function ($) {


    $('#categoryInput').select2();


});



//    alert(12);
function toggleAmountField(group) {

    // alert(12);
    var selectValue = document.getElementById(group + '_select').value;
    var amountField = document.getElementById(group + '_amount');

    // Check if the selected option is 'No Luck' or 'Add Another Spin'
    if (selectValue === 'no_luck' || selectValue === 'add_another_spin') {
        amountField.style.display = 'none';
    } else {
        amountField.style.display = 'block';
    }
}




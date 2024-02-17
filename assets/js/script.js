
jQuery(document).ready(function ($) {

    $(document).on('click', '#gift-button', function (event) {
        $('#popup-overlay').fadeIn();
        $('#popup-container').css('top', '50%').css('transform', 'translate(-50%, -50%)');
    });

    $('#popup-close-btn').click(function () {
        $('#popup-overlay').fadeOut();
        $('#popup-container').css('top', '-600px');
    });




    // Wheel functionality
    const canvas = document.getElementById('canvas');
    var spin_1 = JSON.parse(lucky_spin_wheel.group1);
    var spin_2 = JSON.parse(lucky_spin_wheel.group2);
    var spin_3 = JSON.parse(lucky_spin_wheel.group3);
    var spin_4 = JSON.parse(lucky_spin_wheel.group4);
    var spin_5 = JSON.parse(lucky_spin_wheel.group5);
    var spin_6 = JSON.parse(lucky_spin_wheel.group6);

    const segments = [spin_1, spin_2, spin_3, spin_4, spin_5, spin_6];

    // const segments = ['No Luck!', '10% Discsount', '20 NIS Fixed Discount', '1 Free Product', '5% Discount', '10 NIS Fixed Discount'];
    // const colors = ['#ECC48D', '#B17E4A', '#ECC48D', '#B17E4A', '#ECC48D', '#B17E4A'];
    let currentRotation = 0;
    let isSpinning = false;



    // Your existing drawWheel function with modifications for text wrapping
    function drawWheel() {

        if (!canvas) {

            return;
        }

        const ctx = canvas.getContext('2d');




        const canvasSize = 400;
        const centerX = canvasSize / 2;
        const centerY = canvasSize / 2;
        const wheelRadius = 200; // Adjust the radius as needed

        canvas.width = canvasSize;
        canvas.height = canvasSize;

        const sliceAngle = 2 * Math.PI / segments.length;
        for (let i = 0; i < segments.length; i++) {
            const startAngle = sliceAngle * i;
            const endAngle = sliceAngle * (i + 1);

            const textRotationAngle = startAngle + sliceAngle / 2;
            const textRotationAngleDegrees = (textRotationAngle * 180) / Math.PI;

            ctx.beginPath();
            ctx.arc(centerX, centerY, wheelRadius, sliceAngle * i, sliceAngle * (i + 1));
            ctx.lineTo(centerX, centerY);
            ctx.fillStyle = segments[i].color;
            ctx.fill();

            ctx.beginPath();
            const dotX = centerX + (wheelRadius - 5) * Math.cos(sliceAngle * i + sliceAngle / 2);
            const dotY = centerY + (wheelRadius - 5) * Math.sin(sliceAngle * i + sliceAngle / 2);
            ctx.arc(dotX, dotY, 3, 0, 2 * Math.PI);
            ctx.fillStyle = 'rgba(0, 0, 0, 0)';
            ctx.fill();

            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(sliceAngle * i + sliceAngle / 2);

            ctx.textAlign = "right";
            ctx.fillStyle = "#fff";
            ctx.font = "bold 21px Inter";

            // Add xOffset to introduce margin from center
            const xOffset = 15; // Adjust the margin as needed
            wrapText(ctx, segments[i].displayText, 170, 10, 150, 16, xOffset);

            ctx.restore();
        }
    }


    // Function to wrap text within a specified width
    function wrapText(context, text, x, y, maxWidth, lineHeight, xOffset) {
        context.fillStyle = "#fff"; // Set fill color to black
        const words = text.split(' ');
        let line = '';
        let yOffset = 0;

        for (let i = 0; i < words.length; i++) {
            const testLine = line + words[i] + ' ';
            const metrics = context.measureText(testLine);
            const testWidth = metrics.width;

            if (testWidth > maxWidth && i > 0) {
                context.fillText(line, x + xOffset, y + yOffset);
                line = words[i] + ' ';
                yOffset += lineHeight;
            } else {
                line = testLine;
            }
        }

        context.fillText(line, x + xOffset, y + yOffset);
    }

    drawWheel();




    // spinwheel
    function spinWheel() {
        if (isSpinning) return;
        isSpinning = true;
        // $('#spin').remove(); // Remove the spin button from the DOM

        const spinAngle = Math.random() * 360 + 3600;
        currentRotation += spinAngle;
        canvas.style.transform = `rotate(${currentRotation}deg)`;

        setTimeout(() => {
            isSpinning = false;
            const adjustedRotation = (currentRotation % 360) + 90;
            const segmentAngle = 360 / segments.length;
            const winningIndex = Math.floor((360 - adjustedRotation % 360) / segmentAngle) % segments.length;
            const winningSegment = segments[winningIndex];
            const resultText = winningSegment === 'No Luck!' ? 'No luck this time! Better luck next time.' : `Congratulations! You won ${winningSegment.displayText}.`;

            document.getElementsByClassName('updated-segmant-data').textContent = resultText;
            // $('#spin-result-content').show(); // Make sure the result is visible
            $('#spin-wheel-content').hide(); // Make sure the result is visible


            // console.log(winningSegment);
            // console.log(resultText);


            // Send winningSegment via AJAX
            sendWinningSegment(winningSegment);

        }, 2000);


    }

    // Function to send winningSegment via AJAX
    function sendWinningSegment(winningSegment) {

        // Expiry time
        var currentTime = new Date();
        var spin_expiry = parseInt(lucky_spin_wheel.spin_expiry_day);

        currentTime.setDate(currentTime.getDate() + spin_expiry);
        // Get the timestamp of the updated time
        var updatedExpiryTime = currentTime.getTime();




        // Make an AJAX request
        $.ajax({
            type: 'POST',
            url: lucky_spin_wheel.ajaxurl, // Replace with the correct URL
            data: {
                action: 'save_winning_segment', // Add your action name
                winningSegment: winningSegment,
                updatedExpiryTime: updatedExpiryTime
            },
            success: function (response) {
                $('.updated-segmant-data').html(response);
                startCountdown();
            },
            error: function (error) {
                console.error('Error sending winning segment:', error);
            }
        });
    }


    // Event listener for spinning the wheel
    $('.spin-wheel-btn').click(function () {
        spinWheel();
    });



    function startCountdown() {
        // Make AJAX request to get countdown time from WP DB
        $.ajax({
            type: 'POST',
            url: lucky_spin_wheel.ajaxurl, // Replace with the correct URL
            data: {
                action: 'get_countdown_time', // Add your action name
            },
            success: function (response) {

                if (response && response.data) {
                    // Start countdown using the retrieved time value
                    startTimer(response.data.winning_segment_time);
                } else {
                    console.error('Failed to retrieve countdown time from the database.');
                }
            },
            error: function (error) {
                console.error('Error retrieving countdown time:', error);
            }
        });
    }

    function startTimer(countDownDate) {
        // Update the countdown every 1 second
        var x = setInterval(function () {
            // Get the current date and time
            var now = new Date().getTime();

            var expiryTime = new Date(countDownDate);


            // Calculate the remaining time
            var distance = expiryTime - now;
            // console.log(distance);

            // Calculate days, hours, minutes, and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Display the countdown in the element with id="countdown"
            $("#countdown").html(`
                <span class="bg-secondary text-light p-1 rounded me-1">${days}d</span>
                <span class="bg-secondary text-light p-1 rounded me-1">${hours}h</span>
                <span class="bg-secondary text-light p-1 rounded me-1">${minutes}m</span>
                <span class="bg-secondary text-light p-1 rounded">${seconds}s</span>
            `);

            // If the countdown is finished, display a message
            if (distance < 0) {


                clearInterval(x);

                $("#countdown").html("");
            }
        }, 1000);
    }

    // Call the function to start the countdown
    startCountdown();

    // trigger various events on cart updates
    $(document.body).on('added_to_cart updated_cart_totals removed_from_cart cart_emptied', function (event, fragments, cart_hash, $button) {

     
        $.ajax({
            url: lucky_spin_wheel.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_cart_info'
            },
            success: function (response) {



                if (response.success) {




                    console.log(response);
                    var giftBtnWrapper = $('.gift-button-wrapper');

                    // Handle successful response
                    var total_product_price = response.data.total_product_price;
                    var min_cart_amount = response.data.min_cart_amount;
                    var check_category_condition = response.data.check_category_condition;

                    var isCartEmpty = response.data.cart_empty;
            
                    console.log(response.data.cart_empty);


                    console.log(check_category_condition);


                    if (total_product_price >= min_cart_amount && check_category_condition) {
                        giftBtnWrapper.empty().html('<div id="gift-button" style="display: block;">You have a gift 🎁</div>');
                    } else {
                        giftBtnWrapper.empty();
                    }


                    if(isCartEmpty) {
                        $('#gift-button').remove();
                    }

                } else {
                    // Handle error response
                    console.error(response.data);
                }
            },
            error: function (xhr, status, error) {
                console.error(error); // Log any AJAX errors
            }
        });
    });


    $(document.body).on('added_to_cart updated_cart_totals removed_from_cart cart_emptied', function (event, fragments, cart_hash, $button) {
        if (event.type === 'cart_emptied') {
            // Cart is emptied, perform actions here
            console.log('Cart has been emptied.');
        } else {
            // Other cart-related events, perform actions accordingly
            console.log('Other cart-related event:', event.type);
        }

         if ($('.cart-empty').length > 0) {
        // Cart is empty, perform actions here
        console.log('Cart has been emptied.');
    } else {
        // Cart still has items, perform actions accordingly
        console.log('Cart still has items.');
    }
    });



});





// /* 5 */

// position: absolute;
// width: 592.89px;
// height: 571.59px;

// background: linear-gradient(123.85deg, #F5CE9A 0%, #F7D4A7 18.1%, #FFFAF1 100%);
// border-radius: 25.8498px;





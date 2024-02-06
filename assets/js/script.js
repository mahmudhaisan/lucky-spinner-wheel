
jQuery(document).ready(function ($) {

    $('#gift-button').click(function () {
        $('#popup-overlay').fadeIn();
        $('#popup-container').css('top', '50%').css('transform', 'translate(-50%, -50%)');
    });

    $('#popup-close-btn').click(function () {
        $('#popup-overlay').fadeOut();
        $('#popup-container').css('top', '-600px');
    });

    // Wheel functionality
    const canvas = document.getElementById('canvas');
    // console.log(canvas); // Debugging line
    const ctx = canvas.getContext('2d');

    var spin_1 =JSON.parse( lucky_spin_wheel.group1);
    var spin_2 =JSON.parse( lucky_spin_wheel.group2);
    var spin_3 =JSON.parse( lucky_spin_wheel.group3);
    var spin_4 =JSON.parse( lucky_spin_wheel.group4);
    var spin_5 =JSON.parse( lucky_spin_wheel.group5);
    var spin_6 =JSON.parse( lucky_spin_wheel.group6);

  


    const segments = [spin_1, spin_2, spin_3, spin_4, spin_5, spin_6];


    console.log(segments);


    // const segments = ['No Luck!', '10% Discsount', '20 NIS Fixed Discount', '1 Free Product', '5% Discount', '10 NIS Fixed Discount'];
    // const colors = ['#ECC48D', '#B17E4A', '#ECC48D', '#B17E4A', '#ECC48D', '#B17E4A'];
    let currentRotation = 0;
    let isSpinning = false;


    // Your existing drawWheel function with modifications for text wrapping
    function drawWheel() {
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
            const resultText = winningSegment === 'No Luck!' ? 'No luck this time! Better luck next time.' : `Congratulations! You won ${winningSegment}.`;

            document.getElementById('show-spin-result').textContent = resultText;
            $('#spin-result-content').show(); // Make sure the result is visible
            $('#spin-wheel-content').hide(); // Make sure the result is visible


            console.log(winningSegment);
            // console.log(resultText);


            // Send winningSegment via AJAX
            sendWinningSegment(winningSegment);

        }, 2000);


    }


    // Function to send winningSegment via AJAX
    function sendWinningSegment(winningSegment) {
        // Make an AJAX request
        $.ajax({
            type: 'POST',
            url: lucky_spin_wheel.ajaxurl, // Replace with the correct URL
            data: {
                action: 'save_winning_segment', // Add your action name
                winningSegment: winningSegment
            },
            success: function (response) {
                console.log('Winning segment sent successfully.' + response.data);
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



   
   

});






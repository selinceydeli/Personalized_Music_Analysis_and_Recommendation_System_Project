<x-layout>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap');

        :root {
            --white: #fff;
            --card-bg-clr: #ff4d6f;
            --input-bg-clr: #fcdcae;
            --stripe-bg-clr: #3e5367;
            --text-clr: #3e5367;
            --btn-hvr-clr: #0f355a;
        }

        * {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            box-sizing: border-box;
            outline: none;
        }

        body {
            background: var(--bg-clr);
            color: var(--text-clr);
            font-size: 14px;
        }

        ::placeholder {
            color: var(--text-clr);
        }

        .subscription-price {
            margin-top: 8px;
            font-size: 18px;
            color: #ffffff;
        }

        .price {
            font-weight: bold;
            color: #ffffff;
            font-size: 22px;
            margin-right: 5px;
        }

        .btn {
            background-color: #f47f7f;
            /* Light red color */
            padding: 12px 20px;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            cursor: pointer;
            font-weight: bold;
            color: #333;
            /* Text color */
        }

        .wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .payment_form {
            position: relative;
            width: 425px;
            max-width: 100%;
            height: 275px;
        }

        .payment_form label {
            display: block;
            color: var(--white);
            margin-bottom: 8px;
        }

        .payment_form .input {
            padding: 10px 15px;
            width: 100%;
            height: 40px;
            background: var(--input-bg-clr);
            border: 1px solid var(--input-bg-clr);
            letter-spacing: 4px;
        }

        .payment_form .back_card,
        .payment_form .front_card {
            background: var(--card-bg-clr);
            width: 100%;
            height: 100%;
            border-radius: 25px;
        }

        .payment_form .back_card .stripe {
            position: absolute;
            top: 30px;
            left: 0;
            width: 100%;
            height: 55px;
            background: var(--stripe-bg-clr);
        }

        .payment_form .back_card .input_field {
            position: absolute;
            bottom: 60px;
            right: 30px;
            width: 68px;
        }

        .payment_form .front_card {
            padding: 30px;
            position: absolute;
            bottom: 40px;
            left: -125px;
            box-shadow: 0 0 2px rgb(0, 0, 0, 0.25);
        }

        .payment_form .front_card .pay_sec {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .payment_form .front_card .pay_sec .visa_img {
            width: 65px;
            height: 25px;
        }

        .payment_form .front_card .pay_sec .visa_img img {
            width: 100%;
            height: 100%;
        }

        .payment_form .front_card .pay_sec .pay p {
            color: var(--white);
            font-size: 16px;
        }

        .payment_form .front_card .pay_sec .pay p span {
            font-weight: 900;
            font-size: 20px;
        }

        .payment_form .front_card .form .input_top {
            margin-bottom: 20px;
        }

        .payment_form .front_card .form .input_top .input_grp,
        .payment_form .front_card .form .input_bottom {
            display: flex;
            justify-content: space-between;
        }

        .payment_form .front_card .form .input_grp .input_field {
            margin-right: 15px;
        }

        .payment_form .front_card .form .input_grp .input_field:last-child {
            margin-right: 0;
        }

        .payment_form .front_card .form .input_bottom .input_left {
            margin-right: 25px;
        }

        .payment_form .front_card .form .input_bottom .input_right .input_grp {
            display: flex;
            justify-content: space-between;
        }

        .payment_form .front_card .form .input_bottom .input_right .input_grp .input_field {
            width: 55px;
        }

        .payment_form_wrapper .btn {
            margin-top: 30px;
            width: 225px;
            padding: 15px 20px;
            background: var(--stripe-bg-clr);
            color: var(--white);
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            letter-spacing: 3px;
        }

        .payment_form_wrapper .btn:hover {
            background: var(--btn-hvr-clr);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submitButton = document.getElementById('submit-payment-btn');
            const paymentForm = document.getElementById('payment-form');
            const errorMessages = document.getElementById('error-messages');

            submitButton.addEventListener('click', function() {
                const validationResult = validateInputs();
                if (validationResult.isValid) {
                    paymentForm.submit();
                } else {
                    errorMessages.innerHTML = validationResult.errorMessage;
                }
            });
        });

        function validateInputs() {
            const cvvCvv = document.querySelector('input[name="cvc-cvv"]').value;
            const cardNumber1 = document.querySelector('input[name="cardNumber1"]').value;
            const cardNumber2 = document.querySelector('input[name="cardNumber2"]').value;
            const cardNumber3 = document.querySelector('input[name="cardNumber3"]').value;
            const cardNumber4 = document.querySelector('input[name="cardNumber4"]').value;
            const cardHolder = document.querySelector('input[name="cardHolder"]').value;
            const month = document.querySelector('input[name="month"]').value;
            const date = document.querySelector('input[name="date"]').value;

            const monthRegex = /^(0[1-9]|1[0-2])$/;

            const errorMessages = [];

            if (!isNumeric(cvvCvv) || cvvCvv.length !== 3) {
                errorMessages.push('Please enter a valid 3-digit CVC/CVV.');
            } else if (!isNumeric(cardNumber1) || cardNumber1.length !== 4) {
                errorMessages.push('Please enter a valid 4-digit card number (Part 1).');
            } else if (!isNumeric(cardNumber2) || cardNumber2.length !== 4) {
                errorMessages.push('Please enter a valid 4-digit card number (Part 2).');
            } else if (!isNumeric(cardNumber3) || cardNumber3.length !== 4) {
                errorMessages.push('Please enter a valid 4-digit card number (Part 3).');
            } else if (!isNumeric(cardNumber4) || cardNumber4.length !== 4) {
                errorMessages.push('Please enter a valid 4-digit card number (Part 4).');
            } else if (cardHolder.trim() === '') {
                errorMessages.push('Name cannot be blank.');
            }

            else if ((!monthRegex.test(month)) || month.length!=2) {
                errorMessages.push("Month must be between 01 and 12.");
            }

            else if (date.length!=2) {
                errorMessages.push("Please enter a valid year.");
            }

            return {
                isValid: errorMessages.length === 0,
                errorMessage: `<div style="color: red; padding: 20px; font-size: 18px;">${errorMessages.map(msg => `<p>${msg}</p>`).join('')}</div>`
            };
        }

        function isNumeric(event) {
            // Get the input character code
            const charCode = (event.which) ? event.which : event.keyCode;

            // Allow only numeric characters (0-9) and control keys like Backspace
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                event.preventDefault();
                return false;
            }

            return true;
        }

        function enforceMaxLength(element, maxLength) {
            if (element.value.length >= maxLength) {
                element.value = element.value.slice(0, maxLength);
                return false;
            }
            return true;
        }
    </script>
    <div class="wrapper">
        <div class="payment_form_wrapper">
            <div class="payment_form">
                <div class="back_card">
                    <div class="stripe"></div>
                    <div class="input_field">
                        <label>CVC/CVV</label>
                        <input type="text" name="cvc-cvv" class="input" inputmode="numeric" pattern="[0-9]*"
                            oninput="return enforceMaxLength(this, 3)" placeholder="XXX" maxlength="3"
                            onkeypress="return isNumeric(event)" value="{{ old('cvc-cvv') }}">
                    </div>
                </div>
                <div class="front_card">
                    <div class="pay_sec">
                        <div class="visa_img">
                            <img src="/images/visa.png" alt="visa">
                        </div>
                    </div>
                    <div class="form">
                        <div class="input_top">
                            <label>CARD NUMBER</label>
                            <div class="input_grp">
                                <div class="input_field">
                                    <input type="text" name="cardNumber1" class="input" placeholder="XXXX"
                                        inputmode="numeric" pattern="[0-9]*" onkeypress="return isNumeric(event)"
                                        oninput="return enforceMaxLength(this, 4)" maxlength="4"
                                        value="{{ old('cardNumber1') }}">
                                </div>
                                <div class="input_field">
                                    <input type="text" name="cardNumber2" class="input" placeholder="XXXX"
                                        inputmode="numeric" pattern="[0-9]*" onkeypress="return isNumeric(event)"
                                        oninput="return enforceMaxLength(this, 4)" maxlength="4"
                                        value="{{ old('cardNumber2') }}">
                                </div>
                                <div class="input_field">
                                    <input type="text" name="cardNumber3" class="input" placeholder="XXXX"
                                        inputmode="numeric" pattern="[0-9]*" onkeypress="return isNumeric(event)"
                                        oninput="return enforceMaxLength(this, 4)" maxlength="4"
                                        value="{{ old('cardNumber3') }}">
                                </div>
                                <div class="input_field">
                                    <input type="text" name="cardNumber4" class="input" placeholder="XXXX"
                                        inputmode="numeric" pattern="[0-9]*" onkeypress="return isNumeric(event)"
                                        oninput="return enforceMaxLength(this, 4)" maxlength="4"
                                        value="{{ old('cardNumber4') }}">
                                </div>
                            </div>
                        </div>
                        <div class="input_bottom">
                            <div class="input_left">
                                <div class="input_field">
                                    <label>CARD HOLDER</label>
                                    <input type="text" name="cardHolder" class="input" placeholder="MUSIC TAILOR"
                                        value="{{ old('cardHolder') }}">
                                </div>
                            </div>
                            <div class="input_right">
                                <label>EXPIRATION DATE</label>
                                <div class="input_grp">
                                    <div class="input_field">
                                        <input type="text" name="month" class="input" placeholder="XX"
                                            inputmode="numeric" pattern="[0-9]*" onkeypress="return isNumeric(event)"
                                            oninput="return enforceMaxLength(this, 2)" maxlength="2"
                                            value="{{ old('month') }}">
                                    </div>
                                    <div class="input_field">
                                        <input type="text" name="date" class="input" placeholder="XX"
                                            inputmode="numeric" pattern="[0-9]*" onkeypress="return isNumeric(event)"
                                            oninput="return enforceMaxLength(this, 2)" maxlength="2"
                                            value="{{ old('date') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="error-messages"></div>
            <form id="payment-form" action="/pay" method="POST">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan }}">
                <div class="btn">
                    <div class="subscription-price">
                        @if ($plan === 'silver')
                            <span class="price">$9.99</span>/month
                        @elseif ($plan === 'gold')
                            <span class="price">$14.99</span>/month
                        @endif
                    </div>
                    <button type="button" id="submit-payment-btn" class="complete-payment-btn">COMPLETE
                        PAYMENT</button>
                </div>
                <!-- Your additional form fields here (card details, CVV, etc.) -->
            </form>
        </div>
    </div>
</x-layout>

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

    <div class="wrapper">
        <div class="payment_form_wrapper">
            <div class="payment_form">
                <div class="back_card">
                    <div class="stripe"></div>
                    <div class="input_field">
                        <label>CVC/CVV</label>
                        <input type="text" name="cvc-cvv" class="input" placeholder="XXX" maxlength="3">
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
                                    <input type="text" name="cardNumber" class="input" placeholder="XXXX"
                                        maxlength="4">
                                </div>
                                <div class="input_field">
                                    <input type="text" name="cardNumber" class="input" placeholder="XXXX"
                                        maxlength="4">
                                </div>
                                <div class="input_field">
                                    <input type="text" name="cardNumber" class="input" placeholder="XXXX"
                                        maxlength="4">
                                </div>
                                <div class="input_field">
                                    <input type="text" name="cardNumber" class="input" placeholder="XXXX"
                                        maxlength="4">
                                </div>
                            </div>
                        </div>
                        <div class="input_bottom">
                            <div class="input_left">
                                <div class="input_field">
                                    <label>CARD HOLDER</label>
                                    <input type="text" name="cvc-cvv" class="input" placeholder="MUSIC TAILOR">
                                </div>
                            </div>
                            <div class="input_right">
                                <label>EXPRIATION DATE</label>
                                <div class="input_grp">
                                    <div class="input_field">
                                        <input type="text" name="month" class="input" placeholder="XX"
                                            maxlength="2">
                                    </div>
                                    <div class="input_field">
                                        <input type="text" name="date" class="input" placeholder="XX"
                                            maxlength="2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                    <button type="submit" class="complete-payment-btn">COMPLETE PAYMENT</button>
                </div>
                <!-- Your additional form fields here (card details, CVV, etc.) -->
            </form>
        </div>
    </div>
</x-layout>

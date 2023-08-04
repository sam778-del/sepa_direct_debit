<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sepa Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /*custom font*/
        @import url(https://fonts.googleapis.com/css?family=Montserrat);

        /*basic reset*/
        * {
            margin: 0;
            padding: 0;
        }

        html {
            height: 100%;
            background: #eee;
        }

        body {
            font-family: Montserrat, arial, verdana;
            background: transparent;
            overflow: hidden;
        }

        /*form styles*/
        .msform {
            text-align: center;
            position: relative;
            margin-top: 30px;
        }

        .msform fieldset {
            background: white;
            border: 0 none;
            border-radius: 8px;
            box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
            padding: 20px 30px;
            box-sizing: border-box;
            width: 80%;
            margin: 0 10%;

            /*stacking fieldsets above each other*/
            position: relative;
        }

        /*Hide all except first fieldset*/
        .msform fieldset:not(:first-of-type) {
            display: none;
        }

        /*inputs*/
        .msform input,
        .msform textarea {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
            font-family: montserrat;
            color: #2C3E50;
            font-size: 13px;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .msform input:focus,
        .msform textarea:focus {
            -moz-box-shadow: none !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid #2098ce;
            outline-width: 0;
            transition: All 0.5s ease-in;
            -webkit-transition: All 0.5s ease-in;
            -moz-transition: All 0.5s ease-in;
            -o-transition: All 0.5s ease-in;
        }

        /*buttons*/
        .msform .action-button {
            width: auto;
            background: #2098ce;
            font-weight: bold;
            color: white;
            border: 0 none;
            border-radius: 25px;
            cursor: pointer;
            padding: 10px;
            margin: 10px 5px;
        }

        .msform .action-button:hover,
        .msform .action-button:focus {
            box-shadow: 0 0 0 2px white, 0 0 0 3px #2098ce;
        }

        .msform .action-button-previous {
            width: 100px;
            background: #aCbEd0;
            font-weight: bold;
            color: white;
            border: 0 none;
            border-radius: 25px;
            cursor: pointer;
            padding: 10px 5px;
            margin: 10px 5px;
        }

        .msform .action-button-previous:hover,
        .msform .action-button-previous:focus {
            box-shadow: 0 0 0 2px white, 0 0 0 3px #aCbEd0;
        }

        /*headings*/
        .fs-title {
            font-size: 18px;
            text-transform: uppercase;
            color: #2C3E50;
            margin-bottom: 10px;
            letter-spacing: 2px;
            font-weight: bold;
        }

        .fs-subtitle {
            font-weight: normal;
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
        }

        /*progressbar*/
        #progressbar {
            margin-bottom: 30px;
            overflow: hidden;
            /*CSS counters to number the steps*/
            counter-reset: step;
        }

        #progressbar li {
            list-style-type: none;
            color: #666;
            text-transform: uppercase;
            font-size: 9px;
            width: 33.33%;
            float: left;
            position: relative;
            letter-spacing: 1px;
        }

        #progressbar li:before {
            content: counter(step);
            counter-increment: step;
            width: 24px;
            height: 24px;
            line-height: 26px;
            display: block;
            font-size: 12px;
            color: #333;
            background: white;
            border-radius: 25px;
            margin: 0 auto 10px auto;
        }

        /*progressbar connectors*/
        #progressbar li:after {
            content: '';
            width: 100%;
            height: 2px;
            background: white;
            position: absolute;
            left: -50%;
            top: 9px;
            z-index: -1;
            /*put it behind the numbers*/
        }

        #progressbar li:first-child:after {
            /*connector not needed before the first step*/
            content: none;
        }

        /*marking active/completed steps blue*/
        /*The number of the step and the connector before it = blue*/
        #progressbar li.active:before,
        #progressbar li.active:after {
            background: #2098ce;
            color: white;
        }

        input,
        .StripeElement {
            height: 40px;
            padding: 10px 12px;

            color: #2C3E50;
            border: 1px solid #ccc;
            border-radius: 4px;

            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        input:focus,
        .StripeElement--focus {
            box-shadow: none !important;
            border: 1px solid #2098ce;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
</head>

<body>
    <!-- MultiStep Form -->
    <div class="row align-items-center justify-content-center">
        <div class="col-md-6 col-md-offset-3">
            <form class="msform" id="payment-form">
                @csrf
                <fieldset>
                    <h2 class="fs-title">Personal Details</h2>
                    <div id="error-message" class="fs-subtitle text-danger" role="alert"></div>
                    @if ($message = Session::get('success'))
                        <div class="fs-subtitle text-success" role="alert">{{ $message }}</div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="fs-subtitle text-danger" role="alert">{{ $message }}</div>
                    @endif
                    <div class="form-group">
                        <input id="accountholder-name" name="accountholder-name" placeholder="Jenny Rosen" required />
                    </div>
                    <div class="form-group">
                        <input id="email" name="email" type="email" placeholder="jenny.rosen@example.com"
                            required />
                    </div>
                    {{-- <input type="button" name="next" class="next action-button" value="Next" /> --}}
                    <div class="form-row">
                        <div id="iban-element">
                            <!-- A Stripe Element will be inserted here. -->
                        </div>
                    </div>

                    <!-- Add the client_secret from the SetupIntent as a data attribute   -->
                    <button id="submit-button" class="next action-button">
                        Set up SEPA Direct Debit
                    </button>

                    <!-- Display mandate acceptance text. -->
                    <div id="mandate-acceptance" class="fs-subtitle">
                        By providing your payment information and confirming this payment, you
                        authorise (A) Rocket Rides and Stripe, our payment service provider
                        and/or PPRO, its local service provider, to send instructions to your
                        bank to debit your account and (B) your bank to debit your account in
                        accordance with those instructions. As part of your rights, you are
                        entitled to a refund from your bank under the terms and conditions of
                        your agreement with your bank. A refund must be claimed within 8 weeks
                        starting from the date on which your account was debited. Your rights
                        are explained in a statement that you can obtain from your bank. You
                        agree to receive notifications for future debits up to 2 days before
                        they occur.
                    </div>
                    <!-- Used to display form errors. -->
                </fieldset>
            </form>
        </div>
    </div>
    <!-- /.MultiStep Form -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $(document).ready(function() {
            const stripe = Stripe('{!! env('STRIPE_PUBLIC_KEY') !!}');
            const elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            const style = {
                base: {
                    color: "#32325d",
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
                    fontSmoothing: "antialiased",
                    fontSize: "16px",
                    "::placeholder": {
                        color: "#aab7c4"
                    },
                    ":-webkit-autofill": {
                        color: "#32325d"
                    }
                },
                invalid: {
                    color: "#fa755a",
                    iconColor: "#fa755a",
                    ":-webkit-autofill": {
                        color: "#fa755a"
                    }
                }
            };

            const options = {
                style,
                supportedCountries: ['SEPA'],
                // Elements can use a placeholder as an example IBAN that reflects
                // the IBAN format of your customer's country. If you know your
                // customer's country, we recommend passing it to the Element as the
                // placeholderCountry.
                placeholderCountry: 'DE',
            };

            // Create an instance of the IBAN Element
            const iban = elements.create('iban', options);

            // Add an instance of the IBAN Element into the `iban-element` <div>
            iban.mount('#iban-element');

            const form = document.getElementById('payment-form');
            const accountholderName = document.getElementById('accountholder-name');
            const email = document.getElementById('email');
            const submitButton = document.getElementById('submit-button');

            iban.on('change', (event) => {
                const displayError = document.getElementById('error-message');
                if (event.error) {
                    displayError.textContent = event.error.message;
                    $('#submit-button').attr("disabled", true);
                } else {
                    displayError.textContent = '';
                    $('#submit-button').attr("disabled", false);
                }
            });

            submitButton.addEventListener("click", async (event) => {
                event.preventDefault();

                $('#submit-button').text('Processing...');
                $('#submit-button').attr("disabled", false);

                try {
                    // Retrieve CSRF token from meta tag
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');

                    // Serialize form data
                    const formValues = $(form).serialize();

                    // Use $.post to send a POST request to your PHP script
                    $.post("{{ route('customer.details') }}", formValues, function(data) {
                        // Display the returned data in the browser console
                        stripe.confirmSepaDebitSetup(
                            data.clientSecret, {
                                payment_method: {
                                    sepa_debit: iban,
                                    billing_details: {
                                        name: accountholderName.value,
                                        email: email.value,
                                    },
                                },
                            }
                        ).then(function(data) {
                            if (data.setupIntent.status == "succeeded") {

                                // Show "Set up succeeded" message for a brief time
                                $('#submit-button').text('Set up succeeded');
                                setTimeout(() => {
                                    location.reload('/');
                                }, 2000); // Wait for 2 seconds before reverting

                            } else {
                                $('#submit-button').attr("disabled", false);
                                const displayError = document.getElementById(
                                    'error-message');
                                displayError.textContent =
                                    "An error occurred during SEPA Direct Debit setup.";
                                $('#submit-button').text('Set up SEPA Direct Debit');
                            }
                        });
                    }).fail(function(error) {
                        $('#submit-button').attr("disabled", false);
                        const displayError = document.getElementById('error-message');
                        displayError.textContent =
                            "An error occurred during SEPA Direct Debit setup.";
                        $('#submit-button').text('Set up SEPA Direct Debit');
                    });
                } catch (error) {
                    $('#submit-button').attr("disabled", false);
                    const displayError = document.getElementById('error-message');
                    displayError.textContent = "An error occurred during SEPA Direct Debit setup.";
                    $('#submit-button').text('Set up SEPA Direct Debit');
                }
            });
        });
    </script>
</body>

</html>

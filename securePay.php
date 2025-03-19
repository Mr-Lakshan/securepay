<?php
$file = 'order_details.txt';

// Retrieve cart ID
$cartId = isset($_GET['cart-id']) ? $_GET['cart-id'] : '';

// Retrieve shipping details
$shipping_details = [
    "first_name" => isset($_GET['shipping-first-name']) ? $_GET['shipping-first-name'] : '',
    "last_name" => isset($_GET['shipping-last-name']) ? $_GET['shipping-last-name'] : '',
    "company" => isset($_GET['shipping-company']) ? $_GET['shipping-company'] : '',
    "phone" => isset($_GET['shipping-phone']) ? $_GET['shipping-phone'] : '',
    "address1" => isset($_GET['shipping-address1']) ? $_GET['shipping-address1'] : '',
    "address2" => isset($_GET['shipping-address2']) ? $_GET['shipping-address2'] : '',
    "city" => isset($_GET['shipping-city']) ? $_GET['shipping-city'] : '',
    "state" => isset($_GET['shipping-state']) ? $_GET['shipping-state'] : '',
    "postal_code" => isset($_GET['shipping-postal-code']) ? $_GET['shipping-postal-code'] : '',
    "country" => isset($_GET['shipping-country']) ? $_GET['shipping-country'] : ''
];

// Retrieve billing details
$billing_details = [
    "first_name" => isset($_GET['billing-first-name']) ? $_GET['billing-first-name'] : '',
    "last_name" => isset($_GET['billing-last-name']) ? $_GET['billing-last-name'] : '',
    "company" => isset($_GET['billing-company']) ? $_GET['billing-company'] : '',
    "phone" => isset($_GET['billing-phone']) ? $_GET['billing-phone'] : '',
    "address1" => isset($_GET['billing-address1']) ? $_GET['billing-address1'] : '',
    "address2" => isset($_GET['billing-address2']) ? $_GET['billing-address2'] : '',
    "city" => isset($_GET['billing-city']) ? $_GET['billing-city'] : '',
    "state" => isset($_GET['billing-state']) ? $_GET['billing-state'] : '',
    "postal_code" => isset($_GET['billing-postal-code']) ? $_GET['billing-postal-code'] : '',
    "country" => isset($_GET['billing-country']) ? $_GET['billing-country'] : ''
];


$order_data = [
    "cart_id" => $cartId,
    "shipping_details" => $shipping_details,
    "billing_details" => $billing_details,
    "timestamp" => date("Y-m-d H:i:s")
];


$existing_data = [];
if (file_exists($file)) {
    $json_content = file_get_contents($file);
    $existing_data = json_decode($json_content, true) ?? [];
}


$existing_data[] = $order_data;

$json_data = json_encode($existing_data, JSON_PRETTY_PRINT);


file_put_contents($file, $json_data);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecurePay Payment</title>
    <link rel="shortcut icon" href="./images/secure-icon.png" type="image/x-icon"/>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .from-main {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            padding: 20px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-container img {
            width: 200px; 
        }

        .payment-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            
             padding: 37px 15px !important; 
             width: 482px !important;
        }

        #securepay-ui-container {
            margin: 20px 0 !important;
        }

       
        .btn button {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn .submit-btn {
            background: #0071eb;
            color: white;
        }

        .btn .reset-btn {
            background: #999;
            color: white;
        }

        .loader-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        .cc-icons[_ngcontent-c3] {
  
         margin: 14px 0 0 !important;
    
       }
       .ph-flex-container[_ngcontent-c1] {
        display: flex;
       gap: 10px;
       }
       .btn .reset-btn {
 
     padding: 10px 40px !important;
      display: none !important;
     }

.btn {
    margin-top: 18px !important;
    display: flex;
    justify-content: center;
    gap: 15px !important;
}
.ph-form-element-label {
    
    font-size: 15px !important;

}


        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .payment-container {
                width: 90%;
            }

            .logo-container img {
                width: 150px;
            }
        }

        .payment-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1); 
            text-align: center;
            width: 420px;
            border: none; 
        }


iframe#securepay-ui-iframe-0 {
    border: none;
}

 #toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #f44336;
            color: white;
            text-align: center;
            padding: 16px;
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
            font-size: 17px;
            z-index: 1000;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }

        /* Show animation */
        #toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @keyframes fadein {
            from { opacity: 0; bottom: 20px; }
            to { opacity: 1; bottom: 30px; }
        }

        @keyframes fadeout {
            from { opacity: 1; bottom: 30px; }
            to { opacity: 0; bottom: 20px; }
        }

    </style>
</head>
<body>
<div id="toast">Card details not correct</div>
<div class="from-main">
    
    <div class="logo-container">
   <img src="./images/secure-pay.png">
    </div>

    
    <div class="loader-container" id="loader">
        <div class="loader"></div>
    </div>

    <div class="payment-container">
        <h2>Secure Payment</h2>

        <form onsubmit="return false;" class="form-add">
           
            <div id="securepay-ui-container"></div>
            
            <div class="btn">
                <button type="button" onclick="tokenizeCard();" class="submit-btn">Pay Securely</button>
                <button type="button" onclick="resetForm();" class="reset-btn">Reset</button>
            </div>
        </form>
    </div>
</div>

<script id="securepay-ui-js" src="https://payments-stest.npe.auspost.zone/v3/ui/client/securepay-ui.min.js"></script>

<script type="text/javascript">
  
    var mySecurePayUI = new securePayUI.init({
        containerId: 'securepay-ui-container',
        scriptId: 'securepay-ui-js',
        clientId: '',
        merchantCode: '',
        card: {
            allowedCardTypes: ['visa', 'mastercard','amex'],
            showCardIcons: true,
            onTokeniseSuccess: function(tokenisedCard) {
                console.log('Tokenisation Success:', tokenisedCard);
                
                let secureToken = tokenisedCard.token;

                fetch('process_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'secureToken=' + encodeURIComponent(secureToken)
                })
                .then(response => response.text())
                .then(data => {
                    data = data.trim();
                    console.log('Response from PHP:', data);
                    document.getElementById("loader").style.display = "none"; 

                    if (data === 'Awaiting Pickup') {
                        window.location.href = 'thankyou.html';
                    }
                })
                .catch(error => {
                    console.error('Error sending token:', error);
                    document.getElementById("loader").style.display = "none"; 
                });
            },
            onTokeniseError: function(errors) {
                console.error('Tokenisation Error:', errors);
                showToast();
                resetForm();
                document.getElementById("loader").style.display = "none"; 
            }
        },
        onLoadComplete: function () {
            console.log('SecurePay UI Component Loaded Successfully!');
        }
    });

    function tokenizeCard() {
        console.log("Submitting Payment...");
        document.getElementById("loader").style.display = "flex"; 
        mySecurePayUI.tokenise();
    }

    function resetForm() {
        console.log("Form Resetting...");
        mySecurePayUI.reset();
    }

            function showToast() {
            let toast = document.getElementById("toast");
            toast.className = "show";
            setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 3000);
        }
</script>


</body>
</html>

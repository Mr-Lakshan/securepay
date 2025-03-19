<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the secure token from the request
    $secureToken = $_POST['secureToken'] ?? '';
  
}
    
    $file = file_get_contents('order_details.txt');
    
    
    $orderDetails = json_decode($file, true);
    
    // Check if the JSON decoding was successful
    if ($orderDetails !== null && is_array($orderDetails) && count($orderDetails) > 0) {
        // Extracting the first order's details
        $order = $orderDetails[0];
        
        // Get cart ID
        $cartId = $order['cart_id'];
        
        
         $shippingFirstName = $order['shipping_details']['first_name'];
        $shippingLastName = $order['shipping_details']['last_name'];
         $shippingAddress1 = $order['shipping_details']['address1'];
        $shippingAddress2 = $order['shipping_details']['address2'];
        $shippingCity = $order['shipping_details']['city'];
        $shippingState = $order['shipping_details']['state'];
        $shippingPostalCode = $order['shipping_details']['postal_code'];
        $shippingCountry = $order['shipping_details']['country'];
        
         $billingFirstName = $order['billing_details']['first_name'];
        $billingLastName = $order['billing_details']['last_name'];
         $billingAddress1 = $order['billing_details']['address1'];
        $billingAddress2 = $order['billing_details']['address2'];
        $billingCity = $order['billing_details']['city'];
        $billingState = $order['billing_details']['state'];
        $billingPostalCode = $order['billing_details']['postal_code'];
        $billingCountry = $order['billing_details']['country'];

        
        
     //cart Api   
        $storeHash = '';
        $authToken = '';
        
        // Fetch Cart Details
        $apiUrl = "https://api.bigcommerce.com/stores/{$storeHash}/v3/carts/{$cartId}";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                "X-Auth-Token: {$authToken}"
            ],
        ]);
       $response = curl_exec($curl);    
       

// Decode the JSON response into an associative array
 $data = json_decode($response, true);

$customer_id = $data['data']['customer_id'];
$email = $data['data']['email'];
$currency_code = $data['data']['currency']['code'];
$base_amount = $data['data']['base_amount'];
$cart_amount = $data['data']['cart_amount'];
$created_time = $data['data']['created_time'];
$updated_time = $data['data']['updated_time'];

// Extract product details into a single variable
$products = [];
    foreach ($data['data']['line_items']['physical_items'] as $product) {
        $products[] = [
            'name' => $product['name'],
            'quantity' => $product['quantity'],

            'product_id' => $product['product_id']


             
        ];

    }


//Checkout Api

$curl = curl_init();
 
$api_url = "https://api.bigcommerce.com/stores/$storeHash/v3/checkouts/$cartId";

// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
           "X-Auth-Token: {$authToken}"
    ],
]);

// Execute the request and get the response
$response = curl_exec($curl);


$checkoutDecode = json_decode($response);


$totalAmount = $checkoutDecode->data->grand_total;
$totalAmount = $totalAmount *100;


    // Create Order in BigCommerce

$orderData = [
    "status_id" => 0, // Incomplete status
    "customer_id" => $customer_id,
    "billing_address" => [
        "first_name" => $billingFirstName,
        "last_name" => $billingLastName,
        "street_1" => $billingAddress1,
        "city" => $billingCity,
        "state" => $billingState,
        "zip" => $billingPostalCode,
        "country" => $billingCountry,
        "country_iso2" => "AU",
        "email" => $email
    ],
    "shipping_addresses" => [
        [
            "first_name" => $shippingFirstName,
            "last_name" => $shippingLastName,
            "street_1" => $shippingAddress1,
            "city" => $shippingCity,
            "state" => $shippingState,
            "zip" => $shippingPostalCode,
            "country" => $shippingCountry,
            "country_iso2" => "AU",
            "email" => $email
        ]
    ],
    "products" => $products
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.bigcommerce.com/stores/{$storeHash}/v2/orders",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($orderData),
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "Content-Type: application/json",
        "X-Auth-Token: {$authToken}"
    ],
]);
$response = curl_exec($curl);


$dataaaaa = json_decode($response, true);


}

// get access token for securpay


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => '	https://welcome.api2.auspost.com.au/oauth/token',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'grant_type=client_credentials&audience=https%3A%2F%2Fapi.payments.auspost.com.au',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic ',
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

$response = curl_exec($curl);

$response;



$data = json_decode ($response);

$accessToken = $data->access_token;



// creating payment in securepay 

$orderId = $dataaaaa['id'];
$merchantCode = "";
$ip = "127.0.0.1"; 
$idempotencyKey = uniqid(); 

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://payments.auspost.net.au',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{ "amount": '.$totalAmount.', 
            "merchantCode": "", 
            "token": '.$secureToken.', 
            "ip": "127.0.0.1", 
            "orderId":'.$orderId.'
          }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Idempotency-Key:'.$idempotencyKey,
      'Authorization: Bearer '.$accessToken
    ),
  ));
  
  $response = curl_exec($curl);
  
  curl_close($curl);
  $response;
  


// update order api bigcommrece 
$data = json_decode($response, true);

$status = $data['status'];

if ($status == 'paid') {
    
    $storeHash = ""; 

    $authToken = ""; 
    
    $customerId = $customer_id;
    $statusId = 8;
    
    $billingAddress = [
        "first_name" => $billingFirstName,
        "last_name" => $billingLastName,
        "company" => "",
        "street_1" => $billingAddress1,
        "street_2" => $billingAddress2,
        "city" => $billingCity,
        "state" => $billingState,
        "zip" => $billingPostalCode,
        "country" =>  $billingCountry,
        "country_iso2" => "AU", 
        "email" => $email
        
    ];
    
    // Prepare the API URL
    $apiUrl = "https://api.bigcommerce.com/stores/{$storeHash}/v2/orders/{$orderId}";
    
    // Construct the request payload
    $orderData = [
        "customer_id" => $customerId,
        "status_id" => $statusId,
        "billing_address" => $billingAddress
    ];

    $jsonData = json_encode($orderData, JSON_PRETTY_PRINT);
    
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Auth-Token: ' . $authToken
        ],
    ]);
    
    $response = curl_exec($curl);
    
    $data = json_decode($response);


    echo $status = $data->status;
       
}



// } 
?>



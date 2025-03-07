<?php
define('SITE_ID', 's2');
// global $USER;
// if (!$USER->IsAuthorized()) {
//     header('HTTP/1.1 401 Unauthorized');
//     http_response_code(403);
//     print(http_response_code());
//     die;
// }
// Create order
$entityBody = file_get_contents('php://input');
$customer_id = json_decode($entityBody, true)['id'];

$description = json_decode($entityBody, true)['description'];
if(is_null($description)){
    $description = 'Created by lin-web';
}
$quantity = json_decode($entityBody, true)['quantity'];
if(is_null($quantity)){
    $quantity = 1;
}
// $data = [
//     "customerId"                    =>$customer_id,          
//     "orderCanOverwriteContainer"    =>false,
//     "orderIsDemo"                   =>true,
//     "customerRequired"              =>false,
//     "orderQuantity"                 =>1,
//     "orderActivationLimit"          =>1,
//     "orderDescription"              =>'Order created by lin-web',
//     "orderStatus"                   =>0,
//     "orderType"                     =>0,
//     "isHardwareLicense"             => false];
$data = [
    "customerId"                    =>$customer_id,      
    "orderDescription"              =>$description,
    "orderQuantity"                 =>$quantity,
    ];

$serviceUrl = 'http://guardantapi:5000/create-order';
$headers = [
    'Content-Type: application/json',
    'Accept: */*',
];
$myCurl = curl_init();
curl_setopt_array($myCurl, [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $serviceUrl,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data)
]);
$response = curl_exec($myCurl);
curl_close($myCurl);

echo json_encode($response);
?>


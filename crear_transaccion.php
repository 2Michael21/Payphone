<?php
// crear_transaccion.php
header('Content-Type: application/json');

// Configuración de PayPhone
$apiUrl = 'https://pay.payphonetodoesposible.com/api/Links';
$token = 'SEY_ZM2WFaI-FLBMBxwFygIozoBXzoArIKJJt4a22YsDX02zoTGfCLKFd71E6WikC-M2fdJ_GD8gFdPD_AAogUTmeJURL2MVB_EFi1We0yPdr3LpyntFwX7vwXvhFjQ2bkaoEyLxEG7ZHiQMtLc8KNzpBRkSYbQdmjrWXIIMChPzspO8PWWKXQubG0nEdfYuW4lax0DRUTub3BCbOeOBJ3DYne88IpG4qhUys8uO70WnyU2uESsnBRFXyPWn443XueFjEVZ0pbqfl8XNHPujPGjzgd1VaUl2r0gvzhlF4Pcn7k8wurgT5-qoN6WuvD0cqtd8Tw'; // Reemplaza con tu token de PayPhone

$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$documentId = $_POST['documentId'] ?? '';
$phoneNumber = $_POST['phoneNumber'] ?? '';
$amount = $_POST['amount'] ?? 0;
$reference = $_POST['reference'] ?? '';
$amountCents = round(floatval($amount) * 100);
$clientTransactionId = uniqid('pp_', true);

// Datos para la transacción
$data = [
    'amount' => $amountCents, // PayPhone usa centavos
    'amountWithoutTax' => $amountCents, // Ajusta según tu lógica de impuestos
    'tax' => 0,
    'service' => 0,
    'tip' => 0,
    'clientTransactionId' => $clientTransactionId,
    'phoneNumber' => $phoneNumber,
    'email' => $email,
    'documentId' => $documentId,
    'reference' => $reference,
    'responseUrl' => 'http://localhost/pagoseguro/confirma.php',
    'cancellationUrl' => 'http://localhost',
    'storeId' => '151907d5-0476-42c2-8f9d-ee424c5480bc' // StoreId real proporcionado
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo $response;
} else {
    // Intenta decodificar el error de la API para mostrarlo claramente
    $errorMsg = $response;
    $jsonError = json_decode($response, true);
    if (is_array($jsonError) && isset($jsonError['message'])) {
        $errorMsg = $jsonError['message'];
    }
    echo json_encode(['error' => 'No se pudo crear la transacción', 'details' => $errorMsg]);
}
?>

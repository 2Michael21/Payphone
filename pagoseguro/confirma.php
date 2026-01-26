<?php
// pagoseguro/confirma.php
// Esta página recibe la respuesta de PayPhone después del pago y consulta el estado real

$id = $_GET['id'] ?? '';
$clientTransactionId = $_GET['clientTransactionId'] ?? '';
$resultado = null;
$error = null;

if ($id && $clientTransactionId) {
    $apiUrl = 'https://pay.payphonetodoesposible.com/api/button/V2/Confirm';
    $token = 'SEY_ZM2WFaI-FLBMBxwFygIozoBXzoArIKJJt4a22YsDX02zoTGfCLKFd71E6WikC-M2fdJ_GD8gFdPD_AAogUTmeJURL2MVB_EFi1We0yPdr3LpyntFwX7vwXvhFjQ2bkaoEyLxEG7ZHiQMtLc8KNzpBRkSYbQdmjrWXIIMChPzspO8PWWKXQubG0nEdfYuW4lax0DRUTub3BCbOeOBJ3DYne88IpG4qhUys8uO70WnyU2uESsnBRFXyPWn443XueFjEVZ0pbqfl8XNHPujPGjzgd1VaUl2r0gvzhlF4Pcn7k8wurgT5-qoN6WuvD0cqtd8Tw'; // Usa tu token real
    $data = [
        'id' => $id,
        'clientTxId' => $clientTransactionId
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
    if ($response === false) {
        $error = 'Error de conexión: ' . curl_error($ch);
    } elseif ($httpCode != 200) {
        $error = 'Error de la API: ' . $response;
    } else {
        $resultado = json_decode($response, true);
    }
    curl_close($ch);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Pago</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: rgba(30,41,59,0.10); margin: 0; padding: 0; }
        .modal-bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30,41,59,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            z-index: 10;
        }
        .modal {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(60,72,100,0.18);
            padding: 2.2rem 2.2rem 1.7rem 2.2rem;
            max-width: 420px;
            width: 95vw;
            text-align: center;
            position: relative;
        }
        .modal h1 {
            color: #2563eb;
            margin-bottom: 1.2rem;
        }
        .ok { color: #16a34a; font-weight: bold; font-size: 1.15rem; margin-bottom: 1.2rem; }
        .fail { color: #dc2626; font-weight: bold; font-size: 1.1rem; margin-bottom: 1.2rem; }
        .info-list { text-align: left; margin: 1.2rem 0 1.5rem 0; padding: 0; list-style: none; }
        .info-list li { margin-bottom: 0.7rem; font-size: 1.08rem; }
        .btn-close {
            background: linear-gradient(90deg, #6366f1 0%, #60a5fa 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-size: 1.08rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 1.2rem;
            transition: background 0.18s, transform 0.18s;
        }
        .btn-close:hover {
            background: linear-gradient(90deg, #4f46e5 0%, #2563eb 100%);
            transform: scale(1.04);
        }
    </style>
</head>
<body>
    <div class="modal-bg">
        <div class="modal">
            <h1>Confirmación de Pago</h1>
            <?php if ($id && $clientTransactionId): ?>
                <?php if ($resultado): ?>
                    <p class="ok">¡Pago confirmado correctamente!</p>
                    <ul class="info-list">
                        <li><b>ID de transacción:</b> <?= htmlspecialchars($id) ?></li>
                        <li><b>Referencia:</b> <?= htmlspecialchars($resultado['reference'] ?? '-') ?></li>
                        <li><b>Estado:</b> <?= htmlspecialchars($resultado['transactionStatus'] ?? '-') ?></li>
                        <li><b>Monto:</b> $<?= isset($resultado['amount']) ? number_format($resultado['amount']/100,2) : '-' ?></li>
                        <li><b>Fecha:</b> <?= htmlspecialchars($resultado['date'] ?? '-') ?></li>
                        <li><b>Autorización:</b> <?= htmlspecialchars($resultado['authorizationCode'] ?? '-') ?></li>
                        <li><b>Medio de pago:</b> <?= htmlspecialchars($resultado['paymentMethod'] ?? '-') ?></li>
                    </ul>
                <?php elseif ($error): ?>
                    <p class="fail">Error al confirmar el pago:</p>
                    <div style="background:#f1f5f9;border-radius:8px;padding:1rem;overflow-x:auto;"> <?= htmlspecialchars($error) ?> </div>
                <?php else: ?>
                    <p class="fail">No se pudo obtener el estado del pago.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="fail">No se recibieron los parámetros necesarios en la URL.</p>
            <?php endif; ?>
            <button class="btn-close" onclick="window.close();window.location='http://localhost/form/index.html'">Cerrar</button>
        </div>
    </div>
</body>
</html>

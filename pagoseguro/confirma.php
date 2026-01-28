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
    <!-- Librerías de UI -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($id && $clientTransactionId): ?>
                <?php if ($resultado): ?>
                    // Transacción Aprobada (o con estado obtenido)
                    <?php if (($resultado['transactionStatus'] ?? '') === 'Approved'): ?>
                        // Lanzar Confetti
                        var duration = 3 * 1000;
                        var animationEnd = Date.now() + duration;
                        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

                        function randomInRange(min, max) {
                            return Math.random() * (max - min) + min;
                        }

                        var interval = setInterval(function () {
                            var timeLeft = animationEnd - Date.now();

                            if (timeLeft <= 0) {
                                return clearInterval(interval);
                            }

                            var particleCount = 50 * (timeLeft / duration);
                            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
                        }, 250);

                        Swal.fire({
                            title: '¡Pago Exitoso!',
                            html: `
                                <div style="text-align: left; font-size: 1rem; line-height: 1.6;">
                                    <p><b>Transacción:</b> <?= htmlspecialchars($id) ?></p>
                                    <p><b>Monto:</b> $<?= isset($resultado['amount']) ? number_format($resultado['amount'] / 100, 2) : '-' ?></p>
                                    <p><b>Referencia:</b> <?= htmlspecialchars($resultado['reference'] ?? '-') ?></p>
                                    <p><b>Autorización:</b> <?= htmlspecialchars($resultado['authorizationCode'] ?? '-') ?></p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Volver a la Tienda',
                            allowOutsideClick: false,
                            backdrop: `rgba(99, 102, 241, 0.1)`
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = '/index.html';
                            }
                        });

                    <?php else: ?>
                        // Transacción con otro estado (e.g. Rechazada, Pendiente)
                        Swal.fire({
                            title: 'Estado de la Transacción',
                            text: 'El estado es: <?= htmlspecialchars($resultado['transactionStatus'] ?? 'Desconocido') ?>',
                            icon: 'info',
                            confirmButtonText: 'Volver',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = '/index.html';
                            }
                        });
                    <?php endif; ?>

                <?php elseif ($error): ?>
                    // Error en la API
                    Swal.fire({
                        title: 'Hubo un problema',
                        text: '<?= addslashes($error) ?>',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    }).then(() => {
                        window.location = 'http://localhost/form/index.html';
                    });
                <?php else: ?>
                    // Error desconocido
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo obtener la información del pago.',
                        icon: 'error',
                        confirmButtonText: 'Volver'
                    }).then(() => {
                        window.location = 'http://localhost/form/index.html';
                    });
                <?php endif; ?>
            <?php else: ?>
                // Sin parámetros
                Swal.fire({
                    title: 'Acceso Inválido',
                    text: 'No se recibieron los parámetros de transacción.',
                    icon: 'warning',
                    confirmButtonText: 'Ir al Inicio'
                }).then(() => {
                    window.location = 'http://localhost/form/index.html';
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>
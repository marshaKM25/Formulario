<?php
/**
 * Procesar CAPTCHA seguro con fingerprint
 * - SHA-256 para hash
 * - hash_equals() para comparación segura
 * - Máximo de intentos por usuario
 * - Bloqueo temporal individual
 * - Captcha de un solo uso
 */

session_start();
include_once 'funcs/funcs.php';

// --- CONFIGURACIÓN ---
$maxIntentos = 3;
$tiempoBloqueoSeg = 300; // 5 minutos
$tiempoExpiracionCaptcha = 120; // 2 minutos

// --- SANITIZACIÓN DE ENTRADAS ---
$nombre = trim($_POST['nombre'] ?? '');
$codigo = trim($_POST['codigo'] ?? '');
$contraseña = $_POST['contraseña'] ?? ''; // No se usa trim en contraseñas por si los espacios son intencionales

//Obtenemos el form_id del formulario para saber qué captcha validar.
// Se asume 'default' si no se envía.
$formID = 'default';
if (isset($_POST['form_id'])) {
    $formID = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['form_id']);
}

if (empty($nombre) || empty($codigo) || empty($contraseña)) {
    setFlashData('error', 'Debe llenar todos los datos');
    redirect('index.php');
}

// Generar fingerprint del navegador
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . session_id());

// Inicializa intentos por fingerprint si no existe
if (!isset($_SESSION['intentos_por_fingerprint'])) {
    $_SESSION['intentos_por_fingerprint'] = [];
}

$intentosUsuario = $_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] ?? 0;
$ultimoIntento = $_SESSION['intentos_por_fingerprint'][$fingerprint]['last_attempt'] ?? 0;

// Verificar bloqueo temporal
if ($intentosUsuario >= $maxIntentos) {
    $tiempoRestante = $tiempoBloqueoSeg - (time() - $ultimoIntento);
    if ($tiempoRestante > 0) {
        setFlashData('error', 'Has excedido el número máximo de intentos. Intenta de nuevo en ' . ceil($tiempoRestante) . ' segundos.');
        redirect('bloqueado.php');
    } else {
        // Reiniciar contador después del bloqueo
        $_SESSION['intentos_por_fingerprint'][$fingerprint] = ['count' => 0, 'last_attempt' => 0];
        $intentosUsuario = 0;
    }
}

$captchaData = $_SESSION['captcha'][$formID] ?? null;
$captchaHash = $captchaData['hash'] ?? '';
$captchaTime = $captchaData['time'] ?? 0;
$codigoIngresadoHash = hash('sha256', $codigo);

// Verificar si el captcha existe en la sesión
if (empty($captchaHash)) {
    setFlashData('error', 'No se encontró un captcha válido. Por favor, recarga la página.');
    redirect('index.php');
}

// Verificar expiración
if (time() - $captchaTime > $tiempoExpiracionCaptcha) {
    unset($_SESSION['captcha'][$formID]); // Limpiar captcha expirado
    setFlashData('error', 'El captcha ha expirado. Por favor, intenta de nuevo.');
    redirect('index.php');
}

// Comparación segura a prueba de ataques de tiempo
if (!hash_equals($captchaHash, $codigoIngresadoHash)) {
    // Registrar intento fallido
    $_SESSION['intentos_por_fingerprint'][$fingerprint] = [
        'count' => $intentosUsuario + 1,
        'last_attempt' => time()
    ];
    unset($_SESSION['captcha'][$formID]); // Captcha de un solo uso
    sleep(2); // Retardo para disuadir ataques de fuerza bruta
    setFlashData('error', 'Código de verificación incorrecto. Intento #' . ($intentosUsuario + 1));
    redirect('index.php');
}

// El captcha es correcto
unset($_SESSION['captcha'][$formID]); // Borra el captcha usado para que no se pueda reutilizar
$_SESSION['intentos_por_fingerprint'][$fingerprint]['count'] = 0; // Reinicia el contador de intentos
echo "¡Verificación exitosa! Bienvenido, " . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');

?>
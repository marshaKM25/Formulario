<?php
session_start();


header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// --- CONFIGURACIÓN DE ALTA SEGURIDAD ---
define('ANCHO', 200); // Más ancho para mayor espaciado
define('ALTO', 60);
define('CODIGO_LENGTH', rand(5, 6));
define('NUM_LINEAS', 10);
define('NUM_PUNTOS', 800);

// Juego de Caracteres Mejorado

$caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
$codigo = '';
for ($i = 0; $i < CODIGO_LENGTH; $i++) {
    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}

// Apuntamos a la fuente que instalamos DENTRO del contenedor.
$fuente = '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf'; // Usamos la versión Bold para mejor visibilidad

// Guardar en sesión con hash seguro + tiempo de expiración
$_SESSION['codigo_verificacion'] = hash('sha256', $codigo);
$_SESSION['captcha_time'] = time();
$_SESSION['captcha_expires'] = 120; // 2 minutos

// Crear lienzo
$imagen = imagecreatetruecolor(ANCHO, ALTO);

// Paleta de Colores de Alto Contraste

$colorFondo = imagecolorallocate($imagen, rand(220, 255), rand(220, 255), rand(220, 255));
$colorRuidoClaro = imagecolorallocate($imagen, rand(160, 220), rand(160, 220), rand(160, 220));
imagefill($imagen, 0, 0, $colorFondo);

// Dibujar "ruido" (líneas y puntos)
for ($i = 0; $i < NUM_LINEAS; $i++) {
    imageline($imagen, 0, rand(0, ALTO), ANCHO, rand(0, ALTO), $colorRuidoClaro);
}
for ($i = 0; $i < NUM_PUNTOS; $i++) {
    imagesetpixel($imagen, rand(0, ANCHO), rand(0, ALTO), $colorRuidoClaro);
}

// Escribir cada carácter con distorsión y variaciones
for ($i = 0; $i < strlen($codigo); $i++) {
    // Variación por Carácter

    $tamanioFuente = rand(22, 28);
    $angulo = rand(-15, 15);
    $x = 20 + ($i * 30);
    $y = rand(35, 45);
    $colorLetra = imagecolorallocate($imagen, rand(0, 120), rand(0, 120), rand(0, 120));
    imagettftext($imagen, $tamanioFuente, $angulo, $x, $y, $colorLetra, $fuente, $codigo[$i]);
}

// Distorsión de Onda (Warping)
$centroY = ALTO / 2;
$amplitud = rand(3, 5);
$periodo = rand(80, 100);
$distorsionX = rand(10, 20);

for ($x = 0; $x < ANCHO; $x++) {
    // Fórmula de la onda sinusoidal para calcular el desplazamiento vertical
    $desplazamientoY = (int)($amplitud * sin(2 * M_PI * ($x + $distorsionX) / $periodo));
    // Copia una columna de píxeles a su nueva posición distorsionada
    imagecopy($imagen, $imagen, $x, $desplazamientoY, $x, 0, 1, ALTO);
}

// Enviar imagen final y liberar memoria
imagepng($imagen);
imagedestroy($imagen);

?>





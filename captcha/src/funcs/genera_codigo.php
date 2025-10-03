<?php
session_start();

// SANITIZACIÓN DE ENTRADA 
$formID = 'default';
if (isset($_GET['form_id'])) {
    $formID = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['form_id']);
}

// --- CONFIGURACIÓN ---
define('ANCHO', 220);
define('ALTO', 80);
define('CODIGO_LENGTH', rand(5, 6));
define('NUM_FRAMES', 15);
define('FRAME_DURATION', 12);
define('NUM_LINEAS', 20);   // Más líneas de ruido
define('NUM_ARCS', 15);     // tipo de ruido arcos

// --- GENERACIÓN DE CÓDIGO ---
$caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$codigo = '';
for ($i = 0; $i < CODIGO_LENGTH; $i++) {
    $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}

$_SESSION['captcha'][$formID] = [
    'hash' => hash('sha256', $codigo),
    'time' => time()
];

$fuentes = [
    '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
    '/usr/share/fonts/dejavu/DejaVuSerif-Bold.ttf',
    '/usr/share/fonts/dejavu/DejaVuSansMono-Bold.ttf'
];

// --- CREACIÓN DE LA ANIMACIÓN ---
$animacion = new Imagick();
$animacion->setFormat('gif');

for ($frame = 0; $frame < NUM_FRAMES; $frame++) {
    $imagen = new Imagick();
    $colorFondo = new ImagickPixel('rgb(' . rand(220, 255) . ',' . rand(220, 255) . ',' . rand(220, 255) . ')');
    $imagen->newImage(ANCHO, ALTO, $colorFondo);
    $imagen->setImageFormat('png');

    $dibujo = new ImagickDraw();

    // Ruido de fondo extremo y dinámico
    $dibujo->setStrokeWidth(1.5);
    for ($i = 0; $i < NUM_LINEAS; $i++) {
        $dibujo->setStrokeColor(new ImagickPixel('rgb(' . rand(180, 220) . ',' . rand(180, 220) . ',' . rand(180, 220) . ')'));
        $dibujo->line(rand(0, ANCHO), rand(0, ALTO), rand(0, ANCHO), rand(0, ALTO));
    }
    for ($i = 0; $i < NUM_ARCS; $i++) {
        $dibujo->setStrokeColor(new ImagickPixel('rgb(' . rand(180, 220) . ',' . rand(180, 220) . ',' . rand(180, 220) . ')'));
        $dibujo->arc(rand(0, ANCHO), rand(0, ALTO), rand(0, ANCHO), rand(0, ALTO), rand(0, 360), rand(0, 360));
    }
    $imagen->drawImage($dibujo);

    //Aleatoriedad máxima en los caracteres
    for ($i = 0; $i < strlen($codigo); $i++) {
        $dibujoLetra = new ImagickDraw();
        $dibujoLetra->setFont($fuentes[array_rand($fuentes)]);
        $dibujoLetra->setFontSize(rand(28, 40)); // Rango de tamaño más amplio
        $dibujoLetra->setFillColor(new ImagickPixel('rgb(' . rand(0, 100) . ',' . rand(0, 100) . ',' . rand(0, 100) . ')'));
        $dibujoLetra->setGravity(Imagick::GRAVITY_CENTER);

        // Superposición de caracteres y vibración
        $x_offset = ($i - (CODIGO_LENGTH / 2) + 0.5) * 30 + rand(-3, 3); // Espaciado más reducido para superposición
        $y_offset = (int)(sin(($frame / NUM_FRAMES) * 2 * M_PI + ($i / CODIGO_LENGTH) * M_PI) * 8) + rand(-3, 3);

        // cada letra con rotación extrema
        $imagen->annotateImage($dibujoLetra, $x_offset, $y_offset, rand(-45, 45), $codigo[$i]);
    }

    //Distorsión de fotograma completo
    $imagen->swirlImage(rand(-8, 8));
    $imagen->waveImage(2, rand(90, 110));

    $imagen->setImageDelay(FRAME_DURATION);
    $animacion->addImage($imagen);
    $imagen->clear();
}

header('Content-Type: image/gif');
echo $animacion->getImagesBlob();

$animacion->clear();
?>


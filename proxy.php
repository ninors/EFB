<?php
// proxy.php - sert de relais pour afficher les fichiers distants dans un iframe

if (!isset($_GET['url'])) {
    http_response_code(400);
    exit("URL manquante");
}

$url = $_GET['url'];

// Validation basique pour éviter que quelqu’un ne s’en serve pour tout et n'importe quoi
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit("URL invalide");
}

// Télécharge le fichier depuis la source
$context = stream_context_create([
    "http" => [
        "header" => "User-Agent: ChartProxy/1.0\r\n"
    ]
]);

$data = @file_get_contents($url, false, $context);

if ($data === false) {
    http_response_code(500);
    exit("Impossible de récupérer la ressource distante.");
}

// Détecte le type MIME
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->buffer($data);

// Force l'affichage dans le navigateur
header("Content-Type: " . $mime);
header("Content-Disposition: inline");
echo $data;

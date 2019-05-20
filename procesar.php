<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>Document</title>
</head>
<body>
<div class="container">
<?php

require 'vendor/autoload.php';
include_once "conn.php";
include "urlAmigable.php";
include_once "./import/mashable.php";
include_once "./import/gizmodo.php";
use Aws\S3\S3Client;

// S3
$key = 'AKIARUGS3XNMZ3653JHQ';
$secret = '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';
$credentials = new Aws\Credentials\Credentials($key , $secret);
$s3 = new S3Client([
  'version' => 'latest',
  'region'  => 'us-west-2',
  'credentials'=>$credentials
]);
date_default_timezone_set('America/Lima');


// Create DOM from URL or file
$url = $_POST['url'];
$web = $_POST['web'];
$usuario = $_POST['usuario'];

$html = file_get_contents($url);
$html = str_get_html($html);


// Nombre de Imagenes
$nombre = $conn->query("SELECT AUTO_INCREMENT FROM information_schema.`TABLES` T 
                        where TABLE_SCHEMA = 'monases' and TABLE_NAME = 'wp_posts'")->fetch_array(MYSQLI_ASSOC);
$nombre = $nombre['AUTO_INCREMENT'];
$anho = date('Y');
$mes = date('m');

if ($web == 'mashable'){
  $response = mashableImport($html,$s3,$anho,$mes,$nombre);
}elseif ($web == 'gizmodo'){
  $response = gizmodoImport($html,$s3,$anho,$mes,$nombre);
}

$titulo = $response['titulo'];
$contenido = $response['contenido'];
//echo $contenido;
$contenido = addslashes($contenido);
$insertPost = "insert into wp_posts set post_author = ".$usuario.",
                                        post_content = '$contenido',
                                        post_title = '$titulo',
                                        post_name = '".url_amigable($titulo)."',
                                        guid = 'https://monases.com/".url_amigable($titulo)."/',
                                        post_status = 'draft',
                                        post_date_gmt = '".date( 'Y-m-d H:i:s')."',
                                        post_date = '".date( 'Y-m-d H:i:s')."',
                                        post_excerpt = '',
                                        to_ping = '',
                                        pinged = '',
                                        post_content_filtered = '',
                                        post_password = '',
                                        post_mime_type = ''                                       
                                        ";
$conn->query($insertPost);
?>

  <p>
    Se importo con exito <a href="./index.php">Regresar</a></p>
  <p>
    Editar el Post:  <a href="http://monases.com/wp-admin/post.php?post=<?php echo $nombre; ?>&action=edit">Editar Post</a>.
  </p>
</div>
</body>
</html>


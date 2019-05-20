<?php
include_once "vendor/autoload.php";

$url = 'https://gizmodo.com/swedish-prosecutors-reopen-rape-case-against-julian-ass-1834713678';
$html = file_get_html($url);
$titulo = $html->find('h1',0)->plaintext;
echo $titulo;





?>

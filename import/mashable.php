<?php

function mashableImport($html,$s3,$anho,$mes,$nombre){
  $numero = 1;
  $response = array();
  $contenido = '';

  // imagenHome to S3
  $imageHome = $html->find('figure.article-image img',0)->src;
  $result = $s3->putObject([
    'Bucket' => 'monases',
    'Key'    => $anho.'/'.$mes.'/'.$nombre.'_'.$numero.'.jpg',
    'ContentType' => 'image/jpeg',
    'Body'   => file_get_contents($imageHome),
    'ACL'    => 'public-read'
  ]);


  // video Youtube formatea
  foreach ($html->find('.youtube-wrapper p') as $youtube){
    $youtube->class = 'youtube';
    $youtube->children(0)->width = "640";
    $youtube->children(0)->height = "360";
    $youtube->children(0)->frameborder = "0";
  }


  // Elimina el "SEE ALSO"
  foreach ($html->find('.see-also p') as $seeAlso){
    $seeAlso->outertext = '';
  }


  // Elimina creditos imagen
  foreach ($html->find('.image-credit p') as $imageCredit){
    $imageCredit->outertext = '';
  }


  // Elimina imagen Video ultimo
  foreach ($html->find('.bonus-video-card p') as $bonusVideoCard){
    $bonusVideoCard->outertext = '';
  }


  // Elimina Twwiters
  foreach ($html->find('.twitter-tweet') as $twitter){
    $twitter->find('p',0)->outertext = '';
    $twitterUrl = $twitter->find('p',1)->children(0)->href;
    $twitter->find('p',1)->class = "twitter";
    $twitter->find('p',1)->innertext = $twitterUrl;
  }



  // Add Title Home
  $titulo = trim($html->find('h1',1)->plaintext);
  $response['titulo'] = $titulo;
  $contenido .= '<h1>'.$titulo.'</h1>';

  // Add Image Home
  $contenido .= '<img src="https://s3-us-west-2.amazonaws.com/monases/'.$anho.'/'.$mes.'/'.$nombre.'_'.$numero.'.jpg"/>';
  $numero ++;

  $firstPost = 1;
  foreach ($html->find('section.article-content p') as $p){

    if (strlen($p->outertext) > 0){

      // Primer parrafo
      if ($firstPost == 1){
        $p->class = 'has-drop-cap';
        $firstPost++;
      }

      // Sube imagen
      if ($img = $p->find('img',0)){
        if (strpos($img->src,'.gif')){
          $type = 'image/gif';
          $ext = '.gif';
        }elseif (strpos($img->src,'.png')){
          $type = 'image/png';
          $ext = '.png';
        }else {
          $type = 'image/jpeg';
          $ext = '.jpg';
        }
        $result = $s3->putObject([
          'Bucket' => 'monases',
          'Key'    => $anho.'/'.$mes.'/'.$nombre.'_'.$numero.$ext,
          'ContentType' => $type,
          'Body'   => file_get_contents($img->src),
          'ACL'    => 'public-read'
        ]);

        // Si hay una imagen la reemplaza
        $p->find('img',0)->outertext = '<img src="https://s3-us-west-2.amazonaws.com/monases/'.$anho.'/'.$mes.'/'.$nombre.'_'.$numero.$ext.'"/>';
        $contenido .= $p->innertext;
        $numero++;
      }elseif ('youtube' == $p->class){
        $contenido .= '<!-- wp:html -->'.$p->innertext.'<!-- /wp:html -->';
      }elseif ('twitter' == $p->class){
        $contenido .= '<!-- wp:html --><blockquote class="twitter-tweet"><a href="'.$p->innertext.'"></a></blockquote><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script><!-- /wp:html -->';
      }else{
        $contenido .= $p->outertext;
      }
    }
  }
  $response['contenido'] = $contenido;
  return $response;
}

?>
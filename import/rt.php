<?php



function rtImport($html,$s3,$anho,$mes,$nombre){
$numero = 1;
$contenido = '';



//    Titulo
$titulo = $html->find('.article__heading',0)->plaintext;

$contenido .= "<h1>$titulo</h1>";


// Resumen
$resumen = $html->find('.article__summary',0)->plaintext;



// Imagenes formatea
$imagePrimero = 1;
foreach ($html->find('.article picture') as $image){
    $src = $image->find('source',0)->{'data-srcset'};
    $temp = explode(',',$src);
    if (count($temp) > 1){
        $src = explode(' 1960w',$temp['5']);
        $src = trim($src['0']);
    }

    // Agrega la primera imagen despues el resumen
    if ($imagePrimero == 1){
        $image->outertext = "<p class='image'><img src='$src'/></p><p class='resumen'>$resumen</p>";
        $imagePrimero++;
    }else{
        $image->outertext = "<p class='image'><img src='$src'/></p>";
    }

}

// Youtube
foreach ($html->find('.video-iframe') as $youtube){
    if ($you = $youtube->find('iframe',0)->{'data-src'}){
        $youtube->outertext = '<p class="youtube"><iframe width="640" height="360" src="https:'.$you.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></p>';
    }
}

// Twitter
foreach ($html->find('.rtcode blockquote') as $twitter){
    if ($href = $twitter->find('a',-1)->href){
        $twitter->outertext = '<p class="twitter"><blockquote class="twitter-tweet"><a href='.$href.'></a></blockquote><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></p>';
    }
}


// Remove post relacionados medianos y grandes
foreach ($html->find('.read-more') as $remove){
    $remove->outertext = '';
}
foreach ($html->find('.read-more-big') as $remove){
    $remove->outertext = '';
}
foreach ($html->find('.article p') as $readMore){
    $tempReadMore = explode('READ MORE:',$readMore->outertext);
    $stories = explode('Share this story',$readMore->outertext);
    $stories2 = explode('Like this story?',$readMore->outertext);
    $stories3 = explode('to RT newsletter to get stories the mainstream',$readMore->outertext);
    $stories4 = explode('If you like this story',$readMore->outertext);

    if (count($tempReadMore) > 1){
        $readMore->outertext = '';
    }
    if (count($stories) > 1){
        $readMore   ->outertext = '';
    }
    if (count($stories2) > 1){
        $readMore   ->outertext = '';
    }
    if (count($stories3) > 1){
        $readMore   ->outertext = '';
    }
    if (count($stories4) > 1){
        $readMore   ->outertext = '';
    }
}


// Contenido
$html = str_get_html($html);
$interaccion = 1;
foreach ($html->find('.article p') as $p){
    if ('youtube' == $p->class){
        $contenido .= '<!-- wp:html -->'.$p->innertext.'<!-- /wp:html -->';
    }elseif ('twitter' == $p->class){
        $contenido .= '<!-- wp:html -->'.$p->innertext.'<!-- /wp:html -->';
    }elseif ('image' == $p->class){

        $img = $p->find('img',0)->src;
        if (strpos($img,'.gif') && $interaccion > 1){
            $type = 'image/gif';
            $ext = '.gif';
        }elseif (strpos($img,'.png') && $interaccion > 1 ){
            $type = 'image/png';
            $ext = '.png';
        }else {
            $type = 'image/jpeg';
            $ext = '.jpg';
        }
        $s3->putObject([
            'Bucket' => 'monases',
            'Key'    => $anho.'/'.$mes.'/'.$nombre.'_'.$numero.$ext,
            'ContentType' => $type,
            'Body'   => file_get_contents($img),
            'ACL'    => 'public-read'
        ]);
        $contenido .= '<p><img src="https://s3-us-west-2.amazonaws.com/monases/'.$anho.'/'.$mes.'/'.$nombre.'_'.$numero.$ext.'"/></p>';
        $numero++;
        $interaccion++;
    }else{
        $contenido .= "<p>$p->innertext</p>";
    }
}
//echo $contenido;
$response = Array();
$response['titulo'] = $titulo;
$response['contenido'] = $contenido;
return $response;
}

?>
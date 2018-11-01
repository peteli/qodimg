<?php
require 'qod.php';
?>
<?php
  // Create a blank image and add some text
  //$url='https://upload.wikimedia.org/wikipedia/commons/b/b4/JPEG_example_JPG_RIP_100.jpg';
  // url for request

$todqod = new QOD();
$todqod->getQuote();
//echo ($todqod->quote);
//echo ($todqod->author);
//echo ($todqod->background);
//$todqod = NULL;

//exit;

  // define url
  $url='https://source.unsplash.com/800x500/?wallpaper';

  //create image
  $im = imagecreatefromstring(getImage($url)); 

  // create fallback image if url doesn't work
  if(!$im)
  {
      /* Erzeuge ein schwarzes Bild */
      $im  = imagecreatetruecolor(800, 500);
      $bgc = imagecolorallocate($im, 255, 255, 255);
      $tc  = imagecolorallocate($im, 0, 0, 0);

      imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

      /* Gib eine Fehlermeldung aus */
      imagestring($im, 5, 5, 5, 'Fehler beim Laden von ' . $url, $tc);
  }

  // define color
  $black = imagecolorallocatealpha ($im, 0, 0, 0, 10);
  $red = imagecolorallocatealpha($im, 0xFF, 0x00, 0x00, 10);
  $white = imagecolorallocatealpha($im, 0xFF, 0xFF, 0xFF, 70);
  //imagestring($im, 5, 15, 15,  'A Simple Text String', $black);

  
  // Set the enviroment variable for GD to find font file
  putenv('GDFONTPATH=' . realpath('.'));
  // Path to our ttf font file
  $font_file = 'Kalam-Regular.ttf';
  $font_size = 30;
  $goldenratio= 1.61803398875;
  #$text = 'Wir stehen hier am Rande des Wahnsinns und denken uns nichts dabei, aber die Welt hat sich weiter gedreht und wir hoffen auf bessere Zeiten.';
  $text = $todqod->quote;

  // line breaks depending on image width
  list($textblock,$font_size) = makeTextBlock($text,$font_file,$font_size,(imagesx( $im )/$goldenratio),(imagesy( $im )/$goldenratio));

  // check size and create opaque rectangle 
  #$boxarray = imagettfbbox ( $font_size ,0 , $font_file , $textblock );
  #$height = abs($boxarray[1] - $boxarray[5]);
  #$width = abs($boxarray[4] - $boxarray[0]);  
  list($width,$height) = testTextBlock( $font_size ,0 , $font_file , $textblock );
  #imagestring($im, 5, 15, 400, $boxarray[0].'-'.$boxarray[1].'-'.$boxarray[2].'-'.$boxarray[3].'-'.$boxarray[4].'-'.$boxarray[5].'-'.$boxarray[6].'-'.$boxarray[7].'-'.$width.'-'.$height, $black);
  #imagestring($im, 5, 15, 420,  $height, $black);

  // draw rectangle
  imagefilledrectangle($im,5,5,$width+20,$height+20,$white);

  // Draw the text
  imagettftext($im, $font_size, 0, 10, 40, $black, $font_file, $textblock);




// Set the content type header - in this case image/jpeg
  header('Content-Type: image/png');

  // Output the image png is the better image (instead of jpeg)
  imagepng($im);

  // Free up memory
  imagedestroy($im);


function testTextBlock( $font_size ,$angle = 0 , $font_file , $textblock ) {
  $boxarray = imagettfbbox ( $font_size ,0 , $font_file , $textblock );
  $height = abs($boxarray[1] - $boxarray[5]);
  $width = abs($boxarray[4] - $boxarray[0]);  
  return ([$width,$height]);
}

function makeTextBlock($text, $fontfile, $fontsize, $max_width, $max_height) {    
    $words = explode(' ', $text); 
    $lines = array($words[0]); 
    $currentLine = 0; 
    for($i = 1; $i < count($words); $i++) 
    { 
        $lineSize = imagettfbbox($fontsize, 0, $fontfile, $lines[$currentLine] . ' ' . $words[$i]); 
        if($lineSize[2] - $lineSize[0] < $max_width) 
        { 
            $lines[$currentLine] .= ' ' . $words[$i]; 
        } 
        else 
        { 
            $currentLine++; 
            $lines[$currentLine] = $words[$i]; 
        } 
    } 
    
    $textblock = implode("\n", $lines);
    
    //test height of text block and iterate with smaller fontsize if exceeds max_height of background image
    list(,$height)= testTextBlock( $fontsize ,0 , $fontfile , $textblock);
    if( $height > $max_height ) {
      list($textblock,$fontsize) = makeTextBlock($text,$fontfile,($fontsize-2),$max_width,$max_height);
    }
    return ([$textblock,$fontsize]); 
} 

function getImage($url){
        $ch = curl_init ($url);
      // Set options
    curl_setopt_array($ch, array(
        //CURLOPT_USERAGENT => $userAgent,

        CURLOPT_HEADER => false,
        //CURLOPT_NOBODY => true,

        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_BINARYTRANSFER => true,

        CURLOPT_FOLLOWLOCATION => true, 
        CURLOPT_MAXREDIRS => 10, 
        CURLOPT_AUTOREFERER => true, 

        CURLOPT_TIMEOUT => 5,   // 5 seconds (safety!)
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0',
    ));

       // curl_setopt($ch, CURLOPT_HEADER, 0);
       // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       // curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $resource = curl_exec($ch);
        curl_close ($ch);

        return $resource;
}

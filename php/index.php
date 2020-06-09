<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <p>kyouto</p>
    </header>
    
    <main>
    <?php
//画像全体のサイズ
$width = 600;
$height = 600;
 
//タイル一枚の最低サイズ
$block_size = 100;
 
//画像ディレクトリ
$dir = "./img/";
 
$list = scandir($dir);
$images = array();
 
foreach($list as $value){
    if(!is_file ($dir . $value)) continue;
    $images[] = load_image($dir . $value, $block_size );
}
 
$max_x = ceil($width / $block_size);
$max_y = ceil($height / $block_size);
 
shuffle($images);
 
$x_range = range(0, $max_x);
$y_range = range(0, $max_y);
 
shuffle($x_range);
shuffle($y_range);
 
$points = array();
 
foreach($x_range as $x){
    foreach($y_range as $y){
        $points[] = array("x" => $x, "y" => $y);
    }
}
 
$canvas = imagecreatetruecolor($width, $height);
 
foreach($points as $point){
    $x = $point['x'];
    $y = $point['y'];
        $current = current($images);
        imagecopy(
            $canvas, $current['resource'],
            $x * $block_size - ( round($current['width'] / 2) ),
            $y * $block_size - ( round($current['height'] / 2) ), 
            0, 0, $current['width'], $current['height']
        );
        $res = next($images);
        if($res === false) shuffle($images);
}
 
 
header("Content-type:image/jpeg");
imagejpeg($canvas, null, 80);
imagedestroy($canvas);
 
foreach($images as &$image){
    imagedestroy($image['resource']);
}
 
exit;
 
function load_image($filepath, $block_size){
    $checkimg = getimagesize($filepath);
    $width = $checkimg[0];
    $height = $checkimg[1];
  
    if($checkimg['mime'] == "image/jpeg" || $checkimg['mime'] == "image/pjpeg"){
        $extension = "jpg";
    } else if ($checkimg['mime'] == "image/gif"){
        $extension = "gif";
    } else if ($checkimg['mime'] == "image/png" || $checkimg['mime'] == "image/x-png"){
        $extension = "png";
    } else {
        exit;
    }
  
    if($extension == 'jpg'){$image = ImageCreateFromJPEG($filepath);}
    if($extension == 'gif'){$image = ImageCreateFromGIF($filepath);}
    if($extension == 'png'){$image = ImageCreateFromPNG($filepath);}
      
     
    $scale = $block_size / min($width, $height);
    $thumb_width  = $width * $scale;
    $thumb_height = $height * $scale;
     
    $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
    imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
      
    $info = array();
    $info['resource']    = $thumb;
    $info['width']       = $thumb_width;
    $info['height']      = $thumb_height;
    $info['extension']   = $extension;
     
    imagedestroy($image);
     
    return $info;
}?>
    </main>
</body>
</html>
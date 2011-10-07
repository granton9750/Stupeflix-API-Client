<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Stupeflix Factory Demo - 3</title>
  <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8"/>  
  <script type="text/javascript" charset="utf-8" src="flowplayer/flowplayer-3.1.1.min.js"></script>  
</head>
<body>
  <h1>3. Get the video</h1>
  <?php
    require_once 'stupeflix.php';
    require_once 'key.php';

    // sign the video's url with your editor secret key
    $video_id = $_GET['video_id'];
    $stupeflix = new StupeflixClient($FACTORY_HOST, $EDITOR_SECRET_KEY);    
    $video_url = $stupeflix->signUrl('/video/'.$video_id.'/');
            
    // compute the flash player dimensions
    $hres = (int)$_GET['hres'];
    $vres = (int)$_GET['vres'];   
    $player_dimensions = 'width: '.$hres.'px; height:'.($vres + 25).'px;';
  ?>
  
  <h2>Signed video's url:</h2>
  <div class="section">
    <a href="<?php echo $video_url ?>"><?php echo $video_url ?></a>
    <p class="infos">
      Signed video's url are valid for 1 minute.<br/>
      Generated video are available to download during 24 hours.<br/>
    </p>
  </div>
  
  <div id="flv_container" style="<?php echo $player_dimensions; ?>"></div>  
  <script type="text/javascript" charset="utf-8">
    var container = document.getElementById('flv_container');
    flowplayer(container, "./flowplayer/flowplayer-3.1.1.swf", "<?php echo urlencode($video_url) ?>");
  </script>
</body>
</html>

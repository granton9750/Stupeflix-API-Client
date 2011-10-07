<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Stupeflix Factory Demo - 2</title>
  <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8"/>
</head>
<body>
  
  <h1>2. Handle user's selection</h1>
  <?php
    $video_id = $_POST['video_id']; // selected video id
    $hres = $_POST['hres']; // horizontal resolution of the selected video in pixels
    $vres = $_POST['vres']; // vertical resolution of the selected video in pixels
    $thumb_url = $_POST['thumb_url'] // url of the video thumbnail
  ?>

  <h2>Posted parameters:</h2>
  <div class="section">  
    <table class="params">
    <?php foreach ($_POST as $key => $value){ ?>
      <tr><th><?php echo $key ?></th><td><?php echo $value ?></td></tr>
    <?php } ?>
    </table>
  </div>

  <h2>Video thumbnail:</h2>
  <div class="section">      
    <img src="<?php echo $thumb_url ?>"/>
  </div>
  
  <form method="get" action="./video.php">
    <input type="hidden" name="hres" value="<?php echo $hres ?>"/>
    <input type="hidden" name="vres" value="<?php echo $vres ?>"/>
    <input type="hidden" name="video_id" value="<?php echo $video_id ?>"/>
    
    <h2>Last step: <button type="submit">Get the video</button></h2>
  </form>
  
</body>
</html>

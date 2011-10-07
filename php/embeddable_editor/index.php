<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8"/> 
  <title>Stupeflix Factory Demo - 1</title>
</head>
<body>
  
  <h1>1. Spawn the editor</h1>
        
  <?php                                
    // get the stupeflix client and initialize it with your editor's secret key
    require_once 'stupeflix.php';
    require_once 'key.php';
    if ($EDITOR_ID == 'PUT-YOUR-EDITOR_ID-HERE'){
      die("Please fill in key information in key.php\n");
    }
    $stupeflix = new StupeflixClient($FACTORY_HOST, $EDITOR_SECRET_KEY);
    
    // compute the server url
    $server_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $parts = explode('?', $server_url);
    $server_url = $parts[0];
    
    // set up your editor's parameters
    $params = array();
    $params['action'] = $server_url.'callback.php';
    // the editor will POST on this url when an user select a video
    $params['target'] = '_parent';
    // the 'action' url will be opened in the parent window
    $params = array_merge($params, $_GET);
    // current GET parameters are also passed to the editor for testing
    // they will be POSTed inchanged on the 'action' url
            
    // sign your editor's url and its parameters with your editor's secret key
    $editor_url = $stupeflix->signUrl('/editor/'.$EDITOR_ID.'/', $params);
  ?>
  
  <h2>Signed editor's url:</h2>
  <div class="section">
    <a href="<?php echo $editor_url ?>"><?php echo $editor_url ?></a>
    <p class="infos">
      Signed editor's urls are valid for 1 minute.
    </p>
  </div>
  
  <div class="section">
    <iframe id="SxEditor" src="<?php echo  $editor_url ?>"></iframe>
    <p class="infos">
      Create a video and select it to go to the next step.
    </p>
  </div>
  
</body>
</html>

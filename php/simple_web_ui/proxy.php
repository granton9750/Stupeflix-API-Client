<?php
require_once '../commandline/stupeflix.php';
require_once 'key.php';

// Very simple proxy to demonstrate how to integrate the Stupeflix service to you site.
// This is intended as sample code, not production one : you should 
// for example add you own authentication system to prevent abuse.

// You should setup you access identifiers in key.php
if (stupeflixAccessKey == 'PUT-YOUR-ACCESS-KEY-HERE') 
{
  die("Please fill in key information in key.php\n");
}


// Create the Stupeflix Client
$stupeflixClient = new Stupeflix(stupeflixAccessKey, stupeflixSecretKey, stupeflixHost);

// These are the static names used to identify the video to be generated
// User name : you can use you own user names 
$api_user = "myuser";
// Resource name : this is the identifier of a specific user project
$api_resource = "myvideo";
// Profile name. See http://wiki.stupeflix.com for available profiles
$api_profile = "youtube";
 

// Dispatch on the type of action
if($_GET["action"]){
  $action = "Stupeflix_".$_GET["action"];
  $action();
}

// Launch the generation of a project
function Stupeflix_generate(){
  global $stupeflixClient, $api_user, $api_resource, $api_profile;

  // Get the post information
  $def = $_POST["definition"];
  if (get_magic_quotes_gpc() == 1){
    $def = stripslashes($def);
  }

  $metaDict = array(
    "title" => "Upload test ", 
    "description"=>"This, is, an, upload, test", 
    "tags"=>"upload, test",
    "channels"=>"Tech",
    "acl"=>"public",
    "location"=>"48.8583,2.2945" // A famous tower position
  );

  $meta = new StupeflixMeta($metaDict);
  
  $upload0 = new StupeflixDefaultUpload();
  // $upload1 = new StupeflixYoutubeUpload("MY_YOUTUBE_LOGIN", "MY_YOUTUBE_PASSWORD", $meta); //hardcode the login/password
  $uploads = array($upload0); // Add here upload1 if you want to upload to youtube
  
  $profile = new StupeflixProfile($api_profile, $uploads, $meta); 
  $profileSet = new StupeflixProfileSet(array($profile), null, null);
          
  // Send the xml to the Stupeflix video generation service
  $stupeflixClient->sendDefinition($api_user, $api_resource, null, $def);
  // Launch the creation of profiles
  $stupeflixClient->createProfiles($api_user, $api_resource, $profileSet);
}

// Retrieve the status of the generation
function Stupeflix_status(){
  global $stupeflixClient, $api_user, $api_resource, $api_profile;
  echo json_encode($stupeflixClient->getProfileStatus($api_user, $api_resource, $api_profile));
}

// Retrieve the video url
function Stupeflix_video(){
  global $stupeflixClient, $api_user, $api_resource, $api_profile;
  echo $stupeflixClient->getProfileURL($api_user, $api_resource, $api_profile);
}

?>
<?php
require_once '../commandline/stupeflix.php';
require_once 'key.php';

// Very simple proxy to demonstrate how to integrate the Stupeflix service to you site.
// This is intended as sample code, not production one : you should 
// for example add you own authentication system to prevent abuse.

// You should setup you access identifiers in key.php
if (stupeflixAccessKey == 'PUT-YOUR-ACCESS-KEY-HERE') 
{
  die("{error: 'Please fill in key information in key.php'}");
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
  
  // Sample XML Definition
  $XMLDefinition = '<movie service="craftsman-1.0">
    <body><stack>
      <effect type="none" depthEnable="false" ><image color="#FFFFFF"/></effect>
      <widget type="slideshow.reflect.simple.index">
        <image filename="http://storage.stupeflix.com/store/x3/w1/A2n2K8bpE4f8ISt/medium.jpg"/>
        <image filename="http://storage.stupeflix.com/store/gA/iG/rpupiyCyeJYt7LJ/medium.jpg"/>
        <image filename="http://storage.stupeflix.com/store/sH/CV/WIlSCQSl0DKAfqZ/medium.jpg"/>
        <text type="zone">Sample Movie</text>
        <style layer="main:Barre Titre" color="#2266FF"/>
      </widget>
    </stack></body>
  </movie>';

  // Configure upload targets
  $upload0 = new StupeflixDefaultUpload();
  $uploads = array($upload0);
  
  // Configure video profiles
  $profile = new StupeflixProfile($api_profile, $uploads); 
  $profileSet = new StupeflixProfileSet(array($profile));
          
  // Send the xml to the Stupeflix video generation service
  $stupeflixClient->sendDefinition($api_user, $api_resource, null, $XMLDefinition);
  // Launch the creation of profiles
  $response = $stupeflixClient->createProfiles($api_user, $api_resource, $profileSet);
  
  echo json_encode($response);
}

// Retrieve the status of the generation
function Stupeflix_getStatus(){
  global $stupeflixClient, $api_user, $api_resource, $api_profile;
  echo json_encode($stupeflixClient->getProfileStatus($api_user, $api_resource, $api_profile));
}

// Retrieve the video url
function Stupeflix_getVideoUrl(){
  global $stupeflixClient, $api_user, $api_resource, $api_profile;
  echo json_encode($stupeflixClient->getProfileURL($api_user, $api_resource, $api_profile));
}

?>
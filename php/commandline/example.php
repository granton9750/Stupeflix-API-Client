<?php
// This file is not intended to run on a server, as it will timeout 
// while waiting for the completion of the videos.
// It is intended to be run on the command line, to demonstrate how the different
// calls to the api can be used.
// For an online example code, see http://www.stupeflix.com/services.

// Require the stupeflix client library
require_once 'stupeflix.php';

// Includes the access key and secret key definition : YOU SHOULD SETUP THIS FIRST
require_once 'key.php';

// Includes the access key and secret key definition : YOU SHOULD SETUP THIS FIRST
require_once 'conf.php';

// Report all errors except E_NOTICE
// This is intended only for debugging purpose, not production !
error_reporting(E_ALL);

if (stupeflixAccessKey == 'PUT-YOUR-ACCESS-KEY-HERE') 
{
  die("ERROR : Please fill in key information in key.php\n");
}

try 
{
    // Create the helper class
    $debug = False;
    $stupeflix = new Stupeflix(stupeflixAccessKey, stupeflixSecretKey, stupeflixHost, $service = 'stupeflix-1.0', $debug);
    
    // If you plan to offer the service to your own users, 
    // you can set the user variable to your user id.
    // Otherwise, you can always keep the same name
    $user = "user";
    
    // Each user can have different projects : 
    // select the name of the project to test
    $resource = "resource";
    
    // name of the xml definition file
    $definitionFilename = "movie.xml";

    // These are meta information that can will be used while uploading to Youtube / Dailymotion for example
    $metaDict = array(//"preview"=>"true", // Uncomment this line if you want to use instant preview
                      "title" => "This is my test video", 
                      "description"=>"Describe here your video.", 
                      "tags"=>"add,your,tags",
                      "channels"=>"Tech",
                      "acl"=>"public" // This is used while uploading to S3 / Youtube / Dailymotion / etc
                      );
    
    $meta = new StupeflixMeta($metaDict);

    if (youtubeLogin != "") {
        // Create sample youtube information
        $tags = implode(",", array("these","are","my","tags"));
        $youtubeInfo = array("title" => "Upload test "  + dateString,
                             "description"=> "Upload test description" + dateString,
                             "tags"=>$tags,
                             "channels"=>"Tech",
                             "acl"=>"public",
                             "location"=>"49,-3");
        
        $youtubeMeta = new StupeflixMeta($youtubeInfo);
# There is no currently notification 
        $youtubeNotify = null;
    }


    $upload = new StupeflixDefaultUpload();

    $profileName = "iphone";
    // If you want to use http POST multipart/form-data upload to your own server, use this line instead of the previous (you can use too both upload types)
    //upload = new StupeflixHttpPOSTUpload("http://wwww.mycompany.com/upload");
    $profileIphone = new StupeflixProfile($profileName, array($upload), $meta); 

    // Notification
    $notify = null;
    // Uncomment this line if you want to receive a ping when the video is available
    //$notify = new StupeflixNotify("http://mywebserver.com/path/to/receiver/", "available");
    // Uncomment this line if you want to receive a ping when the video state change (queued / start / info ... available).
    //$notify = new StupeflixNotify("http://mywebserver.com/path/to/receiver/");
    
    //    $profileArray = array($profileIphone, $profileQuicktime);
    $profiles = new StupeflixProfileSet(array($profileIphone), null, $notify);
    $profileNames = $profiles->getProfileNames();
    
    echo "Sending definition ...\n";
    $stupeflix->sendDefinition($user, $resource, $definitionFilename);
    
    echo "Launching generation of profiles\n";
    $stupeflix->createProfiles($user, $resource, $profiles);
    
    // This line will give you a url for the preview: you can point a standard flash player at it, you will get a video stream as soon as the video generation starts
    // You will have to ask for a preview in the meta : see before in $metaDict
    $previewURL = $stupeflix->getProfilePreviewUrl($user, $resource, $profileName); 
    echo "preview URL: " . $previewURL . "\n";
        
    // Now wait for the completion of the video
    $lastCompletion = -1;
    
    // Loop : wait for the video to complete
    echo "Waiting for completion...\n";
    while(true) 
    {
        // Retrieve the info for all profiles generated for user/resource
        $infoArray = $stupeflix->getProfileStatus($user, $resource, null);
        $completed = true;
        foreach ($infoArray as $info) 
        {
            $profile = $info->profile;

            // Test if the status concern a profile we just generated : we may get status for other profiles previously generated for the same user/resource
            if (! in_array($profile, $profileNames, True)) 
            {
                //  Skipping existing profile not in the list to be generated presently
                continue;
            }
            $status = $info->status;
            
            if ($status->status != "available") 
            {
                $completed = false; 
            }
            $error = null;
            $complete = 0;
            if (isset($status->complete)) 
            {
                $complete = $status->complete;
            }
            if (isset($status->error)) 
            {
                if ($status->error) 
                {
                    echo "\n";
                    die("ERROR while generating: ". $status->error. "\n");
                }
            }

            echo "$profile : generation $complete % ";
        } 
        echo "\n";
        if ($completed)
        {
            break;
        }
        sleep(5);
    }
    
    foreach ($profileNames as $profileName) 
    {
        $profileUrl = $stupeflix->getProfileURL($user, $resource, $profileName);
        // Additionally, you can download the files using getProfile function
        $movieFilename = "videos/movie$profileName.mp4";
        echo "Downloading from $profileUrl to $movieFilename\n";
        $stupeflix->getProfile($user, $resource, $profileName, $movieFilename);    
        echo "Done !\n";
    }    
}
catch (Exception $e) {
  echo $e->getMessage();
}

?>
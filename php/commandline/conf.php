<?php

# These are optional variables, by default read from the environement variables, but you can too override them with your own credentials directly.
define('youtubeLogin',  getenv("YOUTUBE_LOGIN"));
define('youtubePassword',  getenv("YOUTUBE_PASSWORD"));
define('s3AccessKey',  getenv("S3_ACCESS_KEY"));
define('s3SecretKey',  getenv("S3_SECRET_KEY"));
define('s3Bucket',  getenv("S3_BUCKET"));
define('httpUploadPrefix',  getenv("HTTP_UPLOAD_PREFIX"));

if (getenv("STUPEFLIX_TEST_TIME") == "") {
    define('dateString', str_replace("k", "a", gmdate("YkmkdkGkiks")));
} else {
    define('dateString', getenv("STUPEFLIX_TEST_TIME"));
}

define('filename', getenv("STUPEFLIX_MOVIE"));
if (filename == "") {
    define('filename', "movie.xml");
}

?>
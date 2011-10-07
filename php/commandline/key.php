<?php

// Define access information
define('stupeflixAccessKey', getenv("STUPEFLIX_ACCESS_KEY"));
define('stupeflixSecretKey', getenv("STUPEFLIX_SECRET_KEY"));
define('stupeflixHost', getenv("STUPEFLIX_HOST"));

if (stupeflixHost == "") { 
    define('stupeflixHost', 'http://services.stupeflix.com/');
}

?>
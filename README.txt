---------------------------------------------------------------------------------
Stupeflix web services client libraries & examples.
http://www.stupeflix.com

The service client libraries are currently available for python, php, java and ruby.

You will get example code in each language directory under example directories.
The "commandline" code can be directly invoked:
  - in php, using php-cli : php example.php
  - in python, just using the command : python commandline.py
  - in java : 
    javac -classpath ".:stupeflix.jar" Example.java; 
    java  -classpath ".:stupeflix.jar" Example
  - in ruby : ruby commandline.rb

These command line examples will use the movie.xml example file in the current directory.
The movie.xml file is commented, and will give you both simple and advanced tricks to use 
the movie description xml language.

You can too directly copy the php "simple_web_ui" directory on your web server : 
it will show you how to quickly start the integration of the stupeflix services.

To run this code, you will need to change the access key and private key 
in the "keys.XXX" file  (keys.php, keys.py, key.rb ...) .
To get your accessKey / secret Key pair, 
go to http://accounts.stupeflix.com/ .

The XML file format is described in the stupeflix wiki: http://wiki.stupeflix.com

---------------------------------------------------------------------------------
Notes on the API:

The web services is REST based, this library is a simple wrapper for
calling HTTP methods with proper signature based authentication : it should be
easy to adapt the client library for a new language.

The signature scheme is heavily inspired from Amazon S3.
The main difference is that only a minimum set of headers must be included,
other parameters are included in the url itself.
This is intended to simplify the process on the client side : read-only requests 
are totally url contained.

---------------------------------------------------------------------------------
External dependancies:

Python
- httplib2, by Joe Gregorio,  that you can found at http://code.google.com/p/httplib2/. 
  A modified version is included here, as there is some bug in the original one with PUT request with 307 redirect.

- demjson library for json encoding, by Deron Meranda <http://deron.meranda.us/

Java
- java Base64 class from http://iharder.sourceforge.net/current/java/base64/

Ruby
- json library for ruby : gem install json

Php
- curl
You will need at least php 5.2 to run the examples.

---------------------------------------------------------------------------------
Licensing :

See LICENSE.txt for more information on licensing issues
(there should be none ... ).

Updates:

The latest version of the Stupeflix Developer Kit is in our Git repository:
http://code.stupeflix.com


Copyright (c) 2008-2009 François Lagunas, http://www.stupeflix.com


<?php
/* Utility class to sign Stupeflix urls */

class StupeflixClient {
  public function __construct($host, $secretKey){
    $this->secretKey = $secretKey;
    $this->host = $host;
  }
  
  public static function paramString($parameters){
    $paramStr = "";
    if ($parameters) {
      ksort($parameters);
      foreach ($parameters as $k => $v){
        $paramStr .= "$k$v";
      }
    }
    return $paramStr;
  }

  // Build the string to sign
  public function strToSign($url, $datestr, $parameters){
    $paramStr = self::paramString($parameters);
    $stringToSign  = "$url$datestr$paramStr";
    return $stringToSign;
  }
  
  // Sign a string with a secretKey
  public static function sign($string, $secretKey){
    return hash_hmac('sha1', $string, $secretKey, false);
  }

  // Sign an url
  public function signUrl($url, $parameters = null){
    $now = floor(time());
    $strToSign = self::strToSign($url, $now, $parameters);    
    $signature = self::sign($strToSign, $this->secretKey);
    $signedUrl = $url.'?Date='.$now.'&Signature='.$signature;    
    if ($parameters != null){
      foreach ($parameters as $k => $v){
        $signedUrl .= "&$k=".urlencode($v);
      }
    }
    return $this->host.$signedUrl;
  } 
}

?>
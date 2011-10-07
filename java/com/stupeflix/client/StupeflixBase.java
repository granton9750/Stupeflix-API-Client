package com.stupeflix.client;

import java.io.File;
import java.io.UnsupportedEncodingException;
import java.io.IOException;
import java.io.InputStream;
import java.io.FileInputStream;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Arrays;

import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.security.MessageDigest;
import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;

import com.stupeflix.thirdparty.Base64;

public class StupeflixBase {
    protected final static String HEADER_CONTENT_TYPE = "Content-Type";
    protected final static String HEADER_CONTENT_LENGTH = "Content-Length";
    protected final static String HEADER_CONTENT_MD5 = "Content-MD5";
    protected final static String ACCESS_KEY_PARAMETER="AccessKey";
    protected final static String SIGNATURE_PARAMETER="Signature";
    protected final static String DATE_PARAMETER="Date";
    protected final static String PROFILE_PARAMETER = "Profiles";
    protected final static String XML_PARAMETER = "ProfilesXML";
    protected final static String HMAC_SHA1_ALGORITHM = "HmacSHA1";
    
    protected String service = "stupeflix-1.0";
    private String accessKey;
    private String secretKey;

    protected boolean debug = false;
    protected HashMap parametersToAdd = new HashMap();

    public StupeflixBase(String accessKey, String secretKey, boolean debug)
    {
        this.accessKey = accessKey;
        this.secretKey = secretKey;
        this.debug = debug;
        this.parametersToAdd.put("Marker", true);
        this.parametersToAdd.put("MaxKeys", true);
    }
    
    // Utility function for debugging
    public void logdebug(Object object)
    {
        if (this.debug) {
            System.out.println(object.toString());
        }
    }

    public static long stringLength(String str)  throws UnsupportedEncodingException
    {
        byte[] buffer = str.getBytes("UTF8");
        return buffer.length;
    }

    public static long fileSize(String filename) 
    {
        File f = new File(filename);
        long size = f.length();
        return size;
    }

    // Build the canonical string containing all parameters for signing
    // @param parameters : The additional parameters to be added to the string to sign
    // @return : The canonical string for parameters
    public String paramString(HashMap parameters)
    {
        String paramStr = "";

        // Check if there are really some parameters
        if (parameters != null)
        {
         
            Iterator p = this.parametersToAdd.keySet().iterator();
            while (p.hasNext()) 
            {
                Object key = p.next();

                if (parameters.containsKey(key)) 
                {
                    // If the parameter is present, add it to the string to sign
                    String v = (String) parameters.get(key);
                    paramStr += v;
                }
            }
        }

        return paramStr;
    }

    // Build the string to sign for a query
    // @param method     : HTTP Method (should be "GET", "PUT" or "POST" )
    // @param url        : base url to be signed
    // @param md5        : content md5
    // @param mime       : mime type ("" for GET query)
    // @param datestr    : date  (seconds since epoch)
    // @param parameters : additional url parameters
    // @return : The string to sign
    public String strToSign(String method, String url, String md5, String mime, long datestr, HashMap parameters)
    {
        // Build the canonical parameter string
        String paramStr = this.paramString(parameters);
        // Build the full service path
        String path = "/" + this.service + url;
        // Build the full string to be signed
        String stringToSign  = method + "\n" + md5 + "\n" + mime + "\n" + datestr + "\n" + path + "\n" + paramStr;
        this.logdebug("String to Sign : " + stringToSign);
        return stringToSign;
    }

    // Sign a request
    // @param string     : The String to be signed
    // @param secretKey  : The secretKey to be user
    // @return : The hmac signature for the request
    public String sign(String string, String secretKey)
    {
        SecretKeySpec signingKey = new SecretKeySpec(secretKey.getBytes(), HMAC_SHA1_ALGORITHM);

        // Acquire the MAC instance and initialize with the signing key.
        Mac mac = null;
        try {
            mac = Mac.getInstance(HMAC_SHA1_ALGORITHM);
        } catch (NoSuchAlgorithmException e) {
            // should not happen
            throw new RuntimeException("Could not find sha1 algorithm", e);
        }
        try {
            mac.init(signingKey);
        } catch (InvalidKeyException e) {
            // also should not happen
            throw new RuntimeException("Could not initialize the MAC algorithm", e);
        }

        byte[] bytes = mac.doFinal(string.getBytes());

        String form = "";
        for (int i = 0; i < bytes.length; i++) {            
            String str = Integer.toHexString(((int)bytes[i]) & 0xff);
            if (str.length() == 1) {
                str = "0" + str;
            }                                    
            
            form = form + str;                
        }
        return form;
    }


    // Sign an request, using url, method body ...
    // @param url        : The url to be signed
    // @param method     : The HTTP method to be used for request
    // @param md5        : The md5 of the body, or "" for "GET" requests
    // @param mime       : The mime type of the request
    // @param parameters : Some optional additional parameters
    // @return the hmac signature of the request
    public String signUrl(String url, String method, String md5, String mime, HashMap parameters, boolean inlineAuth)
    {
        // Get seconds since epoch, integer type
        long now = System.currentTimeMillis() / 1000;
        // Build the string to be signed
        String strToSign = this.strToSign(method, url, md5, mime, now, parameters);
        // Build the signature
        String signature = this.sign(strToSign, this.secretKey);
        // Build the signed url
        String accessKey = this.accessKey;
        String accessKeyParam = this.ACCESS_KEY_PARAMETER;
        String dateParam = this.DATE_PARAMETER;
        String signParam = this.SIGNATURE_PARAMETER;
        // Add date, public accesskey, and signature to the url

        if (inlineAuth) {
            url = url + accessKey + "/" + signature + "/" + now + "/";
        } else {
            url = url + "?" + dateParam + "=" + now + "&" + accessKeyParam + "=" + accessKey + "&" + signParam + "=" + signature;
        }

        // Finally add, if needed, additional parameters
        if (parameters != null)
        {

            Iterator p = parameters.keySet().iterator();
            while (p.hasNext()) 
            {
                Object key = p.next();
                Object value = parameters.get(key);
                url = url + "&" + key.toString() + "=" + value.toString();
            }
        }
        return url;
    }


    public static String bin2hex(byte[] bytes)
    {
        String form = "";
        for (int i = 0; i < bytes.length; i++) {
            String str = Integer.toHexString(((int)bytes[i]) & 0xff);
            if (str.length() == 1) {
                str = "0" + str;
            }                                    
            
            form = form + str;                
        }
        return form;
    }


    // Build a (md5, md5 hexadecimal, md5 base 64) array for a given md5
    // @return : The array
    public static HashMap md5triplet(byte[] md5)
    {
        HashMap hashMap = new HashMap();
        String md5hex = StupeflixBase.bin2hex(md5);
        String md5base64 = Base64.encodeBytes(md5);
        
        hashMap.put("std", md5);        
        hashMap.put("hex", md5hex);
        hashMap.put("base64", md5base64);
        return hashMap;
    }


    // Compute the (md5, md5 hexadecimal, md5 base 64) triplet for of a file
    // @param filename : The file to be hashed
    // @return         : The md5 array
    public static HashMap md5file(String filename) throws NoSuchAlgorithmException, IOException
    {
        // Get a digest instance
        MessageDigest digest = java.security.MessageDigest.getInstance("MD5");
        // Open the file
        File f = new File(filename);
        // Create an input stream
        InputStream is = new FileInputStream(f);				
        // Buffer to read the file
        byte[] buffer = new byte[8192];
        // Read amount
        int read = 0;        

        try {
            while( (read = is.read(buffer)) > 0) 
            {
                digest.update(buffer, 0, read);
            }
        }
        finally 
        {
            is.close();
        }

        byte[] md5 = digest.digest();
        return StupeflixBase.md5triplet(md5);
    }

    // Compute the (md5, md5 hexadecimal, md5 base 64) triplet for of a file
    // @param filename : The file to be hashed
    // @return         : The md5 array
    public static HashMap md5string(String str) throws NoSuchAlgorithmException, java.io.UnsupportedEncodingException
    {
        // Get a digest instance
        MessageDigest digest = java.security.MessageDigest.getInstance("MD5");
        byte[] buffer = str.getBytes("UTF8");

        digest.update(buffer);
                
        byte[] md5 = digest.digest();
        return StupeflixBase.md5triplet(md5);
    }

    // Check if a filename is a zip
    // @param filename : The file name
    // @return         : A boolean : true if it is a zip, false otherwise
    public static boolean isZip(String filename, String str) throws NoSuchAlgorithmException, IOException
    {
        // Buffer to read the file
        byte[] buffer = new byte[4];
        if (filename != null) 
        {
            File f = new File(filename);
            // Create an input stream
            InputStream is = new FileInputStream(f);				

            // Read amount
            int read = 0;
            int totalRead = 0;
            
            try {
                while((read = is.read(buffer, totalRead, 4 - totalRead)) > 0) {
                    totalRead += read;
                }
            }
            finally 
            {
                is.close();
            }
        } else {
            byte[] buf = str.substring(0, 4).getBytes("UTF8");
            for (int i = 0;i < 4 ; i ++){
                buffer[i] = buf[i];
            }
        }
        byte[] zipMagic = "PK\03\04".getBytes("UTF8");
        return Arrays.equals(zipMagic, buffer);
    }
}

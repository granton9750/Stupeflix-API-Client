package com.stupeflix.client;

import java.io.File;
import java.io.BufferedReader;
import java.io.FileWriter;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.FileInputStream;
import java.io.OutputStream;
import java.io.FileOutputStream;
import java.io.InputStreamReader;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;

import java.util.HashMap;
import java.util.Iterator;

import java.net.URL;
import java.net.HttpURLConnection;

public class StupeflixConnection {
    protected final static String HEADER_CONTENT_TYPE = "Content-Type";
    protected String host;
    protected String service;
    protected boolean debug;
    protected HashMap responseHeaders = new HashMap();
    protected int responseCode = -1;
    protected byte[] bodyBytes;
    HttpURLConnection connection;
    

    public StupeflixConnection(String host, String service, boolean debug)
    {
        this.host = host;
        this.service = service;
        this.debug = debug;
    }


    public void execute(String method, String uri, String filename, String body, HashMap headers) throws IOException
    {
        boolean isSecure = false;
        URL url = new URL(isSecure ? "https": "http", this.host, 80, "/" + this.service + uri);

        HttpURLConnection connection = (HttpURLConnection) url.openConnection();
        connection.setRequestMethod(method);
        
        if (!connection.getInstanceFollowRedirects())
            throw new RuntimeException("HTTP redirect support required.");

        if (headers != null)
        { 

            for (Iterator i = headers.keySet().iterator(); i.hasNext();) {
                String key = (String) i.next();
                String value = (String) headers.get(key);

                connection.addRequestProperty(key, value);
            }
        }
        
        connection.setDoInput(true);

        if (method == "PUT" || method == "POST") {
            if (filename != null || body != null) {
                connection.setDoOutput(true);        
                if (body != null) 
                {
                    connection.getOutputStream().write(body.getBytes("UTF8"));
                } 
                else if(filename != null)
                {                

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
                            connection.getOutputStream().write(buffer, 0, read);
                        }
                    }
                    finally 
                    {
                        is.close();
                    }                
                }
            }
        }

        for (int j = 1;; j++) {
            String header = connection.getHeaderField(j);
            if (header == null)
                break;
            String key = connection.getHeaderFieldKey(j).toLowerCase();
            this.responseHeaders.put(key, header);
        }

        try {
            this.bodyBytes = this.readInputStream(connection.getInputStream());
        } catch (Exception e){
            this.bodyBytes = this.readInputStream(connection.getErrorStream());
        }

        
        if (method == "GET" && filename != null) {
            File file = new File(filename);
            FileOutputStream stream = new FileOutputStream(file);
            stream.write(this.bodyBytes);
            stream.close();
        }

        this.responseCode = connection.getResponseCode();
    }

    static byte[] readInputStream(InputStream stream) throws IOException {
        final int chunkSize = 1024;
        byte[] buf = new byte[chunkSize];
        ByteArrayOutputStream byteStream = new ByteArrayOutputStream(chunkSize);
        int count;

        while ((count=stream.read(buf)) != -1) byteStream.write(buf, 0, count);

        return byteStream.toByteArray();
    }

    public int getResponseCode() throws IOException
    {
        return this.responseCode;
    }

    public HashMap getResponseHeaders()
    {
        return this.responseHeaders;
    }

    public byte[] getResponseBodyBytes()
    {
        if (this.bodyBytes != null) {
            return this.bodyBytes;
        } else {
            return new byte[0];
        }
    }
    
}

import com.stupeflix.client.Stupeflix;
import com.stupeflix.client.StupeflixProfileSet;

import java.lang.Thread;

public class Example {

    static final String stupeflixAccessKey = "PUT-YOUR-ACCESS-KEY-HERE";
    static final String stupeflixSecretKey = "PUT-YOUR-SECRET-KEY-HERE";
    static final String stupeflixHost = "services.stupeflix.com";
    final static boolean debug = false;

    public static void main(String args[]) throws Exception {
        // Create the stupeflix client        
        Stupeflix stupeflix = new Stupeflix(Example.stupeflixAccessKey,  Example.stupeflixSecretKey, Example.stupeflixHost, null, debug);
        // Choose some identifiers (you can select whatever you want, provided it is alphanumerical)
        String user = "user";
        String resource = "resource100";
        
        // Send the definition file
        stupeflix.sendDefinition(user, resource, "movie.xml", null);
       
        // Build the profile set
        String[] profiles = new String[1];
        profiles[0] = "iphone";
        StupeflixProfileSet profileSet = new StupeflixProfileSet(profiles);

        // Create the profiles
        stupeflix.createProfiles(user, resource, profileSet);

        // Loop while the profile is not available
        while(true) {
            // Retrieve the status for all the profiles
            String status = stupeflix.getProfileStatus(user, resource, null);
            // Print the current status
            System.out.println(status);
            // Here you can use the XML lib you want to parse status String as an XML file.
            // We will only check that the status is available or not
            if (status.contains("\"status\":\"available\"")) {
                break;
            } 
            // We encountered an error : we will stop too
            if (status.contains("\"status\":\"error\"")) {
                break;
            }
            // Sleep for 5 seconds
            Thread.currentThread().sleep(5000);
        }

        // Retrieve the video for the first profile (you may have to change the file extension on some systems to play the video)
        for(int i = 0; i < profiles.length; i++) {
            
            System.out.println("Downloading OUTPUT_" + i + ".mp4");
            stupeflix.getProfile(user, resource, profiles[i], "OUTPUT_" + i + ".mp4");
            System.out.println("Thumbnail url !");
            System.out.println(stupeflix.getProfileThumbURL(user, resource, profiles[i]));
            System.out.println("Video url !");
            System.out.println(stupeflix.getProfileURL(user, resource, profiles[i]));
            System.out.println("Done !");
        }
    }
}


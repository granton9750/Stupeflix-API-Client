package com.stupeflix.client;


public class StupeflixProfileSet {
    protected String[]  profiles;
    
    public StupeflixProfileSet(String[] profiles) {
        this.profiles = profiles;
    }

    public String toString() {
        String ret =  "<profiles>";
        for (int i = 0; i < this.profiles.length; i++) 
        {
            ret += "<profile name=\"" + this.profiles[i]+ "\">";
            ret += "<stupeflixStore />";
            ret += "</profile>";
        }
        ret += "</profiles>";
        return ret;
    }
}


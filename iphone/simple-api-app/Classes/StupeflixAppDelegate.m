//
//  StupeflixAppDelegate.m
//  Stupeflix
//
//  Created by Sylvain Garrigues on 13/09/09.
//

#import "StupeflixAppDelegate.h"
#import "StupeflixViewController.h"

@implementation StupeflixAppDelegate

@synthesize window;
@synthesize viewController;


- (void)applicationDidFinishLaunching:(UIApplication *)application {    
	[window addSubview:viewController.view];
    [window makeKeyAndVisible];
}


- (void)dealloc {
    [viewController release];
    [window release];
    [super dealloc];
}

@end

//
//  StupeflixViewController.m
//  Stupeflix
//
//  Created by Sylvain Garrigues on 13/09/09.
//

#import "StupeflixViewController.h"

#define ACCESS_KEY		@"Mj7EmhkeMu4l1ztDheqD"
#define PRIVATE_KEY		@"hhaEnOmzYJBUZeBLxeA8IxwNpNPX9ItCB5y6xY6u"

@implementation StupeflixViewController

@synthesize sendDefinitionButton;
@synthesize createProfilesButton;
@synthesize playProfileButton;
@synthesize moviePlayerController;
@synthesize stupeflix;

- (id)initWithCoder:(NSCoder *)decoder {
	if (self = [super initWithCoder:decoder]) {
		Stupeflix *stup = [[Stupeflix alloc] initWithAccessKey:ACCESS_KEY andPrivateKey:PRIVATE_KEY];
		if (stup) {
			self.stupeflix = stup;
			[stup release];
		}
	}
	return self;
}

- (IBAction)sendDefinition:(id)sender {
	NSString *resourcePath = [[NSBundle mainBundle] resourcePath];	
	[stupeflix sendDefinition:@"user" resource:@"resource200" filename:[resourcePath stringByAppendingString:@"/movie.xml"] body:nil];
	createProfilesButton.enabled = YES;
}

- (IBAction)createProfiles:(id)sender {
	[stupeflix createProfiles:@"user" resource:@"resource200" profiles:[NSSet setWithObject:@"iphone"]];
	while (1) {
		NSString * status = [stupeflix getProfileStatus:@"user" resource:@"resource200" profile:nil];
		NSRange r = [status rangeOfString:@"\"status\":\"available\""];
		if (r.location != NSNotFound)
			break;
		r = [status rangeOfString:@"\"status\":\"error\""];
		if (r.location != NSNotFound)
			break;
		[NSThread sleepForTimeInterval:3];
	}
	self.playProfileButton.enabled = YES;
}

- (IBAction)playProfile:(id)sender {
	NSString *urlString = [stupeflix profileURL:@"user" resource:@"resource200" profile:@"iphone"];
	NSString *strToSign = [stupeflix signUrl:urlString method:@"GET" md5:@"" mime:@"" parameters:nil];
	NSString *wholeURL = [@"http://services.stupeflix.com/stupeflix-1.0" stringByAppendingString:strToSign];
	MPMoviePlayerController *mpc = [[MPMoviePlayerController alloc] initWithContentURL:[NSURL URLWithString:wholeURL]];
	if (mpc) {
		self.moviePlayerController = mpc;
		[mpc release];
	
		if ([moviePlayerController respondsToSelector:@selector(view)]) {
			UIWindow *window = [UIApplication sharedApplication].keyWindow;
			moviePlayerController.controlStyle = MPMovieControlStyleFullscreen;
			[moviePlayerController.view setFrame:[window bounds]];
			[window addSubview:moviePlayerController.view];
		}
		
		[moviePlayerController play];
	}

}

- (void)viewDidUnload {
	self.sendDefinitionButton = nil;
	self.createProfilesButton = nil;
	self.playProfileButton = nil;
	[super viewDidUnload];
}

- (void)dealloc {
	[sendDefinitionButton release];
	[createProfilesButton release];
	[playProfileButton release];
	[moviePlayerController release];
	[stupeflix release];
    [super dealloc];
}

@end

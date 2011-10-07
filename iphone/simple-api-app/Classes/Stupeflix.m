//
//  Stupeflix.m
//  Stupeflix
//
//  Created by Sylvain Garrigues on 12/09/09.
//

#import "Stupeflix.h"


@implementation Stupeflix

- (id)initWithAccessKey:(NSString *)accKey andPrivateKey:(NSString *)priKey {
	if (self = [super init]) {
		self.accessKey = accKey;@"Mj7EmhkeMu4l1ztDheqD";
		self.privateKey = priKey;@"hhaEnOmzYJBUZeBLxeA8IxwNpNPX9ItCB5y6xY6u";
	}
	return self;
}

- (void)sendDefinition:(NSString *)user resource:(NSString *)resource filename:(NSString *)filename body:(NSString *)body {
	NSString *definitionURL = [self definitionURL:user resource:resource];
	[self sendContent:@"PUT" url:definitionURL contentType:TEXT_XML_CONTENT_TYPE filename:filename body:nil parameters:nil];
}

- (void)createProfiles:(NSString *)user resource:(NSString *)resource profiles:(NSSet *)profiles {
	NSMutableString *xml = [NSMutableString string];
	[xml appendString:@"<?xml version=\"1.0\" encoding=\"UTF-8\"?><profiles>"];
	for (NSString *profileName in profiles) {
		[xml appendFormat:@"<profile name=\"%@\"><stupeflixStore /></profile>", profileName];
	}
	[xml appendString:@"</profiles>"];
	NSString *urlString = [self createProfilesURL:user resource:resource];
	NSString *escapedXML1 = (NSString *) CFURLCreateStringByAddingPercentEscapes(kCFAllocatorDefault, (CFStringRef)xml, (CFStringRef)@" ", (CFStringRef)@";/?:@&=+$,", kCFStringEncodingUTF8);
	NSString *escapedXML2 = [escapedXML1 stringByReplacingOccurrencesOfString:@" " withString:@"+"];
	[escapedXML1 release];
	NSString *body = [NSString stringWithFormat:@"%@=%@", XML_PARAMETER, escapedXML2];
	[self sendContent:@"POST" url:urlString contentType:APPLICATION_URLENCODED_CONTENT_TYPE filename:nil body:body parameters:nil];
}

- (NSString *)getProfileStatus:(NSString *)user resource:(NSString *)resource profile:(NSString *)profile {
	NSString *urlString = [self profileStatusURL:user resource:resource profile:@"iphone"];
	NSString *strToSign = [self signUrl:urlString method:@"GET" md5:@"" mime:@"" parameters:nil];
	NSString *wholeURL = [@"http://services.stupeflix.com/stupeflix-1.0" stringByAppendingString:strToSign];
	NSError *error;
	NSString *response = [NSString stringWithContentsOfURL:[NSURL URLWithString:wholeURL] encoding:NSUTF8StringEncoding error:&error];
	if (!response) {
		NSLog(@"error in getProfileStatus: %@", [error localizedDescription]);
		[error release];
		return nil;
	}
	NSLog(@"getProfileStatus: %@", response);
	return response;
		
}

@end

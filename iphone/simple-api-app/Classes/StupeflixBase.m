//
//  StupeflixBase.m
//  Stupeflix
//
//  Created by Sylvain Garrigues on 06/09/09.
//

#import "StupeflixBase.h"
#import "NSData+Crypto.h"

@implementation StupeflixBase

@synthesize service;
@synthesize accessKey;
@synthesize privateKey;
@synthesize host;
@synthesize parametersToAdd;

- (id)init {
    if (self = [super init])
    {
		self.host = @"services.stupeflix.com";
		self.service = @"stupeflix-1.0";
		self.parametersToAdd = [NSArray arrayWithObjects: @"Marker", @"MaxKeys", nil];
    }
    return self;
}

- (NSString *)profileStatusURL:(NSString *)user resource:(NSString *)resource profile:(NSString *)name {
	return [NSString stringWithFormat:@"/%@/%@/status/", user, resource];
}

- (NSString *)profileURL:(NSString *)user resource:(NSString *)resource profile:(NSString *)name {
	return [NSString stringWithFormat:@"/%@/%@/%@/", user, resource, name];
}

- (NSString *)definitionURL:(NSString *)user resource:(NSString *)resource {
	return [NSString stringWithFormat:@"/%@/%@/definition/", user, resource];
}

- (NSString *)createProfilesURL:(NSString *)user resource:(NSString *)resource {
	return [NSString stringWithFormat:@"/%@/%@/", user, resource];
}

- (NSString *)paramString:(NSDictionary *)parameters {
	NSMutableString *paramStr = [NSMutableString stringWithCapacity:64];
	if (parameters != nil) {
		
		for (NSString *key in self.parametersToAdd) {
			if ([[parameters allKeys] containsObject:key]) {
				[paramStr appendFormat:@"%@\n%@\n", key, [parameters objectForKey:key]];
			}
		}
	}
	return paramStr;
}

- (NSString *)strToSign:(NSString *)method resource:(NSString *)resource md5:(NSString *)md5 mime:(NSString *)mime dateStr:(NSString *)datestr parameters:(NSDictionary *)parameters {
	NSString *paramStr = [self paramString:parameters];
	NSString *path = [NSString stringWithFormat:@"/%@%@", self.service, resource];
	NSString *stringToSign = [NSString stringWithFormat: @"%@\n%@\n%@\n%@\n%@\n%@", method, md5, mime, datestr, path, paramStr];
	return stringToSign;
}

- (NSString *)sign:(NSString *)s privateKey:(NSString *)thePrivateKey {
	return [[[s dataUsingEncoding:NSASCIIStringEncoding] hmacSHA1Signature:thePrivateKey] hexDescription];
}

- (NSString *)signUrl:(NSString *)url method:(NSString *)method md5:(NSString *)md5 mime:(NSString *)mime parameters:(NSDictionary *)parameters {
	NSString *now = [NSString stringWithFormat:@"%d", (int)[[NSDate date] timeIntervalSince1970]];
	NSString *strToSign = [self strToSign:method resource:url md5:md5 mime:mime dateStr:now parameters:parameters];
	NSString *signature = [self sign:strToSign privateKey:self.privateKey];
	NSMutableString *resultURL = [[url mutableCopy] autorelease];
	[resultURL appendFormat:@"%?Date=%@&AccessKey=%@&Signature=%@", now, self.accessKey, signature];
	for (NSString *key in parameters) {
		[resultURL appendFormat:@"&%@=%@", key, [parameters objectForKey:key]];
	}
	return resultURL;
}

- (void)sendContent:(NSString *)method url:(NSString *)url contentType:(NSString *)contentType filename:(NSString *)filename body:(NSString *)body parameters:(NSDictionary *)parameters {
	NSData *data;

	if (body)
		data = [body dataUsingEncoding:NSUTF8StringEncoding];
	else {
		data = [NSData dataWithContentsOfFile:filename];
	}
	
	NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:nil];

	NSData *md5 = [data md5];
	NSString *md5Base64 = [md5 base64EncodedString];
	[request setValue:md5Base64 forHTTPHeaderField:@"Content-Md5"];
	[request setValue:contentType forHTTPHeaderField:@"Content-Type"];
	[request setHTTPMethod:method];
	[request setHTTPBody:data];
	NSString *urlString = [self signUrl:url method:method md5:md5Base64 mime:contentType parameters:parameters];
	NSString *finalURLString = [NSString stringWithFormat:@"http://%@/%@%@", self.host, self.service, urlString];
	NSLog(@"Used URL: %@", finalURLString);
	[request setURL:[NSURL URLWithString:finalURLString]];

	NSString *initialMD5 = [md5 hexDescription];
	NSURLResponse *response = nil;
	NSError *error = nil;
	NSData *receivedData = [NSURLConnection sendSynchronousRequest:request returningResponse:&response error:&error];
	if (!receivedData) {
		NSLog(@"error: %@", [error localizedDescription]);
	}
	NSInteger statusCode = [(NSHTTPURLResponse *)response statusCode];
	if (statusCode != 200) {
		[self answerError:[NSString stringWithFormat:@"sendContent: bad STATUS %d", statusCode]];
	}
	else {
		NSString *etag = [[(NSHTTPURLResponse *)response allHeaderFields] valueForKey:@"Etag"];
		if (etag) {
			NSCharacterSet *cs = [NSCharacterSet characterSetWithCharactersInString:@"\""];
			NSString *newEtag = [etag stringByTrimmingCharactersInSet:cs];
			
			if ([newEtag compare:initialMD5] != NSOrderedSame) {
				[self error:[NSString stringWithFormat:@"sendContent : bad returned etags %@ =! %@ (ref)", newEtag, initialMD5]]; 
			}
		}
		else {
			NSString *errorMessage = [[[NSString alloc] initWithData:receivedData encoding:NSUTF8StringEncoding] autorelease];
			[self error:[NSString stringWithFormat:@"corrupted answer: no etag in headers. Response body is %@", errorMessage]]; 
		}
	}
}

#pragma mark -
#pragma mark Error handling

- (void)error:(NSString *)error {
	NSLog(@"error: %@", error);
}

- (void)answerError:(NSString *)error {
	NSLog(@"answerError: %@", error);
}

@end

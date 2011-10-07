//
//  StupeflixBase.h
//  Stupeflix
//
//  Created by Sylvain Garrigues on 06/09/09.
//

#import <Foundation/Foundation.h>

static NSString * const HEADER_CONTENT_TYPE = @"Content-Type";
static NSString * const HEADER_CONTENT_LENGTH = @"Content-Length";
static NSString * const HEADER_CONTENT_MD5 = @"Content-MD5";
static NSString * const ACCESS_KEY_PARAMETER = @"AccessKey";
static NSString * const SIGNATURE_PARAMETER = @"Signature";
static NSString * const DATE_PARAMETER = @"Date";
static NSString * const PROFILE_PARAMETER = @"Profiles";
static NSString * const HMAC_SHA1_ALGORITHM = @"HmacSHA1";
static NSString * const TEXT_XML_CONTENT_TYPE = @"text/xml";

static NSString * const APPLICATION_ZIP_CONTENT_TYPE = @"application/zip";
static NSString * const APPLICATION_JSON_CONTENT_TYPE = @"application/json";
static NSString * const APPLICATION_URLENCODED_CONTENT_TYPE = @"application/x-www-form-urlencoded";
static NSString * const PROFILES_PARAMETER = @"Profiles";
static NSString * const XML_PARAMETER = @"ProfilesXML";
static NSString * const MARKER_PARAMETER = @"Marker";
static NSString * const MAXKEYS_PARAMETER = @"MaxKeys";


@interface StupeflixBase : NSObject {
    NSString *service;
    NSString *accessKey;
    NSString *privateKey;
	NSString *host;
    NSArray *parametersToAdd;	
}

@property (nonatomic, retain) NSString *service;
@property (nonatomic, retain) NSString *accessKey;
@property (nonatomic, retain) NSString *privateKey;
@property (nonatomic, retain) NSString *host;
@property (nonatomic, retain) NSArray *parametersToAdd;

- (NSString *)profileURL:(NSString *)user resource:(NSString *)resource profile:(NSString *)name;
- (NSString *)profileStatusURL:(NSString *)user resource:(NSString *)resource profile:(NSString *)name;
- (NSString *)createProfilesURL:(NSString *)user resource:(NSString *)resource;
- (NSString *)definitionURL:(NSString *)user resource:(NSString *)resource;
- (void)sendContent:(NSString *)method url:(NSString *)url contentType:(NSString *)contentType filename:(NSString *)filename body:(NSString *)body parameters:(NSDictionary *)parameters;
- (NSString *)signUrl:(NSString *)url method:(NSString *)method md5:(NSString *)md5 mime:(NSString *)mime parameters:(NSDictionary *)parameters;
- (void)error:(NSString *)error;
- (void)answerError:(NSString *)error;

@end

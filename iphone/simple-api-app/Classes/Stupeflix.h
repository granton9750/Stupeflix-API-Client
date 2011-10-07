//
//  Stupeflix.h
//  Stupeflix
//
//  Created by Sylvain Garrigues on 12/09/09.
//

#import <Foundation/Foundation.h>
#import "StupeflixBase.h"


@interface Stupeflix : StupeflixBase {

}

- (id)initWithAccessKey:(NSString *)accessKey andPrivateKey:(NSString *)privateKey;
- (void)sendDefinition:(NSString *)user resource:(NSString *)resource filename:(NSString *)filename body:(NSString *)body;
- (void)createProfiles:(NSString *)user resource:(NSString *)resource profiles:(NSSet *)profiles;
- (NSString *)getProfileStatus:(NSString *)user resource:(NSString *)resource profile:(NSString *)profile;

@end

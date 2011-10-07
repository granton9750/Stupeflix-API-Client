//
//  NSData+Crypto.h
//  Stupeflix
//
//  Created by Sylvain Garrigues on 06/09/09.
//

#import <Foundation/Foundation.h>


@interface NSData (Crypto)

- (NSString *)base64EncodedString;
- (NSData *)md5;
- (NSString *)hexDescription;
- (NSData *)hmacSHA1Signature:(NSString *)secretKey;

@end

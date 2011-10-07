//
//  NSData+Crypto.m
//  Stupeflix
//
//  Created by Sylvain Garrigues on 06/09/09.
//

#import "NSData+Crypto.h"
#import <CommonCrypto/CommonDigest.h>
#import <CommonCrypto/CommonHMAC.h>

char *base64Encode(const void *buffer, size_t length) {
	const unsigned char *inputBuffer = (const unsigned char *)buffer;
	
	static unsigned char base64EncodeLookup[65] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	
#define BINARY_UNIT_SIZE 3
#define BASE64_UNIT_SIZE 4
	
#define MAX_NUM_PADDING_CHARS 2
#define OUTPUT_LINE_LENGTH 64
#define INPUT_LINE_LENGTH ((OUTPUT_LINE_LENGTH / BASE64_UNIT_SIZE) * BINARY_UNIT_SIZE)
#define CR_LF_SIZE 2
	
	size_t outputBufferSize = ((length / BINARY_UNIT_SIZE) + ((length % BINARY_UNIT_SIZE) ? 1 : 0))	* BASE64_UNIT_SIZE + 1;
	
	char *outputBuffer = (char *)malloc(outputBufferSize);
	if (!outputBuffer)
	{
		return NULL;
	}
	
	size_t i = 0;
	size_t j = 0;
	const size_t lineLength = length;
	size_t lineEnd = lineLength;
	
	
	for (; i + BINARY_UNIT_SIZE - 1 < lineEnd; i += BINARY_UNIT_SIZE) {
		outputBuffer[j++] = base64EncodeLookup[(inputBuffer[i] & 0xFC) >> 2];
		outputBuffer[j++] = base64EncodeLookup[((inputBuffer[i] & 0x03) << 4) | ((inputBuffer[i + 1] & 0xF0) >> 4)];
		outputBuffer[j++] = base64EncodeLookup[((inputBuffer[i + 1] & 0x0F) << 2) | ((inputBuffer[i + 2] & 0xC0) >> 6)];
		outputBuffer[j++] = base64EncodeLookup[inputBuffer[i + 2] & 0x3F];
	}
	
	
	if (i + 1 < length)	{
		outputBuffer[j++] = base64EncodeLookup[(inputBuffer[i] & 0xFC) >> 2];
		outputBuffer[j++] = base64EncodeLookup[((inputBuffer[i] & 0x03) << 4) | ((inputBuffer[i + 1] & 0xF0) >> 4)];
		outputBuffer[j++] = base64EncodeLookup[(inputBuffer[i + 1] & 0x0F) << 2];
		outputBuffer[j++] =	'=';
	}
	else if (i < length) {
		outputBuffer[j++] = base64EncodeLookup[(inputBuffer[i] & 0xFC) >> 2];
		outputBuffer[j++] = base64EncodeLookup[(inputBuffer[i] & 0x03) << 4];
		outputBuffer[j++] = '=';
		outputBuffer[j++] = '=';
	}
	
	outputBuffer[j] = 0;
	return outputBuffer;
}

char *hexEncode(const unsigned char *buffer, size_t length) {
	char *outbuf = (char *)malloc(length * 2 + 1);
	for (size_t i = 0 ; i < length ; i++) {
		sprintf(outbuf + 2 * i, "%02x", buffer[i]);
	}
	return outbuf;
}


@implementation NSData(Crypto)

- (NSString *)base64EncodedString {
	char *outputBuffer = base64Encode([self bytes], [self length]);
	
	NSString *result = [NSString stringWithCString:outputBuffer encoding:NSASCIIStringEncoding];

	free(outputBuffer);
	return result;
}

- (NSString *)hexDescription {
	char *outputBuffer = hexEncode([self bytes], [self length]);
	
	NSString *result = [NSString stringWithCString:outputBuffer encoding:NSASCIIStringEncoding];
	
	free(outputBuffer);
	return result;
}

- (NSData *)md5 {
	unsigned char output[CC_MD5_DIGEST_LENGTH];
	CC_MD5([self bytes], [self length], output);
	
	return [NSData dataWithBytes:output length:CC_MD5_DIGEST_LENGTH];
}

- (NSData *)hmacSHA1Signature:(NSString *)secretKey {
	unsigned char output[CC_SHA1_DIGEST_LENGTH];
	CCHmac(kCCHmacAlgSHA1, [secretKey cStringUsingEncoding:NSASCIIStringEncoding], [secretKey lengthOfBytesUsingEncoding:NSASCIIStringEncoding], [self bytes], [self length], output);
	
	return [NSData dataWithBytes:output length:CC_SHA1_DIGEST_LENGTH];
}

@end

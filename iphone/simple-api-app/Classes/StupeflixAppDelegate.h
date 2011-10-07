//
//  StupeflixAppDelegate.h
//  Stupeflix
//
//  Created by Sylvain Garrigues on 13/09/09.
//

#import <UIKit/UIKit.h>

@class StupeflixViewController;

@interface StupeflixAppDelegate : NSObject <UIApplicationDelegate> {
    UIWindow *window;
    StupeflixViewController *viewController;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) IBOutlet StupeflixViewController *viewController;

@end


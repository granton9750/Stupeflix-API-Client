//
//  StupeflixViewController.h
//  Stupeflix
//
//  Created by Sylvain Garrigues on 13/09/09.
//

#import <UIKit/UIKit.h>
#import <MediaPlayer/MediaPlayer.h>
#import "Stupeflix.h"

@interface StupeflixViewController : UIViewController {
	Stupeflix *stupeflix;
	MPMoviePlayerController *moviePlayerController;
	UIButton *sendDefinitionButton;
	UIButton *createProfilesButton;
	UIButton *playProfileButton;
}

- (IBAction)sendDefinition:(id)sender;
- (IBAction)createProfiles:(id)sender;
- (IBAction)playProfile:(id)sender;

@property (nonatomic, retain) IBOutlet UIButton *sendDefinitionButton;
@property (nonatomic, retain) IBOutlet UIButton *createProfilesButton;
@property (nonatomic, retain) IBOutlet UIButton *playProfileButton;
@property (nonatomic, retain) MPMoviePlayerController *moviePlayerController;
@property (nonatomic, retain) Stupeflix *stupeflix;

@end


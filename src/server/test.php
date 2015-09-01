<?php

mb_internal_encoding('utf-8');
require_once( 'Facebook/FacebookSession.php' );
require_once( 'Facebook/FacebookRedirectLoginHelper.php' );
require_once( 'Facebook/FacebookRequest.php' );
require_once( 'Facebook/FacebookResponse.php' );
require_once( 'Facebook/FacebookSDKException.php' );
require_once( 'Facebook/FacebookRequestException.php' );
require_once( 'Facebook/FacebookAuthorizationException.php' );
require_once( 'Facebook/FacebookJavaScriptLoginHelper.php' );
require_once( 'Facebook/FacebookServerException.php' );
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphUser.php' );
require_once( 'UserDao.php' );

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookServerException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\FacebookJavaScriptLoginHelper;

	
//hard code access token in test page will be replaced. Using access token from phonegap
$accessToken = 'CAAEGMy4vqzQBAFZBbkAUfDQjv47ttsbmM8B2QQxY1he5pRY3tJmZBU6Mh5RWykud8XDIa5JPSPOlqPHTKny8ZAFZCMIcpohrUZA4kaUxBZBPDzuN9dvDhzb6OIxJk7YO0tthw21i3bKZAKo0TBImmaaavGJM7wDV6JobxMGt7u231TrzzftHKu5ZCtEzpRhEeVcZD';

$debug;

$user = new User;
$user->setAccessToken($accessToken);
echo $user->accessTokenIsValid();

//$debug = $user->getUserIDFromFB();
//$debug = $user->upgradeAccessToken();
if($debug == 190){
echo $debug .' please login again. Possibly access token expired.';

}

$host = "127.0.0.1";
$dbUser = "snapbooklogin";
$pass = "23512921";
$db = "snapbook";
//echo $user->insertUserIDandAccessToken($host,$dbUser,$pass, $db);
//echo $user->getAccessTokenByUserID($host,$dbUser,$pass, $db);



echo '<br>test end';
?>
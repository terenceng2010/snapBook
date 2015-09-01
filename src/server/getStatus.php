<?php
    

require_once( 'Facebook/FacebookSession.php' );
require_once( 'Facebook/FacebookRedirectLoginHelper.php' );
require_once( 'Facebook/FacebookRequest.php' );
require_once( 'Facebook/FacebookResponse.php' );
require_once( 'Facebook/FacebookSDKException.php' );
require_once( 'Facebook/FacebookRequestException.php' );
require_once( 'Facebook/FacebookAuthorizationException.php' );
require_once( 'Facebook/FacebookJavaScriptLoginHelper.php' );
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphUser.php' );

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\FacebookJavaScriptLoginHelper;

$input = $_GET['msg'];

$message = $input . " " . time();
FacebookSession::setDefaultApplication('288291864685364','1d2da08a7d2ef5d51b83d2c4947122e0');

// Use one of the helper classes to get a FacebookSession object.
//   FacebookRedirectLoginHelper288291864685364
//   FacebookCanvasLoginHelper
//   FacebookJavaScriptLoginHelper
// or create a FacebookSession with a valid access token:
$session = new FacebookSession('CAAEGMy4vqzQBAFSqAdgTJKYQRbtntDQl8MqIhHfDpBVWR6hfL7Yc829ZBh3w4cLEwxZB8roWWqqDxjsTGpi6XZBLkblvH9cIZC23XdMzRD0biorKuKTT2X6trhM8ZC0UWtRJOplULIoU9IhoGQ0LAIwtgL1ZAb1ysb0ZCjHcXvelRSu1LyiZAAwn4LMLzyUNVPcZD');


$request = new FacebookRequest($session, 'GET', '/me');

$response = $request->execute();

$user = $response->getGraphObject(GraphUser::className());
echo $user->getName();

if($session) {

  try {

    $response = (new FacebookRequest(
      $session, 'POST', '/me/feed', array(
        'message' => $message
      )
    ))->execute()->getGraphObject();

    echo "Posted with id: " . $response->getProperty('id');

  } catch(FacebookRequestException $e) {

    echo "Exception occured, code: " . $e->getCode();
    echo " with message: " . $e->getMessage();

  }   

}

?>
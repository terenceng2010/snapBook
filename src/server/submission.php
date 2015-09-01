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
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphUser.php' );
require_once( 'UserDao.php' );

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

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');  

error_reporting(E_ERROR | E_PARSE);

$host = "127.0.0.1";
$dbUser = "snapbooklogin";
$pass = "23512921";
$db = "snapbook";

$input = $_GET['msg'];
$userid = $_GET['userid'];
$removeTimeInSecond = $_GET['removeAfter'];

$message = $input . " " . time();




  try {

	$user = new User;
	$user->setUserID($userid);
	$user->getAccessTokenByUserID($host,$dbUser,$pass,$db);
	$session = new FacebookSession($user->getAccessToken());

    $response = (new FacebookRequest(
      $session, 'POST', '/me/feed', array(
        'message' => $message
      )
    ))->execute()->getGraphObject();

    echo "Posted with id: " . $response->getProperty('id');
    $statusid = $response->getProperty('id');

    //write $userid plus statusid plus delete moment to the db
    // set database server access variables:

    $host = "127.0.0.1";
    $user = "snapbooklogin";
    $pass = "23512921";
    $db = "snapbook";

    echo 'here';
    // open connection

    $connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");

    // select database

    mysql_select_db($db) or die ("Unable to select database!");
	mysql_query("SET NAMES 'utf8'");
    $query = "INSERT INTO status (userid,statusid,message,createTime) VALUES ('$userid', '$statusid','$input',now())";
    if($removeTimeInSecond){
     $query = "INSERT INTO status (userid,statusid,message,createTime,removeTime) VALUES ('$userid', '$statusid','$input',now(),DATE_ADD(now(), INTERVAL $removeTimeInSecond SECOND))";
    }

    // execute query

    $result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());

    

    // print message with ID of inserted record

    echo "New record inserted with ID ".mysql_insert_id();

    

    // close connection

    mysql_close($connection);


  } catch(FacebookRequestException $e) {

    echo "Exception occured, code: " . $e->getCode();
    echo " with message: " . $e->getMessage();

  }   



?>
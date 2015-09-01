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

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');  

//error_reporting(E_ERROR | E_PARSE);

$host = "127.0.0.1";
$dbUser = "snapbooklogin";
$pass = "23512921";
$db = "snapbook";

$action = $_GET['action'];

if($action){

	/*
	login
	in: accesstoken
	out: tokenInvalid/ok/newAccessToken
	*/
	if($action == 'login'){
		$accessToken = $_GET['accesstoken'];
		$user = new User;
		$user->setAccessToken($accessToken);
		
		//echo $user->accessTokenIsValid();
		if($user->accessTokenIsValid()==false){
			echo 'tokenInvalid';
		}else{
			$user->getUserIDFromFB();
			
			if($user->getAccessTokenByUserID($host,$dbUser,$pass,$db) == -1)
			{
				//new user
				
				$user->upgradeAccessToken();
				$user->insertUserIDandAccessToken($host,$dbUser,$pass, $db);
				
				//return long-term access token to client
				echo $user->getAccessToken(); 
			}else{
				//old user
				
				//if the access token in the db has expired
				if($user->accessTokenIsValid()==false){
				
					//upgrade client short term token to long-term
					$user->setAccessToken($accessToken);
					$user->upgradeAccessToken();
					$user->insertUserIDandAccessToken($host,$dbUser,$pass, $db);

					//return long-term access token to client
					echo $user->getAccessToken(); 
				}else{
					//normal case
					echo 'ok';
				}
				
				
				
			}
		}
		
	}else if ($action == 'getMessages'){
		$userid = $_GET['userid'];
			
			try{
			$connection = mysql_connect($host, $dbUser, $pass) or die ("Unable to connect!");
			mysql_select_db($db) or die ("Unable to select database!");
			mysql_query("SET NAMES 'utf8'");
			$query = "SELECT message,`like`,activate,removeTime FROM status where userid='$userid' order by createTime desc";
			$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
			$results = array();

			while($row = mysql_fetch_array($result) ){
				$results[] = array(
					'message' => $row['message'],
					'like' => $row['like'],
					'activate' => $row['activate'],
					'removeTime' => $row['removeTime']
				);
			}
			
			$json = json_encode($results);
			
			// close connection

			mysql_close($connection);
	
			echo $json;
			}catch(Exception $e){
				echo $e->getMessage();
			}
	}else if($action = 'regularDelete'){
			try{
				
				$connection = mysql_connect($host, $dbUser, $pass) or die ("Unable to connect!");
				mysql_select_db($db) or die ("Unable to select database!");
				//mysql_query("SET NAMES 'utf8'");
				
				//find all status that needs to be deleted
				$query = "SELECT id,statusid,userid FROM `status` where activate = 1 and removeTime < now() ORDER BY createTime asc";			
				$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
				$statusArray = array();

				//add return result to status array
				while($row = mysql_fetch_array($result) ){
					
					//$statusidSplit = explode('_',$row['statusid']);
					
					$statusArray[] = array(
						'id' => $row['id'],
						'statusid' => $row['statusid'],
						'userid' => $row['userid']
					);
				}
				

				//add access token and like fields to status array
				foreach($statusArray as &$status){
					$tempUser = new User;
					$tempUser->setUserID($status['userid']);
					$tempUser->getAccessTokenByUserID($host,$dbUser,$pass,$db);
					
					$status['accessToken'] = $tempUser->getAccessToken();
					
					$status['like'] = $tempUser->getNumberOfLikeOfMessage($status['statusid']);
					
					//delete msg from fb
					
					//update status table
				}
				
				//delete status
 				foreach($statusArray as &$status){
					$tempUser = new User;
					$tempUser->setUserID($status['userid']);
					$tempUser->setAccessToken($status['accessToken']);
					// delete msg from fb
					$deleteResult = $tempUser->deleteStatus($status['statusid']);
					
					//add delete result to the array
					if($deleteResult == true){
							$status['deleteResult'] = true;
					}else{
							$status['deleteResult'] = false;
					}
				} 

				//debug loop
				
				foreach($statusArray as &$status){
				
					echo $status['id']."&nbsp;";
					echo $status['like']."&nbsp;";
					echo $status['userid']."&nbsp;";
					echo $status['statusid']."&nbsp;";
					echo $status['accessToken']."&nbsp;";
					echo $status['deleteResult']."&nbsp;";
					echo '<br>';
					 
				}
				
				// update status table
 				foreach($statusArray as &$status){					
					
					//if delete success, update status table
					if($status['deleteResult'] == true){

						$connection = mysql_connect($host, $dbUser, $pass) or die ("Unable to connect!");
						mysql_select_db($db) or die ("Unable to select database!");
						
						//update like field and activate in db
						$query = "update `status` set `like` = {$status['like']} , `activate` = 0 where `id` = {$status['id']}";			
						$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
				
					}else{
							
					}
				} 				
				
			}catch(Exception $e){
				echo $e->getMessage();
			}
	
	
	
	}

}


?>
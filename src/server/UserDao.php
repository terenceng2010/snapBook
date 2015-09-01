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

error_reporting(E_ERROR | E_PARSE);

class User
{

	
	
	public $userid;
	public $accessToken;

	public function User(){
	FacebookSession::setDefaultApplication('288291864685364','1d2da08a7d2ef5d51b83d2c4947122e0');
	}

	public function getUserID(){
	
		return $this->userid;
	
	}
	
	public function setUserID($userid){
		$this->userid = $userid;
	}

	public function getAccessToken(){
	
		return $this->accessToken;
	
	}
	
	public function setAccessToken($accessToken){
		$this->accessToken = $accessToken;
	}
	
	//below are fb methods
	public function getUserIDFromFB(){
	
		try {
			$session = new FacebookSession($this->accessToken);
			$request = new FacebookRequest($session, 'GET', '/me');

			$response = $request->execute();

			$user = $response->getGraphObject(GraphUser::className());
			//echo $user->getName();
			$userid = $user->GetId();
			
			$this->userid = $userid;
			//return $this->userid;
		}catch(FacebookAuthorizationException $e){
			//echo "Exception occured, code: " . $e->getCode();
			//echo " with message: " . $e->getMessage();
			
			//return error code 190 for error handling
			return $e->getCode();
		}catch(FacebookRequestException $e) {

			//echo "Exception occured, code: " . $e->getCode();
			//echo " with message: " . $e->getMessage();
			return $e->getCode();
		}
	}
	public function upgradeAccessToken(){
		$session = new FacebookSession($this->accessToken);
		if($session) {

		  try {
			$request = new FacebookRequest($session, 'POST', '/oauth/access_token', array(
				'grant_type' => 'fb_exchange_token',
				'client_id' => FacebookSession::_getTargetAppId(),
				'client_secret' => FacebookSession::_getTargetAppSecret(),
				'fb_exchange_token' => $this->accessToken
			  ));
			  

			$response = $request->execute();
			$object = $response->getGraphObject();
			
			//echo "assigned long-term access token to user object";
			//echo $object->getProperty('access_token');
			$this->accessToken = $object->getProperty('access_token');

			//return $this->accessToken;

		  }catch(FacebookAuthorizationException $e){
			//echo "Exception occured, code: " . $e->getCode();
			//echo " with message: " . $e->getMessage();
			
			//return error code 190 for error handling
			return $e->getCode();
		  }catch(FacebookRequestException $e) {

			//echo "Exception occured, code: " . $e->getCode();
			//echo " with message: " . $e->getMessage();
			
			//return error code 190 for error handling
			return $e->getCode();
		  }

		}
	
	}
	
	public function accessTokenIsValid(){
		try {
			$session = new FacebookSession($this->accessToken);

			$request = new FacebookRequest($session, 'GET', '/me');

			$response = $request->execute();

			$user = $response->getGraphObject(GraphUser::className());
			//echo $user->getName();
			$userid = $user->GetId();
			
			return true;
			//return $this->userid;
		}catch(FacebookAuthorizationException $e){
			//echo "Exception occured, code: " . $e->getCode();
			//echo " with message: " . $e->getMessage();
			
			//return error code 190 for error handling
			return false;
		}catch(FacebookRequestException $e) {

			//echo "Exception occured, code: " . $e->getCode();
			//echo " with message: " . $e->getMessage();
			return false;
		}	
	}
	
	//below are db methods
	public function insertUserIDandAccessToken($host,$user,$pass, $db){
		if($this->userid && $this->accessToken){
		
			$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
			mysql_select_db($db) or die ("Unable to select database!");
			
			$query = "INSERT INTO user (userid, accesstoken, createTime) VALUES ('$this->userid', '$this->accessToken',now())";
			// execute query
			$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
			// print message with ID of inserted record
			//echo "New record inserted with ID ".mysql_insert_id();
			// close connection
			mysql_close($connection);
			
			return 0;
		}else{
			return -1;
		}
		
	}
	
	//select top 1 sort by descending inserted time
	public function getAccessTokenByUserID($host,$user,$pass,$db){
		if($this->userid){
		
			$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
			mysql_select_db($db) or die ("Unable to select database!");
			
			$query = "SELECT accesstoken FROM user where userid='$this->userid' order by createTime desc limit 1";
			$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
			
			if (mysql_num_rows($result) > 0) {
			
			 if(list($accessToken)  = mysql_fetch_row($result)) {

				//echo $accessToken;
				
				$this->accessToken = $accessToken;
				
				//return $accessToken;

				}
			}else{
				//echo 'not token by userid';
				return -1;
			}
			
			// close connection
			mysql_close($connection);
			
		}
	}
	
	public function getNumberOfLikeOfMessage($statusid){
			try{
				$session = new FacebookSession($this->accessToken);
				$request = new FacebookRequest($session, 'GET', '/'.$statusid.'/likes');

				$response = $request->execute();

				$object = $response->getGraphObject();
				$data = $object->getProperty('data');
				$total_count = count($data);
				echo 'total amount'.$total_count;
				return $total_count;
			}catch(FacebookAuthorizationException $e){
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			
				//return error code 190 for error handling
				return 0;
			}catch(FacebookRequestException $e) {

				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
				return 0;
			}
	}

	public function deleteStatus($statusid){
			try{
				$session = new FacebookSession($this->accessToken);
				$request = new FacebookRequest($session, 'DELETE', '/'.$statusid.'/');

				$response = $request->execute();

				return $response;
			}catch(FacebookAuthorizationException $e){
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			
				//return error code 190 for error handling
				return 0;
			}catch(FacebookRequestException $e) {

				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
				return 0;
			}
	}

}
?>
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

var app = {
    // Application Constructor
    initialize: function() {
        this.bindEvents();
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
        //document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicity call 'app.receivedEvent(...);'
    onDeviceReady: function() {
        app.receivedEvent('deviceready');
        console.log("device is ready");
        //app.submissionTestEvent();
        console.log("submission of status");
    },
    // Update DOM on a Received Event
    receivedEvent: function(id) {
        var parentElement = document.getElementById(id);
        var listeningElement = parentElement.querySelector('.listening');
        var receivedElement = parentElement.querySelector('.received');

        listeningElement.setAttribute('style', 'display:none;');
        receivedElement.setAttribute('style', 'display:block;');

        console.log('Received Event: ' + id);
    },
    submissionTestEvent:function(userid){
    	
 		$.support.cors = true;

		$.ajax({
		  url: "http://terenceng2010.asuscomm.com/SnapBook/submission.php",
		  type: "GET",
		  dataType: 'text',
		  data: {userid:userid,msg: "testing123", removeAfter: "400" },
		  crossDomain: true
		  
		}).done(function() {
		  console.log( "second success" );
		}).error( function (e) {
		  console.log(e.message);	
		});
   },
    submissionStatusEvent:function(userid,msg,removeAfter){
    	
 		$.support.cors = true;

		$.ajax({
		  url: "http://terenceng2010.asuscomm.com/SnapBook/submission.php",
		  type: "GET",
		  dataType: 'text',
		  data: {userid: userid, msg: msg, removeAfter: removeAfter },
		  crossDomain: true
		  
		}).done(function(result) {
		  console.log(result);
		  console.log( "send message success" );
		}).error( function (e) {
		  console.log(e.message);	
		});
   },
   setUserId:function(userid){
   		window.localStorage.setItem("userid", userid);
   },
   getUserId:function(){
   			var value = window.localStorage.getItem("userid");
   			console.log(value);
   			return value;
   },
   removeUserId:function(){
   		window.localStorage.removeItem("userid");
   },
   login:function(shortTermAccessToken){
  		$.support.cors = true;

		$.ajax({
		  url: "http://terenceng2010.asuscomm.com/SnapBook/action.php",
		  type: "GET",
		  data: { action: "login", accesstoken: shortTermAccessToken },
		  crossDomain: true
		  
		}).done(function(result) {
		  console.log(result);
		  if(result=='ok'){
		  	//do nothing
		  	console.log("ok from server");
		  }else if(result =='tokenInvalid'){
		  	console.log("tokenInvalid from server");
		  	$( "html" ).append( "<p>please go to <a href='login.html'>login page</a> to login again!</p>" );
		  	this.removeUserId();
		  	console.log("user id removed from localstorage");
		  	
		  }else{//save long term access token
		  	console.log("save long term access token");
		  	window.localStorage.setItem("accessToken",result);
		  }
		}).error( function (e) {
		  console.log(e.message);	
		});  
   },
   getSentMessages:function(userid){
  		$.support.cors = true;

		$.ajax({
		  url: "http://terenceng2010.asuscomm.com/SnapBook/action.php",
		  type: "GET",
		  data: { action: "getMessages", userid: userid },
		  crossDomain: true
		  
		}).done(function(result) {
		  console.log(result);
		  
		  var resultJSON = JSON.parse(result);
		
		  for(var i =0; i < resultJSON.length; i++){
		  	var obj = resultJSON[i];
		  	for(var key in obj){
		  		var attrName = key;
		  		var attrValue = obj[key];
		  		$( "html" ).append( attrName + " " + attrValue + " ");
		  	}
		  	$( "html" ).append("<br>");
		  }
		
		}).error( function (e) {
		  console.log(e.message);	
		});  	
   }
   
};

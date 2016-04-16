/*
		例1：
		var access_url	 = 'http://www.example.com';
		var method		 = 'POST';
		var params		 = {};
		params['params'] = {
			act:'test',
			id:1234,
			name:'test'
		}
		var source		 = accessUrl(access_url,method,getMakeQueryString(params['params']);

		例2：
		var access_url	 = 'http://www.example.com';
		var method		 = 'GET';
		var source		 = accessUrl(access_url,method,'');
*/

var ACCESS_UA		 = '';
var ACCESS_TIMEOUT	 = 15000;
var MAX_RETRY_LIMIT	 = 60;
var RETRY_COUNT		 = 0;
var STOP_TOOL_FLG	 = 'ok';
var xmlObj			 = null;
var get_source		 = null;

function sleep(time) {
	var d1 = new Date().getTime();
	var d2 = new Date().getTime();
	while (d2 < d1 + time)
		d2 = new Date().getTime();
	return;
}

function getMakeQueryString(hashData){
	var listData = [];
	var listCount = 0;
	for( var index in hashData ){
		listData[listCount] = index+'='+hashData[index];
		listCount++;
	}
	return listData.join('&');
}

function createXMLObj(){
	xmlObj = null;
	if("XMLHttpRequest" in window){
		xmlObj= new XMLHttpRequest();
	}else if("ActiveXObject" in window){
		try{
			xmlObj=new ActiveXobject("Msxml2.XMLHTTP");
		}catch(e){
			try{
				xmlObj=new ActiveXObject("Microsoft.XMLHTTP");
			}catch(e){}
		}
	}
}

var accessSource = {
	source:function(){
		var newObj = xmlObj;
		if (newObj.readyState == 4 ){
			if( newObj.status == 200 ){
				get_source	 = newObj.responseText;
				newObj = null;
				xmlObj = null;
			}
		}
	}
}

function retryAccess(access_url,method,params){
	RETRY_COUNT++;
	//再接続
	if( RETRY_COUNT <= MAX_RETRY_LIMIT ){
		accessUrl(access_url,method,params);
	//停止
	}else{
		STOP_TOOL_FLG = false;
		RETRY_COUNT = 0;
	}
}

function accessUrl(access_url,method,params){
	get_source		 = null;
	var user_agent	 = ACCESS_UA;
	var set_timeout	 = ACCESS_TIMEOUT;
	try{
		createXMLObj();
		xmlObj.open(method,access_url,false);
		xmlObj.timeout = set_timeout;
		if( method == 'GET' ){
//			xmlObj.setRequestHeader("If-Modified-Since","01 Jan 2000 00:00:00 GMT");
			xmlObj.setRequestHeader('User-Agent', user_agent);
//			xmlObj.setRequestHeader("Cache-Control", "no-cache");
//			xmlObj.setRequestHeader("Connection", "close");
			xmlObj.onreadystatechange = accessSource.source;
			xmlObj.send(null);
		}else if( method == 'POST' ){
			xmlObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
//			xmlObj.setRequestHeader("If-Modified-Since","01 Jan 2000 00:00:00 GMT");
			xmlObj.setRequestHeader('User-Agent', user_agent);
//			xmlObj.setRequestHeader("Cache-Control", "no-cache");
			xmlObj.setRequestHeader("Content-length", params.length);
//			xmlObj.setRequestHeader("Connection", "close");
			xmlObj.onreadystatechange = accessSource.source;
			xmlObj.send(params);
		}
	}catch(e){
		xmlObj.abort();
		sleep(4000);
		retryAccess(access_url,method,params);
	}
	if( get_source == null ){
		xmlObj.abort();
		sleep(4000);
		retryAccess(access_url,method,params);
	}
	RETRY_COUNT = 0;
	return get_source;
}

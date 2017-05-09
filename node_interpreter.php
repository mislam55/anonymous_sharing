<?php
	//Global Settings
	//Be sure to use a Node User and not a Transfer user
        $NODE_SERVER_AND_PORT = '137.92.56.132:9091';
        $NODE_SERVER_USER = “askme”;
        $NODE_SERVER_PASS = “askme”;
        $directory = "/";

	$allowedSites = "*"; //When using different PHP server specify CORS support.  Use * for ALL [not recommended]
	header('Access-Control-Allow-Origin: ' . $allowedSites);
	
	//This section checks for POST Data and returns content
	if(isset($_POST['upload'])) 
  	{
    	$spec = array('transfer_requests' => array(array('transfer_request' =>  (object) array('destination_root' => $_POST['upload'],'paths' => array( (object) array())))));
    	echo makeNodeRequest('upload_setup', $spec);
    	exit;
  	}
  	else if(isset($_POST['download'])) 
  	{
    	$spec = array('transfer_requests' => array(array('transfer_request' => array('paths' => array(array('source' => $_POST['download']))  )))); //,'destination_root' => '/','source_root' => '/'))));
    	echo makeNodeRequest('download_setup', $spec);
    	exit;
  	}
  	else if(isset($_POST['changeDirectory'])) 
  	{
    	echo makeNodeRequest('browse', array('path' => $_POST['changeDirectory']));
    	exit;
  	}
  	else if(isset($_POST['deleteFile'])) 
  	{
  		$spec = array('paths' => array(array(array('path' => $_POST['deleteFile']))));
    	echo makeNodeRequest('delete', $spec);
    	exit;
  	}
  	else if(isset($_POST['createDir'])) 
  	{
  		$spec = array('paths' => array(array(array('path' => $_POST['createDir'],'type' => 'directory'))));
    	echo makeNodeRequest('create', $spec);
    	exit;
  	}
  	else if(isset($_POST['renamePath'])) 
  	{
  		//Split up Path to get path and original name
		$originalName = end(explode("/", $_POST['renamePath']));
		$fullPath = str_replace($originalName, "", $_POST['renamePath']);
		$spec = array('paths' => array(array(array('path' => $fullPath,'source' => $originalName,'destination' => $_POST['renameName']))));   	
    	echo makeNodeRequest('rename', $spec);
    	exit;
  	}
  	else if(isset($_POST['startingdirectory'])) 
  	{
  		echo $directory;
    	exit;
  	}
	
	function makeNodeRequest($command, $spec) {
    	global $NODE_SERVER_AND_PORT, $NODE_SERVER_USER, $NODE_SERVER_PASS;
    	$json_url = $NODE_SERVER_AND_PORT . "/files/" . $command;
   		$json_string = json_encode($spec, JSON_UNESCAPED_SLASHES);
    	$ch = curl_init( $json_url );
    	$options = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_USERPWD => $NODE_SERVER_USER . ":" . $NODE_SERVER_PASS,  CURLOPT_HTTPHEADER => array("Content-type: application/json") , CURLOPT_POSTFIELDS => $json_string);
		curl_setopt_array( $ch, $options );
		$result = curl_exec($ch);
if(curl_error($ch))
{
    echo 'error:' . curl_error($ch);
} else {
		return $result;
}
	}
?>

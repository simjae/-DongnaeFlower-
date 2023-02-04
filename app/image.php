<?

$filename = $_REQUEST['src'];
if(_isInt($_REQUEST['w'])) $w = $_REQUEST['w'];
if(_isInt($_REQUEST['h'])) $h = $_REQUEST['h'];

// 파일명 조합
if(false !== $pos = strrpos('.',$filename)){
	$name = substr($filename,0,$pos);
	$ext = substr($filename,$pos+1);
	$destfile = $name.'_'.$width.'_'.$height.'.'.$ext;
	if(!file_exists($destfile) || filesize($destfile) < 1){
		$obj = new thumbnail();
		$obj->_read($filename);
		$obj->_make($destfile,$w,$h);
	}
	
	
	if(!file_exists($destfile) || filesize($destfile) < 1){
		header('404');		
	}else{
		
		@ob_end_clean(); // decrease cpu usage extreme
		/*
		HTTP/1.1 200 OK
		Content-Type: image/png
		Connection: keep-alive
		Accept-Ranges: bytes
		Content-Length: 8186
		*/
		
		header('Content-Type: ' . $mime_type); //(******
		header('Content-Disposition: $downview; filename="'.$name.'"');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header('Accept-Ranges: bytes');
		header("Cache-control: private");
		header('Pragma: private');
	
		$size2=$size-1;
		header("Content-Length: ".$size);
		
		if ($file = fopen($destfile, 'rb')){
			if(isset($_SERVER['HTTP_RANGE']))
				fseek($file, $range);
			while(!feof($file) and (connection_status()==0)){
				$buffer = fread($file, $chunksize);
				print($buffer);//echo($buffer); // is also possible				
			}
			@flush();
			fclose($file);
		} else					
		header("Connection: close");
	}
	
}

?>
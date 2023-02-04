<?PHP
$upload_max1 = 8000000;
if($_FILES["file"]) {
	$strFile1 = $_FILES["file"][tmp_name];
	$strFile1_name = $_FILES["file"][name];
	$strFile1_size = $_FILES["file"][size];
	$strFile1_type = $_FILES["file"][type];
}

if($strFile1_size>0&&$strFile1) {

	if(!is_uploaded_file($strFile1)){
		echo('{"result":"badFileErr"}');
		exit;
	}
	
	$strFile1_size=filesize($strFile1);
	if ($strFile1_size>$upload_max1){
		echo('{"result":"fileSizeErr"}');
		exit;
	}

	if($strFile1_size>0) {
		$s_file_name1=$strFile1_name;		
		if(preg_match("/.inc/",$s_file_name1)||preg_match("/.phtm/",$s_file_name1)||preg_match("/.htm/",$s_file_name1)||preg_match("/.shtm/",$s_file_name1)||preg_match("/.php/",$s_file_name1)||preg_match("/.dot/",$s_file_name1)||preg_match("/.asp/",$s_file_name1)||preg_match("/.cgi/",$s_file_name1)||preg_match("/.pl/",$s_file_name1)){
			echo('{"result":"badAccessErr"}');
			exit;
		}		
		$temp1_array=explode(".",$strFile1_name);
		$s_point=count($temp1_array)-1;
		$upload_ext1=$temp1_array[$s_point];
		$s_file_name1=time().".".$upload_ext1;
		
		if(!is_dir($_SERVER['DOCUMENT_ROOT'].$upload_Dir_1)) { 
			@mkdir($_SERVER['DOCUMENT_ROOT'].$upload_Dir_1,0777);
			@chmod($_SERVER['DOCUMENT_ROOT'].$upload_Dir_1,0777);
		}

	
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$upload_Dir_1.$s_file_name1)) {
			
			for($i=0;$i<(sizeof($temp1_array)-1);$i++){
				$temp_name1=$temp_name1.$temp1_array[$i].".";
			}
			
		 	$temp_name1=substr($temp_name1, 0, -1);			


			do
			{
				$loop_count1=$loop_count1+1;				
				$file_num_id=$file_num_id+1;					
				$temp_strFile1=$temp_name1."_".$file_num_id.".".$upload_ext1 ;
				
				if ($loop_count1>1000){ break;}
				
			} while ($s_file_name1==$temp_strFile1 || file_exists($_SERVER['DOCUMENT_ROOT'].$upload_Dir_1.$temp_strFile1));
			
			// 중복화일명 변경
			$s_file_name1=$temp_strFile1;
						
		}

		// 화일 업로드 
		if(!move_uploaded_file($strFile1,$_SERVER['DOCUMENT_ROOT'].$upload_Dir_1.$s_file_name1)){
			echo('{"result":"uploadFailed"}');
			exit;
		}
		else{

			$makesize='640'; //리사이징 가로사이즈(가로 800 이상일 때)
			$imagequality='90'; //리사이징 이미지 퀄리티			
			/* 리사이징 처리 */
			$imgname=$upload_Dir_1.$s_file_name1;
			$size=getimageSize($imgname);
			$width=$size[0];
			$height=$size[1];
			$imgtype=$size[2];
			
			if($width>=$makesize){
				if($imgtype==1){
					$im=ImageCreateFromGif($imgname);
				}else if($imgtype==2){
					$im=ImageCreateFromJpeg($imgname);
				}else if($imgtype==3){
					$im=ImageCreateFromPng($imgname);
				}
					
				$small_width=$makesize;
				$small_height=($height*$makesize)/$width;
				
				//회전값 설정 시작
				$rotate = 0;
				$exif = exif_read_data($imgname);
				if(!empty($exif['Orientation'])) {
					switch($exif['Orientation']) {
						case 8:
							$rotate = 90;
							break;
						case 3:
							$rotate = 180;
							break;
						case 6:
							$rotate = -90;
							break;
					}
				}
				//회전값 설정 끝
					
				if($imgtype==1){ //GIF일 경우
					$im2=ImageCreate($small_width,$small_height);
					ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					//이미지 회전 시작
					$im2 = imagerotate($im2, $rotate, 0);
					//이미지 회전 끝
					imageGIF($im2,$imgname);
					
				}else if($imgtype==2){ //JPG일 경우
					$im2=ImageCreateTrueColor($small_width,$small_height);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					//이미지 회전 시작
					$im2 = imagerotate($im2, $rotate, 0);
					//이미지 회전 끝
					imageJPEG($im2,$imgname,$imagequality);
						
				}else{ //PNG일 경우
					$im2=ImageCreateTrueColor($small_width,$small_height);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					//이미지 회전 시작
					$im2 = imagerotate($im2, $rotate, 0);
					//이미지 회전 끝
					imagePNG($im2,$imgname);
				}
					
				ImageDestroy($im);
				ImageDestroy($im2);
			}else{
				if($imgtype==1){
					$im=ImageCreateFromGif($imgname);
				}else if($imgtype==2){
					$im=ImageCreateFromJpeg($imgname);
				}else if($imgtype==3){
					$im=ImageCreateFromPng($imgname);
				}
				
				if($imgtype==1){ //GIF일 경우
					imageGIF($im,$imgname);
					
				}else if($imgtype==2){ //JPG일 경우
					imageJPEG($im,$imgname,$imagequality);
					
				}else{ //PNG일 경우
					imagePNG($im,$imgname);
				}
				
				ImageDestroy($im);
			}
		}
		$file_name1=$_SERVER['DOCUMENT_ROOT'].$upload_Dir_1.$s_file_name1;  
		$db_file_name=$s_file_name1; 
		@chmod($file_name1,0706);		

		$resultCode = "Y";
		$resultArr = array("result" => $resultCode,"file_name" => $db_file_name);
		$output =  json_encode($resultArr);
		 // 출력
		
		echo $output;		
	}
}	


?>
<?
	function _getQnaName($board){
		$temp[0] = explode("",$board);
		$temp[1] = explode("=", $temp[0][0]);

		$set_qna_bname = trim($temp[1][1]);
		unset($temp);
		
		return $set_qna_bname;
	}

	function _getCategoryList($categorycode){
		$fabcatenameSQL = "SELECT code_name FROM tblproductcode WHERE ";
		$realcatenameSQL ="";
		$tearmsCode="";
		for($i=0;$i<4;$i++){
			$code = substr($categorycode,$i*3,3);
			if(strlen($code) >=3 && $code !='000'){
				$realcode .= "code".chr(65+$i)."='".$code."'";
				$loop = 3 - $i;
				for($j=0;$j<$loop;$j++){
					$fabrication .= " AND code".chr(65+$i+1)."='000'";
				}

				$tearmsCode .= $code;
			}
			$realcatenameSQL = $fabcatenameSQL.$realcode.$fabrication;
			if(false !== $realcatenameRes = mysql_query($realcatenameSQL,get_db_conn())){
				$returndata .= '<a href="/m/productlist.php?code='._categoryCode($tearmsCode).'">'.mysql_result($realcatenameRes,0,0).'</a>'; 
				mysql_free_result($realcatenameRes);
				$returndata .= " > ";
			}
			if($i<3){
				$realcode.=" AND ";
			}
			$fabrication="";
		}
		if(substr($returndata,-3) == " > "){
			$returndata = substr($returndata,0,-3);
		}
		return $returndata;
	}
	
	function _categoryCode($code){
		$fullcategorycode="";
		for($i=0;$i<4;$i++){
			$realcode = substr($code,$i*3,3);
			if(strlen($realcode) >=3 && $realcode != "000"){
				$fullcategorycode .= $realcode;
			}else{
				$fullcategorycode .= "000";
			}
		}
		return $fullcategorycode;
	}
	function _currentCategoryName($categorycode){
		$length = strlen($categorycode);
		$loop = ceil($length/3);
		$tearmsSQL=array();
		$returndata="";
		for($i=0;$i<$loop;$i++){
			$code = substr($categorycode,$i*3,3);
			if(strlen($code) >=3 && $code != "000"){
				array_push ($tearmsSQL,"code".chr(65+$i)."='".$code."'");
			}else{
				array_push ($tearmsSQL,"code".chr(65+$i)."='000'");
			}
		}
		$lastDepthSQL = "SELECT code_name FROM tblproductcode ";
		if(sizeof($tearmsSQL) >0){
			$lastDepthSQL .= "WHERE ".implode(" AND ",$tearmsSQL);
		}
		if(false !== $lastDepthRes = mysql_query($lastDepthSQL,get_db_conn())){
			$rowcount = mysql_num_rows($lastDepthRes);
			if($rowcount >0){
				$returndata = mysql_result($lastDepthRes,0,0);
			}else{
				$returndata = "설정정보없음";
			}
			@mysql_free_result($lastDepthRes);
		}
		return $returndata;
	}
	function _getPage($totalRecord,$recordPerPage,$pagePerBlock,$currentPage,$pagetype,$variable=null){ 
			
			$totalNumOfPage = ceil($totalRecord/$recordPerPage); 
			$totalNumOfBlock = ceil($totalNumOfPage/$pagePerBlock); 
			$currentBlock = ceil($currentPage/$pagePerBlock); 

			$startPage = ($currentBlock-1)*$pagePerBlock+1;   
			$endPage = $startPage+$pagePerBlock -1;  
			if($endPage > $totalNumOfPage) $endPage = $totalNumOfPage; 

			 
			$isNext = false; 
			$isPrev = false; 

			if($currentBlock < $totalNumOfBlock)    $isNext = true; 
			if($currentBlock > 1)                     $isPrev = true; 

			if($totalNumOfBlock == 1){ 
				$isNext = false; 
				$isPrev = false; 
			}  
			if($pagetype == "product"){
				if($isPrev){ 
					$goPrevPage = $startPage-$pagePerBlock;  
					echo "<a rel=\"external\" href=\"$PHP_SELF?".$variable."page=$goPrevPage\"><li><span class=\"arrow_left\"></span></li></a>"; 
				
				}     
				for($i=$startPage;$i<=$endPage;$i++){ 
					if($i == $currentPage){
						echo "<a rel=\"external\" href=\"$PHP_SELF?".$variable."page=$i\"><li class=\"cur_page\">".$i."</li></a>";
					}else{
						
						echo "<a rel=\"external\" href=\"$PHP_SELF?".$variable."page=$i\"><li>".$i."</li></a>";
					}
				} 
				if($isNext){ 
						$goNextPage = $startPage+$pagePerBlock; 
						echo "<a rel=\"external\" href=\"$PHP_SELF?".$variable."page=$goNextPage\"><li><span class=\"arrow_right\"></span></li></a>"; 
				}   
		
			}else if($pagetype == "board"){
				if($isPrev){ 
					$goPrevPage = $startPage-$pagePerBlock;  
					echo "<a rel=\"external\" href=\"$PHP_SELF?page=$goPrevPage\"><li><span class=\"arrow_left\"></span></li></a>";         
				}     
				for($i=$startPage;$i<=$endPage;$i++){ 
					if($i == $currentPage){
						echo "<a rel=\"external\" href=\"$PHP_SELF?page=$i\"><li class=\"cur_page\">".$i."</li></a>";
					}else{
			
						echo "<a rel=\"external\" href=\"$PHP_SELF?page=$i\"><li>".$i."</li></a>";
					}
				} 
				if($isNext){ 
						$goNextPage = $startPage+$pagePerBlock; 
						echo "<a rel=\"external\" href=\"$PHP_SELF?page=$goNextPage\"><li><span class=\"arrow_right\"></span></li></a>"; 
				}
			}

			
		}
	function _getPaging($allRowNum,$listNum,$blockNum,$currentPage,$style=null,$variable=null){
		
		$totalNumOfPage = ceil($allRowNum/$listNum); 
		$totalNumOfBlock = ceil($totalNumOfPage/$blockNum); 
		$currentBlock = ceil($currentPage/$blockNum); 

		$startPage = ($currentBlock-1)*$blockNum+1;   
		$endPage = $startPage+$blockNum -1;  
		if($endPage > $totalNumOfPage) $endPage = $totalNumOfPage; 

		$isNext = false; 
		$isPrev = false; 

		if($currentBlock < $totalNumOfBlock)    $isNext = true; 
		if($currentBlock > 1)                     $isPrev = true; 

		if($totalNumOfBlock == 1){ 
			$isNext = false; 
			$isPrev = false; 
		}  
		if($isPrev){ 
			$goPrevPage = $startPage-$blockNum; 
			echo "<a rel=\"external\" href=\"?page=$goPrevPage$variable\"><span class=\"page_prev\">&nbsp;</span></a>";         
		}     

		for($i=$startPage;$i<=$endPage;$i++){ 
			
			if($i == $currentPage){
				echo "<a rel=\"external\" href=\"?page=$i$variable\" $style>".$i."</a>";
			}else{
	
				echo "<a rel=\"external\" href=\"?page=$i$variable\">".$i."</a>";
			}
		} 
		if($isNext){ 
			$goNextPage = $startPage+$blockNum; 
			echo "<a rel=\"external\" href=\"?page=$goNextPage$variable\"><span class=\"page_next\">&nbsp;</span></a>"; 
		}
	}

	function _getImageSize($location){
		$size = array();
		
		if(is_file($location)){
			$temp = getimagesize($location);
			$temp_size = explode("\"",$temp[3]);
			$size['width'] = $temp_size[1];
			$size['height'] = $temp_size[3];
			$size['error'] = "false";
		}else{
			$size['error'] = "true";
		}
		return $size;
	}

	function _getImageRateSize($src,$size=80){
		$set_rate="";
		if(is_file($src)){
			$tempsize = getimagesize($src);
			$_width = $tempsize[0];//가로사이즈
			$_height = $tempsize[1];//세로사이즈
			if($_width >= $_height){
				$set_rate = 'width="'.$size.'"';
			}else{
				$set_rate = 'height="'.$size.'"';
			}
		}
		return $set_rate;
	}
	
	function _getMobileThumbnail($origloc,$saveloc,$filename,$width,$height,$quality=75){
		$thumb ="";
		$nofile = "/images/no_img.gif";
		$origfilename = $filename;
		$thumbdirname = "w_".$width."_h_".$height; // 디렉토리명
		$returndir = "/data/shopimages/mobile/".$thumbdirname."/";
		$loc_originalfile = $origloc.$origfilename;  // 원본 이미지
		$loc_thumbnail = $saveloc.$thumbdirname."/"; // 썸내일 이미지 경로
		if(!is_dir($loc_thumbnail)){
			if(mkdir($loc_thumbnail)){
				@chmod($loc_thumbnail, 0707);
			}
		}
		$pwd_thumbnail = $loc_thumbnail.$origfilename; // 섬내일 파일 절대 경로및 파일이름
		
		if(!is_file($pwd_thumbnail)){  //썸내일 파일이 없다면
			if(is_file($loc_originalfile)){ // 원본파일이 있냐?
				$thumb = _createMobileThumbnail($origloc,$saveloc,$origfilename,$width,$height,$quality=75);
			}else{
				$thumb = $nofile;
			}
		}else{
			$origcheckdate = date("H-i-s-n-j-Y",filemtime($loc_originalfile)); // 원본 파일 업데이트 시간
			$thumbcheckdate = date("H-i-s-n-j-Y",filemtime($pwd_thumbnail)); // 썸네일 파일 업데이트 시간
			$origdate = explode("-",$origcheckdate);
			$thumbdate = explode("-",$thumbcheckdate);
			$origmktime = mktime($origdate[0],$origdate[1],$origdate[2],$origdate[3],$origdate[4],$origdate[5]); 
			$thumbmktime = mktime($thumbdate[0],$thumbdate[1],$thumbdate[2],$thumbdate[3],$thumbdate[4],$thumbdate[5]); 
			if($origmktime > $thumbmktime){ // 이미지 파일이 업데이트 되었다면 다시 썸네일을 만든다.
				$thumb = _createMobileThumbnail($origloc,$saveloc,$origfilename,$width,$height,$quality=75);
			}else{
				$thumb = $returndir.$origfilename;
			}
		}
		return $thumb;
	}

	function _createMobileThumbnail($origloc,$saveloc,$filename,$width,$height,$quality=75){
		unset($filetype);
		unset($src_img);
		unset($thumbloc);
		$isfile = $filename;
		$thumbnailName = "w_".$width."_h_".$height; // 썸내일 추가 파일명 및 디렉토리명
		$loc_originalfile = $origloc.$isfile;  // 원본 이미지
		$loc_thumbnail = $saveloc.$thumbnailName."/"; // 썸내일 이미지
		$thumbname = $loc_thumbnail.$isfile;
		$fileinfo = getimagesize($loc_originalfile);
		$returndata = "";

		switch($fileinfo[2]){
			case 1: // gif
				$src_img = imagecreatefromgif($loc_originalfile);
			break;
			case 2: //jpg
				$src_img = imagecreatefromjpeg($loc_originalfile);
			break;
			case 3: //png
				$src_img = imagecreatefrompng($loc_originalfile);
			break;
		}
			
		$originWidth = imagesx($src_img); // 원본 이미지 가로
		$originHeight = imagesy($src_img); // 원본 이미지 세로

		if($originWidth == $originHeight){// 비율이 같은경우
			$thumbnameWidth = $width;
			$thumbnameHeight = $height;
		}elseif($originWidth > $originHeight){ // 비율이 가로가 큰 경우
			$thumbnameWidth = $width;
			$thumbnameHeight = ceil(($width / $originWidth) * $originHeight);
	
		}else{ 
			// 비율이 세로가 큰 경우
			$thumbnameHeight = $height;
			$thumbnameWidth =ceil(($height / $originHeight) * $originWidth);

		}
	
		if($thumbnameWidth < $width) $srcx = ceil(($width - $thumbnameWidth)/2); else $srcx = 0;
		if($thumbnameHeight < $height) $srcy = ceil(($height - $thumbnameHeight)/2); else $srcy = 0;

		if($fileinfo[2] == 1) {
			   $destimage = imagecreate($thumbnameWidth, $thumbnameHeight);
		}else{
			   $destimage = imagecreatetruecolor($thumbnameWidth, $thumbnameHeight);
		}

		$rgb = ImageColorAllocate($destimage, 255, 255, 255);
		ImageFilledRectangle($destimage, 0, 0, $width, $height, $rgb); 
		ImageCopyResampled($destimage, $src_img, 0, 0, 0, 0, $thumbnameWidth, $thumbnameHeight,$originWidth,$originHeight);

		switch($fileinfo[2]){
			case 1: // gif
				imagegif($destimage,$thumbname);
			break;
			case 2: //jpg
				imagejpeg($destimage,$thumbname,$quality);
			break;
			case 3: //png
				imagepng($destimage,$thumbname);
			break;
		}
		chmod($thumbname, 0707);

		imagedestroy($destimage);
		imagedestroy($src_img);
		
		$returndata = "/data/shopimages/mobile/".$thumbnailName."/".$isfile;
		return $returndata;
				
	}
	
	function _getSubCateName($category,$start=0, $end=0){
		$where = array();
		$subcategory = true;
		for($i=0;$i<4;$i++){
			$realCode = substr($category, $i*3,3);
			
			if(strlen($realCode) == 3 && $realCode != '000'){
				array_push($where," code".chr(65+$i)."='".$realCode."'");
			}else{
				if($subcategory === true || ($i == 0 && _empty($category))){
					array_push($where," code".chr(65+$i)."!='000'");
					$subcategory = false;
				}else{
					array_push($where," code".chr(65+$i)."='000'");
				}
			}
		}
		array_push($where,"type NOT LIKE 'S%'","group_code!='NO' ");
		$where = ' WHERE '.implode(' and ',$where);
		$cateSql = "SELECT code_name, codeA, codeB, codeC, codeD FROM tblproductcode ".$where." ORDER BY sequence DESC ";
		if($end > 0){
			$cateSql .= "LIMIT ".$start.", ".$end." ";
		}
		$cateResult = mysql_query($cateSql, get_db_conn());
		$cateRowsNum = mysql_num_rows($cateResult);
		$categoryList = array('state'=>0,'list'=>array());

		if($cateRowsNum > 0){
			unset($fullcategoryCode);
			while($cateRow = mysql_fetch_object($cateResult)){
				$fullcategoryCode = $cateRow->codeA.$cateRow->codeB.$cateRow->codeC.$cateRow->codeD;
				if(substr($category,-3,3) == "000"){
					array_push($categoryList['list'],$cateRow->code_name."|".$fullcategoryCode);
				}
			}
			$categoryList['state'] = 'list';
			mysql_free_result($cateResult);
		}else{
			$categoryList['state'] = 'null';
		}
		return $categoryList;
	}

	function _getUnitConverter($size) {
		$unitList = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
		$setUnit = round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $unitList[$i];
		return $setUnit;
	}

	function _uploadMaxFileSize(){
		$_FileInfo = array();
		
		$_converterUnit = substr(ini_get('upload_max_filesize'),-1);
		$_origFileSize = substr(ini_get('upload_max_filesize'),0,-1);

		switch($_converterUnit){
			case 'K':
			case 'k':
				$_converterFileSize = $_origFileSize * 1024;
			break;
			case 'M':
			case 'm':
				$_converterFileSize = $_origFileSize * 1048576;
			break;
			case 'G':
			case 'g':
				$_converterFileSize = $_origFileSize * 1073741824;
			break;
		}

		$_FileInfo['unit'] = _getUnitConverter($_converterFileSize);
		$_FileInfo['maxsize'] = $_converterFileSize;
		
		return $_FileInfo;
	}

	function _iPhoneCheck(){
		$_checkdevice = '/(iPod|iPhone)/';
		$_returndata = "";
		if(preg_match($_checkdevice,$_SERVER['HTTP_USER_AGENT'])){
			$_returndata="I";
		}else{
			$_returndata="E";
		}
		return $_returndata;
	}

	function _strCut($char,$baselength=0,$applelength,$charset){
		$returnstring = "";
		$_device = _iPhoneCheck();
		$_charset = $charset;
		$charactorlangth = "";
		switch($_device){
			case "I":
				$charactorlangth = $baselength - $applelength;
				if(mb_strlen($char) > $baselength){
					$returnstring = mb_substr($char,0,$charactorlangth,$_charset)."...";
				}else{
					$returnstring = $char;
				}
			break;
			default:
				$charactorlangth=$baselength;
				if(mb_strlen($char) > $baselength){
					$returnstring = mb_substr($char,0,$charactorlangth,$_charset)."...";
				}else{
					$returnstring = $char;
				}
			break;
		}
		return $returnstring;
	}

	function cutStr($msg,$cut_size) {
		if($cut_size<=0) return $msg;
		//if(ereg("\[re\]",$msg)) $cut_size=$cut_size+4;
		for($i=0;$i<$cut_size;$i++) if(ord($msg[$i])>127) $han++; else $eng++;
		$cut_size=$cut_size+(int)$han*0.6;
		$point=1;
		for ($i=0;$i<strlen($msg);$i++) {
			if ($point>$cut_size) return $pointtmp."...";
			if (ord($msg[$i])<=127) {
				$pointtmp.= $msg[$i];
				if ($point%$cut_size==0) return $pointtmp."..."; 
			} else {
				if ($point%$cut_size==0) return $pointtmp."...";
				$pointtmp.=$msg[$i].$msg[++$i];
				$point++;
			}
			$point++;
		}
		return $pointtmp;
	}
?>
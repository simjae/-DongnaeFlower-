<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."app/inc/function.php");

	$mode = !_empty($_POST['mode'])?trim($_POST['mode']):"";  //모드
	$categorycode = !_empty($_POST['code'])?trim($_POST['code']):""; // 카테고리코드
	$productcode = !_empty($_POST['productcode'])?trim($_POST['productcode']):""; // 상품코드
	$sort = !_empty($_POST['sort'])?trim($_POST['sort']):""; // 정렬
	$quality = !_empty($_POST['quality'])?trim($_POST['quality']):"";//품질
	$price = !_empty($_POST['price'])?trim($_POST['price']):"";//가격
	$delitime = !_empty($_POST['delitime'])?trim($_POST['delitime']):"";//배송시간
	$recommend = !_empty($_POST['recommend'])?trim($_POST['recommend']):"";//추천
	$writer = !_empty($_POST['rname'])?trim($_POST['rname']):"";//작성자
	$contents= !_empty($_POST['rcontent'])?trim($_POST['rcontent']):"";//내용
	
	$avermark = floor(((int) $quality+ (int) $price + (int) $delitime+ (int) $recommend) /4);

	if($_data->review_type =="N" || $_data->ETCTYPE["REVIEW"]=="N") {
		echo '<script>alert("사용후기 기능 설정이 되지 않아 사용할 수 없습니다.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}
	if(strlen($_ShopInfo->getMemid())==0 && $_data->review_memtype=="Y"){
		echo '<script>alert("회원전용 기능입니다.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}

	if(strlen($mode) <= 0 || $mode != "write"){
		echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($categorycode) <= 0 || strlen($categorycode)>12){
		echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다2.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($productcode) <= 0 || strlen($productcode)>18){
		echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다3.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($quality) <= 0 || ($quality < 1 || $quality > 5)){
		echo '<script>alert("품질 점수를 선택해 주세요.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($price) <= 0 || ($price < 1 || $price > 5)){
		echo '<script>alert("가격 점수를 선택해 주세요.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($delitime) <= 0 || ($delitime < 1 || $delitime > 5)){
		echo '<script>alert("배송 점수를 선택해 주세요.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($recommend) <= 0 || ($recommend < 1 || $recommend > 5)){
		echo '<script>alert("추천 점수 선택해 주세요.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($writer) <= 0){
		echo '<script>alert("작성자를 입력해 주세요.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}else if(strlen($contents) <= 0){
		echo '<script>alert("내용을 입력해 주세요.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}

	$allowfile = array('image/pjpeg','image/jpeg','image/JPG','image/X-PNG','image/PNG','image/png','image/x-png','image/gif');
	$saveattechfile = $Dir."data/shopimages/productreview/";
	$getmaxfilesize = _uploadMaxFileSize();
	
	$maxfilesize = $getmaxfilesize['maxsize'];//실제사이즈
	$maxfilesize_unit = $getmaxfilesize['unit'];//컨버팅사이즈
	$filename="";
	$queryattechname="";
	$attechfilename = !_empty($_FILES['attech']['name'])?trim($_FILES['attech']['name']):"";
	if(strlen($attechfilename)>0){
		$attechfiletype = !_empty($_FILES['attech']['type'])?trim($_FILES['attech']['type']):"";
		$attechfilesize = !_empty($_FILES['attech']['size'])?trim($_FILES['attech']['size']):"";
		$attechtempfilename = !_empty($_FILES['attech']['tmp_name'])?trim($_FILES['attech']['tmp_name']):"";
		if(!in_array($attechfiletype,$allowfile)){
			echo '<script>alert("첨부 가능한 파일이 아닙니다.\n첨부가능한 파일은 jpg, gif, png입니다.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
		}else{
			if($attechfilesize >$maxfilesize){
				echo '<script>alert("첨부 가능한 파일 용량이 초과 되었습니다.\n최대 첨부가능한 파일용량은 '.$maxfilesize_unit.'입니다.");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
			}else{
				$filename = date("YmdHis").$attechfilename;

				if(move_uploaded_file($attechtempfilename,$saveattechfile.$filename)){
					$queryattechname = $filename;
				}
			}
		}
	}
	
	$reviewwriteSQL ="INSERT tblproductreview SET ";
	$reviewwriteSQL.= "productcode	= '".$productcode."'";
	$reviewwriteSQL.= ", id			= '".$_ShopInfo->getMemid()."'";
	$reviewwriteSQL.= ", name		= '".$writer."'";
	$reviewwriteSQL.= ", marks		= '".$avermark."'";
	$reviewwriteSQL.= ", date		= '".date("YmdHis")."'";
	$reviewwriteSQL.= ", content		= '".$contents."'";
	$reviewwriteSQL.= ", device		= 'P'";
	$reviewwriteSQL.= ", quality		= '".$quality."'";
	$reviewwriteSQL.= ", price		= '".$price."'";
	$reviewwriteSQL.= ", delitime		= '".$delitime."'";
	$reviewwriteSQL.= ", recommend = '".$recommend."' ";
	if(strlen($queryattechname)>0){
		$reviewwriteSQL.= ", img = '".$queryattechname."' ";
	}
	
	$returnmsg="";

	if(false !== mysql_query($reviewwriteSQL,get_db_conn())){
		if($_data->review_type=="A") {
			$returnmsg="관리자 인증후 등록됩니다.";
		}else{
			$returnmsg="상품평이 등록되었습니다.";
		}
	}else{
		$returnmsg="상품평이 등록되지 않았습니다.";
	}

	echo '<script>alert("'.$returnmsg.'");location.replace("/m/productdetail_tab01.php?productcode='.$productcode.'");</script>';
	exit;
?>
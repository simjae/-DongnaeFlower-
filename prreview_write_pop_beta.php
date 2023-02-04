<?
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."app/inc/function.php");
	$_FileInfo = _uploadMaxFileSize();

	$_MAX_FILE_SIZE = $_FileInfo['maxsize'];
	$_MSG_UNIT = $_FileInfo['unit'];
	$productcode = !_empty($_REQUEST['productcode'])?trim($_REQUEST['productcode']):"";
	$aoidx = !_empty($_REQUEST['aoidx'])?trim($_REQUEST['aoidx']):"";
	
	$mode = !_empty($_REQUEST['mode'])?trim($_REQUEST['mode']):"";  //모드
	$categorycode = !_empty($_REQUEST['code'])?trim($_REQUEST['code']):""; // 카테고리코드

	$sort = !_empty($_REQUEST['sort'])?trim($_REQUEST['sort']):""; // 정렬
	$quality = !_empty($_REQUEST['quality'])?trim($_REQUEST['quality']):"";//품질
	$price = !_empty($_REQUEST['price'])?trim($_REQUEST['price']):"";//가격
	$delitime = !_empty($_REQUEST['delitime'])?trim($_REQUEST['delitime']):"";//배송시간
	$recommend = !_empty($_REQUEST['recommend'])?trim($_REQUEST['recommend']):"";//추천
	$writer = !_empty($_REQUEST['rname'])?trim($_REQUEST['rname']):"";//작성자
//	$avermark = floor(((int) $quality+ (int) $price + (int) $delitime+ (int) $recommend) /4); //평균
	$avermark = !_empty($_REQUEST['marks'])?trim($_REQUEST['marks']):"";//추천
	$badge01 = !_empty($_REQUEST['badge01'])?trim($_REQUEST['badge01']):"";//뱃지01
	$badge02 = !_empty($_REQUEST['badge02'])?trim($_REQUEST['badge02']):"";//뱃지01
	$badge03 = !_empty($_REQUEST['badge03'])?trim($_REQUEST['badge03']):"";//뱃지01
	$badge04 = !_empty($_REQUEST['badge04'])?trim($_REQUEST['badge04']):"";//뱃지01
	$badge05 = !_empty($_REQUEST['badge05'])?trim($_REQUEST['badge05']):"";//뱃지01
	$badge06 = !_empty($_REQUEST['badge06'])?trim($_REQUEST['badge06']):"";//뱃지01
	$badge07 = !_empty($_REQUEST['badge07'])?trim($_REQUEST['badge07']):"";//뱃지01
	$badge08 = !_empty($_REQUEST['badge08'])?trim($_REQUEST['badge08']):"";//뱃지01
	$badge09 = !_empty($_REQUEST['badge09'])?trim($_REQUEST['badge09']):"";//뱃지01
	$badge10 = !_empty($_REQUEST['badge10'])?trim($_REQUEST['badge10']):"";//뱃지01
	if(strlen($writer)==0){
		$writer = $_ShopInfo->getMemName();
	}
	$contents= !_empty($_REQUEST['rcontent'])?trim($_REQUEST['rcontent']):"";//내용


	if(strlen($productcode)<=0){
		echo '<script>alert("잘못된 페이지 접근입니다.");self.close();</script>';exit;
	}
	
	$productSQL ="SELECT 
					TP.* 
					,TVS.brand_name
					,TVI.com_addr
					, IFNULL(
						(SELECT 
							ROUND(AVG(marks),1)
						FROM 
							tblproductreview AS TPR
						LEFT JOIN
							tblproduct AS TP1
						ON
							TPR.productcode = TP1.productcode
						WHERE
							TP1.vender = TP.vender
						GROUP BY TP1.vender)
						,0) AS avg_marks
					, IFNULL(
						(SELECT 
							count(TP2.vender)
						FROM 
							tblproductreview AS TPR
						LEFT JOIN
							tblproduct AS TP2
						ON
							TPR.productcode = TP2.productcode
						WHERE
							TP2.vender = TP.vender
						GROUP BY TP2.vender)
						,0) AS marks_count
				FROM 
					tblproduct AS TP
				LEFT JOIN
					tblvenderstore AS TVS
				ON
					TP.vender = TVS.vender
				LEFT JOIN
					tblvenderinfo AS TVI
				ON
					TP.vender = TVI.vender
				WHERE 
					TP.productcode = '".$productcode."' ";
	$imagesrc = $Dir."data/shopimages/product/";
	if(false !== $productRes = mysql_query($productSQL,get_db_conn())){
		$productrowcount = mysql_num_rows($productRes);

		if($productrowcount>0){
			$productRow = mysql_fetch_assoc($productRes);
			$productname = $productRow['productname'];
			$productimage =$productRow['minimage'];
			$productprice = number_format($productRow['sellprice']);
			$brand_name =$productRow['brand_name'];
			$com_addr =$productRow['com_addr'];
			$avg_marks =$productRow['avg_marks'];
			$marks_count = number_format($productRow['marks_count']);
			$src = $imagesrc.$productimage;
			$size = _getImageRateSize($src,80);
		}
	}


	if($_data->review_type =="N" || $_data->ETCTYPE["REVIEW"]=="N") {
		echo '<script>alert("사용후기 기능 설정이 되지 않아 사용할 수 없습니다.");parent.location.replace("/app");</script>';exit;
	}
	if(strlen($_ShopInfo->getMemid())==0 && $_data->review_memtype=="Y"){
		echo '<script>alert("회원전용 기능입니다.");parent.location.replace("/app");</script>';exit;
	}
	
	if($mode == "write"){
		if(strlen($code) <= 0 || strlen($code)>12){
			echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다2.");location.replace("/app/prreview_write_pop.php?productcode='.$productcode.'&aoidx='.$aoidx.'");</script>';exit;
		}else if(strlen($productcode) <= 0 || strlen($productcode)>18){
			echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다3.");location.replace("/app/prreview_write_pop.php?productcode='.$productcode.'&aoidx='.$aoidx.'");</script>';exit;
		}else if(strlen($marks) <= 0 || ($marks < 1 || $marks > 5)){
			echo '<script>alert("추천 점수 선택해 주세요.");location.replace("/app/prreview_write_pop.php?productcode='.$productcode.'&aoidx='.$aoidx.'");</script>';exit;
		}else if(strlen($writer) <= 0){
			echo '<script>alert("작성자를 입력해 주세요.");location.replace("/app/prreview_write_pop.php?productcode='.$productcode.'&aoidx='.$aoidx.'");</script>';exit;
		}else if(strlen($contents) <= 0){
			echo '<script>alert("내용을 입력해 주세요.");location.replace("/app/prreview_write_pop.php?productcode='.$productcode.'&aoidx='.$aoidx.'");</script>';exit;
		}
	
	
		$allowfile = array('image/pjpeg','image/jpeg','image/JPG','image/X-PNG','image/PNG','image/png','image/x-png','image/gif');
		$saveattechfile = $Dir."data/shopimages/productreview/";
		$getmaxfilesize = _uploadMaxFileSize();
		
		$maxfilesize = $getmaxfilesize['maxsize'];//실제사이즈
		$maxfilesize_unit = $getmaxfilesize['unit'];//컨버팅사이즈
		$filename="";
		$queryattechname="";
		$attechfilename = !_empty($_FILES['attech']['name'])?trim($_FILES['attech']['name']):"";
		//리뷰 적립금 (텍스트 500)
		$reserve = 500;
		if(strlen($attechfilename)>0){
			//리뷰 적립금 (이미지 1,000)
			$reserve = 1000;
			$attechfiletype = !_empty($_FILES['attech']['type'])?trim($_FILES['attech']['type']):"";
			$attechfilesize = !_empty($_FILES['attech']['size'])?trim($_FILES['attech']['size']):"";
			$attechtempfilename = !_empty($_FILES['attech']['tmp_name'])?trim($_FILES['attech']['tmp_name']):"";
			if(!in_array($attechfiletype,$allowfile)){
				echo '<script>alert("첨부 가능한 파일이 아닙니다.\n첨부가능한 파일은 jpg, gif, png입니다.");location.replace("/app/prreview_write_pop.php?productcode='.$productcode.'&aoidx='.$aoidx.'");</script>';exit;
			}else{
				$filename = date("YmdHis").$attechfilename;

				if(move_uploaded_file($attechtempfilename,$saveattechfile.$filename)){
					$queryattechname = $filename;
					$makesize='640'; //리사이징 가로사이즈(가로 800 이상일 때)
					$imagequality='90'; //리사이징 이미지 퀄리티			
					/* 리사이징 처리 */
					$imgname=$saveattechfile.$filename;
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
		$reviewwriteSQL.= ", badge01 = '".$badge01."' ";
		$reviewwriteSQL.= ", badge02 = '".$badge02."' ";
		$reviewwriteSQL.= ", badge03 = '".$badge03."' ";
		$reviewwriteSQL.= ", badge04 = '".$badge04."' ";
		$reviewwriteSQL.= ", badge05 = '".$badge05."' ";
		$reviewwriteSQL.= ", badge06 = '".$badge06."' ";
		$reviewwriteSQL.= ", badge07 = '".$badge07."' ";
		$reviewwriteSQL.= ", badge08 = '".$badge08."' ";
		$reviewwriteSQL.= ", badge09 = '".$badge09."' ";
		$reviewwriteSQL.= ", badge10 = '".$badge10."' ";
		if(strlen($queryattechname)>0){
			$reviewwriteSQL.= ", img = '".$queryattechname."' ";
		}
		//주문서 주문 리뷰완료 처리
		if($aoidx>0){
			$sql="UPDATE auction_order SET status = 5, updateDate = NOW() WHERE aoidx = '".$aoidx."'";
			mysql_query($sql,get_db_conn());
		}
		
		//주문로그 주문 리뷰완료 처리
		$log_sql = "INSERT INTO tblorderlog (vender,aoidx,ordercode,productcode,pay_admin_proc,deli_gbn,deli_com,deli_num,deli_date,reviewWrite,createDate)
				SELECT
					op.vender
					,op.aoidx
					,op.ordercode
					,op.productcode
					,oi.pay_admin_proc
					,op.deli_gbn
					,op.deli_com
					,op.deli_num
					,op.deli_date
					,'Y'
					,NOW()
				FROM
					tblorderproduct op
					LEFT JOIN tblorderinfo oi ON
					op.ordercode = oi.ordercode
				WHERE
					op.productcode = '".$productcode."'";
		mysql_query($log_sql,get_db_conn());
		
//		echo $log_sql;
		$returnmsg="";

		if(false !== mysql_query($reviewwriteSQL,get_db_conn())){
			if($_data->review_type=="A") {
				$returnmsg="관리자 인증후 등록됩니다.";
			}else{
				$returnmsg="리뷰가 등록되었습니다.";
				
				//적립금 DB등록
				$content = "상품리뷰 작성으로 인한 적립금";
				$date = date("YmdHis");
				$reserve_yn="Y";
				
				$sql.= "INSERT tblreserve SET ";
				$sql.= "id				= '".$_ShopInfo->getMemid()."', ";
				$sql.= "reserve			= ".$reserve.", ";
				$sql.= "reserve_yn		= '".$reserve_yn."', ";
				$sql.= "content			= '".$content."', ";
				$sql.= "date			= '".$date."' ";
				mysql_query($sql,get_db_conn());
				$sql = "UPDATE tblmember SET reserve=reserve+".abs($reserve)." WHERE id='".$_ShopInfo->getMemid()."' ";
				mysql_query($sql,get_db_conn());
				$sql = "UPDATE tblproductreview SET reserve=$reserve ";
				$sql.= "WHERE id = '".$_ShopInfo->getMemid()."' AND productcode = '".$productcode."' AND date = '".$date."' ";
				mysql_query($sql,get_db_conn());
				//적립금 DB등록 끝
			}
		}else{
			$returnmsg="리뷰가 등록되지 않았습니다.";
		}

		echo '<script>alert("'.$returnmsg.'");parent.reviewProc('.$aoidx.',"'.$productcode.'");</script>';
	}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="viewport" content="width=420, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<link rel="stylesheet" href="./css/common.css" />
<link rel="stylesheet" href="./css/skin/default.css" />

<script type="text/javascript" src="/app/js/jquery-1.10.2.min.js"></script>
</head>
<style>
.left{
	float: left;
}
.right{
	float: right;
}
.prreviewWriteWrap .headerWrap{
	border-bottom: solid 1px #cccccc;
	background: #ffffff;
    overflow: hidden;
}

.prreviewWriteWrap .headerWrap h2 {
    display: block;
    background: #ffffff;
    text-align: center;
    font-size: 1.4em;
    padding: 8px 12px;
    color: #000000;
    font-weight: 500;
	margin: 20px;
}
.prreviewWriteWrap .contentWrap{
	overflow: hidden;
	margin: 0px auto;
    padding: 0px 15px;
    border: 0px;
    font-size: 14px;
    background: #ffffff;
    overflow: hidden;
	border-bottom: solid 1px #cccccc;

}
.prreviewWriteWrap .contentWrap .infoGroup{
	overflow: hidden;
	padding: 20px 10px 0 20px;
	margin-bottom: 20px;
}
.prreviewWriteWrap .contentWrap .infoRow .bellIcon {
	margin:5px 15px 0 0;
    width: 18px;
}
.prreviewWriteWrap .contentWrap .infoRow .dateText {
	margin-top: 5px;
    margin-left: 18px;
    font-size: 1.2em;
    color: #000000;
    line-height: 1.7em;
}
.prreviewWriteWrap .contentWrap .addrRow{
	margin-top: 5px;
	font-size: 12px;
}
.prreviewWriteWrap .contentWrap .addrRow .addr{
	margin-left: 33px;
}
.prreviewWriteWrap .contentWrap .addrRow .starIcon{
    width: 11px;
    margin: 1px 5px 0px 10px;
}
.prreviewWriteWrap .contentWrap .addrRow .reviewAverage{
	color:#000000;
	margin-left: 5px;
}
.prreviewWriteWrap .contentWrap .priceRow{
	margin-top:5px;
}
.prreviewWriteWrap .contentWrap .priceRow .pick{
	margin-left: 33px;
	border-radius: 30px;
    padding: 3px 7px 3px 7px;
    background: #83838330;
	font-size: 12px;
}
.prreviewWriteWrap .contentWrap .priceRow .dateText{
	font-size: 1.2em;
    color: #000000;
    line-height: 2em;
}
.prreviewWriteWrap .contentWrap .prtitleWrap{
	padding: 20px 10px 0 20px;
    margin-bottom: 20px;
}
.prreviewWriteWrap .contentWrap .prtitleWrap .bookIcon{
	width: 18px;
	margin:5px 15px 0 0;
}
.prreviewWriteWrap .contentWrap .prtitleWrap .abc{
	margin-top: 5px;
    margin-left: 18px;
    font-size: 1.2em;
    color: #000000;
    line-height: 1.7em;
}
.prreviewWriteWrap .pickWrap .pickGrop{
	margin-bottom: 20px;
    overflow: hidden;
}
.prreviewWriteWrap .pickWrap .pickGrop .pickbanner{
	width: fit-content;
    font-weight: 600;
    float: left;
    font-size: 0.9em;
    color: #717071;
    margin: 5px 7px 5px 0;
    padding: 10px;
    border-radius: 15px;
    line-height: 0.9em;
	box-shadow: 1px 1px 4px #888888;
}

.prreviewWriteWrap .pickWrap .pickGrop .pickbanner.selected{
	background-color: #ee4481;
	color:#ffffff!important;
	box-shadow: 1px 1px 4px #ee4481 !important;
}
.prreviewWriteWrap .contentWrap .subContent {
	margin:0 15px 0 50px;
}
.prreviewWriteWrap .contentWrap .subContent .starWrap img{
	width: 20px;
	margin-right: 5px;
}
.prreviewWriteWrap .contentWrap .subContent .addImgWrap label img{
	width: 50px;
}

.prreviewWriteWrap .contentWrap .subContent .writeContentWrap{
	margin: 20px 0 10px 0;
}
.prreviewWriteWrap .contentWrap .subContent .addImgWrap {
	overflow:hidden
}
.prreviewWriteWrap .contentWrap .subContent .addImgWrap .fileBox{
	width: 50px; 
	height: 40px; 
    padding: 25px 20px 20px 20px;
    border-radius: 5px;
    border: solid 1px #cccccc;
	float:left;
}
.prreviewWriteWrap .contentWrap .subContent .addImgWrap .previewImage{
	width: 90px;
	height: 85px;
    border-radius: 5px;
    border: solid 1px #cccccc;
	display:none;
	overflow:hidden;
	margin-right:10px;
	float:left;
}
.prreviewWriteWrap .contentWrap .subContent .addImgWrap .previewImage img{
	width:100%;
}
.filebox input[type="file"] {
    position: absolute;
    width: 0;
    height: 0;
    padding: 0;
    overflow: hidden;
    border: 0;
}
.prreviewWriteWrap .contentWrap .noticeWrap{
	margin: 20px 0 20px 0;
}
.prreviewWriteWrap .contentWrap .noticeWrap .noticeGrop .noticeIcon {
	width: 20px;
	margin:0 12px 0 20px;
}
.prreviewWriteWrap .contentWrap .noticeWrap .noticeGrop .noticeRow{
	margin-left: 10px;
	color: #ee4481;
}
.prreviewWriteWrap .contentWrap .noticeWrap .noticeGrop .noticeRow .noticeContent:not(:last-of-type){
	font-weight: bold;
}
.prreviewWriteWrap .reviewSubmitWrap h2{
	color:#ffffff;
	text-align: center;
	font-size: 1.4em;
	padding: 20px;
	font-weight: 500;
}
.prreviewWriteWrap .reviewSubmitWrap{
	background: #ee4481;
	border-radius: 0 0px 20px 20px;
}
.prreviewWriteWrap .reviewSubmitDisableWrap{
	display:none;
	background: gray;
	border-radius: 0 0px 20px 20px;
}


</style>
<body style="background:none;">
	<div class="prreviewWriteWrap">
		<div class="headerWrap">
			<h2>별점 및 리뷰</h2>
		</div>
			<!-- 종합 START -->

		<form name="reviewForm" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="code" value="<?=substr($productcode,0,12)?>">
			<input type="hidden" name="productcode" value="<?=$productcode?>" />
			<input type="hidden" name="page" value="<?=$currentPage?>">
			<input type="hidden" name="aoidx" value="<?=$aoidx?>" />
			<input type="hidden" name="mode" value="write" />
			<input type="hidden" name="sort" value="<?=$sort?>" />
			<input type="hidden" name="rname" value="<?=$_ShopInfo->getMemName();?>" />
			<input type="hidden" name="marks" value="5" />
			<input type="hidden" name="badge01" value="N" />
			<input type="hidden" name="badge02" value="N" />
			<input type="hidden" name="badge03" value="N" />
			<input type="hidden" name="badge04" value="N" />
			<input type="hidden" name="badge05" value="N" />
			<input type="hidden" name="badge06" value="N" />
			<input type="hidden" name="badge07" value="N" />
			<input type="hidden" name="badge08" value="N" />
			<input type="hidden" name="badge09" value="N" />
			<input type="hidden" name="badge10" value="N" />
			<div class="contentWrap">
				<div class="infoGroup">
					<div class="infoRow">
						<div class="bellIcon left"><img src="/app/skin/basic/svg/question_main01.svg"></div>
						<div class="dateText"><?=$brand_name?></div>
					</div>
					<div class="addrRow">
						<div class="addr left"><?=$com_addr?></div>
						<div class="starIcon left"><img src="/app/skin/basic/svg/review_star_on.svg" alt=""></div>
						<div class="reviewAverage"><?=$avg_marks?> (<?=$marks_count?>)</div>
					</div>
					<div class="priceRow">
						<div class="pick left"><?=$productname?></div>
						<div class="dateText right"><?=$productprice?>원</div>
					</div>
				</div>
			</div>
				<!-- 상품 후기작성 시작 -->
			<div class="contentWrap">
				<div class="prtitleWrap">
						<div class="bookIcon left"><img src="/app/skin/basic/svg/review_subtitle.svg"></div>
						<div class="abc">소중한 의견은 꽃집과 고객에게 큰 도움이 됩니다</div>
				</div>
				<div class="subContent">
					<div class="pickWrap">
						<div class="pickGrop">
							<div class="pickbanner">가성비갑</div>
							<div class="pickbanner">고급져요</div>
							<div class="pickbanner">장인정신</div>
							<div class="pickbanner">빨리와요</div>
							<div class="pickbanner">친절해요</div>
							<div class="pickbanner">센스쟁이</div>
							<div class="pickbanner">신선해요</div>
							<div class="pickbanner">풍성해요</div>
						</div>
					</div>
					<div class="starWrap">
						<img src="/app/skin/basic/svg/review_star_on.svg" class="marksStar" onclick="setMarks(1)" alt="">
						<img src="/app/skin/basic/svg/review_star_on.svg" class="marksStar" onclick="setMarks(2)" alt="">
						<img src="/app/skin/basic/svg/review_star_on.svg" class="marksStar" onclick="setMarks(3)" alt="">
						<img src="/app/skin/basic/svg/review_star_on.svg" class="marksStar" onclick="setMarks(4)" alt="">
						<img src="/app/skin/basic/svg/review_star_on.svg" class="marksStar" onclick="setMarks(5)" alt="">
					</div>
						<div class="writeContentWrap">
							<textarea onkeyup="removeEmojis(this)" style="height: 150px; width: calc(100% - 20px); border-radius: 7px;padding:10px;" name="rcontent"></textarea>	
							<br><span style="color:#e61e6e;"">* 현재 이모지 사용은 구현중입니다. </span>
						</div>
					<div class="addImgWrap">
						<div class="previewImage" id="previewImage">
							<img id="previewImageObj"src="" alt="">
						</div>
						<div class="fileBox">
							<input style="display: none;" type="file" name="attech" accept="image/*" id="attech" value=""/>
							<label for="attech"><img src="/app/skin/basic/svg/review_addImg.svg" alt=""></label>
						</div>
					</div>
				</div>
				<div class="noticeWrap">
					<div class="noticeGrop">
						<div class="noticeIcon left"><img src="/app/skin/basic/svg/review_Notice.svg" alt=""></div>
						<div class="noticeRow">
							<div class="noticeContent">리뷰를 남기시면 적립금을 드려요!</div>
							<div class="noticeContent">사진리뷰 +1,000원 / 일반리뷰 +500원</div>
						</div>
					</div>
				</div>
			</div>
			<div class="reviewSubmitWrap" id="btn_submit">
				<h2>리뷰 등록</h2>
			</div>
			<div class="reviewSubmitDisableWrap" id="btn_disable_submit">
				<h2>처리중</h2>
			</div>
		</form>
	</div>
</body>
<script>
$(document).ready(function() {
	var pickCnt = 0;
	$(".pickbanner").click(function(){
		var badgeName = "badge" + (($(this).index()+1)<10?"0"+($(this).index()+1):($(this).index()+1));
		var badgeVal = "N";
		if($(this).hasClass("selected")){
			$(this).removeClass("selected");
			pickCnt--;
		}
		else{
			if(pickCnt<3){
				$(this).addClass("selected");
				badgeVal = "Y";
				pickCnt++;
			}
			else{
				alert("최대 3개까지 선택하실 수 있습니다.");
				return;
			}
		}
		$("input[name=" + badgeName + "]").val(badgeVal);
	});
	$("#attech").change(readImage);
	var form = document.reviewForm;
	$("#btn_submit").click(function(){
		if($("input[name=rname]").val() == "" || $("input[name=rname]").val() == null){
			alert("이름을 작성하세요.");
			$("input[name=rname]").focus();
			return false;
		}else if($("textarea[name=rcontent]").val() == "" || $("textarea[name=rcontent]").val() == null){
			alert("내용을 작성하세요.");
			$("textarea[name=rcontent]").focus();
			return false;
		}else{

			var filestate = document.getElementById('attech');
			if(filestate.value != "" || filestate.value == "undefined" || filestate.value == null){

				var imageMaxSize = "<?=$_MAX_FILE_SIZE?>";
				var fileSize = filestate.files[0].size;
				if(fileSize > imageMaxSize){
					alert("첨부할수 있는 최대 용량은 <?=$_MSG_UNIT?>입니다.");
					return false;
				}
			}

			if(confirm("후기를 등록하시겠습니까?")){
				$("#btn_submit").hide();
				$("#btn_disable_submit").show();
				form.submit();
				return;
			}else{
				return false;
			}
		}
	});
});	
function setMarks(marks){
	for(var i = 0 ; i < 5 ; i++){
		if( i < marks ) {	
			$(".marksStar").eq(i).attr("src","/app/skin/basic/svg/review_star_on.svg");
		}
		else{
			$(".marksStar").eq(i).attr("src","/app/skin/basic/svg/review_star_off.svg");
		}
	}
	$("input[name=marks]").val(marks);
}

function readImage(e) {
    // 인풋 태그에 파일이 있는 경우
	var input = e.target;
    if(input.files && input.files[0]) {
        $("#previewImage").show();
        // FileReader 인스턴스 생성
        var reader = new FileReader()
        // 이미지가 로드가 된 경우
        reader.onload = e => {
            $("#previewImageObj").attr("src",e.target.result);
        }
        // reader가 이미지 읽도록 하기
        reader.readAsDataURL(input.files[0])
    }
	else{
		$("#previewImage").hide();
	}
}
function removeEmojis (e) {
    const regex = /(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff]|[\u0023-\u0039]\ufe0f?\u20e3|\u3299|\u3297|\u303d|\u3030|\u24c2|\ud83c[\udd70-\udd71]|\ud83c[\udd7e-\udd7f]|\ud83c\udd8e|\ud83c[\udd91-\udd9a]|\ud83c[\udde6-\uddff]|\ud83c[\ude01-\ude02]|\ud83c\ude1a|\ud83c\ude2f|\ud83c[\ude32-\ude3a]|\ud83c[\ude50-\ude51]|\u203c|\u2049|[\u25aa-\u25ab]|\u25b6|\u25c0|[\u25fb-\u25fe]|\u00a9|\u00ae|\u2122|\u2139|\ud83c\udc04|[\u2600-\u26FF]|\u2b05|\u2b06|\u2b07|\u2b1b|\u2b1c|\u2b50|\u2b55|\u231a|\u231b|\u2328|\u23cf|[\u23e9-\u23f3]|[\u23f8-\u23fa]|\ud83c\udccf|\u2934|\u2935|[\u2190-\u21ff])/g;
    if(regex.test($(e).val())) {
        alert("현재 이모지 사용은 구현중입니다.");
        let strVal = $(e).val().replace(regex,'');
        $(e).val(strVal);
    }
}
</script>
</html>
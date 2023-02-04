<?
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."app/inc/function.php");
	$_FileInfo = _uploadMaxFileSize();

	$_MAX_FILE_SIZE = $_FileInfo['maxsize'];
	$_MSG_UNIT = $_FileInfo['unit'];

	$productcode = !_empty($_POST['productcode'])?trim($_POST['productcode']):$_GET['productcode'];

	$mode = !_empty($_POST['mode'])?trim($_POST['mode']):"";  //모드
	$categorycode = !_empty($_POST['code'])?trim($_POST['code']):""; // 카테고리코드

	$sort = !_empty($_POST['sort'])?trim($_POST['sort']):""; // 정렬
	$quality = !_empty($_POST['quality'])?trim($_POST['quality']):"";//품질
	$price = !_empty($_POST['price'])?trim($_POST['price']):"";//가격
	$delitime = !_empty($_POST['delitime'])?trim($_POST['delitime']):"";//배송시간
	$recommend = !_empty($_POST['recommend'])?trim($_POST['recommend']):"";//추천
	$writer = !_empty($_POST['rname'])?trim($_POST['rname']):"";//작성자
	$contents= !_empty($_POST['rcontent'])?trim($_POST['rcontent']):"";//내용

	$avermark = floor(((int) $quality+ (int) $price + (int) $delitime+ (int) $recommend) /4); //평균

	if(strlen($productcode)<=0){
		echo '<script>alert("잘못된 페이지 접근입니다.");history.back(-1);</script>';exit;
	}
	
	$productSQL ="SELECT * FROM tblproduct WHERE productcode = '".$productcode."' ";
	$imagesrc = $Dir."data/shopimages/product/";
	if(false !== $productRes = mysql_query($productSQL,get_db_conn())){
		$productrowcount = mysql_num_rows($productRes);

		if($productrowcount>0){
			$productRow = mysql_fetch_assoc($productRes);
			$productname = $productRow['productname'];
			$productimage =$productRow['minimage'];
			$productprice = number_format($productRow['sellprice']);
			$src = $imagesrc.$productimage;
			$size = _getImageRateSize($src,80);
		}
	}

	if($_data->review_type =="N" || $_data->ETCTYPE["REVIEW"]=="N") {
		echo '<script>alert("사용후기 기능 설정이 되지 않아 사용할 수 없습니다.");location.replace("productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
	}

	if(strlen($_ShopInfo->getMemid())==0 && $_data->review_memtype=="Y"){
		echo "<script>alert('상품평 작성은 회원전용입니다.');location.replace('login.php?chUrl=productdetail_tab01.php?productcode=".$productcode."');</script>";exit;
	}
	
	if($mode == "write"){
		//if(strlen($code) <= 0 || strlen($code)>12){
		//	echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다2.");history.back(-1);</script>';exit;
		//}else if(strlen($productcode) <= 0 || strlen($productcode)>18){

		if(strlen($productcode) <= 0 || strlen($productcode)>18){
			echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다3.");history.back(-1);</script>';exit;
		}else if(strlen($quality) <= 0 || ($quality < 1 || $quality > 5)){
			echo '<script>alert("품질 점수를 선택해 주세요.");history.back(-1);</script>';exit;
		}else if(strlen($price) <= 0 || ($price < 1 || $price > 5)){
			echo '<script>alert("가격 점수를 선택해 주세요.");history.back(-1);</script>';exit;
		}else if(strlen($delitime) <= 0 || ($delitime < 1 || $delitime > 5)){
			echo '<script>alert("배송 점수를 선택해 주세요.");history.back(-1);</script>';exit;
		}else if(strlen($recommend) <= 0 || ($recommend < 1 || $recommend > 5)){
			echo '<script>alert("추천 점수 선택해 주세요.");history.back(-1);</script>';exit;
		}else if(strlen($writer) <= 0){
			echo '<script>alert("작성자를 입력해 주세요.");history.back(-1);</script>';exit;
		}else if(strlen($contents) <= 0){
			echo '<script>alert("내용을 입력해 주세요.");history.back(-1);</script>';exit;
		}

		$allowfile=array('image/pjpeg','image/jpeg','image/JPG','image/X-PNG','image/PNG','image/png','image/x-png','image/gif');
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
				echo '<script>alert("첨부 가능한 파일이 아닙니다.\n첨부가능한 파일은 jpg, gif, png입니다.");location.replace("productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
			}else{
				if($attechfilesize >$maxfilesize){
					echo '<script>alert("첨부 가능한 파일 용량이 초과 되었습니다.\n최대 첨부가능한 파일용량은 '.$maxfilesize_unit.'입니다.");location.replace("productdetail_tab01.php?productcode='.$productcode.'");</script>';exit;
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
		$reviewwriteSQL.= ", content	= '".$contents."'";
		$reviewwriteSQL.= ", device		= 'P'";
		$reviewwriteSQL.= ", quality		= '".$quality."'";
		$reviewwriteSQL.= ", price		= '".$price."'";
		$reviewwriteSQL.= ", delitime	= '".$delitime."'";
		$reviewwriteSQL.= ", recommend = '".$recommend."' ";
		if(strlen($queryattechname)>0){
			$reviewwriteSQL.= ", img = '".$queryattechname."' ";
		}

		$returnmsg="";

		if(false !== mysql_query($reviewwriteSQL,get_db_conn())){
			if($_data->review_type=="A") {
				$returnmsg="<script>alert('관리자 인증후 등록됩니다.');</script>";
			}else{
				$returnmsg="<script>alert('상품평이 등록되었습니다.');location.href='productdetail_tab01.php?productcode=".$productcode."';</script>";
			}
		}else{
			$returnmsg="<script>alert('상품평이 등록되지 않았습니다.');</script>";
		}
	}

	include "header.php";
?>
<div id="content">
	<div class="h_area2">
		<h2>상품후기 작성</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div class="review_wrap">

		<? if($productname){ ?>
		<div class="productnamepriceWrap">
			<table cellpadding="0" cellspacing="0" border="0" class="prwrap" width="100%">
				<tr>
					<td>
						<img src="<?=$src?>" <?=$size?>/>
					</td>
					<td style="padding-left:5px;">
						<p><?=$productname?></p>
						<p><?=$productprice?>원</p>
					</td>
				</tr>
			</table>
		</div>
		<? } ?>

		<form name="reviewForm" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="code" value="<?=substr($productcode,0,12)?>">
		<input type="hidden" name="productcode" value="<?=$productcode?>" />
		<input type="hidden" name="page" value="<?=$currentPage?>">
		<input type="hidden" name="mode" value="write" />
		<input type="hidden" name="sort" value="<?=$sort?>" />

		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="reviewForm">
			<tr>
				<td colspan="2"><input type="text" name="rname" maxlength="6" value="" placeholder="작성자" style="width:100%;height:40px;line-height:40px;padding-left:5px;box-sizing:border-box;" /></td>
			</tr>
			<tr>
				<td>
					<span class="basic_select">
						<select name="quality" style="width:100%;height:40px;">
							<option value="" selected>품질</option>
							<option value="5">★★★★★</option>
							<option value="4">★★★★</option>
							<option value="3">★★★</option>
							<option value="2">★★</option>
							<option value="1">★</option>
						</select>
					</span>
				</td>
				<td style="text-align:right;">
					<span class="basic_select">
						<select name="price" style="width:100%;height:40px;">
							<option value="" selected>가격</option>
							<option value="5">★★★★★</option>
							<option value="4">★★★★</option>
							<option value="3">★★★</option>
							<option value="2">★★</option>
							<option value="1">★</option>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<span class="basic_select">
						<select name="delitime" style="width:100%;height:40px;">
							<option value="" selected>배송</option>
							<option value="5">★★★★★</option>
							<option value="4">★★★★</option>
							<option value="3">★★★</option>
							<option value="2">★★</option>
							<option value="1">★</option>
						</select>
					</span>
				</td>
				<td style="text-align:right;">
					<span class="basic_select">
						<select name="recommend" style="width:100%;height:40px;">
							<option value="" selected>추천</option>
							<option value="5">★★★★★</option>
							<option value="4">★★★★</option>
							<option value="3">★★★</option>
							<option value="2">★★</option>
							<option value="1">★</option>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="2"><textarea name="rcontent" placeholder="내용을 입력하세요." style="height:120px;"></textarea></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="filebox addfile">
						<label for="attech">파일첨부</label>
						<input type="file" name="attech" id="attech" class="upload-hidden" />
						<input class="upload-name width2" placeholder="파일을 첨부해 주세요." disabled />
					</div>
					<p class="addfileinfo"><strong><?=$_MSG_UNIT?> 이하의 이미지(jpg, gif, png)</strong> 파일만 업로드 가능합니다.</p>

					<style>
						.filebox input[type="file"] {position: absolute;width: 1px;height: 1px;padding: 0;margin: -1px;overflow: hidden;clip:rect(0,0,0,0);border: none;}
						.filebox label {display: inline-block;padding: 0.2em 0.6em;color: #999;font-size: inherit;line-height: normal;vertical-align: middle;background-color: #fdfdfd;cursor: pointer;border: 1px solid #ebebeb;border-bottom-color: #e2e2e2;border-radius: .25em;}
						.filebox label:hover {color: #555;font-size: inherit;line-height: normal;vertical-align: middle;background-color: #f9f9f9;cursor: pointer;border: 1px solid #555555;border-bottom-color: #555555;border-radius: .25em;}
						/* named upload */
						.filebox .upload-name {display: inline-block;width:auto;padding: 0em;font-size: inherit;font-family: inherit;line-height: normal;vertical-align: middle;background: #ffffff;border: none; -webkit-appearance: none; -moz-appearance: none; appearance: none;}
						.filebox.addfile label {border: 1px solid #e5e5e5;border-radius:15px;}
					</style>

					<!--파일첨부 js-->
					<script>
						$(document).ready(function(){
							var fileTarget=$('.filebox .upload-hidden');

							fileTarget.on('change', function(){
								if(window.FileReader){
									var filename=$(this)[0].files[0].name;
								} else {
									var filename=$(this).val().split('/').pop().split('\\').pop();
								}
								$(this).siblings('.upload-name').val(filename);
							});
						});
					</script>
				</td>
			</tr>
		</table>

		<div class="basic_btn_area">
			<input type="button" class="basic_button" id="btn_reset" value="다시쓰기" />
			<input type="button" class="basic_button grayBtn" id="btn_submit" value="리뷰등록" />
		</div>

		<input type="hidden" name="MAX_FILE_SIZE" value="<?=$_MAX_FILE_SIZE?>" />
		</form>
	</div>
</div>

<script>
	<!--
	$(function(){
		//selectbox design
		$('.basic_select').jqTransform();
	});

	$(".write_btn").click(function(){
		var loginid = "<?=$_ShopInfo->getMemid()?>";
		var writetype = "<?=$_data->review_memtype?>";

		if(writetype =="Y"){
			if(loginid.length > 0 && loginid !=""){
				$(".review_container").css("display", "block");
				$(".write_btn").css("display","none");
				$(".write_close").css("display", "block");
			}else{
				if(confirm("상품평 작성은 회원 전용입니다.\로그인 하시겠습니까?")){
					window.location='/m/login.php?chUrl='+"<?=getUrl()?>";
				}
			}
		}else{
			$(".review_container").css("display", "block");
			$(".write_btn").css("display","none");
			$(".write_close").css("display", "block");
		}
		return;
	});

	$(".write_close").click(function(){
		$(".review_container").css("display", "none");
		$(".write_close").css("display", "none");
		$(".write_btn").css("display","block");
	});

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

			if(confirm("상품평을 등록하시겠습니까?")){
				$("#btn_submit").css("display", "none");
				form.submit();
				return;
			}else{
				return false;
			}
		}
	});

	$("#btn_reset").click(function(){
		form.reset();
		return;
	});

</script>

<?=$returnmsg?>

<? include ("footer.php") ?>
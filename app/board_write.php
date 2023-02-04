<?php
//게시판 이름 출력, 블로그형 게시물 페이지번호 추가 2016-03-17 Seul
$prd=isset($_REQUEST['prd'])? $_REQUEST['prd']:"";

if($prd == 'yes'){ //상세페이지를 통해서 글쓰기로 왔을 때 공통상단 미출력
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."app/inc/function.php");
}else{
	include_once("header.php");
}
include_once($Dir."board/head.php");

$board_name = isset($_REQUEST[board])? $_REQUEST[board]:"";
$board_num = isset($_REQUEST[num])? $_REQUEST[num]:"";
$board_pass = isset($_REQUEST[pass])? $_REQUEST[pass]:"";

$board_pridx = isset($_REQUEST[pridx])? $_REQUEST[pridx]:"";
$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";


if(empty($board_name)){
	echo '<script>alert("접근이 금지 되었습니다.");</script>';
	exit;
}

$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
$get_qna_row = mysql_fetch_array($get_qna_result);

$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
$set_qna_list_write = $get_qna_row[grant_write]; // 게시판 쓰기 권한
$set_qna_category = $get_qna_row[subCategory]; // 카테고리 리스트
$set_qna_lock = $get_qna_row[use_lock]; // 잠금상태 설정 상태
$set_qna_max_num = $get_qna_row[max_num]; // 최상위 글 번호
$set_board_name = $get_qna_row[board_name]; //게시판 이름

mysql_free_result($get_qna_result);


//글쓰기 권한
//if($set_qna_list_write == "Y" || $set_qna_list_write == "A"){//회원 전용
if($set_qna_list_write == "Y"){ //회원 전용
	if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
		//echo '<script>alert("회원만 글쓰기가 가능합니다.\n로그인후 이용하세요.");history.go(-1);</script>';exit;
		echo '<script>alert("회원만 글쓰기가 가능합니다.\n로그인후 이용하세요.");location.href=\'./login.php?chUrl='.getUrl().'\';</script>';exit;
	}
}else if($set_qna_list_write == "A"){ //관리자 전용
	echo '<script>alert("관리자만 글쓰기가 가능합니다.");history.go(-1);</script>';exit;
}


if($_ShopInfo->getMemid() != "" ){
	$member_sql = "SELECT * FROM tblmember WHERE id = '".$_ShopInfo->getMemid()."' ";
	$member_result = mysql_query($member_sql, get_db_conn());
	$member_row = mysql_fetch_object($member_result);
}

if($member_row){
	$name = $member_row->name;
	$email = $member_row->email;
	$id = $member_row->id;
	$name = $member_row->name;
	$lock = "readonly";
}


if($mode == "upload"){

	$thread = $setup[thread_no] - 1;
	if ($thread<=0) {
		$que2 = "SELECT MIN(thread) FROM tblboard ";
		$result = mysql_query($que2,get_db_conn());
		$row = mysql_fetch_array($result);
		if ($row[0]<=0) {
			$thread = 999999999;
		} else {
			$thread = $row[0] - 1;
		}
		mysql_free_result($result);
	}


	$thread_no = $thread;
	$secret = isset($_POST[secret])? $_POST[secret]:"";
	$name = isset($_POST[name])? $_POST[name]:"";
	$passwd = isset($_POST[passwd])? $_POST[passwd]:"";
	$email = isset($_POST[email])? $_POST[email]:"";
	$cate = isset($_POST[cate])? $_POST[cate]:"";
	$title = isset($_POST[title])? $_POST[title]:"";
	$content = isset($_POST[content])? $_POST[content]:"";
	$pridx = isset($_POST[pridx])? $_POST[pridx]:"";
	$pageIndex = isset($_POST[pageindex])? $_POST[pageindex]:""; //블로그형 게시물 페이지 번호 2016-03-16 Seul
	//$username = isset($_POST[userid])? $_POST[userid]:"";
	
	if($set_qna_lock == "A"){
		$secret=1;
	}

	$name = addslashes($name);
	$title = str_replace("<!","&lt;!",$title);
	$title = addslashes($title);
	$content = str_replace("<!","&lt;!",$content);
	$content = addslashes($content);
	$userid = $_ShopInfo->getMemid();
	//$up_usercel = $up_cel1."-".$up_cel2."-".$up_cel3;

	if($setup[use_html]=="N") $up_html="";
	if (!$up_html) {
		$send_memo = nl2br(stripslashes($up_memo));
	}

	$next_no = $setup[max_num];

	$up_sql  = "INSERT tblboard SET ";
	$up_sql .= "board				= '".$board_name."', ";
	$up_sql .= "subCategory		= '".$cate."', ";
	//$up_sql .= "num				= '', ";
	$up_sql .= "thread				= '".$thread_no."', ";
	$up_sql .= "pos				= '0', ";
	$up_sql .= "depth				= '0', ";
	$up_sql .= "prev_no			= '0', ";
	if(strlen($pridx)>0) {
		$up_sql.= "pridx			= '".$pridx."', ";
	}
	$up_sql .= "next_no			= '".$set_qna_max_num."', ";
	$up_sql .= "name				= '".$name."', ";
	$up_sql .= "passwd				= '".$passwd."', ";
	$up_sql .= "email				= '".$email."', ";
	$up_sql .= "userid				= '".$userid."', ";
	//$up_sql .= "usercel			= '".$up_usercel."', ";
	$up_sql .= "is_secret			= '".$secret."', ";
	$up_sql .= "use_html			= '".$up_html."', ";
	$up_sql .= "title				= '".$title."', ";
	//$up_sql .= "filename			= '".$up_filename."', ";
	$up_sql .= "filename			= '', ";
	$up_sql .= "writetime			= '".time()."', ";
	$up_sql .= "ip					= '".getenv("REMOTE_ADDR")."', ";
	$up_sql .= "access				= '0', ";
	$up_sql .= "total_comment		= '0', ";
	$up_sql .= "content			= '".$content."', ";
	$up_sql .= "notice				= '0', ";
	$up_sql .= "deleted			= '0' ";
	$insert = mysql_query($up_sql,get_db_conn());

	if($insert) {

		$qry = "SELECT LAST_INSERT_ID() ";
		$res = mysql_fetch_row(mysql_query($qry,get_db_conn()));
		$thisNum = $res[0];

		if ($next_no) {
			$qry9 = "SELECT thread FROM tblboard WHERE board='$board' AND num='$next_no' ";
			$res9 = mysql_query($qry9,get_db_conn());
			$next_thread = mysql_fetch_row($res9);
			@mysql_free_result($res9);

			mysql_query("UPDATE tblboard SET prev_no='".$thisNum."' WHERE board='".$board."' AND thread = '".$next_thread[0]."'",get_db_conn());
			mysql_query("UPDATE tblboard SET prev_no='$thisNum' WHERE board='$board' AND num = '$next_no'",get_db_conn());
			mysql_query("UPDATE tblboardadmin SET thread_no = '".$thread_no."'",get_db_conn());
		}

		// ===== 관리테이블의 게시글수 update =====
		$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1, max_num='$thisNum' ";
		$sql3.= "WHERE board='$board' ";
		$update = mysql_query($sql3,get_db_conn());

		/*
		if (($setup[use_admin_mail]=="Y") && $setup[admin_mail]) {
			INCLUDE "SendForm.inc.php";

			$title = $send_subject;
			$message = GetHeader() . GetContent($send_name, $send_email, $send_subject, $send_memo,$send_date,$send_filename,$setup[board_name]) . GetFooter();

			$tmp_admin_mail_list = split(",",$setup[admin_mail]);

			sendMailForm($send_name,$send_email,$message,$bodytext,$mailheaders);

			for($jj=0;$jj<count($tmp_admin_mail_list);$jj++) {
				if (ismail($tmp_admin_mail_list[$jj])) {
					mail($tmp_admin_mail_list[$jj], $title, $bodytext, $mailheaders);
				}
			}
		}
		*/

		//게시판 글등록 SMS발송
		$sqlsms = "SELECT * FROM tblsmsinfo ";
		$resultsms= mysql_query($sqlsms,get_db_conn());
		if($rowsms=mysql_fetch_object($resultsms)){
			function getStringCut($strValue,$lenValue){
				preg_match('/^([\x00-\x7e]|.{2})*/', substr($strValue,0,$lenValue), $retrunValue);
				return $retrunValue[0];
			}

			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;

			if ($rowsms->admin_board =="Y"){
				$totellist=$rowsms->admin_tel;
				if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
				if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
				if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
				$totellist=str_replace(" ","",$totellist);
				$totellist=str_replace("-","",$totellist);

				$fromtel=$rowsms->return_tel;
				$smsboardname=str_replace("\\n"," ",str_replace("\\r","",strip_tags($setup[board_name])));
				$smsboardsubject=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($title)))));
				$new_post_msg = $rowsms->new_post_msg;
				$pattern = array("(\[BOARD\])","(\[TITLE\])");
				$replace = array($setup[board_name], getStringCut($smsboardsubject,20));
				$new_post_msg=preg_replace($pattern, $replace, $new_post_msg);
				$new_post_msg=addslashes($new_post_msg);
				$etcmsg="게시판 글등록 메세지(관리자)";
				if($rowsms->sleep_time1!=$rowsms->sleep_time2){
					$date="0";
					$time = date("Hi");
					if($rowsms->sleep_time2<"12" && $time<=substr("0".$rowsms->sleep_time2,-2)."59") $time+=2400;
					if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

					if($time<substr("0".$rowsms->sleep_time1,-2)."00" || $time>=substr("0".$rowsms->sleep_time2,-2)."59"){
						if($time<substr("0".$rowsms->sleep_time1,-2)."00") $day = date("d");
						else $day=date("d")+1;
						$date = date("Y-m-d H:i:s",mktime($rowsms->sleep_time1,0,0,date("m"),$day,date("Y")));
					}
				}

				if(strlen($new_post_msg)>80){
					for($i=0; $i<ceil(strlen($new_post_msg)/80); $i++){
						$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, substr($new_post_msg, $i*81, ($i*81)+80), $etcmsg);
					}
				}else{
					$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $new_post_msg, $etcmsg);
				}
				mysql_free_result($resultsms);
			}
		}

		/*
		//게시판 글등록 SMS발송
		$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_board='Y' ";
		$resultsms= mysql_query($sqlsms,get_db_conn());
		if($rowsms=mysql_fetch_object($resultsms)){
			function getStringCut($strValue,$lenValue){
				preg_match('/^([\x00-\x7e]|.{2})* /', substr($strValue,0,$lenValue), $retrunValue);
				return $retrunValue[0];
			}

			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;

			if (($setup[use_admin_sms]=="Y") && $setup[admin_sms]) {
				$totellist = $setup[admin_sms];

				$fromtel=$rowsms->return_tel;
				$smsboardname=str_replace("\\n"," ",str_replace("\\r","",strip_tags($setup[board_name])));
				$smsboardsubject=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($up_subject)))));

				$new_post_msg = $rowsms->new_post_msg;
				$pattern = array("(\[BOARD\])","(\[TITLE\])");
				$replace = array($setup[board_name], getStringCut($smsboardsubject,20));
				$new_post_msg=preg_replace($pattern, $replace, $new_post_msg);
				$new_post_msg=addslashes($new_post_msg);

				$etcmsg="게시판 글등록 메세지(관리자)";
				if($rowsms->sleep_time1!=$rowsms->sleep_time2){
					$date="0";
					$time = date("Hi");
					if($rowsms->sleep_time2<"12" && $time<=substr("0".$rowsms->sleep_time2,-2)."59") $time+=2400;
					if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

					if($time<substr("0".$rowsms->sleep_time1,-2)."00" || $time>=substr("0".$rowsms->sleep_time2,-2)."59"){
						if($time<substr("0".$rowsms->sleep_time1,-2)."00") $day = date("d");
						else $day=date("d")+1;
						$date = date("Y-m-d H:i:s",mktime($rowsms->sleep_time1,0,0,date("m"),$day,date("Y")));
					}
				}
				if(strlen($new_post_msg)>80)
					for($i=0; $i<ceil(strlen($new_post_msg)/80); $i++)
						$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, substr($new_post_msg, $i*81, ($i*81)+80), $etcmsg);
				else
					$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $new_post_msg, $etcmsg);
				mysql_free_result($resultsms);
			}
		}
		*/

		echo '<script>alert("정상적으로 등록되었습니다.");location.href="board_list.php?board='.$board_name.'";</script>';
		//echo("<meta http-equiv='Refresh' content='0; URL=\"board_list.php?board=".$board_name."\">");
		exit;
	} else {
		echo "<script>window.alert('글쓰기 입력중 오류가 발생하였습니다.');</script>";
		//reWriteForm();
		exit;
	}
}

if(!empty($set_qna_category)){
	$catetory = explode(",",$set_qna_category);
}

if(!empty($board_pridx)){
	$filepath = "../data/shopimages/product/";
	$pridx_sql = "SELECT * FROM tblproduct WHERE pridx = ".$board_pridx;
	$pridx_result = mysql_query($pridx_sql, get_db_conn());
	$pridx_row = mysql_fetch_object($pridx_result);

	$img_state = $filepath.$pridx_row->tinyimage;
	if(file_exists($img_state)){
		$img = $img_state;
		$size = _getImageSize($img); // 이미지 사이즈
		$img_class = "";
		if($size[width] >= $size[height]){ //이미지 크기에 따른 stylesheet 클래스 부여
			$class_name = "img_width";
		}else{
			$class_name = "img_height";
		}
	}else{
		$img ="../images/no_img.gif";
	}

	if(strlen($pridx_row->productname) > 28){
		$write_productname = substr($pridx_row->productname, 0, 28)."...";
	}else{
		$write_productname = $pridx_row->productname;
	}
	if($pridx_row->productcode){
		$return_url="./productdetail_tab04.php?productcode=".$pridx_row->productcode;
	}
}
?>
<!--ui개선 20180723-->
<? if($prd == 'yes'){ //상세페이지를 통해서 글쓰기로 왔을 때 ?>
<!doctype html>
<html>
<head>
	<meta charset="<?=$charset?>">
	<title><?=$shopname?> 쇼핑몰 - 모바일</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="format-detection" content="telephone=no" />
	<link rel="stylesheet" href="/m/skin/basic/css/common.css" />
	<link href="/m/skin/basic/css/default.css" rel="stylesheet" type="text/css"/>
	<link href="/m/skin/basic/css/swiper.min.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="/m/skin/basic/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="/m/skin/basic/js/swiper.min.js"></script>
	<script type="text/javascript" src="/m/skin/basic/js/jquery.transform.js"></script>

	<style>
		#pop_photoreview{margin:0;padding:20px;box-sizing:border-box;background:#fff;overflow:hidden;}
		#pop_photoreview .photo_review_content{margin:15px 0px;overflow:hidden;}
	</style>
</head>

<body>
<? } ?>
	<div id="content">
		<div class="h_area2">
			<h2><?=$set_board_name?></h2>
			<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
			<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
		</div>

		<form name="qnaForm" id="qnaForm" action="<?=$PHP_SELF?>" method="post">
		<input type="hidden" name="board" value="<?=$board_name?>" />
		<input type="hidden" name="userid" value="<?=$id?>" />
		<input type="hidden" name="mode" value="upload" />

		<div class="board_writeWrap">
		<table border="0" cellpadding="0" cellspacing="0" class="writeForm">
			<caption>상품정보</caption>
			<col width="25%"></col>
			<col width=""></col>

			<? if($pridx_row){ ?>
			<tr>
				<td colspan="2">
					<a href="<?=$return_url?>">
					<div class="img_container">
						<div class="img_box"><img class= "<?=$class_name?>" src="<?=$img?>"></div>
						<div class="img_contents">
							<b><?=$write_productname?></b><br />
							판매가 : <span class="sellprice"><?=number_format($pridx_row->sellprice);?>원</span><br />
							시중가 : <strike><?=number_format($pridx_row->consumerprice);?>원</strike>
						</div>
						<input type="hidden" name="pridx" value="<?=$pridx_row->pridx?>">
					</div>
					</a>
				</td>
			</tr>
			<? } ?>

			<tr>
				<!--<th>잠금기능</th>-->
				<TD colspan="3">
					<?if($set_qna_lock == "Y"){?>
						<span class="basic_select">
							<select name="secret" id="lock">
								<option value="0" <?=$no_lock?>>잠금기능 사용안함</option>
								<option value="1" <?=$lock?>>잠금기능 사용</option>
							</select>
						</span>
					<?} else if($set_qna_lock == "A"){?>
						<font color="#FF0000">*자동으로 비밀글로 전환됩니다</font>
						<input type="hidden" name="secret" value="1" />
					<?} else{?>
						<font color="#FF0000">*자동으로 공개글로 전환됩니다</font>
						<select name="secret" class="basic_select" id="lock" style="display:none">
							<option value="0" <?=$no_lock?>>사용안함</option>
						</select>
					<?}?>
				</td>
			</tr>
			<tr>
				<!--<th>작성자</th>-->
				<TD colspan="3"><input type="text" name="name" placeholder="작성자명" value="<?=$name?>" class="basic_input m_input" <?=$lock?> style="width:100%; height:40px; line-height:40px;" /></td>
			</tr>
			<? if (strlen($_ShopInfo->getMemid()) == 0) { ?>
			<tr>
				<!--<th>비밀번호</th>-->
				<TD colspan="3"><input type="password" name="passwd" placeholder="비밀번호" class="basic_input m_input" style="width:100%; height:40px; line-height:40px;" /></td>
			</tr>
			<? } ?>
			<tr>
				<!--<th>이메일</th>-->
				<TD colspan="3"><input type="text" name="email" placeholder="이메일" value="<?=$email?>" class="basic_input m_input" <?=$lock?> style="width:100%; height:40px; line-height:40px;" /></td>
			</tr>

            <?
            //말머리
            $subCateSQL="SELECT `subCategory`,`sub_title` FROM `tblboardadmin` WHERE `board` = '".$board."' ;";
            $subCateRes=mysql_query($subCateSQL,get_db_conn());
            $subCateRow=mysql_fetch_assoc ($subCateRes);
            $subCategoryArray=explode(",",$subCateRow[subCategory]);

            //말머리 제목기능 2016-08-30 Seul
            $subCategoryTitle=$subCateRow[sub_title];
            $subTitleUse="";
            if($subCategoryTitle=="Y") {
                $subTitleUse=" onchange='changeTitle()' ";
            }

            //if($_GET['getmall']=='Y'){ echo $subCategoryTitle; }
            ?>
			<tr>
				<!--<th>말머리</th>-->
				<TD colspan="3">
					<span class="basic_select">
						<select name="cate" class="cate" <?=$subTitleUse?>>
							<option value="">말머리 선택</option>
							<? foreach($catetory as $key){ ?>
							<option value="<?=$key?>" <? if($write_category == $key){echo 'selected';}?>><?=$key?></option>
							<? } ?>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<!--<th>글제목</th>-->
				<TD colspan="3"><input type="text" name="title" placeholder="제목을 입력하세요" class="basic_input m_input" style="width:100%; height:40px; line-height:40px;" <?=($subCategoryTitle=="Y"?"readonly":"")?> /></td>
			</tr>
			<tr>
				<!--<th>글내용</th>-->
				<TD colspan="3">

					<? if($setup['use_html'] != "N"){ //html 사용일 때 ?>
					<script src="/ckeditor/ckeditor.js"></script>

					<!--<script type="text/javascript" src="<?=$Dir?>navereditor/js/HuskyEZCreator.js" charset="utf-8"></script>-->
					<? } ?>
					<div id="editor">
						<!-- 기존 에디터를 지원하지 않는 텍스트 필드 --
						<textarea name="content" class="m_input"><?=$content_row->content?></textarea>
						-->
						<textarea name="content" id="ir1" style="width:100%; height:200px;padding:10px;box-sizing:border-box;border:1 solid <?=$list_divider?>; cursor: text;" wrap="<?=$setup[wrap]?>"><?=$thisBoard[content]?><?=( $setup[linkboard]?"******\"":"")?>내용을 입력해주세요.</textarea>
					</div>
					<? if($setup['use_html'] != "N"){ //html 사용일 때 ?>
						<script>CKEDITOR.replace('ir1',{height:250});</script>
					<? } ?>

				</td>
			</tr>
		</table>


		<? if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ ?>
		<style>
			.write_protect_agree{padding:15px 4px;box-sizing:border-box;}
			.tbl_protect_agree{background:#eee;font-size:12px;}
			.tbl_protect_agree caption{padding-bottom:10px;font-weight:bold;letter-spacing:-1px;text-align:left;}
			.tbl_protect_agree th{padding:5px 10px;background:#f8f8f8;border:none;}
			.tbl_protect_agree td{padding:5px 10px;background:#fff;border:none;}
			.tbl_protect_agree label{cursor:pointer;}
			.tbl_protect_agree input{vertical-align:middle;}
		</style>

		<div class="write_protect_agree">
			<table cellpadding="0" cellspacing="1" border="0" width="100%" class="tbl_protect_agree">
				<caption>[필수] 개인정보 수집 및 이용동의</caption>
				<colgroup>
					<col width="100" />
					<col width="150" />
					<col width="150" />
					<col width="200" />
					<col width="" />
				</colgroup>
				<tr>
					<th>구분</th>
					<th>필수정보</th>
					<th>선택정보</th>
				</tr>
				<tr>
					<th>수집시점</th>
					<td colspan="2">게시물 작성</td>
				</tr>
				<tr>
					<th>이용목적</th>
					<td colspan="2">이용자 식별 및 서비스 이용상담</td>
				</tr>
				<tr>
					<th>이용항목</th>
					<td>성명, 회원아이디, 비밀번호</td>
					<td>연락처(이메일, 휴대폰 번호)</td>
				</tr>
				<tr>
					<th>보유/파기</th>
					<td>회원탈퇴(비회원은 게시물 삭제) 시 즉시 삭제. 단, 아이디로 사용되는 이메일은 부정사용 및 중복가입 방지를 위해 회원탈퇴DB에서 6개월 후 삭제</td>
					<td>회원탈퇴(비회원은 게시물 삭제) 시 즉시 삭제</td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tbl_protect_agree">
				<tr>
					<td>※ 서비스 제공을 위한 최소한의 개인정보이므로 동의해주셔야만 서비스를 이용하실 수 있습니다.</td>
				</tr>
				<tr>
					<td><label><input type="checkbox" name="chk_protect_agree" value="Y" class="checkbox" /> 개인정보 수집 및 이용에 동의합니다.</label></td>
				</tr>
			</table>
		</div>
		<? } ?>


		<div class="basic_btn_area" style="border-top:1px solid #ebebeb;padding-top:15px;">
			<a class="basic_button" onclick="history.back(-1)">돌아가기</a>
			<a class="basic_button grayBtn" id="btn_submit">등록하기</a>
		</div>

		</div>
		</form>
	</div>

	<script language="javascript">
		var form = document.qnaForm;

        //말머리(카테고리) 타이틀 자동적용
        function changeTitle() { //jbum 191106
            form.title.value = form.cate.value;
        }

		$(".basic_btn_area #btn_submit").on('click', function(){ // 폼체크 및 서브밋
			var issecurity = "<?=$set_qna_lock?>";
			if(issecurity == "Y" && ($("select[name=secret]").val() == "" || $("select[name=secret]").val() == null)){
				alert("잠금기능을 설정하세요.");
				$("select[name=secret]").focus();
				return;
			}else if($("input[name=name]").val() == "" || $("input[name=name]").val() == null){
				alert("작성자를 입력하세요.");
				$("input[name=name]").focus();
				return;
			}
			<? if (strlen($_ShopInfo->getMemid()) == 0) { ?>
			else if($("input[name=passwd]").val() == "" || $("input[name=passwd]").val() == null){
				alert("비밀번호를 입력하세요.");
				$("input[name=passwd]").focus();
				return;
			}
			<? } ?>
			/*else if($("input[name=email]").val() == "" || $("input[name=email]").val() == null){
				alert("이메일을 입력하세요.");
				$("input[name=email]").focus();
				return;
			}*/
			else if($("input[name=title]").val() == "" || $("input[name=title]").val() == null){
				alert("제목을 입력하세요");
				$("input[name=title]").focus();
				return;
			}else if($("textarea[name=content]").val() == "" || $("textarea[name=content]").val() == null){
				alert("내용을 입력하세요");
				$("textarea[name=content]").focus();
				return;
			}else{
				<? if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ ?>
					if(!form.chk_protect_agree.checked){
						alert('개인정보 수집 및 이용동의를 체크해 주세요.\n\n서비스 제공을 위한 최소한의 개인정보이므로 동의해주셔야만 이용하실 수 있습니다.');
						form.chk_protect_agree.focus();
						return false;
					}
				<? } ?>

				$("#btn_submit").css("display","none");
				form.submit();
			}
		});

		$("#btn_reset").click(function(){ //초기화
			$(".m_input").each(function(){
				this.value= "";
			});

			//$("#ejEdt_content").contents().find("body").text("");
			$(".cate").find('option:first').attr('selected', 'selected');
			//$("#content").value="";

		});
	</script>

<?
	if($prd == 'yes'){ //상세페이지를 통해서 글쓰기로 왔을 때 공통상단 미출력
		echo "
			<script type=\"text/javascript\">
				<!--
				$(function(){
					// selectbox design
					$('.basic_select').jqTransform();
				});
				//-->
			</script>
		";
		echo "</body></html>";
	}else{
		include_once('footer.php');
	}
?>
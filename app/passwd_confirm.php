<?php
include_once("header.php");
include ($Dir."board/lib.inc.php");

$board_name = isset($_REQUEST[board])? trim($_REQUEST[board]):"";
$board_num = isset($_REQUEST[num])? trim($_REQUEST[num]):"";
$board_type = isset($_REQUEST[type])? trim($_REQUEST[type]):"";

if(empty($board_name) || empty($board_num)){
	echo '<script>alert("잘못된 경로로 접근하였습니다.");history.go(-1);</script>';
	exit;
}

$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
$get_qna_row = mysql_fetch_array($get_qna_result);

$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
$set_qna_list_write = $get_qna_row[grant_write]; // 게시판 쓰기 권한

if($set_qna_list_view == "U" || $set_qna_list_view == "Y"){
	if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ 
		echo '<script>alert("쇼핑몰 회원만 이용 가능합니다.\n로그인 하시기 바랍니다.");history.go(-1);</script>';
		exit;
	}
}

if($board_type == "view"){
	$location = "./board_view.php";
} else if ($board_type == "modify"){
	$location = "./board_modify.php?num=".$board_num."";
} else if ($board_type == "delete"){
	$location = "./board_delete.php?num=".$board_num."";
}

// 로그인 사용자와 글쓴 사용자가 동일하거나 관리자일 경우 비밀번호 묻는 부분 패스
$content_sql = "SELECT * FROM tblboard WHERE board = '".$board_name."' AND num = ".$board_num;
$content_result = mysql_query($content_sql, get_db_conn());
$content_row = mysql_fetch_object($content_result);

if($_ShopInfo->getMemid() != '' && ($content_row->userid == $_ShopInfo->getMemid()) || $member[admin]=="SU") {
	if ($board_type == "view"){
		echo "<script>window.location.href = '".$location."?num=".$board_num."&board=".$board_name."';</script>";
	} else if ($board_type == "delete"){
		echo "<script>if(confirm('삭제하시겠습니까?')) { window.location.href = '".$location."&board=".$board_name."'; }else{history.go(-1);}</script>";
	} else if ($board_type == "modify"){
		echo "<script>window.location.href = '".$location."&board=".$board_name."';</script>";
	}
}
?>
<!--ui개선 20180723-->

<div id="content">
	<div class="h_area2">
		<h2>비밀번호 확인</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div id="passwd_container">
		<div class="passwd_box">
			<div class="passwd_top">
				잠금 기능을 사용하여 등록한 게시물입니다.<br />
				관리자 비밀번호나 작성자 비밀번호를 입력하세요.
			</div>
			<div class="passwd_bottom">
				<form name="passwd_form" method="post" action="<?=$location?>">
					<!--<label>비밀번호 &nbsp;:&nbsp;</label>-->
					<input type="password" name="pass" value="" class="basic_input" />
					<input type="button" value="확인" class="basic_button grayBtn" onClick="passForm();" />

					<input type="hidden" name="num" value="<?=$_REQUEST[num]?>" />
					<input type="hidden" name="board" value="<?=$_REQUEST[board]?>" />
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	function passForm(){
		var form = document.passwd_form;
		
		if(form.pass.value.length <= 0 || form.pass.value == null){
			alert("비밀번호를 입력하세요.");
			form.pass.focus();
			return;
		}else{
			form.submit();
		}
	}
</script>

<? include_once('footer.php'); ?>
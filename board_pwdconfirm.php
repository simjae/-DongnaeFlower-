<?php
//블로그형 게시물 페이지 번호 추가 2016-03-17 Seul
include_once("header.php");

$board_name = isset($_REQUEST[board])? trim($_REQUEST[board]):"";
$board_num = isset($_REQUEST[num])? trim($_REQUEST[num]):"";
$c_num = isset($_REQUEST[c_num])? trim($_REQUEST[c_num]):"";
$pageIndex = isset($_REQUEST[pageindex])? trim($_REQUEST[pageindex]):""; //블로그형 게시물 페이지 번호 2016-03-16 Seul

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

?>
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
				<form name="passwd_form" method="post" action="comment_delete.php">
					<label>비밀번호 &nbsp;:&nbsp;</label>
					<input type="password" name="up_passwd" value="" class="basic_input" />
					<input type="button" value="확인" onClick="passForm();" class="basic_button grayBtn" />

					<input type="hidden" name="num" value="<?=$_REQUEST[num]?>" />
					<input type="hidden" name="board" value="<?=$_REQUEST[board]?>" />
					<input type="hidden" name="c_num" value="<?=$c_num?>" />
					<!-- 블로그형 게시물 페이지 번호 추가 2016-03-17 Seul -->
					<input type="hidden" name="pageindex" value="<?=$pageIndex?>" />
					<input type="hidden" name="mode" value="delete" />
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function passForm(){
		var form = document.passwd_form;
		
		if(form.up_passwd.value.length <= 0 || form.up_passwd.value == null){
			alert("비밀번호를 입력하세요.");
			form.up_passwd.focus();
			return;
		}else{
			form.submit();
		}
	}
</script>

<? include_once('footer.php'); ?>
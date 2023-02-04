<?php
include_once("header.php");

$board_name = isset($_REQUEST[board])? $_REQUEST[board]:"";
$board_num = isset($_REQUEST[num])? $_REQUEST[num]:"";
$board_pass = isset($_REQUEST[pass])? $_REQUEST[pass]:"";
$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";

$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
$get_qna_row = mysql_fetch_array($get_qna_result);

$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
$set_qna_list_write = $get_qna_row[grant_write]; // 게시판 쓰기 권한
$set_qna_category = $get_qna_row[subCategory]; // 카테고리 리스트  
$set_qna_lock = $get_qna_row[use_lock]; // 잠금상태 설정 상태 

if(empty($board_name) || empty($board_num)){
	echo '<script>alert("잘못된 경로로 접근하였습니다.");history.go(-1);</script>';
	exit;
}

if($set_qna_list_view == "U" || $set_qna_list_view == "Y"){
	if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ 
		echo '<script>alert("쇼핑몰 회원만 이용 가능합니다.\n로그인 하시기 바랍니다.");history.go(-1);</script>';
		exit;
	}
}

$content_sql= "SELECT * FROM tblboard WHERE board = '".$board_name."' AND num = ".$board_num;
$content_result= mysql_query($content_sql, get_db_conn());
$content_row = mysql_fetch_object($content_result);

if($mode != "modify"){
	if($content_row->passwd != $board_pass){
		echo '<script>alert("비밀번호가 맞지 않습니다.");history.go(-1);</script>';
		exit;
	}
}else{
	$secret = isset($_POST[secret])? $_POST[secret]:"0";
	$name = isset($_POST[name])? $_POST[name]:"";
	$passwd = isset($_POST[passwd])? $_POST[passwd]:"";
	$email = isset($_POST[email])? $_POST[email]:"";
	$cate = isset($_POST[cate])? $_POST[cate]:"";
	$title = isset($_POST[title])? $_POST[title]:"";
	$content = isset($_POST[content])? $_POST[content]:"";
	$thread = isset($_POST[thread])? $_POST[thread]:"";

	$modify_sql = "UPDATE tblboard SET ";
	$modify_sql .= "subCategory = '".$cate."', ";
	$modify_sql .= "name = '".$name."', ";
	$modify_sql .= "passwd = '".$passwd."', ";
	$modify_sql .= "email = '".$email."', ";
	$modify_sql .= "is_secret = '".$secret."', ";
	$modify_sql .= "title = '".$title."', ";
	$modify_sql .= "content = '".$content."' ";

	$modify_sql .= "WHERE num = ".$board_num." AND board = '".$board_name."' AND thread = ".$thread;

	if(($secret >= 0) && !empty($name) && !empty($passwd) && !empty($email) && !empty($cate) && !empty($title) && !empty($content) && !empty($thread)){
		
		if(mysql_query($modify_sql, get_db_conn())){
			echo '<script>alert("정상적으로 수정되었습니다.");location.href="./customer_qna_list.php"</script>';
			exit;
		}else{
			echo '<script>alert("정상적으로 수정되지 않았습니다.");"</script>';
		}
	}else{
		echo '<script>alert("필수값이 누락되어 수정되지 않았습니다.");</script>';
	}
}

$modify_name = $content_row->name;
$modify_email = $content_row->email;
$modify_title = $content_row->title;
$modify_content = $content_row->content;
$modify_secret = $content_row->is_secret;
$modify_category = trim($content_row->subCategory);
$modify_thread = $content_row->thread;
$modify_pridx = $content_row->pridx;

switch($modify_secret){
	case 0:
		$no_lock = "selected";
		$lock = "";
	break;
	case 1:
		$no_lock = "";
		$lock = "selected";
	break;
	default:
		$no_lock = "";
		$lock = "selected";
	break;
}

if(!empty($modify_pridx)){
	$filepath = "../data/shopimages/product/";
	$pridx_sql = "SELECT * FROM tblproduct WHERE pridx = ".$modify_pridx;
	$pridx_result = mysql_query($pridx_sql, get_db_conn());
	$pridx_row = mysql_fetch_object($pridx_result);
	
	
	$img_state = $filepath.$pridx_row->tinyimage;
	if(file_exists($img_state)){
		$img = $img_state;
		$size = _getImageSize($img); // 이미지 사이즈 
		if($size[width] >= $size[height]){ //이미지 크기에 따른 stylesheet 클래스 부여
			$class_name = "img_width";
		}else{
			$class_name = "img_height";
		}

	}else{
		$img ="../images/no_img.gif";
	}

	if(strlen($pridx_row->productname) > 28){
		$modify_productname = substr($pridx_row->productname, 0, 28)."...";
	}else{
		$modify_productname = $pridx_row->productname;
	}
	if($pridx_row->productcode){
		$return_url="./productdetail_tab04.php?productcode=".$pridx_row->productcode;
	}
}

if(!empty($set_qna_category)){
	$catetory = explode(",",$set_qna_category);
}
?>

<style type="text/css">
  /*@import url("./gmeditor/common.css");*/
</style>

<div id="content">
	<div class="h_area2">
		<h2>상품 Q&A</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<form name="qnaForm" id="qnaForm" action="<?=$PHP_SELF?>" method="post">

		<table border="0" cellpadding="0" cellspacing="0" class="writeForm">
			<caption>상품정보</caption>
			<col width="70"></col>
			<col width=""></col>
			
			<? if($pridx_row){ ?>
			<tr>
				<td colspan="2">
					<a href="<?=$return_url?>">
					<div class="img_container">
						<div class="img_box"><img class= "<?=$class_name?>" src="<?=$img?>"></div>
						<div class="img_contents">
							<b><?=$modify_productname?></b><br />
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
				<th>잠금기능</th>
				<td>
					<?if($set_qna_lock == "Y"){?>
						<select name="secret" class='cate' id="lock">
							<option value="">--선택--</option>
							<option value="0" <?=$no_lock?>>사용안함</option>
							<option value="1" <?=$lock?>>잠금사용</option>
						</select>	
					<?} else if($set_qna_lock == "A"){?>
						<font color="#FF0000">*자동으로 비밀글로 전환됩니다</font>
					<?}?>
				</td>
			</tr>
			<tr>
				<th>작성자</th>
				<td><input type="text" name="name" value="<?=$modify_name?>" class="m_input" /></td>
			</tr>
			<tr>
				<th>비밀번호</th>
				<td><input type="password" name="passwd" value="" class="m_input" /></td>
			</tr>
			<tr>
				<th>이메일</th>
				<td><input type="text" name="email" value="<?=$modify_email?>" class="m_input" /></td>
			</tr>
			<tr>
				<th>말머리</th>
				<td>
					<select name="cate" class="cate">
						<option value="">말머리 선택</option>
						<option value=".">--- 없음 ---</option>
						<? foreach($catetory as $key){ ?>
							<option value="<?=$key?>" <? if($modify_category == $key){echo 'selected';}?>><?=$key?></option>
						<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>글제목</th>
				<td><input type="text" name="title" value="<?=$modify_title?>" class="m_input" /></td>
			</tr>
			<tr>
				<th>글내용</th>
				<td><textarea name="content" id="content" lang="ej-editor4"><?=$content_row->content?></textarea></td>
			</tr>
		</table>
		<div style="text-align:center; margin:10px 0px 30px 0px;"><a class="button black bigrounded" id="btn_submit">수정완료</a> <a class="button white bigrounded" id="btn_reset">다시작성</a></div>

		<input type="hidden" name="board" value="<?=$board_name?>"/>
		<input type="hidden" name="num" value="<?=$board_num?>"/>
		<input type="hidden" name="thread" value="<?=$modify_thread?>"/>
		<input type="hidden" name="mode" value="modify"/>
	</form>
</div>

<script type="text/javascript" src="./gmeditor/js/jquery.js"></script>
<script type="text/javascript" src="./gmeditor/js/jquery.event.drag-2.0.min.js"></script>
<script type="text/javascript" src="./gmeditor/js/jquery.resizable.js"></script>
<script type="text/javascript" src="./gmeditor/js/ajax_upload.3.6.js"></script>
<script type="text/javascript" src="./gmeditor/js/ej.h2xhtml.js"></script>
<script type="text/javascript" src="./gmeditor/editor.js"></script>

<script language="javascript" type="text/javascript">
$(document).ready(function() {
	ejEditor();
});
</script>
<script>
	var form = document.qnaForm;
	$("#btn_submit").click(function(){ // 폼체크 및 서브밋
		
		if($("select[name=secret]").val() == "" || $("select[name=secret]").val() == null){
			alert("잠금기능을 설정하세요.");
			$("select[name=secret]").focus();
			return;
		}else if($("input[name=name]").val() == "" || $("input[name=name]").val() == null){
			alert("작성자를 입력하세요.");
			$("input[name=name]").focus();
			return;
		}else if($("input[name=passwd]").val() == "" || $("input[name=passwd]").val() == null){
			alert("비밀번호를 입력하세요.");
			$("input[name=passwd]").focus();
			return;
		}else if($("input[name=email]").val() == "" || $("input[name=email]").val() == null){
			alert("이메일을 입력하세요.");
			$("input[name=email]").focus();
			return;
		}else if($("select[name=cate]").val() == "" || $("select[name=cate]").val() == null){
			alert("말머리를 선택하세요.");
			$("select[name=cate]").focus();
			return;
		}else if($("input[name=title]").val() == "" || $("input[name=title]").val() == null){
			alert("비밀번호를 입력하세요");
			$("input[name=title]").focus();
			return;
		}else if($("#ejEdt_content").contents().find("body").text() == "" || $("#ejEdt_content").contents().find("body").text() == null){
			alert("내용을 입력하세요.");
			$("#ejEdt_content").contents().find("body").focus();
			return;
		}else{
			$(".modify_btn").css("display","none");
			form.submit();
		}

	});
	
	$("#btn_reset").click(function(){ //초기화
		$(".m_input").each(function(){
			this.value= "";
		});

		$("#ejEdt_content").contents().find("body").text("");
		$(".cate").find('option:first').attr('selected', 'selected');
		$("#content").value="";
		
	});
</script>

<? include_once('footer.php'); ?>
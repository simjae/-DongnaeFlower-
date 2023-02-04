<?php
include_once("header.php");

$board_name = isset($_REQUEST[board])? $_REQUEST[board]:"";
$board_num = isset($_REQUEST[num])? $_REQUEST[num]:"";
$board_pass = isset($_REQUEST[pass])? $_REQUEST[pass]:"";
$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";

$get_board_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
$get_board_result = mysql_query($get_board_sql, get_db_conn());
$get_board_row = mysql_fetch_array($get_board_result);

$set_board_list_view =$get_board_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
$set_board_list_write = $get_board_row[grant_write]; // 게시판 쓰기 권한
$set_board_category = $get_board_row[subCategory]; // 카테고리 리스트  
$set_board_lock = $get_board_row[use_lock]; // 잠금상태 설정 상태 

if(empty($board_name) || empty($board_num)){
	echo '<script>alert("잘못된 경로로 접근하였습니다.");history.go(-1);</script>';
	exit;
}

if($set_board_list_view == "U" || $set_board_list_view == "Y"){
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
	$secret = isset($_POST[secret]) ? $_POST[secret] : "0";
	$name = isset($_POST[name])? $_POST[name] : "";
	$passwd = isset($_POST[passwd])? $_POST[passwd] : "";
	$email = isset($_POST[email]) ? $_POST[email] : "";
	$cate = isset($_POST[cate]) ? $_POST[cate] : "";
	$title = isset($_POST[title]) ? $_POST[title] : "";
	$content = isset($_POST[content]) ? $_POST[content] : "";
	//$thread = isset($_POST[thread]) ? $_POST[thread] : "";

	if($passwd && $content_row->passwd != $passwd){
		echo '<script>alert("비밀번호가 맞지 않습니다.");history.go(-2);</script>';
		exit;
	}

	$modify_sql = "UPDATE tblboard SET ";
	$modify_sql .= "subCategory = '".$cate."', ";
	$modify_sql .= "name = '".$name."', ";
	//$modify_sql .= "passwd = '".$passwd."', ";
	$modify_sql .= "email = '".$email."', ";
	$modify_sql .= "is_secret = '".$secret."', ";
	$modify_sql .= "title = '".$title."', ";
	$modify_sql .= "content = '".$content."' ";
	$modify_sql .= "WHERE num = ".$board_num." AND board = '".$board_name."'"; // AND thread = ".$thread;

	//if(($secret >= 0) && !empty($name) && !empty($passwd) && !empty($email) && !empty($cate) && !empty($title) && !empty($content) && !empty($thread)){
	//if(($secret >= 0) && !empty($name) && !empty($email) && !empty($title) && !empty($content)) {
	if(($secret >= 0) && !empty($name) && !empty($title) && !empty($content)) {
		if(mysql_query($modify_sql, get_db_conn())){
			echo '<script>alert("정상적으로 수정되었습니다.");location.href="board_list.php?board='.$board_name.'"</script>';
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

if(!empty($set_board_category)){
	$catetory = explode(",",$set_board_category);
}
?>

<!--ui개선 20180723-->

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
		<div class="board_writeWrap">
		<table border="0" cellpadding="0" cellspacing="0" class="writeForm">
			<caption>상품정보</caption>
			<col width="100"></col>
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
				<TD colspan="3">
					<?if($set_board_lock == "Y"){?>
						<span class="basic_select">
							<select name="secret" class='cate' id="lock">
								<option value="0" <?=$no_lock?>>사용안함</option>
								<option value="1" <?=$lock?>>잠금사용</option>
							</select>
						</span>
					<?} else if($set_board_lock == "A"){?>
						<input type="hidden" name="secret" value="1" />
						<span style="color:#FF0000;font-size:0.9em;letter-spacing:-1px">*자동으로 비밀글로 전환됩니다.</font>
					<?}?>
				</td>
			</tr>
			<tr>
				<TD colspan="3"><input type="text" name="name" placeholder="작성자명" value="<?=$modify_name?>" class="m_input" style="width:100%; height:40px; line-height:40px;"/></td>
			</tr>
			<? if (strlen($_ShopInfo->getMemid()) == 0) { ?>
			<tr>
				<TD colspan="3"><input type="password" name="passwd" placeholder="비밀번호" value="" class="m_input" style="width:100%; height:40px; line-height:40px;"/></td>
			</tr>
			<? } ?>
			<tr>
				<TD colspan="3"><input type="text" name="email" placeholder="이메일" value="<?=$modify_email?>" class="m_input" style="width:100%; height:40px; line-height:40px;"/></td>
			</tr>
			<tr>
				<TD colspan="3">
					<span class="basic_select">
						<select name="cate" class="cate">
							<option value="">말머리 선택</option>
							<? foreach($catetory as $key){ ?>
								<option value="<?=$key?>" <? if($modify_category == $key){echo 'selected';}?>><?=$key?></option>
							<? } ?>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<TD colspan="3">
				<input type="text" name="title"  placeholder="제목을 입력하세요" value="<?=$modify_title?>" class="m_input" style="width:100%; height:40px; line-height:40px;"/>
				</td>
			</tr>
			<tr>
				<TD colspan="3">
					<? if($setup['use_html'] != "N"){ //html 사용일 때 ?>
					<script src="/ckeditor/ckeditor.js"></script>
					<? } ?>
					<div id="editor">
						<!-- 에디터 적용 전 기존 텍트스필드 --
						<textarea name="content" id="content" class="textarea" style="width:96.5%;"><?=$content_row->content?></textarea>
						-->
						<textarea name="content" id="ir1" style="width:100%;height:200px;padding:10px;box-sizing:border-box;border:1 solid <?=$list_divider?>;cursor:text;" wrap="<?=$setup[wrap]?>"><?=stripslashes($content_row->content)?></textarea>
					</div>
					<? if($setup['use_html'] != "N"){ //html 사용일 때 ?>
						<script>CKEDITOR.replace('ir1',{height:250});</script>
					<? } ?>
				</td>
			</tr>
		</table>


		<? if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ ?>
		<style>
			.write_protect_agree{padding:0px 15px;box-sizing:border-box;}
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


		<div class="basic_btn_area" style="border-top: 1px solid #ebebeb;padding-top:15px;">
			<a class="basic_button grayBtn" id="btn_submit">수정완료</a>
			<a class="basic_button" id="btn_reset">다시작성</a>
		</div>

		</div>

		<input type="hidden" name="board" value="<?=$board_name?>" />
		<input type="hidden" name="num" value="<?=$board_num?>" />
		<input type="hidden" name="thread" value="<?=$modify_thread?>" />
		<input type="hidden" name="mode" value="modify" />
	</form>
</div>

<script>
	var form = document.qnaForm;
	$("#btn_submit").click(function(){ // 폼체크 및 서브밋
		if($("[name=secret]").val() == "" || $("[name=secret]").val() == null){
			alert("잠금기능을 설정하세요.");
			$("[name=secret]").focus();
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
		/* write에서 주석처리 되어 있음
		else if($("input[name=email]").val() == "" || $("input[name=email]").val() == null){
			alert("이메일을 입력하세요.");
			$("input[name=email]").focus();
			return;
		}
		*/
		/* 글쓰기에서는 왜 체크 안 하고 .. ;;
		else if($("select[name=cate]").val() == "" || $("select[name=cate]").val() == null){
			alert("말머리를 선택하세요.");
			$("select[name=cate]").focus();
			return;
		}
		*/
		else if($("input[name=title]").val() == "" || $("input[name=title]").val() == null){
			alert("비밀번호를 입력하세요");
			$("input[name=title]").focus();
			return;
		}else if($("[name=content]").val() == "" || $("[name=content]").val() == null){
			alert("내용을 입력하세요.");
			$("[name=content]").focus();
			return;

		}else{
			<? if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ ?>
				if(!form.chk_protect_agree.checked){
					alert('개인정보 수집 및 이용동의를 체크해 주세요.\n\n서비스 제공을 위한 최소한의 개인정보이므로 동의해주셔야만 이용하실 수 있습니다.');
					form.chk_protect_agree.focus();
					return false;
				}
			<? } ?>

			$(".modify_btn").css("display","none");
			form.submit();
		}

	});
	
	$("#btn_reset").click(function(){ //초기화
		$(".m_input").each(function(){
			this.value= "";
		});

		$(".cate").find('option:first').attr('selected', 'selected');
		$("#content").value="";
		
	});
</script>

<? include_once('footer.php'); ?>
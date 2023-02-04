<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once("header.php");
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

	mysql_free_result($get_qna_result);

	if($set_qna_list_write == "Y" || $set_qna_list_write == "A"){//회원 전용
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("회원만 글쓰기가 가능합니다.\n로그인후 이용하세요.");history.go(-1);</script>';
			exit;
		}
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
		//$up_sql .= "num					= 0, ";
		$up_sql .= "thread				= '".$thread_no."', ";
		$up_sql .= "pos					= '0', ";
		$up_sql .= "depth				= '0', ";
		$up_sql .= "prev_no				= '0', ";
		if(strlen($pridx)>0) {
			$up_sql.= "pridx				= '".$pridx."', ";
		}
		$up_sql .= "next_no				= '".$set_qna_max_num."', ";
		$up_sql .= "name				= '".$name."', ";
		$up_sql .= "passwd			= '".$passwd."', ";
		$up_sql .= "email				= '".$email."', ";
		$up_sql .= "userid				= '".$userid."', ";
		//$up_sql .= "usercel			= '".$up_usercel."', ";
		$up_sql .= "is_secret			= '".$secret."', ";
		$up_sql .= "use_html			= '".$up_html."', ";
		$up_sql .= "title					= '".$title."', ";
		//$up_sql .= "filename			= '".$up_filename."', ";
		$up_sql .= "writetime			= '".time()."', ";
		$up_sql .= "ip						= '".getenv("REMOTE_ADDR")."', ";
		$up_sql .= "access				= '0', ";
		$up_sql .= "total_comment	= '0', ";
		$up_sql .= "content				= '".$content."', ";
		$up_sql .= "notice				= '0', ";
		$up_sql .= "deleted				= '0' ";

		echo $up_sql;


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
			$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_board='Y' ";
			$resultsms= mysql_query($sqlsms,get_db_conn());
			if($rowsms=mysql_fetch_object($resultsms)){
				function getStringCut($strValue,$lenValue)
				{
					preg_match('/^([\x00-\x7e]|.{2})*/', substr($strValue,0,$lenValue), $retrunValue);
					return $retrunValue[0];
				}

				$sms_id=$rowsms->id;
				$sms_authkey=$rowsms->authkey;

				if (($setup[use_admin_sms]=="Y") && $setup[admin_sms]) {
					$totellist = $setup[admin_sms];

					$fromtel=$rowsms->return_tel;
					$smsboardname=str_replace("\\n"," ",str_replace("\\r","",strip_tags($setup[board_name])));
					$smsboardsubject=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($up_subject)))));

					/*$smsmsg="]신규글이 ".getStringCut($smsboardsubject,20)."으로 등록되었습니다.";
					$smsmsg=getStringCut($setup[board_name],80-strlen($smsmsg)).$smsmsg;*/

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

					//echo $sms_id."===".$sms_authkey."===".$totellist."===".$fromtel."===".$date."===".$smsmsg."===".$etcmsg;
				}
			}

			echo '<script>alert("정상적으로 등록되었습니다.");location.href="./customer_qna_list.php";</script>';
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


		//$img_state = $filepath.$pridx_row->productcode.".jpg";
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

<div id="content">
	<div class="h_area2">
		<h2>고객문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<form name="qnaForm" id="qnaForm" action="<?=$PHP_SELF?>" method="post">
	<input type="hidden" name="board" value="<?=$board_name?>" />
	<input type="hidden" name="userid" value="<?=$id?>" />
	<input type="hidden" name="mode" value="upload" />

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
			<th>잠금기능</th>
			<td>
				<?if($set_qna_lock == "Y"){?>
					<span class="basic_select">
						<select name="secret" class="cate" id="lock">
							<option value="">--선택--</option>
							<option value="0" <?=$no_lock?>>사용안함</option>
							<option value="1" <?=$lock?>>잠금사용</option>
						</select>
					</span>
				<?} else if($set_qna_lock == "A"){?>
					<font color="#FF0000">*자동으로 비밀글로 전환됩니다</font>
				<?}?>
			</td>
		</tr>
		<tr>
			<th>작성자</th>
			<td><input type="text" name="name" value="<?=$name?>" <?=$lock?> class="basic_input m_input" /></td>
		</tr>
		<tr>
			<th>비밀번호</th>
			<td><input type="password" name="passwd" value="" class="basic_input m_input" /></td>
		</tr>
		<tr>
			<th>이메일</th>
			<td><input type="text" name="email" value="<?=$email?>" <?=$lock?> style="width:75%" class="basic_input m_input" /></td>
		</tr>
		<tr>
			<th>말머리</th>
			<td>
				<span class="basic_select">
					<select name="cate" class="cate">
						<option value="">말머리선택</option>
						<? foreach($catetory as $key){ ?>
						<option value="<?=$key?>" <? if($write_category == $key){echo 'selected';}?>><?=$key?></option>
						<? } ?>
					</select>
				</span>
			</td>
		</tr>
		<tr>
			<th>글제목</th>
			<td><input type="text" name="title" value="" style="width:100%" class="basic_input m_input" /></td>
		</tr>
		<tr>
			<th>글내용</th>
			<td><textarea name="content" id="contents" class="m_input"><?=$content_row->content?></textarea></td>
		</tr>
	</table>
	<div class="basic_btn_area">
		<input type="button" class="basic_button grayBtn" id="btn_write" value="문의등록" />
		<input type="button" class="basic_button" id="btn_reset" value="다시작성" />
	</div>
	</form>
</div>

<script>
	var form = document.qnaForm;
	$("#btn_write").on('click', function(){ // 폼체크 및 서브밋
		var issecurity = "<?=$set_qna_lock?>";
		if(issecurity != "A" && ($("select[name=secret]").val() == "" || $("select[name=secret]").val() == null)){
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
		/*}else if($("select[name=cate]").val() == "" || $("select[name=cate]").val() == null){
			alert("말머리를 선택하세요.");
			$("select[name=cate]").focus();
			return;
		*/
		}else if($("input[name=title]").val() == "" || $("input[name=title]").val() == null){
			alert("제목을 입력하세요");
			$("input[name=title]").focus();
			return;
		/*
		}else if($("#ejEdt_content").contents().find("body").text() == "" || $("#ejEdt_content").contents().find("body").text() == null){
			alert("내용을 입력하세요.");
			$("#ejEdt_content").contents().find("body").focus();
			return;
		*/
		}else if($("textarea#contents").val() == "" || $("textarea#contents").val() == null){
			alert("내용을 입력하세요");
			$("textarea#contents").focus();
			return;
		}else{
			$(".write_btn").css("display","none");
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

<? include_once('footer.php'); ?>
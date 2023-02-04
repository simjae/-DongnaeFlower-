<?
//게시판 이름 출력 2016-03-17 Seul
include_once("header.php");
include ($Dir."board/lib.inc.php");
include ($Dir."board/file.inc.php");

$setup = setup_info();
$board_name = isset($_REQUEST[board])? trim($_REQUEST[board]):"";
$board_num = isset($_REQUEST[num])? trim($_REQUEST[num]):"";
$board_pass = isset($_REQUEST[pass])? trim($_REQUEST[pass]):"";
$board_pridx = isset($_REQUEST[pridx])? trim($_REQUEST[pridx]):"";


$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
$get_qna_row = mysql_fetch_array($get_qna_result);

$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
$set_qna_list_write = $get_qna_row[grant_write]; // 게시판 쓰기 권한

$imgdir=$Dir.BoardDir."images/skin/".$setup[board_skin];
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

//게시판 이름 출력 2016-03-17 Seul
$sql = "SELECT board_name FROM tblboardadmin WHERE board = '".$board_name."' ";
$result = mysql_query($sql, get_db_conn());
$row = mysql_fetch_array($result);

$set_board_name = $row[board_name];

mysql_free_result($result);

//if(!empty($board_pridx)){
	//$content_sql= "SELECT b.* FROM tblboard AS b LEFT OUTER JOIN tblproduct AS p ON b.pridx = p.pridx WHERE board = '".$board_name."' AND num =".$board_num;
//}else{
	$content_sql= "SELECT * FROM tblboard WHERE board = '".$board_name."' AND num = ".$board_num;
//}

$content_result= mysql_query($content_sql, get_db_conn());
$content_row = mysql_fetch_object($content_result);

//고객이 설정한 비밀번호로 답변 글 볼수 있도록 게시판 비밀번호 관련 수정 2016-07-28 Seul
$password_sql = "SELECT * ";
$password_sql .= "FROM tblboard ";
$password_sql .= "WHERE title NOT LIKE '[답변]%' ";
$password_sql .= "AND next_no='".trim($content_row->next_no)."'";

$password_result = mysql_query($password_sql, get_db_conn());
$password_row = mysql_fetch_object($password_result);
//고객이 설정한 비밀번호로 답변 글 볼수 있도록 게시판 비밀번호 관련 수정 2016-07-28 Seul 끝

if($content_row->is_secret >= 1){
	//고객이 쓴 글이든 고객에게 달린 답변이든 무조건 비밀번호는 고객이 쓴 글에 대한 비밀번호로 체크 (2015-12-24) Seul
	if(trim($password_row->passwd) != $board_pass) {
		echo '<script>alert("비밀번호가 맞지 않습니다.");history.go(-1);</script>';
		exit;
	}

	/*
	if(trim($content_row->passwd) != $board_pass){
		echo '<script>alert("비밀번호가 맞지 않습니다.");history.go(-1);</script>';
		exit;
	}
	*/
}

if($content_row){
	$filepath = "../data/shopimages/product/";
	$addFilepath = "../data/shopimages/board/".$board_name."/";
	$view_name = $content_row->name;
	$view_num = $content_row->num;
	$view_board = $content_row->board;
	$view_title = $content_row->title;
	$view_content = stripslashes($content_row->content);
	$view_writetime = date("Y-m-d",$content_row->writetime);
	$view_pridx =$content_row->pridx;
	$this_comment = $content_row->total_comment;
	$img_sql = "SELECT * FROM tblproduct WHERE pridx =".$view_pridx;
	$img_result = mysql_query($img_sql, get_db_conn());
	$img_row = mysql_fetch_object($img_result);
	$img_state = $filepath.$img_row->tinyimage;
	$view_prprice = $img_row->sellprice;
	$addFile=$content_row->filename;


	$width = getimagesize($addFilepath.$addFile);
	$imageSize = $width[0]>300 ? "width='100%'" : "";

	$return_url="";

	if(file_exists($img_state)){
		$img = $img_state;
	}else{
		$img ="../images/no_img.gif";
	}
	if(strlen($img_row->productname) > 28){
		$view_productname = _strCut($img_row->productname, 28, 5,$charset);
	}else{
		$view_productname = $img_row->productname;
	}
	if($img_row->productcode){
		$return_url="./productdetail_tab04.php?productcode=".$img_row->productcode;
	}
}
if ($setup[use_comment]=="Y" && $this_comment > 0) {

	$com_query = "SELECT * FROM tblboardcomment WHERE board='".$board_name."' ";
	$com_query.= "AND parent = $board_num ORDER BY num DESC ";
	$com_result = @mysql_query($com_query,get_db_conn());
	$com_rows = @mysql_num_rows($com_result);

	if ($com_rows <= 0) {
		@mysql_query("UPDATE tblboard SET total_comment='0' WHERE board='$board_name' AND num='$board_num'");
	} else {
		unset($com_list);
		while($com_row = mysql_fetch_array($com_result)) {
			$com_list[count($com_list)] = $com_row;
		}
		mysql_free_result($com_result);
	}
}	

if($_data->sns_ok == "Y" ){ //&& $setup[sns_state] == "Y"
// 링크
	$sql = "SELECT code FROM tblsnsboard ";
	$sql.= "WHERE board='".$board."' AND num='".$num."' and id='".$_ShopInfo->getMemid()."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$bodUrl = "http://".$_ShopInfo->getShopurl()."?bcmt=".$row->code;
	}else{
		$cnt = 1;
		while($cnt > 0){
			$tmpid = rand(10000,999999);
			$sql = "SELECT count(1) cnt FROM tblsnsboard WHERE code='".$tmpid."'";
			$result = mysql_query($sql,get_db_conn());
			if($row = mysql_fetch_object($result)) {
				$cnt = (int)$row->cnt;
			}
			mysql_free_result($result);
		}
		$sql = "INSERT tblsnsboard SET ";
		$sql.= "code	= '".$tmpid."', ";
		$sql.= "board	= '".$board."', ";
		$sql.= "num	= '".$num."', ";
		$sql.= "id	= '".$_ShopInfo->getMemid()."' ";
		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$bodUrl = "http://".$_ShopInfo->getShopurl()."?bcmt=".$tmpid;
		}
	}
	$bodsubject = $strSubject;
}
?>

<div id="content">
	<div class="h_area2">
		<h2><?=$set_board_name?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div id="board_view">
		<p class="title"><?=$view_title?></p>
		<p class="writer"><?=$view_writetime?> <span class="hline">|</span> <?=$view_name?> <span class="hline">|</span> <?=$content_row->access?></p>

		<div class="snsbutton"><?include_once('board_sns.php')?></div>
		<div class="bigview"><button class="basic_button" onClick="contentsView();">게시물 확대보기</button></div>

		<div id="contents_area">
			<? if(strlen($addFile)>0){ ?><div class="addFileImage"><img src="<?=$addFilepath.$addFile?>" <?=$imageSize?> border="0" alt="" /></div><? } ?>
			<?=$view_content?>
		</div>
	</div>

	<div class="basic_btn_area">
		<a class="basic_button" href="passwd_confirm.php?type=delete&num=<?=$view_num?>&board=<?=$view_board?>" rel="external">삭제하기</a>
		<a class="basic_button" href="passwd_confirm.php?type=modify&num=<?=$view_num?>&board=<?=$view_board?>" rel="external">수정하기</a>
		<a class="basic_button grayBtn" href="board_list.php?board=<?=$view_board?>" rel="external">목록보기</a>
	</div>
</div>

<div class="reviewList">
<?
	$secuCmtViewCnt=0;

	for ($jjj=0;$jjj<count($com_list);$jjj++) {
		// 단일댓글
		if( $_ShopInfo->getMemid() == $com_list[$jjj][id] AND $setup[onlyCmt] == "Y" ) $secuCmtViewCnt++;
	}

	if ($setup[use_comment] == "Y" && $member[grant_comment]=="Y") {
		if( $secuCmtViewCnt == 0 OR strlen($_ShopInfo->id) > 0 ){
			$cmtFile = ($setup[fileYN] == "Y") ? "<input type=\"file\" name=\"img\" class=\"input\" style=\"width:98%;\" />" : ""; // 파일첨부
		}
	}

	@include ("./comment_write.php");

	for ($jjj=0;$jjj<count($com_list);$jjj++) {
		$c_num = $com_list[$jjj][num];
		$c_name = $com_list[$jjj][name];

		if($setup[use_comip]!="Y") {
			$c_uip=$com_list[$jjj][ip];
		}

		unset($comUserId);

		$c_writetime = getTimeFormat($com_list[$jjj][writetime]);
		$c_comment = nl2br(stripslashes($com_list[$jjj][comment]));
		$c_ip = $com_list[$jjj][ip];
		$c_comment = getStripHide($c_comment);

		// 비밀댓글
		$secuCmtView = true;
		if($setup["secuCmt"] == "Y"){
			$secuCmtView = false;
			if($_ShopInfo->getMemid() == $com_list[$jjj][id] OR strlen($_ShopInfo->id)>0){
				$secuCmtView = true;
			}
		}

		// 관리자 댓글의 댓글
		$adminComment = "";
		$adminCommSQL = "SELECT * FROM `tblboardcomment_admin` WHERE `board` = '".$board."' AND `board_no`= '".$num."' AND `comm_no`= '".$c_num."' ORDER BY `idx` ASC";
		$adminCommResult = mysql_query( $adminCommSQL );
		$adminCommNums = mysql_num_rows($adminCommResult);

		if($adminCommNums > 0){
			$adminComment .= "<div class=\"adminComment\">";
			while( $adminCommRow = mysql_fetch_assoc ( $adminCommResult ) ) {
				$adminComment .= "<p><strong>관리자</strong> : ".$adminCommRow['comment']."</p>"; //(".$adminCommRow['reg_date'].")
			}
			$adminComment .= "</div>";
		}

		// 파일
		$filesname = DirPath.DataDir."shopimages/board/".$board."/".$com_list[$jjj]['file'];
		$filessize = @getimagesize($filesname);
		$c_comment_file_max_width = $setup[comment_width];
		$c_comment_file_width = ( $c_comment_file_max_width < $filessize[0] ) ? ($c_comment_file_max_width) : $filessize[0];
		$c_comment_file = ( strlen($com_list[$jjj]['file']) > 0 ) ? "<img src='".$filesname."' width='80' />" : "";

		$actionurl="";
		if(strlen($com_list[$jjj][id])>0 && $com_list[$jjj][id] == $_ShopInfo->getMemid()){
			$actionurl="comment_delete.php";
		}else{
			$actionurl="board_pwdconfirm.php";
		}

		if($secuCmtView) @include ("./comment_list.php");
	}
	?>
</div>

<!--<script>
	$(document).ready(function(){
		$(".view_con>span").children().remove();
	});
</script>-->

<form name="snsProcForm" action="board_sns_proc.php" method="post" target="SNSPROC">
	<input type="hidden" name="num" value="<?=$board_num?>"/>
	<input type="hidden" name="board" value="<?=$board_name?>"/>
	<input type="hidden" name="type" value=""/>
	<input type="hidden" name="url" value="<?=$bodUrl?>"/>
</form>

<form name="cdelform" method="post">
	<input type="hidden" name="pagetype" value="comment_delpop" />
	<input type="hidden" name="board" value="<?=$board_name?>" />
	<input type="hidden" name="num" value="" />
	<input type="hidden" name="c_num" value="" />
	<input type="hidden" name="mode" value="delete" />
</form>

<IFRAME name="SNSPROC" style="display:none"></IFRAME>

<script type="text/javascript" src="../m/js/kakao.link.js"></script>

<script type="text/javascript">
	function SendSMS(type){
		var _form = document.snsProcForm;
		_form.type.value=type;
		_form.submit();
	}
</script>

<script type="text/javascript">
	<!--
	function comment_delete(num,c_num,actionurl) {
		var _form = document.cdelform;
		_form.action = actionurl;
		_form.num.value=num;
		_form.c_num.value=c_num;

		if(_form.num.value =="" || _form.c_num.value==""){
			alert("필수 정보가 누락되었습니다.");	
		}else{
		_form.submit();
		}
		//document.location.href="comment_delete.php?board=<?=$board?>&num="+num+"&c_num="+c_num;
		return;
	}

	function contentsView(){
		var board = "<?=$view_board?>";
		var num = "<?=$view_num?>";
		var contenturl = "./board_view_contents.php?board="+board+"&num="+num;
		window.open(contenturl,"boardview","");
		return;
	}
	//-->
</SCRIPT>

<? include_once('footer.php'); ?>
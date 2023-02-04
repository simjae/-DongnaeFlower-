<?
//게시판 이름 출력, 블로그형 게시물 페이지번호 추가 2016-03-17 Seul
$prd=isset($_REQUEST['prd'])? $_REQUEST['prd']:"";

$Dir = "../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."app/inc/function.php");

//게시판 이름 출력 2016-03-17 Seul
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

$url = "http://".$_SERVER['HTTP_HOST']."/m/board_view.php?num=".$board_num."&board=".$board_name;
$weburl = "http://".$_SERVER['HTTP_HOST']."/board/board.php?pagetype=view&num=".$board_num."&board=".$board_name;
$view_num = $content_row->num;
$view_board = $content_row->board;
$view_title = $content_row->title;
$view_content = $content_row->content;
$view_filename = $content_row->filename;

//고객이 설정한 비밀번호로 답변 글 볼수 있도록 게시판 비밀번호 관련 수정 2016-07-28 Seul
$password_sql = "SELECT * ";
$password_sql .= "FROM tblboard ";
$password_sql .= "WHERE title NOT LIKE '[답변]%' ";
$password_sql .= "AND next_no='".trim($content_row->next_no)."'";

$password_result = mysql_query($password_sql, get_db_conn());
$password_row = mysql_fetch_object($password_result);
//고객이 설정한 비밀번호로 답변 글 볼수 있도록 게시판 비밀번호 관련 수정 2016-07-28 Seul 끝

//게시판 관리자로 로그인하면 쿠키생성(비번확인 안하도록)
if(trim($setup[passwd]) == $board_pass){
    $cadname=$board."_ADMIN";
    $cadnamrarray=getBoardCookieArray($_ShopInfo->getBoardadmin());
    $cadnamrarray[$cadname]="OK";
    $_ShopInfo->setBoardadmin(addslashes(serialize($cadnamrarray)));
    $_ShopInfo->Save();
}

if($content_row->is_secret >= 1){
	//고객이 쓴 글이든 고객에게 달린 답변이든 무조건 비밀번호는 고객이 쓴 글에 대한 비밀번호로 체크 (2015-12-24) Seul
    if(trim($password_row->passwd) == $board_pass || $_ShopInfo->boardadmin){
        //비밀번호 확인
    }else{
        echo '<script>alert("비밀번호가 맞지 않습니다.");history.go(-1);</script>';
        exit;
    }
}

//게시판 뷰페이지 동일 카운드 증가금지
$isAccessUp=false;
$cname=$board."_".$num."V";
if($setup[hitplus]=="Y") {	//동일인 조회수 증가 금지 (30분으로 제한)
    if(isCookieVal($_COOKIE["board_thread_numV"],$cname)) {
        $isAccessUp=true;
    }
}
if(!$isAccessUp) {
    //cookie set
    $cookiearray=getBoardCookieArray($_COOKIE["board_thread_numV"]);
    $cookiearray[$cname]="OK";
    setBoardCookieArray("board_thread_numV",$cookiearray,1800,RootPath."/m/","");
    $qry = "UPDATE tblboard SET access=access+1 WHERE board='".$board."' AND num = '".$num."' ";
    $update = mysql_query($qry,get_db_conn());
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
	$img_result = mysql_query($img_sql,get_db_conn());
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
    /*
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
    */
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
<?
    }else{
        include($skinPATH."header.php");
    }
?>

<style>
	/* 웹에디터 등록이미지 가로 최대값 비율로 세로 자동 리사이징 처리 */
	#contents_area img{
		max-width: 100%;
		height: auto !important; 
		margin: 5px auto; /* 중앙정렬 처리 */
		display: block;
		float: none; /* 이미지사이즈를 100% 미만으로 줄였을때 중앙정렬 처리 */
		vertical-align: top;
	}

	/* 폰트사이즈 줄간 조정 */
	#contents_area{font-size:12pt;line-height:2.0em;overflow:hidden; text-align:justify;}
	#contents_area h1, h2, h3, h4, h5, h6{text-align:left;}
	#contents_area iframe{width:100%; height:220px; !important;}
	#contents_area iframe:before{content:" ";position:relative;}
</style>

<div id="content">
	<div class="h_area2">
		<h2><?=$set_board_name?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

    <? if($view_pridx){ ?>
        <a href="<?=$return_url?>">
            <div class="img_container">
                <div class="img_box" style="margin:0px"><img class= "<?=$class_name?>" src="<?=$img_state?>" style="max-width:100%"></div>
                <div class="img_contents" style="padding-left:15px;box-sizing:border-box">
                    <b><?=$img_row->productname?></b><br />
                    판매가 : <span class="sellprice"><?=number_format($view_prprice);?>원</span><br />
                    시중가 : <strike><?=number_format($img_row->consumerprice);?>원</strike>
                </div>
            </div>
        </a>
    <? } ?>

	<div id="board_view">
		<p class="title"><?=$view_title?></p>
		<p class="writer"><?=$view_writetime?> <span class="hline">|</span> <?=$view_name?> <span class="hline">|</span> <?=$content_row->access?></p>

		<div class="snsbutton"><?include_once('board_sns.php')?></div>
		<div class="bigview"><button class="basic_button" onClick="contentsView();">게시물 확대보기+</button></div>

		<div id="contents_area">
			<? if(strlen($addFile)>0){ ?><div class="addFileImage"><img src="<?=$addFilepath.$addFile?>" <?=$imageSize?> border="0" alt="" /></div><? } ?>
			<?=$view_content?>
		</div>
	</div>

	<div class="basic_btn_area">
		<a class="basic_button1" href="passwd_confirm.php?type=delete&num=<?=$view_num?>&board=<?=$view_board?>" rel="external">삭제</a>
		<a class="basic_button1" href="passwd_confirm.php?type=modify&num=<?=$view_num?>&board=<?=$view_board?>" rel="external">수정</a>
		<a class="basic_button1" href="board_list.php?board=<?=$view_board?>" rel="external">목록</a>
	</div>
</div>

<? if($setup[use_comment]=='Y'){ //코멘트 기능 사용중일 때 출력 ?>
<div class="reviewList" style="margin:0.5em;">
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
<? } ?>

<!--<script>
	$(document).ready(function(){
		$(".view_con>span").children().remove();
	});
</script>-->

<form name="cdelform" method="post">
	<input type="hidden" name="pagetype" value="comment_delpop" />
	<input type="hidden" name="board" value="<?=$board_name?>" />
	<input type="hidden" name="num" value="" />
	<input type="hidden" name="c_num" value="" />
	<input type="hidden" name="mode" value="delete" />
</form>

<IFRAME name="SNSPROC" style="display:none"></IFRAME>

<!-- <script type="text/javascript" src="../m/js/kakao.link.js"></script> -->
<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>

<script type="text/javascript">
	// 사용할 앱의 JavaScript 키를 설정해 주세요. 처음 한번만 호출하면 됩니다.
	Kakao.init('<?=$kakaousekey?>');

	function snsSendProc(type){
		var bodUrl = "<?=$url?>";
		var bodWebUrl = "<?=$weburl?>";
		var bodsubject = "<?=strip_tags($view_title)?>";
		var bodcontent = "<?=strip_tags($view_content)?>";
		var imagesrc = "http://<?=$_SERVER['HTTP_HOST']?>/data/shopimages/board/<?=$view_board?>/<?=$view_filename?>";
		var kakaousestate = "<?=$kakaousestate?>";
		var kakaokey = "<?=$kakaousekey?>";

		switch(type){
			case "KT": //카카오링크
			if(kakaousestate == "Y" && kakaokey.length > 0){
				// 카카오링크 버튼을 생성합니다.
				Kakao.Link.sendDefault({
					objectType: 'feed',
					content: {
						title: bodsubject,
						description: bodcontent,
						imageUrl: imagesrc,
						link: {
							mobileWebUrl: bodUrl,
							webUrl: bodWebUrl
						}
					},
					buttons: [{
						title: '자세히 보기',
						link: {
							mobileWebUrl: bodUrl,
							webUrl: bodWebUrl
						}
					}]
				});

				/*
				Kakao.Link.createTalkLinkButton({
					container: '#kakao-link-btn',
					label: bodsubject,
					image: {
						src: imagesrc,
						width: '300',
						height: '200'
					},
					webButton: {
						text: '<?=$_data->shoptitle?>',
						url: bodUrl // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
					}
				});
				*/
			}else{
				alert("카카오 키가 발급이 되어있지 않거나\n사용설정이 되어있지 않습니다.");
				return;
			}
			break;

			case "KS":
			if(kakaousestate == "Y" && kakaokey.length > 0){
			    // 사용할 앱의 JavaScript 키를 설정해 주세요.
			    //Kakao.init(kakaokey);
			    // 스토리 공유 버튼을 생성합니다.
			    Kakao.Story.share({
			    	url: bodUrl,
			    	text: bodsubject +' #' + bodsubject +' :)'
			    });
				//executeKakaoStoryLink(returnurl,appid,appname,productname,contents,imagesrc);
			}else{
				alert("카카오 키가 발급이 되어있지 않거나\n사용설정이 되어있지 않습니다.");
				return;
			}
			break;

			case "FB":
				var href = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(bodUrl)+ "&t=" + encodeURIComponent(bodsubject);
				var a = window.open(href, 'Facebook', '');
				if (a) {
					a.focus();
				}
			break;

			case "TW":
				var href = "http://twitter.com/share?text=" + encodeURIComponent(bodsubject) + " " + encodeURIComponent(bodUrl);
				var a = window.open(href, 'Twitter', '');
				if (a) {
					a.focus();
				}
			break;

			case 'PI':
				var href = "http://www.pinterest.com/pin/create/button/?url=" + encodeURIComponent(bodUrl) + "&media=" + encodeURIComponent(imagesrc) + "&description=" + encodeURIComponent(bodsubject);
				var a = window.open(href, 'Pinterest', '');
				if (a) {
					a.focus();
				}
			break;

			case 'GO':
				var href = "https://plus.google.com/share?url=" + encodeURIComponent(bodUrl);
				var a = window.open(href, 'GooglePlus', '');
				if (a) {
					a.focus();
				}
			break;

			case 'NB': //네이버블로그
				var href = "https://share.naver.com/web/shareView.nhn?url=" + encodeURIComponent(bodUrl) + "&title=" + encodeURIComponent(bodsubject);
				var a = window.open(href, 'NaverBlog', '');
				if (a) {
					a.focus();
				}
			break;
		}
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
			if(confirm("댓글을 삭제하시겠습니까?")==true){
				_form.submit();
			}else{
				return false;
			}
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

<?
	if($prd == 'yes'){ //상세페이지를 통해서 글쓰기로 왔을 때 공통상단 미출력
		echo "</body></html>";
	}else{
		include_once('footer.php');
	}
?>
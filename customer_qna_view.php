<?
include_once("header.php");

$board_name = isset($_REQUEST[board])? trim($_REQUEST[board]):"";
$board_num = isset($_REQUEST[num])? trim($_REQUEST[num]):"";
$board_pass = isset($_REQUEST[pass])? trim($_REQUEST[pass]):"";
$board_pridx = isset($_REQUEST[pridx])? trim($_REQUEST[pridx]):"";


$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
$get_qna_row = mysql_fetch_array($get_qna_result);

$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
$set_qna_list_write = $get_qna_row[grant_write]; // 게시판 쓰기 권한


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

//if(!empty($board_pridx)){
	//$content_sql= "SELECT b.* FROM tblboard AS b LEFT OUTER JOIN tblproduct AS p ON b.pridx = p.pridx WHERE board = '".$board_name."' AND num =".$board_num;
//}else{
	$content_sql= "SELECT * FROM tblboard WHERE board = '".$board_name."' AND num = ".$board_num;
//}

$content_result= mysql_query($content_sql, get_db_conn());
$content_row = mysql_fetch_object($content_result);

if($content_row->is_secret >= 1){
	if(trim($content_row->passwd) != $board_pass){
		echo '<script>alert("비밀번호가 맞지 않습니다.");history.go(-1);</script>';
		exit;
	}
}

if($content_row){
	$filepath = "../data/shopimages/product/";
	$view_name = $content_row->name;
	$view_num = $content_row->num;
	$view_board = $content_row->board;
	$view_title = $content_row->title;
	$view_content = stripslashes($content_row->content);
	$view_writetime = date("Y.m.d",$content_row->writetime);
	$view_pridx =$content_row->pridx;
	
	$img_sql = "SELECT * FROM tblproduct WHERE pridx =".$view_pridx;
	$img_result = mysql_query($img_sql, get_db_conn());
	$img_row = mysql_fetch_object($img_result);
	$img_state = $filepath.$img_row->tinyimage;
	$view_prprice = $img_row->sellprice;
	
	$return_url="";

	if(file_exists($img_state)){
		$img = $img_state;
	}else{
		$img ="../images/no_img.gif";
	}
	if(mb_strlen($img_row->productname) > 25){
		$view_productname = _strCut($img_row->productname,25,5,$charset);

	}else{
		$view_productname = $img_row->productname;
	}
	if($img_row->productcode){
		$return_url="./productdetail_tab04.php?productcode=".$img_row->productcode;
	}
}

?>
<div id="content">
	<div class="h_area2">
		<h2>고객문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div id="board_view">
		<p class="title"><?=$view_title?></p>
		<p class="writer"><?=$view_writetime?> <span class="hline">|</span> <?=$view_name?> <span class="hline">|</span> <?=$content_row->access?></p>

		<!--
		<div class="snsbutton"><?//include_once('board_sns.php')?></div>
		<div class="bigview"><button class="button white medium" onClick="contentsView();">게시물 확대보기</button></div>
		-->

		<?if(!empty($view_pridx)){?>
		<div>
			<table class="qna_list view" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" align="center">
						<div class="view_con">
							<div style="padding:5px; background:#ffffff;"><a href="<?=$return_url?>" rel="external" title="상세보기로 이동합니다."><img src="<?=$img?>" width="50" border="0" alt="" /></a></div>
							<ul style="margin-top:8px; text-align:left;">
								<li><a href="<?=$return_url?>" rel="external" title="상세보기로 이동합니다.">상품명 : <b><?=$view_productname?></b></a></li>
								<li>상품가격 : <?=number_format($view_prprice);?>원</li>
							</ul>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?}?>

		<div id="contents_area" class="contentarea" style="border-top:none;"><?=$view_content?></div>
	</div>
	<div class="qna_view_bt">
		<a class="button black bigrounded" href="customer_qna_list.php" rel="external">목록보기</a>
		<a class="button white bigrounded" href="./passwd_confirm.php?type=modify&num=<?=$view_num?>&board=<?=$view_board?>" rel="external">수정하기</a>
		<!-- <a class="button white bigrounded" href="./passwd_confirm.php?type=modify&num=<?=$view_num?>&board=<?=$view_board?>" rel="external">수정하기</a> -->
	</div>

</div>
<!--<script>
	$(document).ready(function(){
		
		$(".view_con>span").children().remove();
	});
</script>-->
<? include_once('footer.php'); ?>
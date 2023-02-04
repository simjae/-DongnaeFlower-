<?
	include_once("header.php");
	include_once($Dir."app/inc/paging_inc.php");

	$curpage=isset($_GET['page'])?trim($_GET['page']):1;
	$boardname = "share";
	$listnum = 5; // 페이지당 게시글 리스트 수
	
	$boardsettingSQL = "SELECT grant_view, grant_write FROM tblboardadmin WHERE board = '".$boardname."' ";
	$boardsettingGrantView=$boardsettingGrantWrite="";
	if(false !== $boardsettingRes = mysql_query($boardsettingSQL,get_db_conn())){
		$boardsettingGrantView=$boardsettingGrantWrite="";
		$boardsettingGrantView = mysql_result($boardsettingRes,0,0);// 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
		$boardsettingGrantWrite = mysql_result($boardsettingRes,0,0);// 게시판 쓰기 권한
	}

	if($boardsettingGrantView== "" || $boardsettingGrantView == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("목록보기 권한이 없습니다.");history.go(-1);</script>';
			exit;
		}
	}
	
	$totallistSQL = "SELECT * FROM tblboard WHERE board = '".$boardname."' ";
	
	if(false !== $totallistRes = mysql_query($totallistSQL,get_db_conn())){
		$totallistrowcount = mysql_num_rows($totallistRes);
		mysql_free_result($totallistRes);
	}else{
		echo '<script>alert("게시판이 지정되지 않았습니다.");history.go(-1)</script>';exit;
	}

	$listSQL = "SELECT * FROM tblboard WHERE board ='".$boardname."' ORDER BY thread, pos ASC LIMIT ".($listnum * ($curpage - 1)) . ", " . $listnum;
?>
<style>
	.boardInfo{padding:15px;letter-spacing:-1px;}
	.boardInfo li{padding:2px 0px;}
	.linkTypeList{width:100%;padding-top:10px;background:#e9e9e9;overflow:hidden;}
	.contentsBox{width:94%;margin:0 auto;margin-bottom:10px;padding-bottom:10px;background:#ffffff;border:1px solid #e0e0e0;}
	.addFile{margin-bottom:10px;text-align:center;}
	.contentsBox p{padding:0px 10px;}
	.title{margin-bottom:5px;font-size:1.2em;font-weight:700;letter-spacing:-1px;}
	.writer{color:#666666;font-weight:700;padding-right:5px;letter-spacing:-1px;}
	.line{color:#aaaaaa;font-size:10px;text-align:center;}
	.writetime{color:#888888;font-size:11px;padding-left:5px;}
</style>

<div id="content">
	<div class="h_area2">
		<h2>나눔톡</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<ul class="boardInfo">
		<li>- <strong>나눔톡</strong>은 고객분의 SNS나 블로그, 카페 등을 통한 쇼핑몰 정보공유 게시판입니다.</li>
		<li>- 상품구입 후기나 쇼핑몰 관련 스토리를 등록하신 후 주소(URL)를 남겨주세요.</li>
		<li>- 나눔톡을 등록하신 회원분들 중 <strong>매월</strong> 몇 분을 선정하여 <strong>소정의 적립금</strong>을 지급해 드립니다.</li>
	</ul>
	<div class="linkTypeList">
	<?
		if(false !== $listRes = mysql_query($listSQL,get_db_conn())){
			$listrowcount = mysql_num_rows($listRes);
			$imgsrc = $Dir."data/shopimages/board/share/";
			if($listrowcount>0){
				while($listRow = mysql_fetch_assoc($listRes)){
					if(mb_strlen($listRow['title']) > 21){
						$title = _strCut($listRow['title'],21,5,$charset);
					}else{
						$title = $listRow['title'];
					}
;
					$url = $listRow['url'];
					$attech = $listRow['filename'];
					$src = $imgsrc.$attech;
					$writer=$listRow['name'];
					$writetime =date("Y/m/d",$listRow['writetime']);

	?>
		<div class="contentsBox">
			<div class="addFile"><a href="<?=$url?>" target="_blank"><img src="<?=$src?>" width="100%" alt="" /></a></div>
			<p class="title"><a href="<?=$url?>" target="_blank"><?=$title?></a></p>
			<p class="etcInfo"><span class="writer"><?=$writer?></span><span class="line">|</span><span class="writetime"><?=$writetime?></span></p>
		</div>
	<?
				}
			}
		}
	?>
	</div>
	<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?page=%u&board=".$boardname; // 링크
				$pagePerBlock = ceil($totallistrowcount/$listnum);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
</div>

<? include_once('footer.php'); ?>
<?
	$sql_mb="SELECT * FROM tblmember where id = '".$_ShopInfo->getMemid()."' ";
	$result_mb=mysql_query($sql_mb,get_db_conn());
	$row_mb=mysql_fetch_object($result_mb);

	$order_type = $row_mb->order_type;
	$reserve = $row_mb->reserve;
	if($order_type < 0){
		$orderURL = $Dir."app/select_order.php";
	}
	else if($order_type == 1){
		$orderURL = $Dir."app/form_request.php";
	}
	else if($order_type == 2){
		$orderURL = $Dir."app/talk_request.php";
	}
	if($sellvidx){
		$vidx = isset($_REQUEST['sellvidx'])?trim($_REQUEST['sellvidx']):"";
	}else{
		$vidx = isset($_REQUEST['vidx'])?trim($_REQUEST['vidx']):"";
	}

	if($vidx){ //미니샵 상단일 때
		include_once($Dir."lib/venderlib.php");

		$sql="SELECT COUNT(p.pridx) AS prcount, v.com_name, v.com_owner, v.com_image ";
		$sql.="FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ON(p.vender = v.vender) ";
		$sql.="WHERE v.vender = '".$vidx."' AND p.display='Y'";
		$result=mysql_query($sql,get_db_conn());
		$corpname=mysql_result($result,0,1);

		//입점사 카테고리 출력 START
		$_MiniLib=new _MiniLib($vidx);
		$_MiniLib->_MiniInit();
		$_minidata=$_MiniLib->getMiniData();
		$corpname=$_minidata->brand_name;

		$code=$_REQUEST["code"];

		$tgbn="10";
		if(strlen($code)==0) {
			$code="000000";
		}else{
			$code = str_pad($code, 12, "0", STR_PAD_RIGHT);
		}

		$codeA=substr($code,0,3);
		$codeB=substr($code,3,3);
		$codeC=substr($code,6,3);
		$codeD=substr($code,9,3);

		if(strlen($codeA)!=3) $codeA="000";
		if(strlen($codeB)!=3) $codeB="000";
		if(strlen($codeC)!=3) $codeC="000";
		if(strlen($codeD)!=3) $codeD="000";
		if($codeA!="000") $likecode.=$codeA;
		if($codeB!="000") $likecode.=$codeB;
		if($codeC!="000") $likecode.=$codeC;
		if($codeD!="000") $likecode.=$codeD;

		$_MiniLib->getCode($tgbn,$code);
		$_MiniLib->getThemecode();

		$prdataA=$_MiniLib->prdataA;
		$prdataB=$_MiniLib->prdataB;
		$themeprdataA=$_MiniLib->themeprdataA;
		$themeprdataB=$_MiniLib->themeprdataB;
		//입점사 카테고리 출력 END
	}else{ //일반 상단일 때
		$sql="SELECT * FROM tblspecialmain WHERE special='1'"; //신상품
		$result=mysql_query($sql,get_db_conn());
		$nums=mysql_num_rows($result);

		$sql2="SELECT * FROM tblspecialmain WHERE special='2'"; //인기상품
		$result2=mysql_query($sql2,get_db_conn());
		$nums2=mysql_num_rows($result2);
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="<?=$charset?>">
	<title><?=$shopname?> 쇼핑몰 - 모바일</title>
	<link rel="icon" type="image/png" sizes="16x16"  href="/favicons/favicon-16x16.png">
	<meta name="viewport" content="width=420, user-scalable=no, viewport-fit=cover" />
	
	<meta http-equiv="page-enter" content="blendTrans(Duration=0.5)">
	<meta http-equiv="page-exit" content="blendTrans(Duration=0.5)">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="format-detection" content="telephone=no" />
	<?
	$updateVer = "?ver=";
	$updateVer .= "20211220_01";
	?>
	<!-- 바로가기 아이콘 -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon-precomposed" href="./upload/<?=$icon?>" />

	<link rel="stylesheet" href="/app/skin/basic/css/common.css<?=$updateVer?>" />
	<link href="/app/skin/basic/css/default.css" rel="stylesheet" type="text/css"/>
	<link href="/app/skin/basic/css/swiper.min.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/normalize.css" rel="stylesheet" type="text/css" media="all">
	<link href="/app/skin/basic/css/top.css<?=$updateVer?>" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/main.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/bottom.css" rel="stylesheet" type="text/css"> 
	<link href="/app/skin/basic/css/detail.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/list.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/login.css" rel="stylesheet" type="text/css">
	<link href="/css/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />
	<link href="/dist/semantic.min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/lib/lib.js.php"></script>
	<script type="text/javascript" src="/app/js/common.js"></script>
	<script type="text/javascript" src="/app/skin/basic/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="/app/skin/basic/js/jquery.transform.js"></script>
	<script type="text/javascript" src="/app/skin/basic/js/swiper.min.js"></script>
	<script type="text/javascript" src="/app/skin/basic/js/common.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/app/skin/basic/js/jquery.ui.touch-punch.min.js"></script>
	<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
	<script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
	<script type="text/javascript" src="/app/skin/basic/js/anime.min.js"></script>
	<script src="/dist/semantic.min.js"></script>
	<script src="/js/dropzone.js"></script>
	<script type="text/javascript" src="/app/skin/basic/js/odoo.js"></script>
	<script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId=ccvcib29m6&submodules=geocoder,drawing"></script>
	<script type="text/javascript">
		<!--
		//카테고리 전체보기
		$(document).ready(function(){
			var sidebar = $('[data-sidebar]');
			sidebar.show(0, function() {
				sidebar.css('transition', 'all 0.3s ease');
			});
		});

		//카테고리 전체보기 메뉴열기(아이폰+크롬 조합에서 미동작 문제 수정)
		function openMenuAll(){
			$('.left_bg').fadeIn();
			$('#left_menu').addClass('on');
			$('body').addClass('lock');
		}
		//-->
	</script>
</head>
<body>

	<!-- 상단 -->
	<div id="top" class="top">
		<div class="wrapper" id="topWrap" style="">
			<div id="header">
				<div id="gnb_button" onclick="openMenuAll()"><!-- 메뉴 전체보기--></div>
				<div id="left_menu" data-sidebar>
					<div class="left_close left_bg"><!-- 모달 BG --></div>
					<div class="left_nav">
						<? if(!$_ShopInfo->getMemid()){ //로그아웃 상태일 때 ?>
							<div class="left_login">
								<div><a href="login.php" rel="external">로그인</a></div>
								<div><a href="member_join.php" rel="external">회원가입</a></div>
							</div>
						<? }else{ //로그인 상태일 때 ?>
							<div class="left_member">
								<div class="memberText">
									<?=$_ShopInfo->memname?>님 어서오세요<br>
								</div>
							</div>
						<? } ?>
						<div class="left_etc">
							<div>
								<a href="venderfavorite.php" rel="external">
									<img src="skin/basic/svg/mypage10.svg" width="100%" style="margin-bottom: 8px;">
									<br><span>단골꽃집</span>
								</a>
							</div>
							<div>
								<a href="mypage_coupon.php" rel="external">
									<img src="skin/basic/svg/mypage2.svg" width="100%" style="margin-bottom: 8px;">
									<br><span>할인쿠폰</span>
								</a>
							</div>
							<div>
								<a href="mypage_usermodify.php" rel="external">
									<img src="skin/basic/svg/setting.svg" width="100%" style="margin-bottom: 8px;">
									<br><span>회원정보</span>
								</a>
							</div>
						</div>
						<div class="left_app_menu">
							<div class="menuGruop1">
								<div style="color: #1e1e28;font-size: 17px;font-weight: bold;padding-left:0px;" class="left_title">
									주문
								</div>
								<ul>
									<li><a href="proposalList.php" rel="external">꽃집 제안 주문내역</a></li>
									<li><a href="timesale_history.php" rel="external">바로 구매 주문내역</a></li>
									<li><a href="mypage_delivery.php" rel="external">배송지 관리</a></li>
									<!--
									<li><a href="community.php" rel="external">결제수단 관리</a></li>
									-->
								</ul>
							</div>
							<div class="menuGruop2">
								<div style="color: #1e1e28;font-size: 17px;font-weight: bold;padding-left:0px;" class="left_title">
									고객지원
								</div>
								<ul>
									<li><a href="board_list.php?board=faq" rel="external">자주묻는질문</a></li>
									<li><a href="board_list.php?board=qna" rel="external">1:1 문의하기</a></li>
									<li><a href="board_list.php?board=notice" rel="external">공지사항</a></li>
									<li><a href="logout.php" rel="external">로그아웃</a></li>
								</ul>
							</div>
							
							<!-- 기존 자료 주석
							<?
								$sql = "SELECT * FROM tbldesign "; //회사소개+브랜드스토리+어바웃어스
								$result=mysql_query($sql,get_db_conn());
								if($crow=mysql_fetch_object($result)){
									$brandstory=$crow->brandstory;
									$aboutus=$crow->aboutus;
								}
							?>

							<ul>
								<li><a href="company.php" rel="external">회사소개</a></li>
								<? if($brandstory){ ?><li><a href="brandstory.php" rel="external">Brand Story</a></li><? } ?>
								<? if($aboutus){ ?><li><a href="aboutus.php" rel="external">About Us</a></li><? } ?>
							</ul>
							-->

						</div>
					</div>
					<div class="left_close">&times;</div>
				</div>

				<div rel="external" style="margin-right: 10px;width:100%;">
					<a href="./" style="float:right;"><img src="/app/skin/basic/svg/main_logo.svg" style="height:20px;margin-top:5px; margin-right:10px;"></a>
				</div>
<!--
				<a href="#" rel="external" class="prsearch" id="prsearch"></a>
				<a href="basket.php" rel="external"id="basket"><div class="cart" value="<?=$basketcount?>"></div></a>
-->
			</div>

			<ul id="top_menu">
				<!-- 섹션상품(신/인기상품) 바로가기 -->
				<? if($nums>0 || $nums2>0){ ?>
					<? if($nums2>0){ ?><li><a href="productbest.php" rel="external">Best</a></li><? } ?>
					<? if($nums>0){ ?><li><a href="productnew.php" rel="external">New</a></li><? } ?>
				<? } ?>
				<li><a href="reviewall.php" rel="external">Review</a></li>
				<li><a href="board_list.php?board=qna" rel="external">Q&amp;A</a></li>
			</ul>
		</div>
	</div>

	<div id="mosearch" style="display:none;position:fixed;top:0px;left:0px;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:1000;">
		<div style="position:absolute;top:5%;left:0px;width:100%;text-align:right;right:1%;"><a href="#" class="close_modal" style="color:#fff;font-size:2.5em;font-weight:lighter;padding-right:5%;">×</a></div>
		<div style="display:table;width:100%;height:100%;">
			<div style="display:table-cell;font-size:0px;text-align:center;vertical-align:middle;">
				<form name="tprSearchForm" action="productsearch.php" method="get">
				<input type="hidden" name="mode" value="search" />
				<input type="hidden" name="terms" value="productname" />
				<input type="text" name="prsearch" value="" placeholder="상품을 검색하세요." style="font-size:0.9rem;height:40px;width:80%;padding:0px 5px;border:0px solid rgba(255,255,255,0.5);box-sizing:border-box;color:#b0afaf;border-bottom:1px solid #b0afaf;" />
				<input type="button" value="" id="btn_tsearch_submit" style="height:40px;width:20px;margin-top:0px;padding:0px 10px;background:url('/app/skin/basic/img/search2.png') no-repeat;background-size:100%;background-position:center;border:none;vertical-align:top;border-bottom:1px solid #b0afaf;" />
				</form>
			</div>
		</div>
	</div>

	<div style="height:78px;" id="gotop"></div>

	<script language="javascript">
		<!--
		//상품검색 - 왼쪽메뉴
		$("#btn_search_submit").click(function(){
			var _form = document.prSearchForm;

			if($("#left_menu input[name=prsearch]").val() == "" || $("#left_menu input[name=prsearch]").val() == ""){
				alert("검색어를 입력하세요.");
				$("#left_menu input[name=prsearch]").focus();
				return false;
			}else{
				$("#left_menu input[name=prsearch]").hide();
				_form.submit();
				return;
			}
		});

		//상품검색 - 상단메뉴
		$('#prsearch').click(function(){
			$('#mosearch').fadeIn(200);
		});
		$('.close_modal').click(function(){
			$('#mosearch').fadeOut(200);
		});
		$("#btn_tsearch_submit").click(function(){
			var _form = document.tprSearchForm;

			if($("#mosearch input[name=prsearch]").val() == "" || $("#mosearch input[name=prsearch]").val() == ""){
				alert("검색어를 입력하세요.");
				$("#mosearch input[name=prsearch]").focus();
				return false;
			}else{
				$("#mosearch input[name=prsearch]").hide();
				_form.submit();
				return;
			}
		});
		//-->
	</script>
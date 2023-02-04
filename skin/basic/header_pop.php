<?

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
	<meta name="viewport" content="width=420, user-scalable=no" />
	
	<meta http-equiv="page-enter" content="blendTrans(Duration=0.5)">
	<meta http-equiv="page-exit" content="blendTrans(Duration=0.5)">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="format-detection" content="telephone=no" />

	<!-- 바로가기 아이콘 -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon-precomposed" href="./upload/<?=$icon?>" />

	<link rel="stylesheet" href="/app/skin/basic/css/common.css" />
	<link href="/app/skin/basic/css/default.css" rel="stylesheet" type="text/css"/>
	<link href="/app/skin/basic/css/swiper.min.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/normalize.css" rel="stylesheet" type="text/css" media="all">
	<link href="/app/skin/basic/css/top.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/main.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/bottom.css" rel="stylesheet" type="text/css"> 
	<link href="/app/skin/basic/css/detail.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/list.css" rel="stylesheet" type="text/css">
	<link href="/app/skin/basic/css/login.css" rel="stylesheet" type="text/css">
	<link href="/css/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />

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

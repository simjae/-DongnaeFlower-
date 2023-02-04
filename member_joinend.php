<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	include "header.php"; 

	if(strlen($_ShopInfo->getMemid())>0) {
		echo "</head><body onload=\"alert('고객님께서는 로그인된 상태입니다.');location.href='/m/main.php'\"></body></html>";exit;
	}
?>

<div id="content">
	<div class="h_area2">
		<h2>회원가입 완료</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div class="joinEndText">
		<h4 <? if(file_exists($logo)==true){ ?>style="background:url('<?=$logo?>') no-repeat;background-size:auto 20px;background-position:50% 20%;"<? } ?>><?=$_data->shopname?> 쇼핑몰의 회원이 되신 것을 진심으로 축하드립니다.</h4>
		<ul>
			<li>로그인을 하시면 각종 이벤트 및 적립/할인 혜택을 적용받으실 수 있습니다.</li>

			<? if($_data->recom_url_ok =="Y"){ ?>
				<li>로그인 후 <span class="orangeFonts">[마이페이지 > 홍보관리]</span>에서 제공되는 홍보URL을 통해 타 회원이 가입할 경우 소정의 적립금을 지급해 드립니다.</li>
			<? } ?>

			<? if($_SESSION['join_reserve']){ ?>
				<li><span style="font-weight:bold">회원가입 적립금이 지급되었습니다.</span> 로그인 후 [마이페이지 > 적립금] 메뉴에서 확인하실 수 있습니다.</li>
			<? } ?>

			<? if($_SESSION['join_coupon']){ ?>
				<li><span style="font-weight:bold">회원가입 쿠폰이 발급되었습니다.</span> 로그인 후 [마이페이지 > 쿠폰내용] 메뉴에서 확인하실 수 있습니다.</li>
			<? } ?>
		</ul>

		<p>감사합니다.</p>
	</div>

	<div class="basic_btn_area">
		<A HREF="./" class="basic_button">홈으로</a>
		<A HREF="./login.php" class="basic_button">로그인</a>
	</div>
</div>

<? include "footer.php"; ?>
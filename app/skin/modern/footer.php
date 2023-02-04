<?
	//고객센터
	if(strlen($_data->info_tel)>0) {
		$tmp_tel=explode(",",$_data->info_tel);
		for($i=0;$i<count($tmp_tel);$i++) {
			$tel_number=trim($tmp_tel[$i]);
			if($i==2) break;
		}
	}else{
		$tel_number='000-000-0000';
	}

	if(strlen($_data->info_email)>0){
		$email_address=$_data->info_email;
	}else{
		$email_address='등록된 이메일 주소가 없습니다.';
	}


	//계좌번호
	if(strlen($_data->bank_account)>0){
		$bankinfo=explode(',',$_data->bank_account);
		$bankcount=count($bankinfo);

		for($i=0;$i<$bankcount;$i++){
			$bank_account=str_replace("=","",$bankinfo[$i])."<br />";
		}
	}else{
		$bank_account="등록된 입금계좌가 없습니다.";
	}
?>

	<div id="bottom">
		<div class="wrapper bot_info">
			<div>
				<h1>Customer Center</h1>
				<h2><?=$tel_number?></h2>
				<p>이메일 <?=$email_address?></p>
				<p>(주말,공휴일은 휴무입니다. 문의 게시판을 이용해주세요)</p>
			</div>
			<div>
				<h1>Accounts</h1>
				<h2><?=$bank_account?></h2>
			</div>
		</div>
		<div class="bot_copy wrapper">
			<ul>
				<li><a href="company.php" rel="external">회사소개</a></li>
				<li><a href="agreement.php" rel="external">이용약관</a></li>
				<li><a href="privercy.php" rel="external">개인정보취급방침</a></li>
				<? if(setUseVender()==true){ ?><li><a href="venderProposal.php" rel="external">입점문의</a></li><? } ?>
				<!--<li><a href="http://www.ftc.go.kr/www/bizCommList.do?key=232" target="_blank">사업자확인</a></li>-->
				<? if($configRow['use_cross_link']=="Y"){ ?><li><a href="/main/main.php?pc=ON" rel="external">PC버전</a></li><? } ?>
			</ul>
			<p><?=$copyright?></p>
		</div>
	</div>

    <?php /*
	<div class="getmallBottomBanner">
		<a href="http://www.getmall.co.kr" target="_blank"  rel="external">
		<div class="bannerWrap">
			<span class="img"><img src="/m/images/logoWhite.png"></span>
			<span class="text">겟몰, 가치가 먼저인 쇼핑몰을 만듭니다.</span>
		</div>
		</a>
	</div>
    */ ?>

	<div class="move_scroll">
		<a href="#gotop" rel="external"><div class="top"><img src="/m/skin/modern/img/icon_arrow_bottom01.png"></div></a>
		<a href="#bottom" rel="external"><div class="bottom"><img src="/m/skin/modern/img/icon_arrow_bottom01.png"></div></a>
	</div>

	<!-- jquery ui 모달창 BG 스타일 -->
	<style>
		.ui-widget-overlay {background:#aaaaaa;opacity:.3;filter:Alpha(Opacity=30);z-index:1001;}
		.ui-widget-shadow {margin:-8px 0 0 -8px;padding:8px;background:#aaaaaa;opacity: .3;filter:Alpha(Opacity=30);border-radius:8px;}
		.ui-dialog .ui-dialog-content{padding:0.5em 0em;height:100% !important;}
	</style>
	<!-- jquery ui 모달창 BG 스타일 -->

	<!-- jquery ui 모달 팝업 -->
	<div id="wrap_layer_popup" style="display:none;">
		<div id="show_contents" style="height:100%;"></div>
	</div>

	<script type="text/javascript">
		<!--
		$(function(){
			// selectbox design
			$('.basic_select').jqTransform();
		});
		//-->
	</script>

</body>
</html>
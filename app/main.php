<?
	$main_check=1;

	include_once('header.php'); 
	include_once($Dir."lib/mobile_eventpopup.php");
	include_once($Dir."lib/check_login.php");
	include_once("counter_app.php");
	
	$targetMonth = "202112";
	$mf_sql = "SELECT * FROM monthly_flower WHERE month = '".$targetMonth."'";
	$mf_result = mysql_query($mf_sql,get_db_conn());
	$imgArr = "";
	while($mf_row = mysql_fetch_object($mf_result)) {
		$imgArr .= $mf_row->cont.",";
	}
	$imgArr = substr($imgArr,0,-1);
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&display=swap" rel="stylesheet">
<script>

</script>

<style>
.fw_bold_800 {font-weight:800;}
.fw_bold_500 {font-weight:500;}
.fc_pink {color:#e61e6e;}
.fc_lgry {color:#cbcacb;}
.fc_dgry {color:#282828;}
.fs_05 {font-size:0.5rem;}
.fs_075 {font-size:0.75rem;}
.contentWrap{margin-bottom: 20px;}
.apolImg{
	display: flex;
    justify-content: center;
    margin: 20px;
}
.apolImg img{width: 90px;}
.bodyContent{padding: 10px 0;line-height: 18px;color:#282828; border-radius:15px;width:calc(100vw - 28px);background-color: #ffffff;margin-left:14px;text-align: center;}
.bCont{margin: 10px 0 10px 0; letter-spacing: 0.5px;}
.bContSp{letter-spacing: 0.5px;}
</style>
<div id="main" style="background-color:#ffe7e7;height:100%;">
	<div class="popupHeader fw_bold" style="padding-top:4%;padding-left:5%;height:40px;text-align:center;background-color:white;font-size:1.4rem;color:black;">
		<strong>동네꽃집 공지사항</strong>
	</div>
	<div class="popupBody" style="padding-top:5%;margin-top:-5px;">
		<div class="bodyTitle fc_pink fw_bold fw_bold_500" style="text-align:center;font-size:1.2rem;">
			동네꽃집 서비스 중단 안내
		</div>
		<div class="apolImg">
			<img src="/vender_m/svg/apol_ character.svg" style="width:85px;height:85px;">
		</div>
		<div class="contentWrap" style="margin-top:-5px;">
			<div class="bodyContent">
				<div class="bCont fw_bold_500 fc_dgry">"동네꽃집" 서비스 잠정 중단 안내</div>
				<div class="bCont fw_bold_500 fc_dgry">그 동안 "동네꽃집"을 사랑해 주신</div>
				<div class="bCont fw_bold_500 fc_dgry"><고객> 여러분 감사합니다.</div>
				<div class="bCont fw_bold_500 fc_dgry">이전보다 발전되고 더욱 도움이 될 수 있는</div>
				<div class="bCont fw_bold_500 fc_dgry">서비스로 한 단계 발전하기 위해</div>
				<div class="bCont fw_bold_500 fc_dgry">"동네꽃집"서비스를 3월 1일 부터</div>
				<div class="bCont fw_bold_500 fc_dgry">잠정 중단하게 되었습니다.</div>
				<div class="bCont fw_bold_500 fc_dgry">향후 고객의 니즈를</div>
				<div class="bCont fw_bold_500 fc_dgry">구체적으로 반영한 모습으로</div>
				<div class="bCont fw_bold_500 fc_dgry">여러분에게 다가갈 수 있는</div>
				<div class="bCont fw_bold_500 fc_dgry">"동네꽃집"이 되겠습니다.</div>
				</br>
				<div class="bCont fw_bold_500 fc_dgry">건강 조심하세요.</div>
				<div class="bCont fw_bold_500 fc_dgry">감사합니다.</div>
			</div>
		</div>
		<div class="bCont fw_bold_500 fc_dgry" style="margin-left:15px;margin-top:-10px;">기타 문의 사항은 카카오채널</div>
		<div class="bCont fw_bold_500 fc_dgry" style="margin-left:15px;margin-top:-10px;"><동네꽃집(디어플로리스트)>를 이용해주세요.</div>
	</div>
</div>

<?
	echo $onload;
	include_once($Dir."lib/mobile_eventlayer.php");
	//include_once('footer.php');
?>

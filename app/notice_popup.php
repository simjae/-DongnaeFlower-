<!DOCTYPE HTML>
<HTML>
<HEAD>
<meta charset="UTF-8">
<title>ID중복확인</title>
<META http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<style>
div, table, ul, li, p, h1, h2, h3, h4, h5, h6, form, select, tr, td {margin: 0px;padding: 0px;list-style: none;color: #666666;font-size: 14px;font-family:'Spoqa Han Sans Neo'!important;letter-spacing: -0.5px;color: #979ba1;font-weight:500;}
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
.bodyContent{padding: 10px 0;line-height: 18px;color:#282828; border-radius:15px;width:calc(100vw - 28px);background-color: #ffffff;margin-left:2%;text-align: center;}
.bCont{margin: 10px 0 10px 0; letter-spacing: 0.5px;}
.bContSp{letter-spacing: 0.5px;}
.btnSave {display: block;width: 200px;height: 55px;padding-top: 15px;font-weight: 700;font-size: 20px;line-height: 35px;color: #ffffff;background: #00aae2;margin: 0px auto;border-radius: 5px;text-align:center;}

</style>
<script>
	function setCookie(name, value) {
		document.cookie = escape(name) + "=" + escape(value) + "; path=/; ";
	}

	function closeApolPopupEvent() {
		setCookie("popupYN", "N");
		parent.popupClose();
	}
</script>
<body bgcolor=#ffe7e7>
	<div class="popupHeader fw_bold" style="padding-top:5%;padding-left:5%;height:50px;text-align:center;background-color:white;font-size:1.4rem;color:black;">
		동네꽃집 공지사항
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
				<!--
				<div class="bCont fw_bold_500 fc_dgry"><span class="bContSp fw_bold_800">2021년 10월 27일(수)</span><br>어플 수정 사항 공지 드립니다.</div>
				<div class="bCont fw_bold_500 fc_dgry">사장님들께서 내 꽃집 소개,마감 할인 등<br>이미지 업로드시,<span class="bContSp  fw_bold_800">사진이 돌아가는 현상</span>이<br>종종 발생했습니다.</div>
				<div class="bCont fw_bold_500 fc_dgry"><span class="bContSp fw_bold_800">10월 27일(수) 오후 7시 30분</span>이후로<br>해당문제를 해결했고,<br>문제없이 사진을 업로드 하실 수 있습니다.</div>
				<div class="bCont fw_bold_500 fc_dgry">기존에 문제가 있으셨던 사장님들은<br>사진을<span class="bContSp  fw_bold_800">삭제 후,재업로드</span>해주시면 됩니다:&#41;</div>
				<div class="bCont fw_bold_500 fc_dgry">감사합니다!</div>
				-->
				<div class="bCont fw_bold_500 fc_dgry"><strong>"동네꽃집" 서비스 잠정 중단 안내</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>그 동안 "동네꽃집"을 사랑해 주신</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong><플로리스트/고객> 여러분 감사합니다.</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>이전보다 발전되고 더욱 도움이 될 수 있는</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>서비스로 한 단계 발전하기 위해</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>"동네꽃집"서비스를 3월 1일 부터</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>잠정 중단하게 되었습니다. </strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>향후 플로리스트/고객의 니즈를</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>구체적으로 반영한 모습으로</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>여러분에게 다가갈 수 있는</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>"동네꽃집"이 되겠습니다.</strong></div>
				</br>
				<div class="bCont fw_bold_500 fc_dgry"><strong>건강 조심하세요.</strong></div>
				<div class="bCont fw_bold_500 fc_dgry"><strong>감사합니다.</strong></div>
			</div>
		</div>
		<div class="bCont fw_bold_500 fc_dgry" style="margin-left:15px;margin-top:-10px;">기타 문의 사항은 카카오채널</div>
		<div class="bCont fw_bold_500 fc_dgry" style="margin-left:15px;margin-top:-10px;"><동네꽃집(디어플로리스트)>를 이용해주세요.</div>
	</div>
	<div class="btnWrap">
		<a onClick="closeApolPopupEvent();" id="closeBtn" class="btnSave" style="position:fixed;left:0;bottom:10;width:100%;background-color:#e61e6e;border-radius:0px!important;"><span>확인 후 닫기</span></a>
	</div>
</body>
</html>

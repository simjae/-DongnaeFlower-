<?
$Dir="../";
include_once($Dir."lib/init.php");
// include_once($Dir."lib/init.debug.php");
//이게 디버그확인해준는 php
include_once($Dir."lib/lib.php");

if (strlen($_ShopInfo->getMemid()) == 0) {
	echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');\"></body></html>";
	exit;
	Header("Location:" . $Dir . FrontDir . "login.php?chUrl=" . getUrl());
	exit;
}
$type = $_POST['type'];
$idx = $_POST['idx'];
$vidx = isset($_GET['vidx'])?trim($_GET['vidx']):"";
?>
<style>
.f_left{
	float: left;
}
.f_right{
	float: right;
}
.p_row{
	margin: 10px 0 10px 0;
	display: flex;
    justify-content: space-between;
}
.finalPayNum{font-size: 20px; font-weight: 500; color: #de1e6e}
.bulletinNum{font-weight: 500; color: #de1e6e}
.h_area2 h2 {
    display: block;
    background: #ffffff;
    text-align: center;
    font-size: 1.6em;
    padding: 8px 12px;
    color: #000000;
    font-weight: 500;
}
.contWrap{
	margin: 20px;
	font-size: 15px;
	font-weight: 400;
	color: #1e1e28;
}
.payHistory{
	padding: 20px;
}
.orderInfo{
	padding: 20px;
}
.pointHistory{
	padding: 20px;
}
.payTitle{
	font-size: 18px;
    font-weight: bold;
}
.payGruop{
	margin:20px 0 20px 0;
}
.addrS{text-align: end;}
.pointBox{
	border: solid 1px #cac9ca;
    border-radius: 10px;
    padding: 10px 30px 60px 30px;
}
.pointM{
	text-align: center;
}
.bulletin{
	margin: 20px;
    text-align: center;
}

</style>
<div id="content">
	<div class="h_area2">
		<h2>주문이 완료 되었습니다</h2>
	</div>
	<div class="contWrap">
		<div class="payHistory">
			<div class="payTitle">최종 결제내역</div>
			<div class="payGruop">
				<div class="date p_row">
					<div>일시</div>
					<div>$2021년 09월 12일</div>
				</div>
				<div class="orderPrice p_row">
					<div>주문금액</div>
					<div><span>$50,000</span><span>원</span></div>
				</div>
				<div class="discountPrice p_row">
					<div>할인금액</div>
					<div><span>$-10,000</span><span>원</span></div>
				</div>
				<div class="finalPay p_row">
					<div>최종결제금액</div>
					<div><span class="finalPayNum">$33,000</span><span>원</span></div>
				</div>
			</div>
		</div>
		<div class="orderInfo">
			<div class="payTitle">주문자정보</div>
			<div class="payGruop">
				<div class="nmae p_row">
					<div>이름</div>
					<div>$심재형</div>
				</div>
				<div class="ph p_row">
					<div>전화번호</div>
					<div>$01077916041</div>
				</div>
				<div class="addr p_row">
					<div>주소</div>
					<div>
						<div class="addrM">$서울시 송파구 문정동 10-5</div>
						<div class="addrS">$201호</div>
					</div>
				</div>
				<div class="payWay p_row">
					<div>결제방법</div>
					<div>$신용카드</div>
				</div>
			</div>
		</div>
		<div class="pointHistory">
		<div class="payTitle">할인 및 적립내역</div>
			<div class="payGruop">
				<div class="pointBox">
					<div class="pointM">해당사항이 없습니다.</div>
				</div>
				<div class="bulletin">
					<span>*상품 구입에 따른 적립금 </span><span class="bulletinNum">$100</span><span>원은<br>배송과 함께 바로 적립됩니다.</span>
				</div>
			</div>
		</div>
	</div>










</div>

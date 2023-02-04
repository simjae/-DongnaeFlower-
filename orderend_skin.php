
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
    padding: 15px;
}
.pointBox .orderend_table1{
	width:100%;
}

.pointBox .orderend_table1 th{
	width:60px;
	padding:5px;
}
.pointBox .orderend_table1 td{
	text-align:right;
	padding:5px;
}
.pointM{
	mrgin:30px;
	text-align: center;
}
.bulletin{
	margin: 20px;
    text-align: center;
}
.contWrap .orderend_close_wrap{text-align:center; margin:10px 0px 20px 0px;}

/*팝업 css*/
#orderPopWrap{
	position: fixed;
    box-sizing: border-box;
    background: rgba(0, 0, 0, 0.7);
    z-index: 910;
    width: 100%;
    height: 100%;
    border: 0px solid rgb(221, 221, 221);
    left: 0%;
    top: 0%;
}
.orderPopGroup{
	position: absolute;
    top: 25%;
    width: 100%;
	height: 440px;
    min-width: 300px;
    background: #ffffff;
    border-radius: 20px;
    text-align: center;
}
.popTitleGroup{
	padding: 20px 0;
	color: #282828;
	font-size: 1.4em;
	font-weight: 900;
}
.popContetGroup{
	padding: 20px 0;
	border-bottom: 1px solid #d3d3d3;
	border-top: 1px solid #d3d3d3;
}
.popImg{
	width: 65px;
}
.popContentTitleGroup{
	padding: 10px 0;
}
.popContentTitle{
	color: #282828;
    font-size: 1.2em;
    font-weight: 900;
    margin: 5px 0;
}
.popContentSubTitleGroup{
	padding: 10px 0;
}
.popContentSubTitle{
	color: #282828;
    font-size: 1.1em;
    font-weight: 400;
    margin: 3px 0;
}
.popBtnGroup{
	padding: 20px 0;
}
.popBtnClose{
	background-color: #e61e6e;
    border-radius: 30px;
    color: #ffffff;
    font-weight: 900;
    font-size: 1.2em;
    text-align: center;
    width: calc(100vw - 40px);
    margin: 20px;
    padding: 15px 0;
}
.popBtnGroup .checkbox{
	display: none;
}
.popBtnGroup .label{
	position: relative;
	font-size: 1.2em;
	padding-left: 25px;
	user-select: none;
}
.popBtnGroup .check-mark{
	width: 15px;
	height: 15px;
	background-color: #ffffff;
    border: 1px solid #d3d3d3;
	position: absolute;
	left:0;
	display: inline-block;
	top: 0;
border-radius: 50%;
}
.popBtnGroup .label .checkbox:checked + .check-mark{
	background-color: #e61e6e  ;
	transition: .1s;
}
.popBtnGroup .label .checkbox:checked + .check-mark:after{
	content: "";
	position: absolute;
	width: 7px;
	transition: .1s;
	height: 5px;
	background: #e61e6e  ;
	top:40%;
	left:50%;
	border-left: 2px solid #fff;
	border-bottom: 2px solid #fff;
	transform: translate(-50%, -50%) rotate(-45deg);  
	}
/*팝업 css*/
</style>
<div class="h_area2"><h2>주문이 완료 되었습니다.</h2></div>
<div class="contWrap">
	<?
		$sql = "SELECT productcode,productname,price,reserve,opt1_name,opt2_name,ordprd_optidx,tempkey,addcode,quantity,order_prmsg,selfcode,package_idx,assemble_idx,assemble_info ";
		$sql.= "FROM tblorderproduct WHERE ordercode='".$ordercode."' ORDER BY productcode ASC ";
		$result=mysql_query($sql,get_db_conn());
		$sumprice=0;
		$sumreserve=0;
		$totprice=0;
		$totreserve=0;
		$totquantity=0;
		$cnt=0;
		unset($etcdata);
		unset($prdata);
		while($row=mysql_fetch_object($result)) {
			$optvalue="";
			if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
				$optioncode=$row->opt1_name;
				$row->opt1_name="";
				$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."' ";
				$sql.= "AND opt_idx='".$optioncode."' ";
				$result2=mysql_query($sql,get_db_conn());
				if($row2=mysql_fetch_object($result2)) {
					$optvalue=$row2->opt_name;
				}
				mysql_free_result($result2);
			}

			$isnot=false;
			if (substr($row->productcode,0,3)!="999") {
				if(substr($row->productcode,0,3)!="COU") {
					$no++;
					$isnot=true;
					$totquantity+=$row->quantity;
				}
				$sumreserve=$row->reserve*$row->quantity;
				$totreserve+=$sumreserve;
			}
			if(ereg("^(COU)([0-9]{8})(X)$",$row->productcode)) {				#쿠폰
				$etcdata[]=$row;
				continue;
			} else if(ereg("^(9999999999)([0-9]{1})(X)$",$row->productcode)) {
				#99999999999X : 현금결제시 결제금액에서 추가적립/할인
				#99999999998X : 에스크로 결제시 수수료
				#99999999997X : 부가세(VAT)
				#99999999990X : 상품배송비
				$etcdata[]=$row;
				continue;
			} else {															#진짜상품
				$prdata[]=$row;
			}
			$sumprice=$row->price*$row->quantity;
			$totprice+=$sumprice;

		}
		mysql_free_result($result);
		
		$dc_price=(int)$_ord->dc_price;
		$salemoney=0;
		$salereserve=0;
		$dc_article = '';
		$dc_state = false;
		if($dc_price<>0) {
			$dc_state = true;
			if($dc_price>0) $salereserve=$dc_price;
			else $salemoney=-$dc_price;
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
				$sql.= "WHERE a.id='".$_ord->id."' AND b.group_code=a.group_code AND MID(b.group_code,1,1)!='M' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$group_name=$row->group_name;
				}
				mysql_free_result($result);
			}
			
			if($salemoney > 0){
				$dc_article = '그룹할인';
			}
			if($salereserve>0){
				$dc_article = '그룹적립';
			}
		}
	?>
	<div class="payHistory">
		<div class="payTitle">최종 결제내역</div>
		<div class="payGruop">
			<div class="date p_row">
				<div>일시</div>
				<div><?=substr($ordercode,0,4)?>년 <?=substr($ordercode,4,2)?>월 <?=substr($ordercode,6,2)?>일</div>
			</div>
			<div class="orderPrice p_row">
				<div>주문금액</div>
				<div><span><?=number_format($totprice)?></span><span>원</span></div>
			</div>
			<?
			$plus_etcprice=0;
			$etcreserve=0;
			$tot_etcdata=0;
			$pr_article = '';
			
			$tot_etcdata=count($etcdata);
			$pr_article = '배송비';
			if($tot_etcdata > 0){
				for($i=0;$i<$tot_etcdata;$i++) {

					if(($etcdata[$i]->productcode == "99999999998X") || ($etcdata[$i]->productcode=="99999999990X") || ($etcdata[$i]->productcode=="99999999997X")){
						if($etcdata[$i]->productcode == "99999999998X"){
							$plus_etcprice+=$etcdata[$i]->price;
							$etcreserve+=$etcdata[$i]->reserve;
							$pr_article = '결제 수수료';
						}
						if($etcdata[$i]->productcode=="99999999997X"){
							$pr_article = '부가세(VAT)';
						}
					}
				}
				$plus_etcprice+=$_ord->deli_price;
			?>
				<div class="orderPrice p_row">
					<div><?=$pr_article?></div>
					<div><span><?=number_format($plus_etcprice)?></span><span>원</span></div>
				</div>
			<?}?>
			<div class="discountPrice p_row">
				<div>할인금액</div>
				<div>
					
					<?
						$tot_dc_price = 0;
						$tot_dc_price = $salemoney+$_ord->reserve;
					?>
					<span>-<?=number_format($tot_dc_price)?></span><span>원</span>
				</div>
			</div>
			<div class="finalPay p_row">
				<div>최종결제금액</div>
				<div>
					<?
						$tot_price = 0;
						$tot_price = ($totprice+$plus_etcprice)-$tot_dc_price;
					?>
				<span class="finalPayNum"><?=number_format($tot_price)?></span><span>원</span></div>
			</div>
		</div>
	</div>
	<div class="orderInfo">
		<div class="payTitle">주문자정보</div>
		<div class="payGruop">
			<div class="nmae p_row">
				<div>이름</div>
				<div><?=$_ord->sender_name?></div>
			</div>
			<div class="ph p_row">
				<div>전화번호</div>
				<div><?=$_ord->sender_tel?></div>
			</div>
			<div class="ph p_row">
				<div>이메일</div>
				<div><?=$_ord->sender_email?></div>
			</div>
			<div class="payWay p_row">
				<div>결제방법</div>
				<div>
					<?
						$orderend_paytype='';
						if(preg_match("/^(V|C|P|M|B|O|Q){1}/", $_ord->paymethod)){
							
							$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌");

							$orderend_paytype = $arpm[substr($_ord->paymethod,0,1)];
						}else{
							$orderend_paytype = "거래실패";
						}
					?>
					<?=$orderend_paytype?>
				</div>
			</div>
		</div>
	</div>
	<div class="pointHistory">
	<div class="payTitle">할인 및 적립내역</div>
		<div class="payGruop">
			<div class="pointBox">
				<?
				
				if($dc_price<>0) {
				?>
					<table cellpadding="0" cellspacing="0" class="orderend_table1">
					<tr>
						<th>항목</th>
						<td><?=$dc_article?> (<?=$group_name?>)</td>
					</tr>
					<tr>
						<th>금액</th>
						<td>
							<?=($salemoney>0?"-".number_format($salemoney).'원':'')?>
							<?=($salereserve>0?"+ ".number_format($salereserve).'원':'')?>
						</td>
					</tr>
					<tr>
						<th <?if($_ord->reserve>0){?>class="orderend_info_last"<?}?>>적용대상</th>
						<td <?if($_ord->reserve>0){?>class="orderend_info_last"<?}?>>주문서 전체 적용</td>
					</tr>
					</table>
				<?}
				if($_ord->reserve>0){
					$dc_state = true;
				?>
					<table cellpadding="0" cellspacing="0" class="orderend_table1">
					<tr>
						<th>항목</th>
						<td>적립금 사용</td>
					</tr>
					<tr>
						<th>금액</th>
						<td>- <?=number_format($_ord->reserve)?> 원</td>
					</tr>
					<tr>
						<th>적용대상</th>
						<td>주문서 전체 적용</td>
					</tr>
					</table>
				<?}?>
				<?if($dc_state == false){?>
					<div class="pointM">해당사항이 없습니다.</div>
				<?}?>
			</div>
			<?if($sumreserve > 0 ){?>
				<div class="bulletin">
					<span>*상품 구입에 따른 적립금 </span><span class="bulletinNum"><?=number_format($sumreserve)?></span><span>원은<br>배송과 함께 바로 적립됩니다.</span>
				</div>
			<?}?>
		</div>
	</div>
	<div class="orderend_close_wrap">
		<div style="margin:15px;text-align:center;">
			<img src="/lib/barcode.php?str=<?=$_ord->ordercode?>" >
		</div>
		<button type="button" onClick="orderendClose();" class="basic_button" style="padding:0.5em 150px;height:auto;">확인</button>
	</div>
</div>


<div id="orderPopWrap">
	<div class="orderPopGroup">
		<div class="popTitleGroup">주문 완료</div>
		<div class="popContetGroup" >
			<div>
				<img class="popImg" src="/app/skin/basic/svg/order_popup_loading.gif" alt="">
			</div>
			<div class="popContentTitleGroup">
				<div class="popContentTitle">꽃집에서 주문을 확인하고 있어요!</div>
				<div class="popContentTitle">주문이 확정되는 데로</div>
				<div class="popContentTitle">알려드릴게요</div>
			</div>
			<div class="popContentSubTitleGroup">
				<div class="popContentSubTitle">10분내로 주문확정이 이뤄지지 않으면</div>
				<div class="popContentSubTitle">주문이 자동으로 취소되니,걱정하지 마세요</div>
			</div>
		</div>
		<div class="popBtnGroup">
			<div style="text-align: start;padding-left: 20px;">
				<label class="label">
					<span>카카오톡으로 주문 확정 알림 받기</span>
					<input type="checkbox" checked="checked" class="checkbox"/>
					<span class="check-mark"></span>
				</label>
			</div>
			<div class="popBtnClose" onclick="orderPopClose()">확인 후 닫기</div>
		</div>
	</div>
</div>


<script>
	function orderendClose(){
		location.href="./";
	}
	function orderPopClose(){
		$('#orderPopWrap').hide();
	}
</script>

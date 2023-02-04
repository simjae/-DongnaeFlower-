<?
//include_once('header.php');
?>
<style>
.orderList{
	margin: 0 10px;
	padding: 20px 10px 20px 10px;
    border-bottom: solid 1px #ebebeb;
    line-height: 2;
}
.imgWrap{
	margin-right: 15px;
    width: 100px;
	float: left;
}
.infoWrap{
	float: left;
}
.infoDate{
	font-size: 12px;
}
.infoName{
	color: #00000b;
    font-size: 18px;
    font-weight: 500;
}
.btnWrap >div>span{
	cursor: pointer;
	color: #16161c;
	border-radius: 20px;
    padding: 2px 5px 2px 5px;
    box-shadow: 1px 1px 2px 1px #e2e2e2;
	font-size: 11px;
}
.btnWrap{
	width: 120px;
}
.infoBtn{
	float: left;
	margin-right: 7px;
}
.priceWrap{
    text-align: end;
    padding: 34px 0 34px 60px;
    font-size: 15px;
    font-weight: 500;
    color: #00000b;
}
</style>
<div id="content">
	<?
	$reviewSQL = "SELECT toi.id, tp.vender, tvs.brand_name, tp.ordercode, tp.productcode, tp.productname, toi.reserve, tp.date, tpc.maximage ";
	$reviewSQL .= "FROM tblorderproduct AS tp ";
	$reviewSQL .= "LEFT JOIN tblorderinfo AS toi ON tp.ordercode = toi.ordercode ";
	$reviewSQL .= "LEFT JOIN tblproduct AS tpc ON tpc.productcode = tp.productcode ";
	$reviewSQL .= "LEFT JOIN tblvenderstore AS tvs ON tp.vender = tvs.vender ";
	$reviewSQL .= "where toi.id = '".$_ShopInfo->getMemid()."' AND tp.productcode NOT LIKE '%99999999991X%' ";
	
	$reviewResult = mysql_query($reviewSQL,get_db_conn());
	while($reviewRow = mysql_fetch_object($reviewResult)){
		$brand_name=$reviewRow->brand_name;
		$reserve=$reviewRow->reserve;
		$vender= $reviewRow->vender;
		$maximage=$reviewRow->maximage;
		$date=$reviewRow->date;
		$fDate = date("Y년 m월 d일", strtotime( $date ) );
		$hour = date("H",strtotime( $date ));
			if ($hour > 12) {
				$hour = $hour - 12;
				$result = " 오후 " . $hour."시";
			} else {
				$result = " 오전 " . $hour."시";
			}
	?>
	<div class="orderList">
		<div class="imgWrap">
			<img style="width: 100px; height: 90px;" src="../data/shopimages/product/9000000000000000031.jpg" alt="$이미지">
		</div>
		<div class="infoWrap">
			<div class="infoDate"><?=$fDate,$result?></div>
			<div class="infoName"><?=$brand_name?></div>
			<div class="btnWrap">
				<div class="infoBtn">
					<span onclick="iframePopupOpen('/app/venderinfo.php?vidx=<?=$vender?>&pagetype=pop')">꽃집정보</span>
				</div>
				<div class="reviewBtn">
				<span style="color: #DC2872;">리뷰남기기</span>
				</div>
			</div>
		</div>
		<div class="priceWrap"><?=$reserve?>원</div>
	</div>
<?
	}
?>
</div>
<script>
	$(".btnWrap>div>span").click(function(){
		console.log(this);
	});

</script>

<?
//include_once('footer.php');
?>

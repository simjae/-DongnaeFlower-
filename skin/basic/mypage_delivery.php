<?
$exOk=0;
$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);
if($_ShopInfo->getMemid()==$_VenderInfo->getId()){
	$Vender = 1;
	$exOk=1;
}

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');\"></body></html>";exit;
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$type=$_POST['type'];
$idx=$_POST['idx'];

if($type=='delete'){
	$delSql="DELETE FROM tblorderreceiver WHERE idx='".$idx."'";
	mysql_query($delSql,get_db_conn());
	
	$updSql="UPDATE tblorderreceiver SET repAddrFlg='1' ";
	$updSql.="WHERE ";
	$updSql.="member_id='".$_ShopInfo->getMemid()."' AND ";
	$updSql.="idx=( ";
	$updSql.="SELECT idx FROM ( ";
	$updSql.="SELECT MIN(idx) FROM tblorderreceiver WHERE ";
	$updSql.="member_id='".$_ShopInfo->getMemid()."' ";
	$updSql.=")tmp)";
	mysql_query($updSql,get_db_conn());
	
	$onload="<script>alert('배송지 삭제가 완료되었습니다.');</script>";
} else if ($type=='repAddr') {
	$updSql_0="UPDATE tblorderreceiver SET repAddrFlg='0' WHERE member_id='".$_ShopInfo->getMemid()."'";
	mysql_query($updSql_0,get_db_conn());
	
	$updSql_1="UPDATE tblorderreceiver SET repAddrFlg='1' ";
	$updSql_1.="WHERE ";
	$updSql_1.="member_id='".$_ShopInfo->getMemid()."' AND ";
	$updSql_1.="idx='".$idx."'";
	mysql_query($updSql_1,get_db_conn());
}

$msql="SELECT * FROM tblorderreceiver WHERE member_id='".$_ShopInfo->getMemid()."' ORDER BY idx DESC ";
$mresult=mysql_query($msql,get_db_conn());
$mnums=mysql_num_rows($mresult);
?>

<script language="javascript">
	<!--
	function DeleteForm(idx){
		if(!confirm("배송지를 삭제하시겠습니까?")){
			return;
		}
		document.form3.type.value='delete';
		document.form3.idx.value=idx;
		document.form3.submit();
	}
	
	function RepAddrForm(idx){
		if(!confirm("대표주소로 설정하시겠습니까?")){
			return;
		}
		document.form3.type.value='repAddr';
		document.form3.idx.value=idx;
		document.form3.submit();
	}
	//-->
</script>
<style>
	.input{height:30px;padding-left:5px;box-sizing:border-box;}
	.btn_s_line{display:inline-block;line-height:30px;padding:0px 5px;border:1px solid #ddd;box-sizing:border-box;}
	#insert_delivery_pop{display:none;-webkit-transition: all 0.3s ease;-moz-transition: all 0.3s ease;-ms-transition: all 0.3s ease;-o-transition: all 0.3s ease;transition: all 0.3s ease;}
	.wrap_insert_delivery{position:fixed;top:0px;left:0px;display:table;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:999;}
	#cell_insert_delivery{display:table-cell;border:1px solid #ddd;box-sizing:border-box;vertical-align:middle;-webkit-transition: all 0.3s ease;-moz-transition: all 0.3s ease;-ms-transition: all 0.3s ease;-o-transition: all 0.3s ease;transition: all 0.3s ease;}

	.board_list td{border-bottom:1px solid #9e9e9e36;}

	.modify_delivery, .delete_delivery, .repAddr_delivery{display:block;padding:4px 4px;border:1px solid #ddd;text-align:center;}
	.modify_delivery{margin-bottom:4px; margin-top:4px;}
	.repAddr_delivery{margin-bottom:4px; margin-top:4px;}
	.delete_delivery{margin-bottom:4px;}
	.adrBox{
	margin:14px 14px 4px 14px;
	width:auto;
	border-radius: 10px;
	border:solid #b9b8b8 1px;
	overflow:hidden;
	box-shadow: 3px 3px 5px 0px RGBA(0,0,0,0.05);
	}
	.adrBox .content01{
		display: flex;
		justify-content: space-between;
		margin: 20px;
	}
	.adrBox .content01 .textBox .title{
	font-weight:500;
	font-size:1.4em;
	padding: 5px 0px 0px 0px;
	color: #000000;
	}
	
	
</style>


<div id="content">
	<div class="h_area2">
		<h2>배송지 설정</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div class="adrBox" onclick="location.href='mypage_delivery.php?type=insert'">
		<div class="content01">
			<div class="imageBox">
				<img src="/app/skin/basic/svg/adr_add.svg" style="width: 20px;">
			</div>
			<div class="textBox" style="position: relative;right: 65px;">
				<div class="title">
					새로운 배송 주소 추가 
				</div>
			</div>
			<div class="imageBox" style="position: relative;top: 2px;">
				<img src="/app/skin/basic/svg/adr_arrow.svg" style="width: 12px;">
			</div>
		</div>
	</div>
<!-- 	
	<div style="width:75%;margin:25px auto;text-align:center;">
		<h4 style="line-height:20px;font-weight:normal;">상품 주문시 사용하는 배송지 정보를<br />미리 등록/관리할 수 있습니다.</h4>
		<a href="mypage_delivery.php?type=insert" style="display:inline-block;*display:inline;*zoom:1;margin-top:10px;"><span class="basic_button" style="padding:0px 10px;">내 배송지 등록하기</span></a>
	</div> -->

	<form name="form3" action="<?=$_SERVER[PHP_SELF]?>" method="post">
		<input type="hidden" name="type" />
		<input type="hidden" name="idx" />

		<table border="0" cellpadding="0" cellspacing="0" width="90%" style="margin:0 auto" class="board_list">
			<colgroup>
				<col width="" />
				<col width="50" />
			</colgroup>
			<?
				$i=0;
				while($mrow=mysql_fetch_object($mresult)){
					$receiver_nick=stripslashes($mrow->receiver_nick); 
					$receiver_addr=stripslashes($mrow->receiver_addr);
					$receiver_addr_temp=explode("=",$receiver_addr);
					$receiver_addr1= $receiver_addr_temp[0];
					$receiver_addr2= stripslashes($receiver_addr_temp[1]);
			?>
			<tr>
				<td style="display: flex;padding: 25px 0px 25px 0px;">
					<div style="padding:0px 20px 0px 12px;">
					<img src="/app/skin/basic/svg/adr_list.svg" style="width: 20px;">
					</div>
					<div>
					<p class="title" style="padding: 0px; margin-bottom:5px; font-weight: 500; font-size: 1.4em; color:#000000"><?="".$receiver_nick." "?></p>
					<p class="title" style="padding: 0px; color:#777777"><?="".$receiver_addr1." "?></p>
					<p class="title" style="padding: 0px; color: #777777"><?="".$receiver_addr2?></p>
					<p class="writer" style="padding: 0px;"><?=$mrow->receiver_name?><?=str_replace('-',' ',$mrow->receiver_tel1)?></p>
					</div>
				</td>
				<td align="right">
					<?
						$repAddrFlg=$mrow->repAddrFlg;
						if ($repAddrFlg==="1") {
							$bgcolor="#2ECC40";
						} else {
							$bgcolor="#BDBDBD";
						}
					?>
					<a href="javascript:RepAddrForm('<?=$mrow->idx?>')" class="repAddr_delivery" style="background-color:<?=$bgcolor?>; color:white"><span class="btn_s_line2" style="font-size:0.8em !important;">대표주소</span></a>
					<a href="mypage_delivery.php?type=modify&idx=<?=$mrow->idx?>" class="modify_delivery"><span class="btn_s_line2">수정</span></a>
					<a href="javascript:DeleteForm('<?=$mrow->idx?>')" class="delete_delivery"><span class="btn_s_line2">삭제</span></a>
				</td>
			</tr>
			<?
					$i++;
				}

				if($mnums<1){
					echo "<tr><td colspan='6' align='center' style='padding:20px 0px;border-bottom:1px solid #eee;'>등록된 배송지가 없습니다.</td></tr>";
				}
			?>
		</table>
	</form>
</div>

<?=$onload?>
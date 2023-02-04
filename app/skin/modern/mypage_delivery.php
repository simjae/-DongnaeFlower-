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
	$sql="DELETE FROM tblorderreceiver WHERE idx=".$idx." ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('배송지 삭제가 완료되었습니다.');</script>";
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
	//-->
</script>

<style>
	.input{height:30px;padding-left:5px;box-sizing:border-box;}
	.btn_s_line{display:inline-block;line-height:30px;padding:0px 5px;border:1px solid #ddd;box-sizing:border-box;}
	#insert_delivery_pop{display:none;-webkit-transition: all 0.3s ease;-moz-transition: all 0.3s ease;-ms-transition: all 0.3s ease;-o-transition: all 0.3s ease;transition: all 0.3s ease;}
	.wrap_insert_delivery{position:fixed;top:0px;left:0px;display:table;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:999;}
	#cell_insert_delivery{display:table-cell;border:1px solid #ddd;box-sizing:border-box;vertical-align:middle;-webkit-transition: all 0.3s ease;-moz-transition: all 0.3s ease;-ms-transition: all 0.3s ease;-o-transition: all 0.3s ease;transition: all 0.3s ease;}

	.board_list td{border-top:1px solid #eee;}

	.modify_delivery, .delete_delivery{display:block;padding:5px 4px;border:1px solid #ddd;text-align:center;}
	.modify_delivery{margin-bottom:4px;}
</style>


<div id="content">
	<div class="h_area2">
		<h2>배송지 등록</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div style="width:75%;margin:25px auto;text-align:center;">
		<h4 style="line-height:20px;font-weight:normal;">상품 주문시 사용하는 배송지 정보를<br />미리 등록/관리할 수 있습니다.</h4>
		<a href="mypage_delivery.php?type=insert" style="display:inline-block;*display:inline;*zoom:1;margin-top:10px;"><span class="basic_button" style="padding:0px 10px;">내 배송지 등록하기</span></a>
	</div>

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
					$receiver_addr=stripslashes($mrow->receiver_addr);
					$receiver_addr_temp=explode("=",$receiver_addr);
					$receiver_addr1= $receiver_addr_temp[0];
					$receiver_addr2= stripslashes($receiver_addr_temp[1]);
			?>
			<tr>
				<td>
					<p class="title"><?="[".$mrow->receiver_post."] ".$receiver_addr1." ".$receiver_addr2?></p>
					<p class="writer"><?=$mrow->receiver_name?><?=($mrow->receiver_tel2?" / ":"").$mrow->receiver_tel2?></p>
				</td>
				<td align="right">
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
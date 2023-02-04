<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	include_once($Dir."lib/venderlib.php");
	$exOk=0;
	$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);
	if($_ShopInfo->getMemid()==$_VenderInfo->getId()){
		$Vender = 1;
		$exOk=1;
	}
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_mdata=$row;
		if($row->member_out=="Y") {
			$_ShopInfo->SetMemNULL();
			$_ShopInfo->Save();
			echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
		}

		if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
			$_ShopInfo->SetMemNULL();
			$_ShopInfo->Save();
			echo "<html><head><title></title></head><body onload=\"alert('처음부터 다시 시작하시기 바랍니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
		}
			if($row->wholesaletype=="Y") $exOk=1;

	}
	mysql_free_result($result);


	if($_data->memberout_type=="N") {
		echo "<html><head><title></title></head><body onload=\"alert('회원탈퇴를 하실 수 없습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.');history.go(-1)\"></body></html>";exit;
	}

	include "header.php";
?>

<script type="text/javascript">
	<!--
	function CheckForm() {
		if(confirm("회원탈퇴를 하시겠습니까?")==true) {
			document.form1.type.value="exit";
			document.form1.submit();
		}
	}
	//-->
</script>

<div id="content">
	<div class="h_area2">
		<h2>회원탈퇴</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
	<input type="hidden" name="type" />
	<input type="hidden" name="id" value="<?=$_ShopInfo->getMemid()?>" />
	<div style="width:75%;margin:25px auto;text-align:center;">
		<h4 style="line-height:20px;font-weight:normal;">
		<?if($_data->memberout_type=="Y"){?>
			<span class="font-red bold"><?=$_data->shopname?></B> 쇼핑몰</span> 탈퇴신청을 하실 경우 쇼핑몰 운영자가 확인 후 탈퇴처리를 해드리며,<BR>
			<?}else if($_data->memberout_type=="O"){?>
			<span class="font-red bold"><?=$_data->shopname?></B> 쇼핑몰</span> 회원탈퇴를 하실 경우<br />온라인에서 즉시 처리되며,<BR />
			<?}?>
			<B><?=$_ShopInfo->getMemname()?> (<?=$_ShopInfo->getMemid()?>)</B> 회원님께서 해당 ID로 이용하셨던 모든 서비스의 이용이 불가능하게 됩니다.<BR /><BR />
			또한 가입 시 입력하신 신상정보는 모두 삭제되며 그동안 적립하셨던 적립금, 쿠폰 등은 이용하실 수 없습니다.<BR />
			다만, 주문하신 내역에 대해서는 삭제처리가 안되오니 이점 양지하시기 바랍니다.<BR /><BR />
			회원탈퇴를 하시겠습니까?
		</h4>
		<a href="javascript:CheckForm();" style="display:inline-block;*display:inline;*zoom:1;margin-top:10px;"><span class="basic_button" style="padding:0px 10px;">탈퇴하기</span></a>
	</div>
	</form>
</div>


<? include "footer.php";?>
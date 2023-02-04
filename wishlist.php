<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	//옵션 클래스 2016-09-26 Seul
	include_once($Dir."lib/class/option.php");
	$optClass = new Option;

	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:login.php?chUrl=".getUrl());
		exit;
	}

	include "header.php";

	$mode=$_POST["mode"];
	$sels=(array)$_POST["sels"];
	$wish_idx=$_POST["wish_idx"];
	$up_marks=(int)$_POST["up_marks"];
	$up_memo=$_POST["up_memo"];

	if($mode=="memo" && strlen($wish_idx)>0) {	//구매우선순위 메모
		$sql = "UPDATE tblwishlist SET ";
		$sql.= "marks	= '".$up_marks."', ";
		$sql.= "memo	= '".$up_memo."' ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND wish_idx='".$wish_idx."' ";
		@mysql_query($sql,get_db_conn());
		$onload="<script>alert('저장하였습니다.');</script>";
	} else if($mode=="delete" && count($sels)>0) {	//상품 삭제
		$sellist="";
		for($i=0;$i<count($sels);$i++) {
			$sellist.=$sels[$i].",";
		}
		$sellist=substr($sellist,0,-1);
		if(strlen($sellist)>0) {
			$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getmemid()."' AND wish_idx IN (".$sellist.") ";
			@mysql_query($sql,get_db_conn());
			$onload="<script>alert('선택하신 상품을 위시리스트에서 삭제하였습니다.');</script>";
		}
	}

	$sort=$_REQUEST["sort"];
	$listnum=(int)$_REQUEST["listnum"];
	if($listnum<=0) $listnum=10;
	if(!preg_match("/^(date_desc|marks_desc|price_desc|price|name)$/",$sort)) {
		$sort="date_desc";
	}

	//리스트 세팅
	$setup[page_num] = 10;
	$setup[list_num] = $listnum;

	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];

	if ($block != "") {
		$nowblock = $block;
		$curpage  = $block * $setup[page_num] + $gotopage;
	} else {
		$nowblock = 0;
	}

	if (($gotopage == "") || ($gotopage == 0)) {
		$gotopage = 1;
	}
?>

<!--<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>-->

<SCRIPT LANGUAGE="JavaScript">
	<!--
	function CheckForm(gbn,idx) {
		if(document.form1["assembleuse_"+idx].value=="Y") {
			if(confirm("해당 상품은 상품상세페이지에서 구성상품을 선택 후에만 구매가 가능합니다.\n\n                     상품상세페이지로 이동 하겠습니까?")) {
				location.href="productdetail_tab01.php?productcode="+document.form1["productcode_"+idx].value;
			}
		} else if(document.form1["packagenum_"+idx].value.length>0) {
			if(confirm("해당 상품은 패키지 선택 상품으로써 상품상세페이지에서 패키지 정보를 확인 해 주세요.\n\n                              상품상세페이지로 이동 하겠습니까?")) {
				location.href="productdetail_tab01.php?productcode="+document.form1["productcode_"+idx].value;
			}
		} else {
			document.basketform.productcode.value=document.form1["productcode_"+idx].value;
			document.basketform.opts.value=document.form1["opts_"+idx].value;
			document.basketform.option1.value=document.form1["option1_"+idx].value;
			document.basketform.option2.value=document.form1["option2_"+idx].value;
			document.basketform.quantity.value=document.form1["quantity_"+idx].value;
			document.basketform.opt_comidx.value=document.form1["opt_comidx_"+idx].value;
			document.basketform.ordertype.value=gbn;
			document.basketform.submit();
		}
	}

	function CheckBoxAll() {
		var sa = true;
		var form = document.form1;

		if(form.flag.value==1) sa = false;

		for (var i=0;i<form.elements.length;i++) {
			var e = form.elements[i];
			if(e.type.toUpperCase()=="CHECKBOX" && e.name=="sels[]") {
				if(sa)
					e.checked = false;
				else
					e.checked = true;
			}
		}

		if(form.flag.value == 1) {
			form.flag.value = 0;
		} else{
			form.flag.value = 1;
		}
	}

	function GoDelete() {
		var form = document.form1;
		var issel=false;
		for (var i=0;i<form.elements.length;i++) {
			var e = form.elements[i];
			if(e.type.toUpperCase()=="CHECKBOX" && e.name=="sels[]") {
				if(e.checked==true) {
					issel=true;
					break;
				}
			}
		}
		if(!issel) {
			alert("삭제할 상품을 선택하세요.");
			return;
		}
		if(confirm("삭제하시겠습니까?")) {
			form.mode.value="delete";
			form.submit();
		}
	}

	function SaveMemo(idx) {
		try {
			document.memoform.mode.value="memo";
			document.memoform.wish_idx.value=idx;
			document.memoform.up_marks.value=document.form1["up_marks_"+idx].value;
			document.memoform.up_memo.value=document.form1["up_memo_"+idx].value;
			document.memoform.submit();
		} catch(e) {}
	}

	function ChangeSort(val) {
		document.form3.block.value="";
		document.form3.gotopage.value="";
		document.form3.sort.value=val;
		document.form3.submit();
	}

	function ChangeListnum(val) {
		document.form3.block.value="";
		document.form3.gotopage.value="";
		document.form3.listnum.value=val;
		document.form3.submit();
	}

	function GoPage(block,gotopage) {
		document.form2.block.value=block;
		document.form2.gotopage.value=gotopage;
		document.form2.submit();
	}
	//-->
</SCRIPT>

<?
	echo "<form name=form1 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
	echo "<input type=hidden name=mode>\n";
	echo "<input type=hidden name=listnum value=\"".$listnum."\">\n";
	echo "<input type=hidden name=sort value=\"".$sort."\">\n";
	echo "<input type=hidden name=block value=\"".$block."\">\n";
	echo "<input type=hidden name=gotopage value=\"".$gotopage."\">\n";
	echo "<input type=hidden name=flag value=1>\n";
	include_once('./wishlist_skin.php');

	echo "</form>\n";
?>

<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=sort value="<?=$sort?>">
</form>

<form name=form3 method=get action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=listnum value="<?=$listnum?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<form name=memoform method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=mode>
	<input type=hidden name=wish_idx>
	<input type=hidden name=up_marks>
	<input type=hidden name=up_memo>
</form>

<form name=basketform method=post action="./basket.php">
	<input type=hidden name=productcode>
	<input type=hidden name=ordertype>
	<input type=hidden name=opts>
	<input type=hidden name=option1>
	<input type=hidden name=option2>
	<input type=hidden name=quantity>
	<input type=hidden name=opt_comidx>
</form>

<? include "footer.php"; ?>
<?
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	//옵션 클래스 2016-09-26 Seul
	include_once($Dir."lib/class/option.php");
	$optClass = new Option;

	$productcode=$_GET['productcode'];
	$code=substr($productcode,0,12);

	$sql="SELECT * FROM tblproduct WHERE productcode='".$productcode."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$price=$row->sellprice;
	$productname=$row->productname;

	#####################상품별 회원할인율 적용 시작#######################################
	$discountprices=getProductDiscount($productcode);
	if($discountprices>0){
		$memberprice=$row->sellprice-$discountprices;
	}else{
		$memberprice='';
	}
	#####################상품별 회원할인율 적용 끝 #######################################

	$dicker="";
	if ($memberprice > 0){
		$dicker=new_dickerview($row->etctype,number_format($memberprice),1);
		$row->sellprice=$memberprice;
	} else {
		$dicker=new_dickerview($row->etctype,number_format($row->sellprice),1);
	}
?>

<style>
	.opt_select{overflow:hidden;}
	.opt_select h2{position:relative;height:36px;line-height:36px;margin-bottom:10px;padding:0px 15px;background:#222;color:#fff;font-size:1em;font-weight:600;}
	.opt_select_view select{font-size:0.9em !important;}
	.btn_opt_submit{padding:20px;text-align:center;}
	.btn_opt_submit a{display:inline-block;padding:5px 10px;border:1px solid #ddd;box-sizing:border-box;border-radius:50px;}
	#div_opts{height:200px;margin-top:5px;padding:0.4em;padding-bottom:0px;background:#f2f2f2;border:1px solid #eee;box-sizing:border-box;font-size:0.9em;overflow-y:scroll;}
	.optionTotalPrice{display:none;margin-top:10px;font-size:1.1em;font-weight:bold;text-align:right;}
	#multitotprice{color:#ff6600;}
	#btn_close_opt_select{position:absolute;right:0px;top:0px;width:36px;height:36px;line-height:36px;line-height:px;font-size:2em;font-weight:100;text-align:center;}
</style>

<form name="form1" id="form1" method="post" action="/m/basket.php">
	<div class="opt_select">
		<div style="overflow-y:scroll;">
			<p style="margin:0px;padding:10px 0px;font-size:1em;font-weight:normal;">상품명 : <?=$productname?></p>
			<div style="padding:0px 0px;box-sizing:border-box;">
			<?
				//옵션 사용여부 2016-10-04 Seul
				$optClass->setOptUse($productcode);
				$optClass->setOptType($productcode);

				if($optClass->optUse){
					if($dicker['memOpen']==1){
						$onlyMember=1;
					} else {
						$onlyMember=0;
					}
					echo "<div class='opt_select_view'>".$optClass->createOptDetailForm($Dir, 1, $optClass->optType, $optClass->optNormalType, $onlyMember, "productdetail")."</div>";
				}
			?>
			</div>

			<div style="padding:0px 0px;box-sizing:border-box;">
				<div class="option_button" id="div_btn">?????</div>
				<div id="div_opts" class="detail_amount"><!--옵션목록 출력--></div>
				<div class="optionTotalPrice"><span id="multitotprice"><?=$price?></span>원</div>
			</div>
		</div>

		<div class="btn_opt_submit">
			<a href="javascript:;" onClick="CheckFormOpt('','')">장바구니</a>
			<a href="javascript:;" onClick="CheckFormOpt('ordernow','')">바로구매</a>
		</div>
	</div>

	<input type="hidden" name="quantity" required value="1" />
	<input type='hidden' name='price' value="<?=$price?>" />
	<input type="hidden" name="code" value="<?=$code?>" />
	<input type="hidden" name="productcode" value="<?=$productcode?>" />
	<input type="hidden" name="ordertype" />
	<input type="hidden" name="opts" />
	<input type="hidden" name="arropts" />
</form>

<script>
	//옵션선택 레이어 팝업창 닫기
	$("#btn_close_opt_select").click(function(){
		$("#fixed_opt_pop").fadeOut(200);
	});
</script>

<script type="text/javascript">
	//상품별 옵션처리 관련
	var r_count=0;

	//선택옵션 합계
	function solvprice(){
		var totalprice=0;
		$('input[name="opt_price[]"]').each(function(index, item) {
			var idx = item.id.replace('opt_price_', '');
			totalprice += parseInt($(item).val() * $('#opt_quantity_'+idx).val());
		});
		$('#multitotprice').html(number_format(totalprice));
	}

	//선택옵션 삭제
	function remove_optbox(obj_name){
		$('#'+obj_name).remove();
		document.form1.arropts.value=document.form1.arropts.value-1;

		//옵션을 선택하고 지운 다음에 바로구매, 장바구니 눌렀을 때 옵션값 없이 넘어가는 현상 수정 2016-02-25 Seul
		r_count=r_count-1;
		if(document.form1.arropts.value==0){
			document.form1.arropts.value="";
		}
		//옵션을 선택하고 지운 다음에 바로구매, 장바구니 눌렀을 때 옵션값 없이 넘어가는 현상 수정 2016-02-25 Seul
		solvprice();

		if($('input[name="opt_price[]"]').length==0){
			$('.optionTotalPrice').hide();
		}
	}

	//옵션수량 변경
	function change_opt_quantity(idx, gbn, max_quantity) {
		var tmp=$("#opt_quantity_"+idx).val();

		if(gbn=="up"){
			tmp++;
		}else if(gbn=="dn"){
			if(tmp>1){
				tmp--;
			}
		}

		if($("#opt_quantity_"+idx).val() != tmp){
			if(max_quantity<tmp){
				<? if($_data->ETCTYPE["STOCK"]=="N"){ ?>
					alert('해당 옵션 상품의 수량이 부족합니다.');
				<? }else{ ?>
					alert('해당 옵션 상품의 수량은 '+max_quantity+'개 입니다.');
				<? } ?>
				return;
			}else{
				$("#opt_quantity_"+idx).val(tmp);
				$("#price_"+idx).html(number_format(tmp*$("#opt_price_"+idx).val())+"원");
			}
		}
		solvprice();
	}

	function CheckFormOpt(gbn,temp2){
		var optMust=true;

		if(gbn=="ordernow"){
			document.form1.ordertype.value="ordernow";
		}else{
			document.form1.ordertype.value="";
		}

		//무제한 옵션 사용시 체크
		<? if($optClass->optUse){ ?>
			$('input[name="optMustCnt[]"]').each(function(index, item){
				if($(item).val()<=0){
					if(document.getElementById("div_btn").style.display != 'none' || document.getElementById("div_btn").style.display != ''){
						alert('필수옵션을 선택해주세요.');
						optMust=false;
						return false;
					}else{
						optMust=false;
						return false;
					}
				}
			});

			if(!optMust){
				return;
			}

			if($("#div_opts:has(div)").length == 0) {
				alert('필수 옵션을 선택해주세요.');
				optMust=false;
				return false;
			}
		<? } ?>

		document.form1.submit();
	}
</script>
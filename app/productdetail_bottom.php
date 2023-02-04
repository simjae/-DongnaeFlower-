
	<?if($_data->sns_ok == "Y" && ($_pdata->sns_state == "Y" || $_pdata->gonggu_product == "Y")){?>

	<? include ($Dir.FrontDir."snsGongguToCmt.php") ?>
	<?}?>

	<form name=couponform method=get action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=mode value="">
	<input type=hidden name=coupon_code value="">
	<input type=hidden name=productcode value="<?=$productcode?>">
	<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
	</form>
	<form name=idxform method=get action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=productcode value="<?=$productcode?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=qnablock value="<?=$qnablock?>">
	<input type=hidden name=qnagotopage value="<?=$qnagotopage?>">
	<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
	</form>

	<form name=wishform method=post action="confirm_wishlist.php" >
	<input type=hidden name=productcode value="<?=$productcode?>">
	<input type=hidden name=opts>
	<input type=hidden name=option1>
	<input type=hidden name=option2>
	</form>

	<?if($_pdata->vender>0){?>
	<form name=custregminiform method=post>
	<input type=hidden name=sellvidx value="<?=$_vdata->vender?>">
	<input type=hidden name=memberlogin value="<?=(strlen($_ShopInfo->getMemid())>0?"Y":"N")?>">
	</form>
	<?}?>

	<div id="create_openwin" style="display:none"></div>

</div><!-- div id="detail" -->


<script language="JavaScript">
	var miniq=<?=($miniq>1?$miniq:1)?>;
	var ardollar=new Array(3);
	ardollar[0]="<?=$ardollar[0]?>";
	ardollar[1]="<?=$ardollar[1]?>";
	ardollar[2]="<?=$ardollar[2]?>";
	<?
	if(strlen($optcode)==0) {
		//옵션 단일 상품, 멀티옵션 상품 구별 2016-04-28 Seul
		if($count2<=0)
			$maxnum=($count-1)*10;
		else
			$maxnum=($count2-1)*10;

		//옵션 있는상품, 없는상품 수량 구별 2016-05-03 Seul
		echo "var num = new Array(";
		if($optioncnt>0) {
			for($i=0;$i<$maxnum;$i++) {
				if ($i!=0) echo ",";
				if(strlen($optioncnt[$i])==0) echo "100000";
				else echo $optioncnt[$i];
			}
		} else {
			$sql = "SELECT quantity FROM tblproduct ";
			$sql.= "WHERE productcode='".$productcode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				if($row->quantity=="")
					echo "1, 100000";
				else
					echo "1, ".$row->quantity;
			}
		}
		echo ");\n";
	?>

	var r_count = 0;
	function change_price(temp,temp2,temp3) {
		if (!checkMemOpen(<?=$dicker["memOpen"]?>)) {
			return false;
		}

		if(temp3==="") temp3=1;
		price = new Array(
			<?
				$pricetok=explode(",",$option_price); //상품상세 개별디자인 관련 작업 2016-04-06 Seul

				if($priceindex>0) {
					echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',";
					for($i=0;$i<$priceindex;$i++) {
						if ($i>0) {
							echo ",";
						}
						echo "'".number_format($pricetok[$i])."'";
					}
				}
			?>
		);
		doprice = new Array(
			<?
				if($priceindex>0) {
					echo "'".number_format($_pdata->sellprice/$ardollar[1],2)."','".number_format($_pdata->sellprice/$ardollar[1],2)."',";
					for($i=0;$i<$priceindex;$i++) {
						if ($i!=0) {
							echo ",";
						}
						echo "'".$pricetokdo[$i]."'";
					}
				}
			?>
		);

		if(temp==1) {
			if (document.form1.option1.selectedIndex > <? echo $priceindex+2 ?>) {
				temp = <?=$priceindex?>;
			} else {
				temp = document.form1.option1.selectedIndex;
			}
			document.form1.price.value = price[temp];
			var priceValue = document.form1.price.value.replace(/,/gi,""),
				discPrice = 0,
				displayPrice = 0;

			displayPrice = priceValue;
			if (document.all["memberprice"]) {
				var discountprices = parseInt(<?=$discountprices?>);
				discPrice = priceValue - discountprices;
				document.form1.price.value = discPrice;
				priceValue = discPrice;
			}
			// 160623 다중 옵션이면 판매가격 변동 없도록.
			if(temp2 <= 0 && temp3 <= 0) {
				document.all["idx_price"].innerHTML = number_format( displayPrice ) + "원";
				if (discPrice > 0) {
					document.all["memberprice"].innerHTML = number_format(discPrice);
				}
			}
	<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
			if(document.getElementById("idx_reserve")) {
				var reserveInnerValue="0";
				if(priceValue>0) {
					var ReservePer=<?=$_pdata->reserve?>;
					var ReservePriceValue=Number(priceValue);
					if(ReservePriceValue>0) {
						reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
						var result = "";
						for(var i=0; i<reserveInnerValue.length; i++) {
							var tmp = reserveInnerValue.length-(i+1);
							if(i%3==0 && i!=0) result = "," + result;
							result = reserveInnerValue.charAt(tmp) + result;
						}
						reserveInnerValue = result;
					}
				}
				document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
			}
	<? } ?>
			if(typeof(document.form1.dollarprice)=="object") {
				document.form1.dollarprice.value = doprice[temp];
				document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
			}
		} else {
			//상품 옵션 2개 일 때 가격 변경이 되지않는 현상 수정 2016-07-04 Seul
			//임시방편 소스임 옵션 2개일때 change_price 함수 안불러오는 현상을 수정해야함
			if(typeof(price[temp2+1]) != "undefined") {
				document.form1.price.value = price[temp2+1];
			}
		}
		//packagecal(); //패키지 상품 적용
		if(temp2>0 && temp3>0) {
			if(num[(temp3-1)*10+(temp2-1)]==0){
				alert('해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요.');
				if(document.form1.option1.type!="hidden") document.form1.option1.focus();
				return;
			}
		} else {
			if(temp2<=0 && document.form1.option1.type!="hidden") document.form1.option1.focus();
			else document.form1.option2.focus();
			return;
		}
		
		//상품 다중옵션 처리 (2016-01-20) Seul
		if(temp2>0 && temp3>0) {
			var html_content = "";
			r_count++;	
			var optPrice = document.form1.price.value.replace(/,/gi,"");

			if(r_count>1){
				if(document.getElementsByName("opt_idx[]").length>0){
					for(i=0;i<document.getElementsByName("opt_idx[]").length;i++){
						if(document.getElementsByName("opt_idx[]")[i].value==temp2 && document.getElementsByName("opt_idx2[]")[i].value==temp3){
							alert("이미 선택된 옵션입니다.");return;
						}
					}
				}else{
					if(document.getElementsByName("opt_idx[]").value==temp2 && document.getElementsByName("opt_idx2[]").value==temp3){
						alert("이미 선택된 옵션입니다.");return;
					}
				}
			}

			html_content += "<div id='@div_name' style='margin-bottom:0.4em;padding:0.6em;border:1px solid #dddddd;background:#ffffff'>";
			html_content += "<table width=100% border=0 cellpadding=0 cellspacing=0>";
			html_content += "<colgroup><col width='100' /><col width=''><col width='32' /></colgroup>";
			html_content += "<tr><td colspan='3' style='padding-bottom:0.6em;text-align:left'>"+$("#option1>option:selected").text()+" : "+$("#option2>option:selected").text()+"</td></tr>";
			html_content += "<input type='hidden' id='opt_price_@idx' name='opt_price[]' value='"+optPrice+"' />";
			html_content += "<input type='hidden' id='opt_idx_@idx' name='opt_idx[]' value='"+temp2+"' />";
			html_content += "<input type='hidden' id='opt_idx2_@idx' name='opt_idx2[]' value='"+temp3+"' />";

			html_content += "<tr><td><input type=\"button\" value=\"-\" class='basic_button' onClick=\"javascript:change_opt_quantity('@idx','dn')\" />";
			html_content += "<input type='text' id='opt_quantity_@idx' name='opt_quantity[]' class='basic_input' style='margin:0px 2px;padding:0px;width:32px;text-align:center;vertical-align:top' min='1' value='1' readonly='readonly' />";
			html_content += "<input type=\"button\" value=\"+\" class='basic_button' onClick=\"javascript:change_opt_quantity('@idx','up')\" /></td>";


			html_content += "<td align='right' id='price_@idx' style='padding-right:10px;font-size:1.1em;font-weight:bold'>"+document.form1.price.value+"원</td><td align=center><input type='button' value='X' onClick=\"javascript:remove_optbox('@div_name');\" class='basic_button' /></td></tr></table>";

			html_content = html_content.replace(/@div_name/gi , 'optbox_'+r_count);
			html_content = html_content.replace(/@idx/gi , r_count);

			//document.write(html_content);
	   
			$('#div_opts').append(html_content);
			
			document.form1.arropts.value=r_count;
			if (typeof document.form1.option1 !=  "undefined") {
				document.form1.option1.value="";
			}
			if (typeof document.form1.option2 !=  "undefined") {
				document.form1.option2.value="";
			}
		}
		solvprice();
		//상품 다중옵션 처리 끝(2016-01-20) Seul
	}

	function remove_optbox(obj_name)
	{
		$('#'+obj_name).remove();
		document.form1.arropts.value=document.form1.arropts.value-1;

		//옵션을 선택하고 지운 다음에 바로구매, 장바구니 눌렀을 때 옵션값 없이 넘어가는 현상 수정 2016-02-25 Seul
		r_count = r_count-1;

		if(document.form1.arropts.value==0)
		{
			document.form1.arropts.value="";
		}
		//옵션을 선택하고 지운 다음에 바로구매, 장바구니 눌렀을 때 옵션값 없이 넘어가는 현상 수정 2016-02-25 Seul

		solvprice();
		if( $('input[name="opt_price[]"]').length == 0 ) {
			$('.optionTotalPrice').hide();
		}
	}

	function replace(src_str, target_str, replace_str)
	{
		var before_str = src_str;
		var after_str = src_str;

		var count = 0;

		do{
			before_str= after_str;
			after_str = before_str.replace(target_str, replace_str);

			if (count++ > 100) break;
				
		}while(before_str != after_str);
		
		return after_str;
	}

	<? 
		} else if(strlen($optcode)>0) { 
			//그룹옵션 num 배열 생성 2016-05-17 Seul
			$maxnum = 1;
			for($i=0; $i<$opti; $i++) {
				$maxnum = $maxnum*$option3[$i];
			}

			echo "var num = new Array(";
				for($i=0;$i<$maxnum;$i++) {
					if ($i!=0) echo ",";
					echo "100000";
				}
			echo ");\n";
			//그룹옵션 num 배열 생성 끝 2016-05-17 Seul
	?>

	function chopprice(temp){
		if (!checkMemOpen(<?=$dicker["memOpen"]?>)) {
			return false;
		}

		var ind           = document.form1.mulopt[temp],
			price         = ind.options[ind.selectedIndex].value,
			originalprice = document.form1.price.value.replace(/,/g, "");

		document.form1.price.value=Number(originalprice)-Number(document.form1.opttype[temp].value);
		if(price.indexOf(",")>0) {
			optprice = price.substring(price.indexOf(",")+1);
		} else {
			optprice=0;
		}
		document.form1.price.value=Number(document.form1.price.value)+Number(optprice);
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
			document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
		}
		document.form1.opttype[temp].value=optprice;
		var num_str = document.form1.price.value.toString();
		var result = '';

		for(var i=0; i<num_str.length; i++) {
			var tmp = num_str.length-(i+1)
			if(i%3==0 && i!=0) result = ',' + result
			result = num_str.charAt(tmp) + result
		}
		document.form1.price.value = result;
		document.all["idx_price"].innerHTML=document.form1.price.value+"원";

		return true;
		//packagecal(); //패키지 상품 적용
	}

	<?}?>

	function checkMemOpen(open) {
		if (open) {
			alert("회원 전용입니다.\n회원 로그인을 하셔야 합니다.");
			return false;
		}
		return true;
	}


	function solvprice() {
		var totalprice = 0;
		$('input[name="opt_price[]"]').each(function(index, item) {
			var idx = item.id.replace('opt_price_', '');
			totalprice += parseInt($(item).val() * $('#opt_quantity_'+idx).val());
		});
		$('#multitotprice').html(number_format(totalprice));
	}

	<? if($_pdata->assembleuse=="Y") { ?>
	function setTotalPrice(tmp) {
		checkMemOpen(<?=$dicker["memOpen"]?>);

		var i=true;
		var j=1;
		var totalprice=0;
		while(i) {
			if(document.getElementById("acassemble"+j)) {
				if(document.getElementById("acassemble"+j).value) {
					arracassemble = document.getElementById("acassemble"+j).value.split("|");
					if(arracassemble[2].length) {
						totalprice += arracassemble[2]*1;
					}
				}
			} else {
				i=false;
			}
			j++;
		}
		totalprice = totalprice*tmp;
		var num_str = totalprice.toString();
		var result = '';
		for(var i=0; i<num_str.length; i++) {
			var tmp = num_str.length-(i+1);
			if(i%3==0 && i!=0) result = ',' + result;
			result = num_str.charAt(tmp) + result;
		}
		if(typeof(document.form1.price)=="object") { document.form1.price.value=totalprice; }
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
			document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
		}
		if(document.getElementById("idx_assembleprice")) { document.getElementById("idx_assembleprice").value = result; }
		if(document.getElementById("idx_price")) { document.getElementById("idx_price").innerHTML = result+"원"; }
		if(document.getElementById("idx_price_graph")) { document.getElementById("idx_price_graph").innerHTML = result+"원"; }
		<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
			if(document.getElementById("idx_reserve")) {
				var reserveInnerValue="0";
				if(document.form1.price.value.length>0) {
					var ReservePer=<?=$_pdata->reserve?>;
					var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
					if(ReservePriceValue>0) {
						reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
						var result = "";
						for(var i=0; i<reserveInnerValue.length; i++) {
							var tmp = reserveInnerValue.length-(i+1);
							if(i%3==0 && i!=0) result = "," + result;
							result = reserveInnerValue.charAt(tmp) + result;
						}
						reserveInnerValue = result;
					}
				}
				document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
			}
		<? } ?>
	}
	<? } ?>

	function packagecal() {
	<?=(count($arrpackage_pricevalue)==0?"return;\n":"")?>
		pakageprice = new Array(<? for($i=0;$i<count($arrpackage_pricevalue);$i++) { if ($i!=0) { echo ",";} echo "'".$arrpackage_pricevalue[$i]."'"; }?>);
		var result = "";
		var intgetValue = document.form1.price.value.replace(/,/g, "");
		var temppricevalue = "0";
		for(var j=1; j<pakageprice.length; j++) {
			if(document.getElementById("idx_price"+j)) {
				temppricevalue = (Number(intgetValue)+Number(pakageprice[j])).toString();
				result="";
				for(var i=0; i<temppricevalue.length; i++) { 
					var tmp = temppricevalue.length-(i+1);
					if(i%3==0 && i!=0) result = "," + result;
					result = temppricevalue.charAt(tmp) + result;
				}
				document.getElementById("idx_price"+j).innerHTML=result+"원";
			}
		}

		if(typeof(document.form1.package_idx)=="object") {
			var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);
		
			if(packagePriceValue>0) {
				result = "";
				packagePriceValue = packagePriceValue.toString();
				for(var i=0; i<packagePriceValue.length; i++) { 
					var tmp = packagePriceValue.length-(i+1);
					if(i%3==0 && i!=0) result = "," + result;
					result = packagePriceValue.charAt(tmp) + result;
				}
				returnValue = result;
			} else {
				returnValue = "0";
			}
			if(document.getElementById("idx_price")) {
				document.getElementById("idx_price").innerHTML=returnValue+"원";
			}
			if(document.getElementById("idx_price_graph")) {
				document.getElementById("idx_price_graph").innerHTML=returnValue+"원";
			}
			if(typeof(document.form1.dollarprice)=="object") {
				document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;
				if(document.getElementById("idx_price_graph")) {
					document.getElementById("idx_price_graph").innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
				}
			}
		}
	}

	function prdetailView(){
		var openurl = "./productdetail_view.php?prcode=<?=$productcode?>";
		window.open(openurl,"prdetailview","");
		return;
	}

	//옵션 없는상품 수량 체크 2016-05-03 Seul
	function change_quantity(gbn) {
		if (!checkMemOpen(<?=$dicker["memOpen"]?>)) {
			return false;
		}

		var tmp=document.form1.quantity.value;
		if(gbn=="up") {
			tmp++;
		} else if(gbn=="dn") {
			if(tmp>1) tmp--;
		}

		if(document.form1.quantity.value!=tmp) {
			if(num[1]<tmp) {
			<?
				if($_data->ETCTYPE["STOCK"]=="N") {
			?>
				alert('해당 옵션 상품의 수량이 부족합니다.');
			<?
				} else {
			?>
				alert('해당 옵션 상품의 수량은 '+num[1]+'개 입니다.');
			<?
				}
			?>
				return;
			}
			else {
				document.form1.quantity.value = tmp;
			}
		}
		solvprice();
	}

	//옵션무제한 수량체크 2016-10-17 Seul
	function change_opt_quantity(idx, gbn, max_quantity) {
		var tmp = $("#opt_quantity_"+idx).val();

		if(gbn == "up") {
			tmp++;
		} else if(gbn == "dn") {
			if(tmp > 1) { tmp--; }
		}

		if($("#opt_quantity_"+idx).val() != tmp) {
			if(max_quantity<tmp) {
			<?
				if($_data->ETCTYPE["STOCK"]=="N") {
			?>
				alert('해당 옵션 상품의 수량이 부족합니다.');
			<?
				} else {
			?>
				alert('해당 옵션 상품의 수량은 '+max_quantity+'개 입니다.');
			<?
				}
			?>
				return;
			} else {
				$("#opt_quantity_"+idx).val(tmp);
				$("#price_"+idx).html(number_format(tmp*$("#opt_price_"+idx).val())+"원");
			}
		}
		solvprice();
	}
	</script>
	<script type="text/javascript" src="./js/sns.js"></script>
	<script type="text/javascript">
	<!--
	var pcode = "<?=$productcode ?>";
	var memId = "<?=$_ShopInfo->getMemid() ?>";
	var fbPicture ="<?=$fbThumb?>";
	var preShowID ="";
	var snsCmt = "";
	var snsLink = "";
	var snsType = "";
	var gRegFrm = "";

	$(document).ready( function () {
		if(memId != ""){
			snsImg();
			snsInfo();
		}
		showSnsComment();
		showGongguCmt();
	});
	//-->
</script>

<!--
<script type="text/javascript" src="<?=$Dir?>m/js/kakao.link.js"></script>
<script type="text/javascript" src="<?=$Dir?>m/js/kakao-1.0.22.min.js"></script>
-->

<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
<script>
	function kakaoLink(type){
		var userid = "<?=$_ShopInfo->getMemid()?>";
		var _reserveForm = document.snsreseveForm;
		_reserveForm.promotiontype.value = type;
		if(userid.length <= 0 || userid == ""){
			if(confirm("로그인 되어있지 않아 적립금을 받을 수 없습니다.\n로그인 하시겠습니까?")){
				window.location='/m/login.php?chUrl='+"<?=getUrl()?>";
				return;
			}
		}else{
			alert("최종 홍보 등록 완료후 적립금이 지급 됩니다.");
			if(_reserveForm.promotiontype.value.length > 0 && _reserveForm.promotiontype.value == type ){
				_reserveForm.target="PROMOTION";
				_reserveForm.submit();
			}
		}
	}
	function snsSendProc(type){
		var productname = "<?=$kakao_prname?>";
		var returnurl = "<?=$kakao_returnurl?>";
		var appid = "<?=$appid?>";
		var appname = "<?=$appname?>";
		var contents = "";
		var imagesrc = "<?=$kakao_primagesrc?>";
		var imagewidth = "<?=$shareWidth?>";
		var imageheight = "<?=$shareHeight?>";
		var imagecapacity = "<?=$imagecapacity?>";
		var sendmaxcapacity = "<?=$sendmaxcapacity?>";
		var kakaousestate = "<?=$kakaousestate?>";
		var kakaokey = "<?=$kakaousekey?>";

		switch(type){
			case "KT":
			if(kakaousestate == "Y" && kakaokey.length > 0){
/* 20170202.alice
				if(imagecapacity>sendmaxcapacity){
					if(confirm("첨부가능 용량을 초과하였습니다.\n첨부가능한 용량은 500KB로\n그대로 진행할 경우\n이미지가 손상될수 있습니다.\n계속하시겠습니까?")){
						sendLink(kakaokey,imagesrc,productname,returnurl,imagewidth,imageheight);
					}else{
						return;
					}
					
				}else{
					sendLink(kakaokey,imagesrc,productname,returnurl,imagewidth,imageheight);
					return;
				}
*/
			    // // 사용할 앱의 JavaScript 키를 설정해 주세요.
			    Kakao.init(kakaokey);
			    // // 카카오링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
			    Kakao.Link.createTalkLinkButton({
			      container: '#kakao-link-btn',
			      label: productname,
			      image: {
			        src: imagesrc,
			        width: '300',
			        height: '200'
			      },
			      webButton: {
			        text: '<?=$_data->shoptitle?>',
			        url: returnurl // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
			      }
			    });
			}else{
				alert("카카오 키가 발급이 되어있지 않거나\n사용설정이 되어있지 않습니다.");
				return;
			}
			break;
			case "KS":
			if(kakaousestate == "Y" && kakaokey.length > 0){
			    // 사용할 앱의 JavaScript 키를 설정해 주세요.
			    Kakao.init(kakaokey);
			    // 스토리 공유 버튼을 생성합니다.
			    Kakao.Story.share({
			    	url: returnurl,
				    text: productname + ' #' + productname + ' #<?=$_data->shoptitle?> :)'
			    });
				//executeKakaoStoryLink(returnurl,appid,appname,productname,contents,imagesrc);
			}else{
				alert("카카오 키가 발급이 되어있지 않거나\n사용설정이 되어있지 않습니다.");
				return;
			}
			break;
			case "FB":
				var href = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(returnurl)+ "&t=" + encodeURIComponent(productname);
				var a = window.open(href, 'Facebook', '');
				if (a) {
					a.focus();
				}
			break;
			case "TW":
				var href = "http://twitter.com/share?text=" + encodeURIComponent(productname) + " " + encodeURIComponent(returnurl);
				var a = window.open(href, 'Twitter', '');
				if (a) {
					a.focus();
				}
			break;
			case 'PI':
				var href = "http://www.pinterest.com/pin/create/button/?url=" + encodeURIComponent(returnurl) + "&media=" + encodeURIComponent(imagesrc) + "&description=" + encodeURIComponent(productname);
				var a = window.open(href, 'Pinterest', '');
				if (a) {
					a.focus();
				}
			break;
			case 'GO':
				var href = "https://plus.google.com/share?url=" + encodeURIComponent(productUrl);
				var a = window.open(href, 'GooglePlus', '');
				if (a) {
					a.focus();
				}
			break;
		}
	}
	
	function sendLink(kakaokey,imagesrc,productname,returnurl,imagewidth,imageheight){
		Kakao.init(kakaokey);
		Kakao.Link.sendTalkLink({
			label: productname,
			image : {
				src : imagesrc,
				width : imagewidth,
				height : imageheight
			},
			webButton :{
				text : '방문하기',
				url : returnurl
			}
		});
	}
</script>

<SCRIPT LANGUAGE="JavaScript">
	<!--
	function ClipCopy(url) {
		var tmp;
		tmp = window.clipboardData.setData('Text', url);
		if(tmp) {
			alert('주소가 복사되었습니다.');
		}
	}

	<?if($_pdata->vender>0){?>
	function custRegistMinishop() {
		if(document.custregminiform.memberlogin.value!="Y") {
			alert("로그인 후 이용이 가능합니다.");
			return;
		}
		owin=window.open("about:blank","miniregpop","width=100,height=100,scrollbars=no");
		owin.focus();
		document.custregminiform.target="miniregpop";
		document.custregminiform.action="minishop.regist.pop.php";
		document.custregminiform.submit();
	}
	<?}?>


	function ableCouponPOP(productcode){
		var pcwin=window.open("/newfront/ablecoupons.php?productcode="+productcode,"CouponPop","width=617,height=450,scrollbars=yes");
	}

	function primage_view(img,type) {
		if (img.length==0) {
			alert("확대보기 이미지가 없습니다.");
			return;
		}
		var tmp = "height=350,width=450,toolbar=no,menubar=no,resizable=no,status=no";
		if(type=="1") {
			tmp+=",scrollbars=yes";
			sc="yes";
		} else {
			sc="";
		}
		url = "<?=$Dir.FrontDir?>primage_view.php?scroll="+sc+"&image="+img;

		window.open(url,"primage_view",tmp);
	}

	function change_quantity_org(gbn) {
		tmp=document.form1.quantity.value;
		if(gbn=="up") {
			tmp++;
		} else if(gbn=="dn") {
			if(tmp>1) tmp--;
		}
		if(document.form1.quantity.value!=tmp) {
		<? if($_pdata->assembleuse=="Y") { ?>
			if(getQuantityCheck(tmp)) {
				if(document.form1.assemblequantity) {
					document.form1.assemblequantity.value=tmp;
				}
				document.form1.quantity.value=tmp;
				setTotalPrice(tmp);
			} else {
				alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
				return;
			}
		<? } else { ?>
			document.form1.quantity.value=tmp;
		<? } ?>
		}
	}

	function check_login() {
		if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
			document.location.href="login.php?chUrl=<?=getUrl()?>";
		}
		return false;
	}
	<?if($_data->coupon_ok=="Y") {?>
	function issue_coupon(coupon_code){
		document.couponform.mode.value="coupon";
		document.couponform.coupon_code.value=coupon_code;
		document.couponform.submit();
	}
	<?}?>


	function CheckForm(gbn,temp2) {

		if(document.getElementById("div_btn").style.display=='none' || document.getElementById("div_btn").style.display=='' ){
			$('#div_toggle').addClass('on');
			$('#div_btn').css('display','block');
			return;
		}

		var optMust = true;

		if(gbn!="wishlist") {
			<?if($ao_cnt==0){?>
				if(document.form1.receiveType.value.length==0 || document.form1.receiveType.value=="") {
					alert("배송방식을 선택하세요.");
					return;
				}
			<? } ?>
			if(document.form1.quantity.value.length==0 || document.form1.quantity.value==0) {
				alert("주문수량을 입력하세요.");
				document.form1.quantity.focus();
				return;
			}
			if(miniq>1 && document.form1.quantity.value<=1) {
				alert("해당 상품의 구매수량은 "+miniq+"개 이상 주문이 가능합니다.");
				document.form1.quantity.focus();
				return;
			}
		}
		if(gbn=="ordernow") {
			document.form1.ordertype.value="ordernow";
		}
		else if(gbn=="ordernow2" || gbn=="ordernow3") {
			document.form1.ordertype.value=gbn;
			document.form1.action = "<?=$Dir.FrontDir?>basket2.php";
		}
		else if(gbn=="ordernow4" || gbn=="present" || gbn=="pester") {
			document.form1.ordertype.value=gbn;
			document.form1.action = "<?=$Dir.FrontDir?>basket3.php";
		} else {
			// 1606022 바로구매 클릭 시 옵션 미선택 경고 뜨고 장바구니로 담았을 때 바로구매로 가는 오류 수정.
			document.form1.ordertype.value="";
		}

		//무제한 옵션 사용 시 체크
		<?
			if($optClass->optUse) {
		?>
			$('input[name="optMustCnt[]"]').each(function(index, item) {
				if($(item).val()<=0) {
					if(document.getElementById("div_btn").style.display!='none' || document.getElementById("div_btn").style.display!='' ){
						alert('필수 옵션을 선택해주세요.');
						optMust = false;
						return false;
					}
					else {
						optMust = false;
						return false;
					}
				}
			});

			if(!optMust) {
				return;
			}
			
			if($("#div_opts:has(div)").length == 0) {
				alert('필수 옵션을 선택해주세요.');
				optMust = false;
				return false;
			}
		<?
			}
		?>

		if(temp2!="") {
			document.form1.opts.value="";
			try {
				for(i=0;i<temp2;i++) {
					if(document.form1.optselect[i].value==1 && document.form1.mulopt[i].selectedIndex==0) {
						alert('필수선택 항목입니다. 옵션을 반드시 선택하세요');
						document.form1.mulopt[i].focus();
						return;
					}
					document.form1.opts.value+=document.form1.mulopt[i].selectedIndex+",";
				}
			} catch (e) {}
		}
	<?
	if(eregi("S",$_cdata->type)) {
	?>
		if(typeof(document.form1.option)!="undefined" && document.form1.option.selectedIndex<2) {
			alert('해당 상품의 옵션을 선택하세요.');
			$('#div_toggle').show(250);
			document.form1.option.focus();
			return;
		}
		if(typeof(document.form1.option)!="undefined" && document.form1.option.selectedIndex>=2) {
			arselOpt=document.form1.option.value.split("_");
			arselOpt[1] = (arselOpt[1] > 0)? arselOpt[1] :1;
			seq = parseInt(10*(arselOpt[1]-1)) + parseInt(arselOpt[0]);
			if(num[seq-1]==0) {
				alert('해당 상품의 옵션은 품절되었습니다. 다른 옵션을 선택하세요');
				document.form1.option.focus();
				return;
			}
			document.form1.option1.value = arselOpt[0];
			document.form1.option2.value = arselOpt[1];
		}
	<?
	}else{
	?>
		if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex<2 && typeof(document.form1.opt_idx_1)=="undefined") {
			alert('해당 상품의 옵션을 선택하세요.');
			$('#div_toggle').show(250);
			document.form1.option1.focus();
			return;
		}
		if(typeof(document.form1.option2)!="undefined" && document.form1.option2.selectedIndex<2 && typeof(document.form1.opt_idx2_1)=="undefined") {
			alert('해당 상품의 옵션을 선택하세요.');
			$('#div_toggle').show(250);
			document.form1.option2.focus();
			return;
		}
		if(typeof(document.form1.option1)!="undefined" && document.form1.option1.selectedIndex>=2) {
			temp2=document.form1.option1.selectedIndex-1;
			if(typeof(document.form1.option2)=="undefined") temp3=1;
			else temp3=document.form1.option2.selectedIndex-1;
			if(num[(temp3-1)*10+(temp2-1)]==0) {
				alert('해당 상품의 옵션은 품절되었습니다. 다른 옵션을 선택하세요');
				document.form1.option1.focus();
				return;
			}
		}
	<?
	}
	?>
		if(typeof(document.form1.package_type)!="undefined" && typeof(document.form1.packagenum)!="undefined" && document.form1.package_type.value=="Y" && document.form1.packagenum.selectedIndex<2) {
			alert('해당 상품의 패키지를 선택하세요.');
			document.form1.packagenum.focus();
			return;
		}
		if(gbn!="wishlist") {
			<? if($_pdata->assembleuse=="Y") { ?>
			if(typeof(document.form1.assemble_type)=="undefined") {
				alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
				return;
			} else {
				if(document.form1.assemble_type.value.length>0) {
					arracassembletype = document.form1.assemble_type.value.split("|");
					document.form1.assemble_list.value="";

					for(var i=1; i<=arracassembletype.length; i++) {
						if(arracassembletype[i]=="Y") {
							if(document.getElementById("acassemble"+i).options.length<2) {
								alert('필수 구성상품의 상품이 없어서 구매가 불가능합니다.');
								document.getElementById("acassemble"+i).focus();
								return;
							} else if(document.getElementById("acassemble"+i).value.length==0) {
								alert('필수 구성상품을 선택해 주세요.');
								document.getElementById("acassemble"+i).focus();
								return;
							}
						}

						if(document.getElementById("acassemble"+i)) {
							if(document.getElementById("acassemble"+i).value.length>0) {
								arracassemblelist = document.getElementById("acassemble"+i).value.split("|");
								document.form1.assemble_list.value += "|"+arracassemblelist[0];
							} else {
								document.form1.assemble_list.value += "|";
							}
						}
					}
				} else {
					alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');
					return;
				}
			}
			<? } ?>
			document.form1.submit();
		} else {
			document.form1.action = "confirm_wishlist.php";
			document.form1.submit();
			//document.wishform.opts.value=document.form1.opts.value;
			//if(typeof(document.form1.option1)!="undefined") document.wishform.option1.value=document.form1.option1.value;
			//if(typeof(document.form1.option2)!="undefined") document.wishform.option2.value=document.form1.option2.value;

			//window.open("about:blank","confirmwishlist","width=500,height=250,scrollbars=no");
			//document.wishform.submit();
		}
	}

/*
	function view_review(cnt) {
		var review_list = document.getElementsByClassName('reviewspan');
		if(review_list.length>=0 && review_list[cnt].style.display == "none"){
			for(i=0;i<review_list.length;i++) {
				if(cnt==i) {
					if(review_list[i].style.display=="none") {
						review_list[i].style.display="";
					} else {
						review_list[i].style.display="none";
					}
				} else {
					review_list[i].style.display="none";
				}
			}
		} else {
			review_list[cnt].style.display = ( review_list[cnt].style.display == "none" ) ? "" : "none";
		}
	}
*/

	function review_open(prcode,num) {
		window.open("<?=$Dir.FrontDir?>review_popup.php?prcode="+prcode+"&num="+num,"","width=450,height=400,scrollbars=yes");
	}

	function review_write() {
		if(typeof(document.all["reviewwrite"])=="object") {
			if(document.all["reviewwrite"].style.display=="none") {
				document.all["reviewwrite"].style.display="";
			} else {
				document.all["reviewwrite"].style.display="none";
			}
		}
	}

	function write_review(){
		var userid = "<?=$_ShopInfo->getMemid()?>";
		var membergrant = "<?=$_data->review_memtype?>"; //회원 전용일경우
		var reviewgrant = "<?=$_data->review_type?>";
		var reviewetcgrant = "<?=$_data->ETCTYPE['REVIEW']?>";
		var _form = document.reviewWriteForm;
		if(reviewgrant == "N" || reviewetcgrant == "N"){
			alert("사용후기 설정이 되지 않아 사용 할 수 없습니다.");
			return;
		}else if(userid =="" && membergrant == "Y"){
			if(confirm("회원전용 기능입니다. 로그인 하시겠습니까?")){
				location.href="./login.php?chUrl=<?=getUrl()?>";
			}
			return;
		}else{
			
			if(_form.rname.value==""){
				alert("작성자를 입력해 주세요.");
				_form.rname.focus();
				return;
			}else if(_form.rname.rcontents){
				_form.rcontents.focus();
				return;
			}else{		
				if(confirm("사용후기를 등록 하시겠습니까?")){
					_form.mode.value="write";
					_form.submit();
				}

				return;
			}
		}
	}

	function CheckReview() {
		if(document.reviewform.rname.value.length==0) {
			alert("작성자 이름을 입력하세요.");
			document.reviewform.rname.focus();
			return;
		}
		if(document.reviewform.rcontent.value.length==0) {
			alert("사용후기 내용을 입력하세요.");
			document.reviewform.rcontent.focus();
			return;
		}
		document.reviewform.mode.value="review_write";
		document.reviewform.submit();
	}

	var view_qnano="";
	function view_qnacontent(idx) {
		if (idx=="W") {	//쓰기권한 없음
			alert("상품Q&A 게시판 문의 권한이 없습니다.");
		} else if(idx=="N") {	//일기권한 없음
			alert("해당 Q&A게시판 게시글을 보실 수 없습니다.");
		} else if(idx=="S") {	//잠금기능 설정된 글
			if(view_qnano.length>0 && view_qnano!=idx) {
				document.all["qnacontent"+view_qnano].style.display="none";
			}
			alert("해당 문의 글은 잠금기능이 설정된 게시글로\n\n직접 게시판에 가셔서 확인하셔야 합니다.");
		} else if(idx=="D") {
			if(view_qnano.length>0 && view_qnano!=idx) {
				document.all["qnacontent"+view_qnano].style.display="none";
			}
			alert("작성자가 삭제한 게시글입니다.");
		} else {
			try {
				if(document.all["qnacontent"+idx].style.display=="none") {
					view_qnano=idx;
					document.all["qnacontent"+idx].style.display="";
				} else {
					document.all["qnacontent"+idx].style.display="none";
				}
			} catch (e) {
				alert("오류로 인하여 게시내용을 보실 수 없습니다.");
			}
		}
	}

	function GoPage(gbn,block,gotopage) {
		document.idxform.action=document.idxform.action+"?#"+gbn;
		if(gbn=="review") {
			document.idxform.block.value=block;
			document.idxform.gotopage.value=gotopage;
		} else if(gbn=="prqna") {
			document.idxform.qnablock.value=block;
			document.idxform.qnagotopage.value=gotopage;
		}
		document.idxform.submit();
	}

	/* ################ 태그관련 ################## */
	var IE = false ;
	if (window.navigator.appName.indexOf("Explorer") !=-1) {
		IE = true;
	}
	//tag 금칙 문자 (%, &, +, <, >, ?, /, \, ', ", =,  \n)
	var restrictedTagChars = /[\x25\x26\x2b\x3c\x3e\x3f\x2f\x5c\x27\x22\x3d\x2c\x20]|(\x5c\x6e)/g;
	function check_tagvalidate(aEvent, input) {
		var keynum;
		if(typeof aEvent=="undefined") aEvent=window.event;
		if(IE) {
			keynum = aEvent.keyCode;
		} else {
			keynum = aEvent.which;
		}
		//  %, &, +, -, ., /, <, >, ?, \n, \ |
		var ret = input.value;
		if(ret.match(restrictedTagChars) != null ) {
			 ret = ret.replace(restrictedTagChars, "");
			 input.value=ret;
		}
	}

	function tagCheck(productcode) {
	<?if(strlen($_ShopInfo->getMemid())>0){?>
		var obj = document.all;
		if(obj.searchtagname.value.length < 2 ){
			alert("태그를(2자 이상) 입력해 주세요!");
			obj.searchtagname.focus();
			return;
		}
		goProc("prtagreg",productcode);
		return;
	<?}else{?>
		alert("로그인 후 작성해 주세요!");
		return;
	<?}?>
	}

	function goProc(mode,productcode){
		var obj = document.all;
		if(mode=="prtagreg") {
			succFun=myFunction;
			var tag=obj.searchtagname.value;
			var path="<?=$Dir.FrontDir?>tag.xml.php?mode="+mode+"&productcode="+productcode+"&tagname="+tag;
			obj.searchtagname.value="처리중 입니다!";
		} else {
			succFun=prTaglist;
			var path="<?=$Dir.FrontDir?>tag.xml.php?mode="+mode+"&productcode="+productcode;
		}
		var myajax = new Ajax(path,
								{
									onComplete: function(text) {
										succFun(text,productcode);
									}
								}
		).request();
	}

	function myFunction(request,productcode){
		var msgtmp = request;
		var splitString = msgtmp.split("|");

		//다시 초기화
		var obj = document.all;
		obj.searchtagname.value="";
		if(splitString[0]=="OK") {
			var tag = splitString[2];
			if(splitString[1]=="0") {

			} else if(splitString[1]=="1") {
				goProc("prtagget",productcode);
			}
		} else if(splitString[0]=="NO") {
			alert(splitString[1]);
		}
	}

	function prTaglist(request) {
		var msgtmp = request;
		var splitString = msgtmp.split("|");
		if(splitString[0]=="OK") {
			document.all["prtaglist"].innerHTML=splitString[1];
		} else if(splitString[0]=="NO") {
			alert(splitString[1]);
		}
	}

	<? if($_pdata->assembleuse=="Y") { ?>
	var currentSelectIndex = "";
	function setCurrentSelect(thisSelectIndex) {
		currentSelectIndex = thisSelectIndex;
	}

	function setAssenbleChange(thisObj,idxValue) {
		if(thisObj.value.length>0) {
			thisValueSplit = thisObj.value.split('|');
			if(thisValueSplit[1].length>0) {
				if(Number(thisValueSplit[1])==0) {
					alert('현재 상품은 품절 상품입니다.');
				} else {
					if(Number(document.form1.quantity.value)>0) {
						if(Number(thisValueSplit[1]) < Number(document.form1.quantity.value)) {
							alert('구성 상품의 재고량이 부족합니다.');
						} else {
							setTotalPrice(document.form1.quantity.value);
							if(thisValueSplit.length>3 && thisValueSplit[4].length>0 && document.getElementById("acimage"+idxValue)) {
								document.getElementById("acimage"+idxValue).src="<?=$Dir.DataDir."shopimages/product/"?>"+thisValueSplit[4];
							} else {
								document.getElementById("acimage"+idxValue).src="<?=$Dir."images/acimage.gif"?>";
							}
							return;
						}
					} else {
						alert('본 상품 수량을 입력해 주세요.');
					}
				}
			} else {
				setTotalPrice(document.form1.quantity.value);
				if(thisValueSplit.length>3 && thisValueSplit[4].length>0 && document.getElementById("acimage"+idxValue)) {
					document.getElementById("acimage"+idxValue).src="<?=$Dir.DataDir."shopimages/product/"?>"+thisValueSplit[4];
				} else {
					document.getElementById("acimage"+idxValue).src="<?=$Dir."images/acimage.gif"?>";
				}
				return;
			}

			thisObj.options[currentSelectIndex].selected = true;
		} else {
			setTotalPrice(document.form1.quantity.value);
			document.getElementById("acimage"+idxValue).src="<?=$Dir."images/acimage.gif"?>";
			return;
		}
	}

	function getQuantityCheck(tmp) {
		var i=true;
		var j=1;
		while(i) {
			if(document.getElementById("acassemble"+j)) {
				if(document.getElementById("acassemble"+j).value) {
					arracassemble = document.getElementById("acassemble"+j).value.split("|");
					if(arracassemble[1].length>0 && Number(tmp) > Number(arracassemble[1])) {
						return false;
					}
				}
			} else {
				i=false;
			}
			j++;
		}
		return true;
	}

	function assemble_proinfo(idxValue) { // 조립상품 개별 상품 정보보기
		if(document.getElementById("acassemble"+idxValue)) {
			if(document.getElementById("acassemble"+idxValue).value.length>0) {
				thisValueSplit = document.getElementById("acassemble"+idxValue).value.split('|');
				if(thisValueSplit[0].length>0) {
					product_info_pop("assemble_proinfo.php?op=<?=$productcode?>&np="+thisValueSplit[0],"assemble_proinfo_"+thisValueSplit[0],700,700,"yes");
				} else {
					alert("해당 상품정보가 존재하지 않습니다.");
				}
			}
		}
	}

	function product_info_pop(url,win_name,w,h,use_scroll) {
		var x = (screen.width - w) / 2;
		var y = (screen.height - h) / 2;
		if (use_scroll==null) use_scroll = "no";
		var use_option = "";
		use_option = use_option + "toolbar=no, channelmode=no, location=no, directories=no, resizable=no, menubar=no";
		use_option = use_option + ", scrollbars=" + use_scroll + ", left=" + x + ", top=" + y + ", width=" + w + ", height=" + h;

		var win = window.open(url,win_name,use_option);
		return win;
	}
	<? } ?>

	var productUrl = "http://<?=$_data->shopurl?>?prdt=<?=$productcode?>";
	var productName = "<?=strip_tags($_pdata->productname)?>";
	function goFaceBook()
	{
		var href = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(productUrl) + "&t=" + encodeURIComponent(productName);
		var a = window.open(href, 'Facebook', '');
		if (a) {
			a.focus();
		}
	}

	function goTwitter()
	{
		var href = "http://twitter.com/share?text=" + encodeURIComponent(productName) + " " + encodeURIComponent(productUrl);
		var a = window.open(href, 'Twitter', '');
		if (a) {
			a.focus();
		}
	}

	function goMe2Day()
	{
		var href = "http://me2day.net/posts/new?new_post[body]=" + encodeURIComponent(productName) + " " + encodeURIComponent(productUrl) + "&new_post[tags]=" + encodeURIComponent('<?=$_data->shopname?>');
		var a = window.open(href, 'Me2Day', '');
		if (a) {
			a.focus();
		}
	}

	function snsSendCheck(type){
	<?//if($arSnsType[0] != "N"){?>
		//if(confirm("적립금을 받으려면 로그인이 필요합니다. 로그인하시겠습니까?")){
		//	document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
		//}else{
	<?//}?>
			if(type =="t")
				goTwitter();
			else if(type =="f")
				goFaceBook();
			else if(type =="m")
				goMe2Day();
	<?if($arSnsType[0] != "N") {?>
		//}
	<?}?>
	}


	//카테고리 뷰
		function qrCodeView(obj,type){
			var obj;
			var div = eval("document.all." + obj);

			if(type == 'open'){
				div.style.display = "block";
			}else if (type == 'over'){
				div.style.display = "block";
			}else if (type == 'out'){
				div.style.display = "none";
			}
		}

	//-->
</SCRIPT>

<? include "footer.php";?>
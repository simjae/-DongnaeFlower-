<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	exit;
}
$r = mysql_query("select group_name,group_apply_coupon from tblmembergroup where group_code = '".$_ShopInfo->memgroup."'",get_db_conn());
$row = mysql_fetch_object($r);
if($row->group_apply_coupon == "N"){
	echo "
	<script>
		alert('".$row->group_name." 회원 등급은 쿠폰 사용이 불가능합니다.');
		self.close();
	</script>
	";
}
$usereserve=(int)$_POST["usereserve"];	//사용한 적립금
$sumprice=$_POST["sumprice"];
$used=$_POST["used"];
?>
<!DOCTYPE HTML>
<html>
<head>
<!-- 쇼핑몰태그 -->
<meta name="description" content="">
<meta name="keywords" content="">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<meta name="format-detection" content="telephone=no" />
<!-- 바로가기 아이콘 -->
<link rel="apple-touch-icon-precomposed" href="" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<title>겟몰 쇼핑몰 - 모바일</title>
<link rel="stylesheet" href="./css/common.css">
<!-- 스킨 css -->
<link rel="stylesheet" href="./css/skin/default.css">
<!-- 컬러 css -->
<link rel="stylesheet" href="./css/color/black.css">
<!-- 사용자 css -->
<link rel="stylesheet" href="./css/user.css">

<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<!-- <script type="text/javascript" src="./js/common.js"></script> -->
</head>


<SCRIPT LANGUAGE="JavaScript">
<!--
//window.moveTo(10,10);
//window.resizeTo(612,650);
var all_list=new Array();
function prvalue() {
	var argv = prvalue.arguments;
	var argc = prvalue.arguments.length;

	this.classname		= "prvalue"
	this.debug			= false;
	this.bank_only		= new String((argc > 0) ? argv[0] : "N");
	this.sale_type		= new String((argc > 1) ? argv[1] : "");
	this.use_con_type2	= new String((argc > 2) ? argv[2] : "");
	this.sale_money		= new String((argc > 3) ? argv[3] : "");
	this.prname			= new String((argc > 4) ? argv[4] : "");
	this.prprice		= new String((argc > 5) ? argv[5] : "");
}

function CheckForm() {
	if(document.form1.coupon_code.selectedIndex<=0){
		alert("사용하실 쿠폰을 선택하세요.");
		document.form1.coupon_code.focus();
		return;
	}
	if(document.form1.bank_only.value=="Y" && !confirm('해당 쿠폰은 현금결제시에만 사용가능합니다.\n무통장입금을 선택하셔야만 쿠폰 사용이 가능합니다.')){
		document.form1.coupon_code.focus();
		return;
	}
	opener.document.form1.coupon_code.value=document.form1.coupon_code.options[document.form1.coupon_code.selectedIndex].text;
	opener.document.form1.bank_only.value=document.form1.bank_only.value;
	window.close();
}

function coupon_cancel() {
	opener.document.form1.coupon_code.value="";
	opener.document.form1.bank_only.value="N";
	window.close();
}
//-->
</SCRIPT>

<body class="blank_wrap">

<?

	$id=$_ShopInfo->getMemid();
	$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, ";
	$sql.= "a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, a.vender, b.date_start, b.date_end ";
	$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
	$sql.= "WHERE b.id='".$id."' AND a.coupon_code=b.coupon_code AND b.date_start<='".date("YmdH")."' ";
	$sql.= "AND (b.date_end>='".date("YmdH")."' OR b.date_end='') ";
	$sql.= "AND b.used='N' ";
	$result = mysql_query($sql,get_db_conn());

	$row_cnt = mysql_num_rows($result);
?>


<form name=form1 method=post>
<input type=hidden name=bank_only value="N">		
<div class="coupon_blank">

	<h1><span>쿠폰 조회 및 적용</span></h1>	
		<!-- 쿠폰내역 -->
		<div class="coupon">
			<div class="pr_navi">
				<h3>사용가능 쿠폰 : <strong><?=$row_cnt?>장</strong></h3>
			</div>
			
			<div class="coupon_list">
				<ul>
		
<?
		
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$coupon_code[$cnt]		= $row->coupon_code;
			$use_con_type2[$cnt]	= $row->use_con_type2;
			$sale_type[$cnt]		= $row->sale_type;
			$use_con_type1[$cnt]	= $row->use_con_type1;
			$sale_money[$cnt]		= $row->sale_money;
			$mini_price[$cnt]		= $row->mini_price;
			$vender[$cnt]			= $row->vender;
			$bank_only[$cnt]		= $row->bank_only;

			if($row->sale_type<=2) {
				$dan="%";
			} else {
				$dan="원";
			}
			if($row->sale_type%2==0) {
				$sale = "할인";
			} else {
				$sale = "적립";
			}

			if($row->productcode=="ALL") {
				if($row->vender==0) {
					$product="전체상품";
				} else {
					$product="해당 입점업체 전체상품";
				}
				$productcode[$cnt][]="ALL";
			} else {
				$product = "";

				$arrproduct=explode(",",$row->productcode);
				for($a=0;$a<count($arrproduct);$a++) {
					if($a>0) $product.=", ";

					$prleng=strlen($arrproduct[$a]);

					$codeA=substr($arrproduct[$a],0,3);
					$codeB=substr($arrproduct[$a],3,3);
					$codeC=substr($arrproduct[$a],6,3);
					$codeD=substr($arrproduct[$a],9,3);

					$likecode=$codeA;
					if($codeB!="000") $likecode.=$codeB;
					if($codeC!="000") $likecode.=$codeC;
					if($codeD!="000") $likecode.=$codeD;

					if($prleng==18) $productcode[$cnt][]=$arrproduct[$a];
					else $productcode[$cnt][]=$likecode;

					$sql2 = "SELECT code_name FROM tblproductcode WHERE codeA='".substr($arrproduct[$a],0,3)."' ";
					if(substr($arrproduct[$a],3,3)!="000") {
						$sql2.= "AND (codeB='".substr($arrproduct[$a],3,3)."' OR codeB='000') ";
						if(substr($arrproduct[$a],6,3)!="000") {
							$sql2.= "AND (codeC='".substr($arrproduct[$a],6,3)."' OR codeC='000') ";
							if(substr($arrproduct[$a],9,3)!="000") {
								$sql2.= "AND (codeD='".substr($arrproduct[$a],9,3)."' OR codeD='000') ";
							} else {
								$sql2.= "AND codeD='000' ";
							}
						} else {
							$sql2.= "AND codeC='000' ";
						}
					} else {
						$sql2.= "AND codeB='000' AND codeC='000' ";
					}
					$sql2.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
					$result2=mysql_query($sql2,get_db_conn());
					$i=0;
					while($row2=mysql_fetch_object($result2)) {
						if($i>0) $product.= " > ";
						$product.= $row2->code_name;
						$i++;
					}
					if($row->vender>0) $product.=" (일부상품 제외)";
					mysql_free_result($result2);

					if($prleng==18) {
						$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='".$arrproduct[$a]."' ";
						$result2 = mysql_query($sql2,get_db_conn());
						if($row2 = mysql_fetch_object($result2)) {
							$product.= " > ".$row2->product;
						}
						mysql_free_result($result2);
					}
				}
			}

			$cnt++;

			if($row->use_con_type2=="N") {
				if($row->vender==0) {
					$product="[".$product."] 제외";
				} else {
					$product="[".$product."] 제외한 일부상품";
				}
			}
			$s_time=mktime((int)substr($row->date_start,8,2),0,0,(int)substr($row->date_start,4,2),(int)substr($row->date_start,6,2),(int)substr($row->date_start,0,4));
			$e_time=mktime((int)substr($row->date_end,8,2),0,0,(int)substr($row->date_end,4,2),(int)substr($row->date_end,6,2),(int)substr($row->date_end,0,4));

			$date=date("Y.m.d H",$s_time)."시 ~ ".date("Y.m.d H",$e_time)."시";	
?>
					<li>
						<div class="h_area3">
							<h4><?=$row->coupon_name?></h4>
						</div>
						<table class="basic_table">
							<tr>
								<th scope="row"><span>쿠폰번호</span></th>
								<td><span class="point1"><?=$row->coupon_code?></span></td>
							</tr>
							<tr>
								<th scope="row"><span>사용기간</span></th>
								<td><span><?=$date?></span></td>
							</tr>
							<tr>
								<th scope="row"><span>쿠폰 적용상품</span></th>
								<td><span><?=$product?></span></td>
							</tr>
							<tr>
								<th scope="row"><span>제한사항</span></th>
								<td><span><? if($row->mini_price=="0") { echo "제한 없음"; } else { number_format($row->mini_price)."원 이상"; } ?> </span></td>
							</tr>
							<tr>
								<th scope="row"><span>혜 택</span></th>
								<td><span class="point2"><?=number_format($row->sale_money).$dan.$sale?></span></td>
							</tr>
						</table>
					</li>

<?

		}
		mysql_free_result($result);
		if($cnt==0) {
			echo "<li style=height:30px>보유한 쿠폰내역이 없습니다.</li>\n";
		}
?>


		</ul>
			<!-- 페이지네비 -->
			<!-- <div class="pg pg_num_area3">
				<button type="button" onClick="" class="pg_btn pg_btn_prev"><span>이전 페이지</span></button>
				<span class="pg_area">
					<a  href="" class="pg_num">1</a>
					<span class="pg_num pg_num_on">2</span>
					<a  href="" class="pg_num">3</a>
				</span>
				<button type="button" onClick="" class="pg_btn pg_btn_next"><span>다음 페이지</span></button>
			</div> -->
			<!-- //페이지네비 -->
		</div>


	<?if($used!="N"){?>


<?
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.option_price,b.option_quantity,b.option1,b.option2,b.vender,b.sellprice*a.quantity as realprice, ";
		$sql.= "b.etcapply_coupon,b.etcapply_reserve,b.etcapply_gift FROM tblbasket a, tblproduct b ";
		$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$result=mysql_query($sql,get_db_conn());
		$sumprice=array();
		$basketcnt=array();
		$prcode=array();
		$prname=array();
		$productall=array();
		while($row = mysql_fetch_object($result)) {
			if($row->etcapply_coupon=="Y" || $row->etcapply_reserve=="Y" || $row->etcapply_gift=="Y") {
				continue;
			} else {
				//적립금 적용 불가 카테고리 조회를 하여 적립금 불가일 경우 쿠폰 적용상품에서 뺀다.
				$R_codeA=substr($row->productcode,0,3);
				$R_codeB=substr($row->productcode,3,3);
				$R_codeC=substr($row->productcode,6,3);
				$R_codeD=substr($row->productcode,9,3);

				$sql = "SELECT COUNT(*) as cnt FROM tblproductcode ";
				$sql.= "WHERE codeA='".$R_codeA."' AND codeB='".$R_codeB."' AND codeC='".$R_codeC."' AND codeD='".$R_codeD."' ";
				$sql.= "AND noreserve='Y' ";
				$result2=mysql_query($sql,get_db_conn());
				$row2=mysql_fetch_object($result2);
				mysql_free_result($result2);
				if($row2->cnt>0) {
					continue;
				}
			}
			if(strlen($prcode[0])>0) {
				if(substr($row->productcode,0,12)==substr($prcode[0],0,12)) $prcode[0]=substr($prcode[0],0,12);
				else if(substr($row->productcode,0,9)==substr($prcode[0],0,9)) $prcode[0]=substr($prcode[0],0,9);
				else if(substr($row->productcode,0,6)==substr($prcode[0],0,6)) $prcode[0]=substr($prcode[0],0,6);
				else if(substr($row->productcode,0,3)==substr($prcode[0],0,3)) $prcode[0]=substr($prcode[0],0,3);
				else $prcode[0]="";
			}
			if((int)$basketcnt[0]==0) {
				$prcode[0]=$row->productcode;
				$prname[0]=str_replace('"','',strip_tags($row->productname));
			} else {
				$prname[0].="<br>".str_replace('"','',strip_tags($row->productname));
			}
			$productall[0][$basketcnt[0]]["prcode"]=$row->productcode;
			$productall[0][$basketcnt[0]]["prname"]=str_replace('"','',strip_tags($row->productname));
			if($row->vender>0) {
				if(strlen($prcode[$row->vender])>0) {
					if(substr($row->productcode,0,12)==substr($prcode[$row->vender],0,12)) $prcode[$row->vender]=substr($prcode[$row->vender],0,12);
					else if(substr($row->productcode,0,9)==substr($prcode[$row->vender],0,9)) $prcode[$row->vender]=substr($prcode[$row->vender],0,9);
					else if(substr($row->productcode,0,6)==substr($prcode[$row->vender],0,6)) $prcode[$row->vender]=substr($prcode[$row->vender],0,6);
					else if(substr($row->productcode,0,3)==substr($prcode[$row->vender],0,3)) $prcode[$row->vender]=substr($prcode[$row->vender],0,3);
					else $prcode[$row->vender]="";
				}
				if((int)$basketcnt[$row->vender]==0) {
					$prcode[$row->vender]=$row->productcode;
					$prname[$row->vender]=str_replace('"','',strip_tags($row->productname));
				} else {
					$prname[$row->vender].="<br>".str_replace('"','',strip_tags($row->productname));
				}
				$productall[$row->vender][$basketcnt[$row->vender]]["prcode"]=$row->productcode;
				$productall[$row->vender][$basketcnt[$row->vender]]["prname"]=str_replace('"','',strip_tags($row->productname));
			}

			if(ereg("^(\[OPTG)([0-9]{4})(\])$",$row->option1)){
				$optioncode = substr($row->option1,5,4);
				$row->option1="";
				$row->option_price="";
				if($row->optidxs!="") {
					$tempoptcode = substr($row->optidxs,0,-1);
					$exoptcode = explode(",",$tempoptcode);

					$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
					$resultopt = mysql_query($sqlopt,get_db_conn());
					if($rowopt = mysql_fetch_object($resultopt)){
						$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
						$opti=0;
						$option_choice = $rowopt->option_choice;
						$exoption_choice = explode("",$option_choice);
						while(strlen($optionadd[$opti])>0){
							if($exoptcode[$opti]>0){
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								$row->realprice+=($row->quantity*$exop[1]);
							}
							$opti++;
						}
					}
				}
			}

			if (strlen($row->option_price)==0) {
				$price = $row->realprice;
			} else if (strlen($row->opt1_idx)>0) {
				$option_price = $row->option_price;
				$pricetok=explode(",",$option_price);
				$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
			}
			$productall[0][$basketcnt[0]]["price"]=$price;
			$sumprice[0] += $price;

			if($row->vender>0) {
				$productall[$row->vender][$basketcnt[$row->vender]]["price"]=$price;
				$sumprice[$row->vender] += $price;
			}

			$basketcnt[0]++;
			if($row->vender>0) $basketcnt[$row->vender]++;

			if(strlen($row->productcode)==18) {
				$prname2[0][$row->productcode]=str_replace('"','',strip_tags($row->productname));

				$prprice[0][$row->productcode]=$price;
				$prprice[0][substr($row->productcode,0,3)]+=$price;
				if((int)$prbasketcnt[0][substr($row->productcode,0,3)]==0) {
					$prname2[0][substr($row->productcode,0,3)]=str_replace('"','',strip_tags($row->productname));
				} else {
					$prname2[0][substr($row->productcode,0,3)].="<br>".str_replace('"','',strip_tags($row->productname));
				}
				$prbasketcnt[0][substr($row->productcode,0,3)]++;

				$prprice[0][substr($row->productcode,0,6)]+=$price;
				if((int)$prbasketcnt[0][substr($row->productcode,0,6)]==0) {
					$prname2[0][substr($row->productcode,0,6)]=str_replace('"','',strip_tags($row->productname));
				} else {
					$prname2[0][substr($row->productcode,0,6)].="<br>".str_replace('"','',strip_tags($row->productname));
				}
				$prbasketcnt[0][substr($row->productcode,0,6)]++;

				$prprice[0][substr($row->productcode,0,9)]+=$price;
				if((int)$prbasketcnt[0][substr($row->productcode,0,9)]==0) {
					$prname2[0][substr($row->productcode,0,9)]=str_replace('"','',strip_tags($row->productname));
				} else {
					$prname2[0][substr($row->productcode,0,9)].="<br>".str_replace('"','',strip_tags($row->productname));
				}
				$prbasketcnt[0][substr($row->productcode,0,9)]++;

				$prprice[0][substr($row->productcode,0,12)]+=$price;
				if((int)$prbasketcnt[0][substr($row->productcode,0,12)]==0) {
					$prname2[0][substr($row->productcode,0,12)]=str_replace('"','',strip_tags($row->productname));
				} else {
					$prname2[0][substr($row->productcode,0,12)].="<br>".str_replace('"','',strip_tags($row->productname));
				}
				$prbasketcnt[0][substr($row->productcode,0,12)]++;

				if($row->vender>0) {
					$prname2[$row->vender][$row->productcode]=str_replace('"','',strip_tags($row->productname));

					$prprice[$row->vender][$row->productcode]=$price;
					$prprice[$row->vender][substr($row->productcode,0,3)]+=$price;
					if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,3)]==0) {
						$prname2[$row->vender][substr($row->productcode,0,3)]=str_replace('"','',strip_tags($row->productname));
					} else {
						$prname2[$row->vender][substr($row->productcode,0,3)].="<br>".str_replace('"','',strip_tags($row->productname));
					}
					$prbasketcnt[$row->vender][substr($row->productcode,0,3)]++;

					$prprice[$row->vender][substr($row->productcode,0,6)]+=$price;
					if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,6)]==0) {
						$prname2[$row->vender][substr($row->productcode,0,6)]=str_replace('"','',strip_tags($row->productname));
					} else {
						$prname2[$row->vender][substr($row->productcode,0,6)].="<br>".str_replace('"','',strip_tags($row->productname));
					}
					$prbasketcnt[$row->vender][substr($row->productcode,0,6)]++;

					$prprice[$row->vender][substr($row->productcode,0,9)]+=$price;
					if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,9)]==0) {
						$prname2[$row->vender][substr($row->productcode,0,9)]=str_replace('"','',strip_tags($row->productname));
					} else {
						$prname2[$row->vender][substr($row->productcode,0,9)].="<br>".str_replace('"','',strip_tags($row->productname));
					}
					$prbasketcnt[$row->vender][substr($row->productcode,0,9)]++;

					$prprice[$row->vender][substr($row->productcode,0,12)]+=$price;
					if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,12)]==0) {
						$prname2[$row->vender][substr($row->productcode,0,12)]=str_replace('"','',strip_tags($row->productname));
					} else {
						$prname2[$row->vender][substr($row->productcode,0,12)].="<br>".str_replace('"','',strip_tags($row->productname));
					}
					$prbasketcnt[$row->vender][substr($row->productcode,0,12)]++;
				}
			}
			$prname2[0][$prcode[0]]=$prname[0];
			$prprice[0][$prcode[0]]=$sumprice[0];

			$prname2[$row->vender][$prcode[$row->vender]]=$prname[$row->vender];
			$prprice[$row->vender][$prcode[$row->vender]]=$sumprice[$row->vender];

		}
		mysql_free_result($result);
?>
		<div class="pr_navi">
			<h3>사용쿠폰 선택</h3>
			<select class="basic_select" name=coupon_code onchange="change_group(options.value)">
			<option value="">쿠폰선택</option>
				<?
			$prscript="";
			//if($prcode=="") $prcode="ALL";
			for($i=0;$i<=$cnt;$i++) {
				if($prcode[$vender[$i]]=="") $prcode[$vender[$i]]="ALL";

				$isoptiondisplay=false;
				for($a=0;$a<count($productcode[$i]);$a++) {
					$num = strlen($productcode[$i][$a]);
					$tempprcode = substr($prcode[$vender[$i]],0,$num);

					if(($productcode[$i][$a]=="ALL" || ($use_con_type2[$i]=="Y" && $tempprcode==$productcode[$i][$a]) || ($use_con_type1[$i]=="Y" && $use_con_type2[$i]=="Y" && $productcode[$i][$a]!="ALL" && strlen($prname2[$vender[$i]][$productcode[$i][$a]])>0) || ($use_con_type2[$i]=="N" && $use_con_type1[$i]=="N" && strlen($prname2[$vender[$i]][$productcode[$i][$a]])==0) || ($use_con_type1[$i]=="Y" && $use_con_type2[$i]=="N" && $productcode[$i][$a]!="ALL" && $sumprice[$vender[$i]]-$prprice[$vender[$i]][$productcode[$i][$a]]-$usereserve>0)) && ($mini_price[$i]==0 || $mini_price[$i]<=($sumprice[$vender[$i]]-$usereserve)) && isset($prprice[$vender[$i]])==true) {
						$isoptiondisplay=true;
					}

					if($use_con_type2[$i]=="N") {
						$tmp_prname="";
						$tmp_sumprice=0;
						$tmp_prprice=0;
						$kk=0;
						$temparr=$productall[$vender[$i]];
						if(is_array($temparr)) {
							while(list($key,$val)=each($temparr)) {
								if(substr($val["prcode"],0,$num)!=$productcode[$i][$a]) {
									if($kk>0) $tmp_prname.="<br> ";
									$tmp_prname.=$val["prname"];
									$tmp_prprice+=$val["price"];
									$kk++;
								}
								$tmp_sumprice+=$val["price"];
							}
						}
					} else {
						$tmp_prname="";
						$tmp_sumprice=0;
						$tmp_prprice=0;
						$kk=0;
						$temparr=$productall[$vender[$i]];
						if(is_array($temparr)) {
							while(list($key,$val)=each($temparr)) {
								if((substr($val["prcode"],0,$num)==$productcode[$i][$a]) || $productcode[$i][$a]=="ALL") {
									if($kk>0) $tmp_prname.="<br> ";
									$tmp_prname.=$val["prname"];
									$tmp_prprice+=$val["price"];
									$kk++;
								}
								$tmp_sumprice+=$val["price"];
							}
						}
					}
				}

				if($isoptiondisplay==true) {
					echo "<option value=\"".$i."\" style=\"color:#FFFFFF;\">".$coupon_code[$i]."</option>\n";
				}

				$prscript.="var prval=new prvalue();\n";
				$prscript.="prval.bank_only=\"".$bank_only[$i]."\";\n";
				$prscript.="prval.sale_type=\"".$sale_type[$i]."\";\n";
				$prscript.="prval.use_con_type2=\"".$use_con_type2[$i]."\";\n";
				$prscript.="prval.sale_money=\"".$sale_money[$i]."\";\n";

				$prscript.="prval.prname=\"".$tmp_prname."\";\n";
				$prscript.="prval.prprice=\"".number_format($tmp_prprice-$usereserve)."\";\n";
				$prscript.="all_list[".$i."]=prval;\n";
				$prscript.="prval=null;\n";
			}
?>
			</select>
			<? echo "<script>\n".$prscript."</script>\n"; ?>

			<div style="display:none">
			<span id=idx_sale_money1 style="color:red">─</span>
			<span id=idx_sale_money2 style="color:red">─</span>
			<!--<span id=idx_prname style="color:#333333"><?=$prname[0]?></span>
			<span id=idx_prprice style="color:#333333"><?=number_format($sumprice[0])."원";?></span>-->
			<input type=hidden name=prname value="<?=$prname[0]?>">
			<input type=hidden name=prprice value="<?=number_format(($sumprice[0]>0?($sumprice[0]-$usereserve):0))."원";?>">
			<input type=hidden name=sale_money1 value="─">
			<input type=hidden name=sale_money2 value="─">
			</div>

		</div>	
		
		
			
		<section class="basic_btn_area btn_small">
			<button type="button" class="basic_btn c1" onClick="javascript:CheckForm()"><span>확인</span></button> <button type="button" class="basic_btn" onClick="javascript:coupon_cancel();"><span>닫기</span></button>
		</section>		
	
		<input type=hidden name=prname value="<?=$prname[0]?>">
		<input type=hidden name=prprice value="<?=number_format($sumprice[0])."원";?>">
		<input type=hidden name=sale_money1 value="─">
		<input type=hidden name=sale_money2 value="─">	

	<?} else {?>
		<section class="basic_btn_area btn_small">
		 <button type="button" class="basic_btn" onClick="javascript:window.close()"><span>닫기</span></button>
		</section>		
	<?}?>

	</div>
</form>



<SCRIPT LANGUAGE="JavaScript">
<!--
function change_group(idx){
	if(idx.length>0) {
		idx = parseInt(idx);
		sale_money="";
		for(var i=0; i<all_list[idx].sale_money.length; i++) {
			var tmp = all_list[idx].sale_money.length-(i+1)
			if(i%3==0 && i!=0) sale_money = ',' + sale_money
			sale_money = all_list[idx].sale_money.charAt(tmp) + sale_money
		}
		if(all_list[idx].sale_type%2==0){
			money1 = document.form1.sale_money1;
			money2 = document.form1.sale_money2;
		} else{
			money1 = document.form1.sale_money2;
			money2 = document.form1.sale_money1;
		}
		if(all_list[idx].sale_type<=2) {
			money1.value=sale_money+"%";
		} else {
			money1.value=sale_money+"원";
		}
		money2.value="─";
		if(all_list[idx].sale_type%2==0){
			document.all["idx_sale_money1"].innerHTML=money1.value;
			document.all["idx_sale_money2"].innerHTML=money2.value;
		} else{
			document.all["idx_sale_money1"].innerHTML=money2.value;
			document.all["idx_sale_money2"].innerHTML=money1.value;
		}

		document.all["idx_prname"].innerHTML=all_list[idx].prname;
		document.all["idx_prprice"].innerHTML=all_list[idx].prprice+"원";
		document.form1.bank_only.value=all_list[idx].bank_only;
	} else {
		document.form1.sale_money1.value="─";
		document.form1.sale_money2.value="─";
		document.form1.bank_only.value="N";

		document.all["idx_sale_money1"].innerHTML=document.form1.sale_money1.value;
		document.all["idx_sale_money2"].innerHTML=document.form1.sale_money2.value;
		document.all["idx_prname"].innerHTML=document.form1.prname.value;
		document.all["idx_prprice"].innerHTML=document.form1.prprice.value;
	}
}
//-->
</SCRIPT>
</body>
</html>
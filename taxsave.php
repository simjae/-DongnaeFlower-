<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

function getParseTax($temp) {
	$val = array();
	$list = explode("<br>\n",$temp);
	for ($i=0;$i<count($list); $i++) {
		$data = explode("=",$list[$i]);
		$val[$data[0]] = $data[1];
	}
	return $val;
}

$ordercode=$_POST["ordercode"];
$productname=urldecode($_POST["productname"]);

if(strlen($ordercode)==0) {
	echo "<html></head><body onload=\"alert('정상적인 경로로 접근하시기 바랍니다.');window.close()\"></body></html>";exit;
}

$tax_cnum="";
$sql = "SELECT tax_cnum,tax_cname,tax_cowner,tax_caddr,tax_ctel,tax_type,tax_rate,tax_mid,tax_tid ";
$sql.= "FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);

$tax_cnum=$row->tax_cnum;
$taxsavetype=$row->tax_type;
$tax_rate=$row->tax_rate;

$tax_no=$row->tax_cnum;
$kcp_mid=$row->tax_mid;
$kcp_tid=$row->tax_tid;

$tax_cnum1=substr($tax_cnum,0,3);
$tax_cnum2=substr($tax_cnum,3,2);
$tax_cnum3=substr($tax_cnum,5,5);

if(strlen($tax_cnum)==0 || $taxsavetype=="N") {
	echo "<html></head><body onload=\"alert('본 쇼핑몰에서는 현금영수증 발급 기능을 지원하지 않습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.');window.close()\"></body></html>";exit;
}

$sql = "SELECT ordercode,tsdtime,tax_no,type,authno,id_info,mtrsno FROM tbltaxsavelist WHERE ordercode='".$ordercode."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$date = date("Ymd",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
	if($row->type=="Y" && $date."020000">=$row->tsdtime) {
		$query ="cashtype=QURY";
		$query.="&midbykcp=".$kcp_mid;
		$query.="&termid=".$kcp_tid;
		$query.="&cashipaddress1=203.238.36.160";
		$query.="&cashportno1=9981";
		$query.="&cashipaddress2=203.238.36.161";
		$query.="&cashportno2=9981";
		$query.="&tax_no=".$tax_no;

		$id_info=$row->id_info;
		$authno=$row->authno;

		$query.="&tsdtime=".substr($row->tsdtime,2);
		$query.="&id_info=".$row->id_info;
		$query.="&authno=".$row->authno;
		$query.="&mtrsno=".$row->mtrsno;

		//cgi 호출
		$host_url=getenv("HTTP_HOST");
		$host_cgi="/".RootPath.CashcgiDir."bin/cgiway.cgi";

		$resdata=SendSocketPost($host_url,$host_cgi,$query);
		$_taxdata=getParseTax($resdata);

		if(count($_taxdata)>0 && strlen($_taxdata["mrspc"])>0) {
			if($_taxdata["mrspc"]!="00") {
				$msg="현금영수증 조회가 실패하였습니다.\\n\\n--------------------실패사유--------------------\\n\\n".$_taxdata["resp_msg"];
				echo "<script>alert('".$msg."');window.close();</script>";
				exit;
			}
		} else {
			echo "<script>alert('현금영수증 서버 연결이 실패하였습니다.');window.close();</script>";
			exit;
		}

		include ($Dir."lib/taxsaveview.inc.php");

		exit;
	} else if($row->type=="Y") {
		$msg="이미 현금영수증을 발급하셨습니다.\\n\\n발급된 현금영수증은 발급 후 1일 후에 국체청 홈페이지에서 확인이 가능합니다.";
	} else {
		$msg="이미 현금영수증을 발급요청하셨습니다.";
	}
	if(strlen($msg)>0) {
		echo "<html></head><body onload=\"alert('".$msg."');window.close()\"></body></html>";exit;
	}
	exit;
}
mysql_free_result($result);

$mode=$_POST["mode"];

if($mode=="update") {
	$up_tr_code=$_POST["up_tr_code"];
	$up_gbn=$_POST["up_gbn"];
	$up_resno1=$_POST["up_resno1"];
	$up_resno2=$_POST["up_resno2"];
	$up_mobile1=$_POST["up_mobile1"];
	$up_mobile2=$_POST["up_mobile2"];
	$up_mobile3=$_POST["up_mobile3"];
	$up_comnum1=$_POST["up_comnum1"];
	$up_comnum2=$_POST["up_comnum2"];
	$up_comnum3=$_POST["up_comnum3"];

	$sql = "SELECT price,paymethod,bank_date,sender_name,sender_email,sender_tel,del_gbn FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$up_name=$row->sender_name;
		$up_email=$row->sender_email;
		$up_tel=$row->sender_tel;
		$up_productname=$productname;

		if(preg_match("/^(B|O|Q){1}/", $row->paymethod) && $row->deli_gbn!="C") {
			if($tax_rate==10) {
				$up_amt1=$row->price;
				$up_amt4=floor(($up_amt1/1.1)*0.1);
				$up_amt2=$up_amt1-$up_amt4;
				$up_amt3=0;
			} else {
				$up_amt1=$row->price;
				$up_amt2=0;
				$up_amt3=0;
				$up_amt4=0;
			}

			if($up_amt1<1) {
				echo "<html></head><body onload=\"alert('구매금액이 1원 이상 부터 현금영수증 발급이 가능합니다.');window.close()\"></body></html>";exit;
			}

			if($up_tr_code=="0") {	//개인
				if($up_gbn=="0") {
					$up_id_info=$up_resno1.$up_resno2;	//주민번호
				} else {
					$up_id_info=$up_mobile1.$up_mobile2.$up_mobile3;	//핸드폰번호
				}
			} else {	//사업자
				$up_id_info=$up_comnum1.$up_comnum2.$up_comnum3;	//사업자번호
			}

			$tsdtime=date("YmdHis");
			$sql = "INSERT tbltaxsavelist SET ";
			$sql.= "ordercode		= '".$ordercode."', ";
			$sql.= "tsdtime			= '".$tsdtime."', ";
			$sql.= "tr_code			= '".$up_tr_code."', ";
			$sql.= "tax_no			= '".$tax_cnum."', ";
			$sql.= "id_info			= '".$up_id_info."', ";
			$sql.= "name			= '".$up_name."', ";
			$sql.= "tel				= '".$up_tel."', ";
			$sql.= "email			= '".$up_email."', ";
			$sql.= "productname		= '".$up_productname."', ";
			$sql.= "amt1			= ".$up_amt1.", ";
			$sql.= "amt2			= ".$up_amt2.", ";
			$sql.= "amt3			= ".$up_amt3.", ";
			$sql.= "amt4			= ".$up_amt4.", ";
			$sql.= "type			= 'N' ";
			mysql_query($sql,get_db_conn());

			if(mysql_error()) {
				echo "<html></head><body onload=\"alert('현금영수증 발급요청이 실패하였습니다.');history.go(-1)\"></body></html>";exit;
			} else {
				//자동발급
				if($taxsavetype=="Y" && strlen($row->bank_date)==14 && $row->deli_gbn!="C") {
					$flag="Y";
					include($Dir."lib/taxsave.inc.php");
				}
				if(strlen($msg)>0) {
					echo "<html></head><body onload=\"alert('".$msg."');window.close()\"></body></html>";exit;
				} else {
					echo "<html></head><body onload=\"alert('현금영수증 개별발급 요청이 완료되었습니다.\\n\\n관리자가 확인 후 발급해드립니다.\\n\\n발급된 현금영수증은 발급 1일 후에 확인이 가능합니다.');window.close()\"></body></html>";exit;
				}
			}
		}
	}
	mysql_free_result($result);
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>현금영수증 발급신청</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="format-detection" content="telephone=no" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<link rel="stylesheet" href="./css/common.css" />
<style>
	html,body,div,section,table,tr,td,th,form{margin:0px;border:0px;padding:0px;font-size:14px;line-height:180%;}
	h1{font-size:1.2em;height:35px;line-height:35px;text-align:center;background-color:#555555;color:#FFFFFF;letter-spacing: 4px}
	#wrap{margin-top:10px;}
	#select_wrap{margin-bottom:8px;height:20px;text-align:center;}
	.title{display:none;}
	.text{height:22px;border:1px solid #9C9C9C;}
	.title_area{width:100px;}
	.text_area{height:30px;}
	#individual_wrap{}
	#licensee_wrap{display:none;}
	#btn_wrap{text-align:center;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
//window.resizeTo(272,270);

function CheckForm() {
	if(document.form1.up_tr_code[0].checked==true) {
		if(document.form1.up_gbn[0].checked==true) {
			if(document.form1.up_resno1.value.length==0 || document.form1.up_resno2.value.length==0 || document.form1.up_resno1.value.length!=6 || document.form1.up_resno2.value.length!=7) {
				alert("주민번호를 정확히 입력하세요.");
				document.form1.up_resno1.focus();
				return;
			}
			if(!chkResNo(document.form1.up_resno1.value+"-"+document.form1.up_resno2.value)) {
				alert("주민번호 입력이 잘못되었습니다.");
				document.form1.up_resno1.focus();
				return;
			}
		} else {
			mobile1=document.form1.up_mobile1;
			mobile2=document.form1.up_mobile2;
			mobile3=document.form1.up_mobile3;
			if(mobile1.value.length==0 || mobile2.value.length==0 || mobile3.value.length==0) {
				alert("핸드폰번호를 정확히 입력하세요.");
				mobile1.focus();
				return;
			}
			if(!IsNumeric(mobile1.value)) {
				alert("핸드폰번호는 숫자만 입력하세요.");
				mobile1.focus();
				return;
			}
			if(!IsNumeric(mobile2.value)) {
				alert("핸드폰번호는 숫자만 입력하세요.");
				mobile2.focus();
				return;
			}
			if(!IsNumeric(mobile3.value)) {
				alert("핸드폰번호는 숫자만 입력하세요.");
				mobile3.focus();
				return;
			}
			if(mobile1.value=="010" || mobile1.value=="011" || mobile1.value=="016" || mobile1.value=="017" || mobile1.value=="018" || mobile1.value=="019") {
				if(mobile2.value.length<3 && mobile3.value.length<4) {
					alert("핸드폰번호를 정확히 입력하세요.");
					mobile2.focus();
					return;
				}
			} else {
				alert("핸드폰번호를 정확히 입력하세요.");
				mobile1.focus();
				return;
			}
		}
	} else {
		//사업자번호 체크
		biz1=document.form1.up_comnum1.value;
		biz2=document.form1.up_comnum2.value;
		biz3=document.form1.up_comnum3.value;
		if(!chkBizNo(biz1+""+biz2+""+biz3)) {
			alert("사업자번호 입력이 잘못되었습니다.");
			document.form1.up_comnum1.focus();
			return;
		}
	}

	document.form1.mode.value="update";
	document.form1.submit();
}

function ViewLayer(layer) {
	individual = document.getElementById('individual_wrap');
	licensee = document.getElementById('licensee_wrap');
	btn_licensee = document.getElementById('idx_tr_2');
	btn_individul = document.getElementById('idx_tr_1');
	if(layer=="layer2") { // 사업자
		btn_individul.classList.remove('black');
		btn_individul.classList.add('white');
		btn_licensee.classList.remove('white');
		btn_licensee.classList.add('black');

		individual.style.display="none";
		licensee.style.display="block";
		document.form1.up_gbn[2].checked=true;
		document.form1.up_comnum1.disabled=false;
		document.form1.up_comnum2.disabled=false;
		document.form1.up_comnum3.disabled=false;

		document.form1.up_resno1.disabled=true;
		document.form1.up_resno2.disabled=true;
		document.form1.up_mobile1.disabled=true;
		document.form1.up_mobile2.disabled=true;
		document.form1.up_mobile3.disabled=true;

		document.form1.up_resno1.value="";
		document.form1.up_resno2.value="";
		document.form1.up_mobile1.value="";
		document.form1.up_mobile2.value="";
		document.form1.up_mobile3.value="";

	} else {
		btn_licensee.classList.remove('black');
		btn_licensee.classList.add('white');
		btn_individul.classList.remove('white');
		btn_individul.classList.add('black');
		
		licensee.style.display="none";
		individual.style.display="block";
		document.form1.up_gbn[0].checked=true;

		document.form1.up_comnum1.disabled=true;
		document.form1.up_comnum2.disabled=true;
		document.form1.up_comnum3.disabled=true;

		document.form1.up_comnum1.value="";
		document.form1.up_comnum2.value="";
		document.form1.up_comnum3.value="";

		document.form1.up_mobile1.disabled=true;
		document.form1.up_mobile2.disabled=true;
		document.form1.up_mobile3.disabled=true;

		document.form1.up_resno1.disabled=false;
		document.form1.up_resno2.disabled=false;
	}
}
function change_gbn(gbn) {

	if(gbn==0) {
		
		document.form1.up_resno1.disabled=false;
		document.form1.up_resno2.disabled=false;
		document.form1.up_mobile1.disabled=true;
		document.form1.up_mobile2.disabled=true;
		document.form1.up_mobile3.disabled=true;
		document.form1.up_mobile1.value="";
		document.form1.up_mobile2.value="";
		document.form1.up_mobile3.value="";
	} else if(gbn==1) {
		
		document.form1.up_mobile1.disabled=false;
		document.form1.up_mobile2.disabled=false;
		document.form1.up_mobile3.disabled=false;
		document.form1.up_resno1.disabled=true;
		document.form1.up_resno2.disabled=true;
		document.form1.up_resno1.value="";
		document.form1.up_resno2.value="";
	}
}

//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<h1>
	현금영수증 발급 신청
</h1>
<section id="wrap">
	<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
		<input type="hidden" name=mode>
		<input type="hidden" name=ordercode value="<?=$ordercode?>">
		<input type="hidden" name=productname value="<?=urlencode($productname)?>">
		<div id="select_wrap">
			<!-- 현금영수증 종류 -->
			<input type="radio" name="up_tr_code" value="0" onclick="ViewLayer('layer1');" CHECKED id="idx_tr_code0" class="title"/><label for="idx_tr_code0" id="idx_tr_1" class="button black small">개인</label>&nbsp;&nbsp;
			<input type="radio" name="up_tr_code" value="1" onclick="ViewLayer('layer2');" id="idx_tr_code1" class="title"/><label for="idx_tr_code1" id="idx_tr_2" class="button white small">사업자</label>
			<!-- //현금영수증 종류 -->
		</div>
		<div id="individual_wrap">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th class="title_area"><input type="radio" name="up_gbn" value="0" id="idx_gbn0" onclick="change_gbn(0);" CHECKED class="title"/><label for="idx_gbn0" class="button white small">주민번호</label></th>
					<td class="text_area">
						<input type="number" name="up_resno1" maxLength="6" onkeyup="strnumkeyup(this);" class="text" style="width:45%;"/> - <input type="number" name="up_resno2" maxLength="7" onkeyup="strnumkeyup(this);" class="text" style="width:45%;"/>
					</td>
				</tr>
				<tr>
					<th class="title_area"><input type="radio" name="up_gbn" value="1" id="idx_gbn1" onclick="change_gbn(1);" class="title"><label for="idx_gbn1" class="button white small">휴대폰</label></th>
					<td class="text_area">
						<input type="tel" name="up_mobile1" maxLength="3" onkeyup="strnumkeyup(this);" disabled class="text" style="width:27%;"/> - <input type="tel" name="up_mobile2" maxLength="4" onkeyup="strnumkeyup(this);" disabled class="text" style="width:29%;"/> - <input type="tel" name="up_mobile3" maxLength="4" onkeyup="strnumkeyup(this);" disabled class="text" style="width:29%;"/>
					</td>
				</tr>
			</table>
		</div>
		<div id="licensee_wrap">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th class="title_area"><input type="radio" name="up_gbn" value="2" id="idx_gbn2" class="title"/><label for="idx_gbn2" class="button white small">사업자번호</label></th>
					<td class="text_area">
						<input type="number" name="up_comnum1" maxLength="3" onkeyup="strnumkeyup(this);" disabled class="text" style="width:29%;"/> - <input type="number" name="up_comnum2" maxLength="2" onkeyup="strnumkeyup(this);" disabled class="text" style="width:27%;"/> - <input type="number" name="up_comnum3" maxLength="5" onkeyup="strnumkeyup(this);" disabled class="text" style="width:29%;"/>
					</td>
				</tr>
			</table>
			
			
		</div>
	</form>
	<div id="btn_wrap">
	<A HREF="javascript:CheckForm()" class="button white small">현금영수증 요청</A>
	</div>
</section>
</body>
</html>
<?php
	include_once("header.php");
	include_once($Dir."app/inc/paging_inc.php");
	include_once($Dir."lib/ext/product_func.php");
	include_once($Dir."lib/ext/member_func.php");
	include_once($Dir."lib/ext/coupon_func.php");

	$vidx = $_REQUEST["vidx"];
	$refURL = $_REQUEST["refURL"];
	$refPage = $_REQUEST["refPage"];

	//옵션 클래스 2016-09-26 Seul
	include_once($Dir."lib/class/option.php");
	$optClass = new Option;

    //플러그인
    include_once($Dir."lib/plugIn_lib.php");

    $_FileInfo = _uploadMaxFileSize();

	$_MAX_FILE_SIZE = $_FileInfo['maxsize'];
	$_MSG_UNIT = $_FileInfo['unit'];
	$primgsrc = $Dir."data/shopimages/product/";
	$sid = $_REQUEST["sid"];
	$sql = "SELECT id,pcode FROM tblsnsproduct WHERE code='".$sid."'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$sell_memid = ($_ShopInfo->getMemid() != $row->id)? $row->id:"";
	}
	mysql_free_result($result);


	$mode=$_REQUEST["mode"];
	$coupon_code=$_REQUEST["coupon_code"];

	
	
	$code=$_REQUEST["code"];
	$productcode=$_REQUEST["productcode"];
	if(strlen($code)==0) {
		$code=substr($productcode,0,12);
	}
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);
	if(strlen($codeA)!=3) $codeA="000";
	if(strlen($codeB)!=3) $codeB="000";
	if(strlen($codeC)!=3) $codeC="000";
	if(strlen($codeD)!=3) $codeD="000";
	$likecode=$codeA;
	if($codeB!="000") $likecode.=$codeB;
	if($codeC!="000") $likecode.=$codeC;
	if($codeD!="000") $likecode.=$codeD;

	$sort=$_REQUEST["sort"];
	$brandcode=$_REQUEST["brandcode"];


	//QR코드 생성
	include_once($Dir."phpqrcode/qrlib.php");

	$QR_http_host = $_SERVER['HTTP_HOST'];
	$QR_request_uri = $_SERVER['REQUEST_URI'];
	$QR_url = 'http://' . $QR_http_host . $QR_request_uri;
	QRcode::png($QR_url,$Dir."data/qrcode/".$productcode.".png",QR_ECLEVEL_L, 25);


	$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
	$selfcodefont_end = "</font>"; //진열코드 폰트 끝

	$categoryNavi = getCategoryMobile($code, $_ShopInfo->getMemgroup(), $_ShopInfo->getMemid());

	$_cdata="";
	$_pdata="";
	if(strlen($productcode)==18) {
	

		$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD='".$codeD."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$_cdata=$row;
			if($row->group_code=="NO") {	//숨김 분류
				echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');location.href='main.php';\"></body></html>";exit;
			} else if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
				Header("Location:".$Dir."app/login.php?chUrl=".getUrl());
				exit;
			} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
				echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');history.go(-1);\"></body></html>";exit;
			}

			//Wishlist 담기
			if($mode=="wishlist") {
				if(strlen($_ShopInfo->getMemid())==0) {	//비회원
					echo "<html></head><body onload=\"alert('로그인을 하셔야 본 서비스를 이용하실 수 있습니다.');location.href='./login.php?chUrl=".getUrl()."';\"></body></html>";exit;
				}
				$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
				$result2=mysql_query($sql,get_db_conn());
				$row2=mysql_fetch_object($result2);
				$totcnt=$row2->totcnt;
				mysql_free_result($result2);
				$maxcnt=20;
				if($totcnt>=$maxcnt) {
					$sql = "SELECT b.productcode ";
					$sql.= "FROM tblwishlist a, tblproduct b ";
					$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
					$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
					$sql.= "AND b.display='Y' ";
					$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
					$sql.= "GROUP BY b.productcode ";

					$result2=mysql_query($sql,get_db_conn());
					$i=0;
					$wishprcode="";
					while($row2=mysql_fetch_object($result2)) {
						$wishprcode.="'".$row2->productcode."',";
						$i++;
					}
					mysql_free_result($result2);
					$totcnt=$i;
					$wishprcode=substr($wishprcode,0,-1);
					if(strlen($wishprcode)>0) {
						$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode NOT IN (".$wishprcode.") ";
						mysql_query($sql,get_db_conn());
					}
				}
				if($totcnt<$maxcnt) {
					$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='".$productcode."' ";
					$result2=mysql_query($sql,get_db_conn());
					$row2=mysql_fetch_object($result2);
					$cnt=$row2->cnt;
					mysql_free_result($result2);
					if($cnt>0) {
						echo "<html></head><body onload=\"alert('WishList에 이미 등록된 상품입니다.');history.go(-1);\"></body></html>";exit;
					} else {
						$sql = "INSERT tblwishlist SET ";
						$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
						$sql.= "productcode	= '".$productcode."', ";
						$sql.= "date		= '".date("YmdHis")."' ";
						mysql_query($sql,get_db_conn());
						echo "<html></head><body onload=\"alert('WishList에 해당 상품을 등록하였습니다.');history.go(-1);\"></body></html>";exit;
					}
				} else {
					echo "<html></head><body onload=\"alert('WishList에는 ".$maxcnt."개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.');history.go(-1);\"></body></html>";exit;
				}
			}
		} else {
			echo "<html></head><body onload=\"alert('해당 분류가 존재하지 않습니다.');location.href='main.php';\"></body></html>";exit;
		}
		mysql_free_result($result);

		$sql = "SELECT a.* ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode='".$productcode."' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$result=mysql_query($sql,get_db_conn());

		if($row=mysql_fetch_object($result)) {
			$_pdata=$row;
			if($row->display == "Y"){
				$sql = "SELECT * FROM tblproductbrand ";
				$sql.= "WHERE bridx='".$_pdata->brand."' ";
				$bresult=mysql_query($sql,get_db_conn());
				$brow=mysql_fetch_object($bresult);
				$_pdata->brandcode = $_pdata->brand;
				$_pdata->brand = $brow->brandname;

				mysql_free_result($result);

				if($_pdata->assembleuse=="Y") {
					$sql = "SELECT * FROM tblassembleproduct ";
					$sql.= "WHERE productcode='".$productcode."' ";
					$result=mysql_query($sql,get_db_conn());
					if($row=@mysql_fetch_object($result)) {
						$_adata=$row;
						mysql_free_result($result);
						$assemble_list_pridx = str_replace("","",$_adata->assemble_list);

						if(strlen($assemble_list_pridx)>0) {
							$sql = "SELECT pridx,productcode,productname,sellprice,quantity,tinyimage FROM tblproduct ";
							$sql.= "WHERE pridx IN ('".str_replace(",","','",$assemble_list_pridx)."') ";
							$sql.= "AND assembleuse!='Y' ";
							$sql.= "AND display='Y' ";
							$result=mysql_query($sql,get_db_conn());
							while($row=@mysql_fetch_object($result)) {
								$_acdata[$row->pridx] = $row;
							}
							mysql_free_result($result);
						}
					}
				}
			}else{
				echo "<html></head><body onload=\"alert('판매 보류중인 상품입니다.');history.go(-1);\"></body></html>";exit;
			}
		} else {
			echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');location.href='main.php'\"></body></html>";exit;
	}

	if($mode=="coupon" && strlen($coupon_code)==8 && strlen($productcode)==18) {	//쿠폰 발급
		if(strlen($_ShopInfo->getMemid())==0) {	//비회원
			echo "<html></head><body onload=\"alert('로그인 후 쿠폰 다운로드가 가능합니다.');location.href='./login.php?chUrl=".getUrl()."';\"></body></html>";exit;
		} else {
			$sql = "SELECT * FROM tblcouponinfo ";
			if($_pdata->vender>0) {
				$sql.= "WHERE (vender='0' OR vender='".$_pdata->vender."') ";
			} else {
				$sql.= "WHERE vender='0' ";
			}
			$sql.= "AND coupon_code='".$coupon_code."' ";
			$sql.= "AND display='Y' AND issue_type='Y' AND detail_auto='Y' ";
			$sql.= "AND (date_end>".date("YmdH")." OR date_end='') ";
			$sql.= "AND ((use_con_type2='Y' AND productcode IN ('ALL','".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','".$code."','".$productcode."')) OR (use_con_type2='N' AND productcode NOT IN ('".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','".$code."','".$productcode."'))) ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
					$onload="<script>alert(\"모든 쿠폰이 발급되었습니다.\");</script>";
				} else {
					$date=date("YmdHis");
					if($row->date_start>0) {
						$date_start=$row->date_start;
						$date_end=$row->date_end;
					} else {
						$date_start = substr($date,0,10);
						$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
					}
					$sql = "INSERT tblcouponissue SET ";
					$sql.= "coupon_code	= '".$coupon_code."', ";
					$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
					$sql.= "date_start	= '".$date_start."', ";
					$sql.= "date_end	= '".$date_end."', ";
					$sql.= "date		= '".$date."' ";
					mysql_query($sql,get_db_conn());
					if(!mysql_errno()) {
						$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
						$sql.= "WHERE coupon_code = '".$coupon_code."'";
						mysql_query($sql,get_db_conn());

						$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
					} else {
						if($row->repeat_id=="Y") {	//동일인 재발급이 가능하다면,,,,
							$sql = "UPDATE tblcouponissue SET ";
							if($row->date_start<=0) {
								$sql.= "date_start	= '".$date_start."', ";
								$sql.= "date_end	= '".$date_end."', ";
							}
							$sql.= "used		= 'N' ";
							$sql.= "WHERE coupon_code='".$coupon_code."' ";
							$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
							mysql_query($sql,get_db_conn());
							$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
						} else {
							$onload="<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\");</script>";
						}
					}
				}
			} else {
				$onload="<script>alert(\"해당 쿠폰은 사용 가능한 쿠폰이 아닙니다.\");</script>";
			}
			mysql_free_result($result);
		}
	}

	$ref=$_REQUEST["ref"];
	if (strlen($ref)==0) {
		$ref=strtolower(ereg_replace("http://","",getenv("HTTP_REFERER")));
		if(strpos($ref,"/") != false) $ref=substr($ref,0,strpos($ref,"/"));
	}

	if(strlen($ref)>0 && strlen($_ShopInfo->getRefurl())==0) {
		$sql2="SELECT * FROM tblpartner WHERE url LIKE '%".$ref."%' ";
		$result2 = mysql_query($sql2,get_db_conn());
		if ($row2=mysql_fetch_object($result2)) {
			mysql_query("UPDATE tblpartner SET hit_cnt = hit_cnt+1 WHERE url = '".$row2->url."'",get_db_conn());
			$_ShopInfo->setRefurl($row2->id);
			$_ShopInfo->Save();
		}
		mysql_free_result($result2);
	}

	if(strlen($productcode)==18) {
		$viewproduct=$_COOKIE["ViewProduct"];
		if(strrpos(" ".$viewproduct,",".$productcode.",")==0) {
			if(strlen($viewproduct)==0) {
				$viewproduct=",".$productcode.",";
			} else {
				$viewproduct=",".$productcode.$viewproduct;
			}
		} else {
			$viewproduct=str_replace(",".$productcode.",",",",$viewproduct);
			$viewproduct=",".$productcode.$viewproduct;
		}
		$viewproduct=substr($viewproduct,0,571);
		setcookie("ViewProduct",$viewproduct,0,"/".RootPath);
	}


	//상품 상세 공통 이벤트 관리
	if(strlen($_cdata->detail_type)==5) {	//개별디자인이 아닐 경우
		$sql = "SELECT * FROM tbldesignnewpage WHERE type='detailimg' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$row->body=str_replace("[DIR]",$Dir,$row->body);
			$design_type=$row->code;
			$detailimg_eventloc=$row->leftmenu;
			$detailimg_body="<table border=0 cellpadding=0 cellspacing=0>\n";
			if($design_type=="1") {	//이미지 타입
				$detailimg_body.="<tr><td align=center><img src=\"".$Dir.DataDir."shopimages/etc/".$row->filename."\" border=0></td></tr>\n";
			} else if($design_type=="2") {	//html 타입
				$detailimg_body.="<tr><td align=center>".$row->body."</td></tr>\n";
			}
			$detailimg_body.="</table>\n";
		}
		mysql_free_result($result);
	}

	//추천관련상품
	/* coll_loc => 0:사용안함, 1:상세화면 상단 위치, 2:상세화면 하단 위치, 3:상세화면 오른쪽 위치 */
	if($_data->coll_loc>0) {
		$sql = "SELECT collection_list FROM tblcollection ";
		$sql.= "WHERE (productcode='".substr($code,0,3)."000000000' ";
		$sql.= "OR productcode='".substr($code,0,6)."000000' OR productcode='".substr($code,0,9)."000' ";
		$sql.= "OR productcode='".substr($code,0,12)."' OR productcode='".$productcode."') ";
		$sql.= "ORDER BY productcode DESC LIMIT 1 ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$collection_list=$row->collection_list;
		mysql_free_result($result);

		if(strlen($collection_list)>0) {
			$collection=ereg_replace(",","','",$collection_list);
			$sql = "SELECT a.productcode,a.productname,a.sellprice,a.tinyimage,a.etctype,a.reserve,a.reservetype,a.consumerprice,a.option_price,a.tag,a.quantity,a.selfcode ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE a.productcode IN ('".$collection."') ";
			$sql.= "AND a.display='Y' AND a.productcode!='".$productcode."' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			$sql.= "ORDER BY FIELD(a.productcode,'".$collection."') LIMIT ".$_data->coll_num;
			$result=mysql_query($sql,get_db_conn());
			$collcnt=mysql_num_rows($result);
			if($collcnt<$_data->coll_num) $collcnt=$_data->coll_num;
			$collection_body="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" ";
			if($_data->coll_loc=="3") {
				$collection_body.="width=\"100%\" style=\"table-layout:fixed\">\n";
				$collection_body.="<tr>\n";
				$collection_body.="	<td style=\"padding:5;border:#dddddd solid 1\">\n";
				$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			} else {
				$collection_body.="width=100%>";
				$collection_body.="<tr>\n";
				$collection_body.="	<td width=100% style=\"padding:5\">\n";
				$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
				$collection_body.="	<tr>\n";
			}
			$tag_detail_count=2;
			$i=0;
			while($row=mysql_fetch_object($result)) {
				if($_data->coll_loc=="3") {
					if($i>0) {
						$collection_body.="<tr><td height=\"3\"></td></tr>\n";
						$collection_body.="<tr>\n";
						$collection_body.="	<td align=\"center\">";
						$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\" style=\"table-layout:fixed\"><tr><td height=\"1\" bgcolor=\"#dddddd\"></td></tr></table>\n";
						$collection_body.="	</td>\n";
						$collection_body.="</tr>\n";
						$collection_body.="<tr><td height=\"5\"></td></tr>\n";
					} else {
						$collection_body.="<tr><td height=\"3\"></td></tr>\n";
					}
					$collection_body.="<tr>\n";
					$collection_body.="	<td align=center valign=\"top\">\n";
					$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\" id=\"R".$row->productcode."\" onmouseover=\"quickfun_show(this,'R".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'R".$row->productcode."','none')\">\n";
					$collection_body.="<col width=75></col><col width=1></col><col></col>\n";
				} else {
					if($i>0) $collection_body.="<td width=\"5\" nowrap></td>\n";
					$collection_body.="	<td width=\"".ceil(100/$collcnt)."%\" valign=\"top\">";
					$collection_body.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\" id=\"R".$row->productcode."\" onmouseover=\"quickfun_show(this,'R".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'R".$row->productcode."','none')\">\n";
				}

				$collection_body.="	<tr>\n";
				$collection_body.="		<td align=\"center\" valign=middle>\n";
				$collection_body.= "	<A HREF=\"".$Dir."app/productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$collection_body.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($width[0]>$width[1]) $collection_body.="width=70";
					else $collection_body.="height=70";
				} else {
					$collection_body.= "<img src=\"".$Dir."images/no_img.gif\" width=\"70\" border=\"0\" align=\"center\"";
				}
				$collection_body.= "		></A></td>";
				//$collection_body.="		\n";

				if($_data->coll_loc!="3") {
					$collection_body.="	</tr>\n";
					$collection_body.="	<tr><td height=\"5\"></td></tr>\n";
					$collection_body.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','R','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
					$collection_body.="	<tr>";
				} else {
					$collection_body.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','R','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				}

				$collection_body.="		<td ".($_data->coll_loc!="3"?"align=\"center\"":"")." valign=middle style=\"word-break:break-all;\">";
				$collection_body.="		<A HREF=\"./productdetail_tab01.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";

				if($row->consumerprice!=0) {
					if($_data->coll_loc!="3") {
						$collection_body.="		</td>\n";
						$collection_body.="	</tr>\n";
						$collection_body.="	<tr>\n";
						$collection_body.="		<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\">";
					} else {
						$collection_body.="		<BR>";
					}

					$collection_body.= "<img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원";
				}

				if($_data->coll_loc!="3") {
					$collection_body.="		</td>\n";
					$collection_body.="	</tr>\n";
					$collection_body.="	<tr>\n";
					$collection_body.="		<td align=\"center\">";
				} else {
					$collection_body.="		<BR>";
				}
				$collection_body.="		<FONT class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$collection_body.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$collection_body.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $collection_body.="(기본가)";
				} else {
					$collection_body.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $collection_body.= number_format($row->sellprice)."원";
					else $collection_body.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $collection_body.= soldout();

				if($row->reserve!=0) {
					if($_data->coll_loc!="3") {
						$collection_body.="		</font></td>\n";
						$collection_body.="	</tr>\n";
						$collection_body.="	<tr>\n";
						$collection_body.="		<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\">";
					} else {
						$collection_body.="		<BR>";
					}

					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$collection_body.= "<img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원";
				}

				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$tag_detail_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							if($_data->coll_loc!="3") {
								$collection_body.="		</font></td>\n";
								$collection_body.="	</tr>\n";
								$collection_body.="	<tr>\n";
								$collection_body.="		<td align=\"center\" style=\"word-break:break-all;\">";
							} else {
								$collection_body.="		<BR>";
							}
							$collection_body.= "<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$collection_body.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}


				$collection_body.="		</font></td>\n";


				$collection_body.="	</tr>\n";
				$collection_body.="	</table>\n";
				$collection_body.="	</td>\n";
				if($_data->coll_loc=="3") {
					$collection_body.="</tr>\n";
				}

				$i++;
			}
			mysql_free_result($result);
			if($_data->coll_loc!="3") {
				if($i!=$collcnt) {
					for($j=$i;$j<$collcnt;$j++) {
						$collection_body.="<td width=\"".ceil(100/$collcnt)."%\" align=\"center\"></td>";
					}
				}
				$collection_body.="	</tr>\n";
			}
			$collection_body.="	</table>\n";
			$collection_body.="	</td>\n";
			$collection_body.="</tr>\n";
			$collection_body.="</table>\n";
		}
	}

	//쿠폰을 사용할 경우
	if($_data->coupon_ok=="Y") {
		$sql = "SELECT * FROM tblcouponinfo ";
		if($_pdata->vender>0) {
			$sql.= "WHERE (vender='0' OR vender='".$_pdata->vender."') ";
		} else {
			$sql.= "WHERE vender='0' ";
		}
		$sql.= "AND display='Y' AND issue_type='Y' AND detail_auto='Y' ";
		$sql.= "AND (date_end>".date("YmdH")." OR date_end='') ";
		$sql.= "AND ((use_con_type2='Y' AND productcode IN ('ALL','".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','".$code."','".$productcode."')) OR (use_con_type2='N' AND productcode NOT IN ('".substr($code,0,3)."000000000','".substr($code,0,6)."000000','".substr($code,0,9)."000','".$code."','".$productcode."'))) ";
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		while($row=mysql_fetch_object($result)) {
			if($row->date_start>0) {
				$date2 = substr($row->date_start,4,2)."/".substr($row->date_start,6,2)." ~ ".substr($row->date_end,4,2)."/".substr($row->date_end,6,2);
			} else {
				$date2 = date("app/d")." ~ ".date("app/d",mktime(0,0,0,date("m"),date("d")+abs($row->date_start),date("Y")));
			}

			if($i==0) {
				$coupon_body="<table border=0 cellpadding=0 cellspacing=0>\n";
				$couponbody1=$coupon_body;
				$couponbody2=$coupon_body;
			}
			$tmpcouponbody="<tr><td height=\"16\"><font style=\"font-size:8pt;\">* ".$row->description."</font></td></tr>\n";
			$coupon_body.=$tmpcouponbody;
			$couponbody1.=$tmpcouponbody;
			$tmpcouponbody="";
			$tmpcouponbody.="<tr><td>";
			if(file_exists($Dir.DataDir."shopimages/etc/COUPON".$row->coupon_code.".gif")) {
				$tmpcouponbody.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"352\" style=\"table-layout:fixed;cursor:hand;\">\n";
				$tmpcouponbody.="<tr>\n";
				$tmpcouponbody.="	<td onclick=\"issue_coupon('".$row->coupon_code."')\"><a href=\"javascript:issue_coupon('".$row->coupon_code."')\"><img src=\"".$Dir.DataDir."shopimages/etc/COUPON".$row->coupon_code.".gif\" border=0></a></td>\n";
				$tmpcouponbody.="</tr>\n";
				$tmpcouponbody.="<tr><td align=\"right\"><A HREF=\"javascript:issue_coupon('".$row->coupon_code."')\"><IMG SRC=\"".$Dir."images/common/coupon_download.gif\" border=\"0\"></A></td></tr>\n";
				$tmpcouponbody.="</table>\n";
			} else {
				$tmpcouponbody.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"352\" style=\"table-layout:fixed;\">\n";
				$tmpcouponbody.="<col width=\"5\"></col>\n";
				$tmpcouponbody.="<col width=></col>\n";
				$tmpcouponbody.="<col width=\"5\"></col>\n";
				$tmpcouponbody.="<tr style=\"cursor:hand;\" onclick=\"issue_coupon('".$row->coupon_code."')\">\n";
				$tmpcouponbody.="	<td colspan=\"3\"><IMG SRC=\"".$Dir."images/common/coupon_table01.gif\" border=\"0\"></td>\n";
				$tmpcouponbody.="</tr>\n";
				$tmpcouponbody.="<tr style=\"cursor:hand;\" onclick=\"issue_coupon('".$row->coupon_code."')\">\n";
				$tmpcouponbody.="	<td background=\"".$Dir."images/common/coupon_table02.gif\"><IMG SRC=\"".$Dir."images/common/coupon_table02.gif\" border=\"0\"></td>\n";
				$tmpcouponbody.="	<td width=\"100%\" style=\"padding:3pt;\" background=\"".$Dir."images/common/coupon_bg.gif\">\n";
				$tmpcouponbody.="	<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n";
				$tmpcouponbody.="	<tr>\n";
				$tmpcouponbody.="		<td style=\"padding-bottom:4pt;\"><IMG SRC=\"".$Dir."images/common/coupon_title".$row->sale_type.".gif\" border\"0\"></td>\n";
				$tmpcouponbody.="	</tr>\n";
				$tmpcouponbody.="	<tr>\n";
				$tmpcouponbody.="		<td>\n";
				$tmpcouponbody.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$tmpcouponbody.="		<tr>\n";
				$tmpcouponbody.="			<td><font color=\"#585858\" style=\"font-size:11px;letter-spacing:-0.5pt;\">유효기간 : ".$date2."</font>\n";
				if($row->bank_only=="Y") $tmpcouponbody.=" <font color=\"0000FF\">(현금결제만 가능)</font>";
				$tmpcouponbody.="			</td>\n";
				$tmpcouponbody.="		</tr>\n";
				$tmpcouponbody.="		</table>\n";
				$tmpcouponbody.="		</td>\n";
				$tmpcouponbody.="	</tr>\n";
				$tmpcouponbody.="	<tr>\n";
				$tmpcouponbody.="		<td>\n";
				$tmpcouponbody.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$tmpcouponbody.="		<tr>\n";
				$tmpcouponbody.="			<td width=\"100%\" align=\"right\"><font color=#FF5000 style=\"font-family:sans-serif;font-size:48px;line-height:45px\"><b><font color=\"#FF6600\" face=\"강산체\">".number_format($row->sale_money)."</font></b></td>\n";
				$tmpcouponbody.="			<td><IMG SRC=\"".$Dir."images/common/coupon_text".$row->sale_type.".gif\" border=\"0\"></td>\n";
				$tmpcouponbody.="		</tr>\n";
				$tmpcouponbody.="		</table>\n";
				$tmpcouponbody.="		</td>\n";
				$tmpcouponbody.="	</tr>\n";
				$tmpcouponbody.="	</table>\n";
				$tmpcouponbody.="	</td>\n";
				$tmpcouponbody.="	<td background=\"".$Dir."images/common/coupon_table04.gif\"><IMG SRC=\"".$Dir."images/common/coupon_table04.gif\" border=\"0\"></td>\n";
				$tmpcouponbody.="</tr>\n";
				$tmpcouponbody.="<tr style=\"cursor:hand;\" onclick=\"issue_coupon('".$row->coupon_code."')\">\n";
				$tmpcouponbody.="	<td colspan=\"3\"><IMG SRC=\"".$Dir."images/common/coupon_table03.gif\" border=\"0\"></td>\n";
				$tmpcouponbody.="</tr>\n";
				$tmpcouponbody.="<tr><td align=\"right\" colspan=\"3\"><A HREF=\"javascript:issue_coupon('".$row->coupon_code."')\"><IMG SRC=\"".$Dir."images/common/coupon_download.gif\" border=\"0\"></A></td></tr>\n";
				$tmpcouponbody.="</table>\n";
			}
			$tmpcouponbody.="</td></tr>\n";
			$coupon_body.=$tmpcouponbody;
			$couponbody1.=$tmpcouponbody;
			$couponbody2.=$tmpcouponbody;
			$tmpcouponbody="<tr><td height=\"10\"></td></tr>\n";
			$coupon_body.=$tmpcouponbody;
			$couponbody1.=$tmpcouponbody;
			$couponbody2.=$tmpcouponbody;
			$i++;
		}
		mysql_free_result($result);
		if($i>0) {
			$coupon_body.="</table>\n";
			$couponbody1.="</table>\n";
			$couponbody2.="</table>\n";
		}
	}

	//상품단어 필터링
	if(strlen($_data->filter)>0) {
		$arr_filter=explode("#",$_data->filter);
		$detail_filter=$arr_filter[0];
		$filters=explode("=",$detail_filter);
		$filtercnt=count($filters)/2;

		for($i=0;$i<$filtercnt;$i++){
			$filterpattern[$i]="/".str_replace("\0","\\0",preg_quote($filters[$i*2]))."/";
			$filterreplace[$i]=$filters[$i*2+1];
			if(strlen($filterreplace[$i])==0) $filterreplace[$i]="***";
		}

		$review_filter_array=explode("REVIEWROW",$arr_filter[1]);
		$review_filter=$review_filter_array[0];
	}

	//상품다중이미지 확인
	$multi_img="N";
	$sql2 ="SELECT * FROM tblmultiimages WHERE productcode='".$productcode."' ";
	$result2=mysql_query($sql2,get_db_conn());
	if($row2=mysql_fetch_object($result2)) {
		if($_data->multi_distype=="0") {
			$multi_img="I";
		} else if($_data->multi_distype=="1") {
			$multi_img="Y";
			$multi_imgs=array(&$row2->primg01,&$row2->primg02,&$row2->primg03,&$row2->primg04,&$row2->primg05,&$row2->primg06,&$row2->primg07,&$row2->primg08,&$row2->primg09,&$row2->primg10);
			$thumbcnt=0;
			for($j=0;$j<10;$j++) {
				if(strlen($multi_imgs[$j])>0) {
					$thumbcnt++;
				}
			}
			$multi_height=430;
			$thumbtype=1;
			if($thumbcnt>5) {
				$multi_height=490;
				$thumbtype=2;
			}
		}
	}
	mysql_free_result($result2);

	//상품 상세정보 노출정보
	if(strlen($_data->exposed_list)==0) {
		$_data->exposed_list=",0,2,3,4,5,6,7,19,";
	}
	$arexcel = explode(",",substr($_data->exposed_list,1,-1));
	$prcnt = count($arexcel);
	$arproduct=array(&$prproduction,&$prmadein,&$prconsumerprice,&$prsellprice,&$prreserve,&$praddcode,&$prquantity,&$proption,&$prproductname,&$prdollarprice,&$prmodel,&$propendate,&$pruserspec0,&$pruserspec1,&$pruserspec2,&$pruserspec3,&$pruserspec4,&$prbrand,&$prselfcode,&$prpackage);
	$ardollar=explode(",",$_data->ETCTYPE["DOLLAR"]);

	if(strlen($ardollar[1])==0 || $ardollar[1]<=0) $ardollar[1]=1;

	if(ereg("^(\[OPTG)([0-9]{4})(\])$",$_pdata->option1)){
		$optcode = substr($_pdata->option1,5,4);
		$_pdata->option1="";
		$_pdata->option_price="";
	}

	$miniq = 1;
	if (strlen($_pdata->etctype)>0) {
		$etctemp = explode("",$_pdata->etctype);
		for ($i=0;$i<count($etctemp);$i++) {
			if (substr($etctemp[$i],0,6)=="MINIQ=")			$miniq=substr($etctemp[$i],6);
			if (substr($etctemp[$i],0,11)=="DELIINFONO=")	$deliinfono=substr($etctemp[$i],11);
		}
	}

	//입점업체 정보 관련
	if($_pdata->vender>0) {
		$sql = "SELECT a.vender, a.id, a.brand_name, a.deli_info, b.prdt_cnt ";
		$sql.= "FROM tblvenderstore a, tblvenderstorecount b ";
		$sql.= "WHERE a.vender='".$_pdata->vender."' AND a.vender=b.vender ";
		$result=mysql_query($sql,get_db_conn());
		if(!$_vdata=mysql_fetch_object($result)) {
			$_pdata->vender=0;
		}
		mysql_free_result($result);
	}

	//배송/교환/환불정보 노출
	$deli_info="";
	if($deliinfono!="Y") {	//개별상품별 배송/교환/환불정보 노출일 경우
		$deli_info_data="";
		if($_pdata->vender>0) {	//입점업체 상품이면 입점업체 배송/교환/환불정보 누출
			$deli_info_data=$_vdata->deli_info;
			$aboutdeliinfofile=$Dir.DataDir."shopimages/vender/aboutdeliinfo_".$_vdata->vender.".gif";
		} else {
			$deli_info_data=$_data->deli_info;
			$aboutdeliinfofile=$Dir.DataDir."shopimages/etc/aboutdeliinfo.gif";
		}
		if(strlen($deli_info_data)>0) {
			$tempdeli_info=explode("=",$deli_info_data);
			if($tempdeli_info[0]=="Y") {
				if($tempdeli_info[1]=="TEXT") {			//텍스트형
					$allowedTags = "<h1><b><i><a><ul><li><pre><hr><blockquote><u><img><br><font>";

					if(strlen($tempdeli_info[2])>0 || strlen($tempdeli_info[3])>0) {
						$deli_info = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
						$deli_info.= "<tr>\n";
						$deli_info.= "	<td style=\"padding:10,15,10,15\">\n";
						$deli_info.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
						if(strlen($tempdeli_info[2])>0) {	//배송정보 텍스트
							$deli_info.= "	<tr>\n";
							$deli_info.= "		<td><img src=\"".$Dir."images/common/detaildeliinfo_img1.gif\" border=0></td>\n";
							$deli_info.= "	</tr>\n";
							$deli_info.= "	<tr>\n";
							$deli_info.= "		<td style=\"line-height:14pt;padding-left:10\">\n";
							$deli_info.= "		".nl2br(strip_tags($tempdeli_info[2],$allowedTags))."\n";
							$deli_info.= "		</td>\n";
							$deli_info.= "	</tr>\n";
							$deli_info.= "	<tr><td height=15></td></tr>\n";
						}
						if(strlen($tempdeli_info[3])>0) {	//교환/환불정보 텍스트
							$deli_info.= "	<tr>\n";
							$deli_info.= "		<td><img src=\"".$Dir."images/common/detaildeliinfo_img2.gif\" border=0></td>\n";
							$deli_info.= "	</tr>\n";
							$deli_info.= "	<tr>\n";
							$deli_info.= "		<td style=\"line-height:14pt;padding-left:10\">\n";
							$deli_info.= "		".nl2br(strip_tags($tempdeli_info[3],$allowedTags))."\n";
							$deli_info.= "		</td>\n";
							$deli_info.= "	</tr>\n";
							$deli_info.= "	<tr><td height=15></td></tr>\n";
						}
						$deli_info.= "	</table>\n";
						$deli_info.= "	</td>\n";
						$deli_info.= "</tr>\n";
						$deli_info.= "</table>\n";
					}
				} else if($tempdeli_info[1]=="IMAGE") {	//이미지형
					if(file_exists($aboutdeliinfofile)) {
						$deli_info = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
						$deli_info.= "<tr>\n";
						$deli_info.= "	<td align=center><img src=\"".$aboutdeliinfofile."\" align=absmiddle border=0></td>\n";
						$deli_info.= "</tr>\n";
						$deli_info.= "</table>\n";
					}
				} else if($tempdeli_info[1]=="HTML") {	//HTML로 입력
					if(strlen($tempdeli_info[2])>0) {
						$deli_info = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
						$deli_info.= "<tr><td>".$tempdeli_info[2]."</td></tr>\n";
						$deli_info.= "</table>\n";
					}
				}
			}
		}
	}

	//리뷰관련 환경 설정
	$reviewlist=$_data->ETCTYPE["REVIEWLIST"];
	$reviewdate=$_data->ETCTYPE["REVIEWDATE"];
	if(strlen($reviewlist)==0) $reviewlist="N";

	if($mode=="review_write") {
		function ReviewFilter($filter,$memo,&$findFilter) {
			$use_filter = split(",",$filter);
			$isFilter = false;
			for($i=0;$i<count($use_filter);$i++) {
				if (eregi($use_filter[$i],$memo)) {
					$findFilter = $use_filter[$i];
					$isFilter = true;
					break;
				}
			}
			return $isFilter;
		}

		$rname=$_POST["rname"];
		$rcontent=$_POST["rcontent"];
		$rmarks=$_POST["rmarks"];
		if((strlen($_ShopInfo->getMemid())==0) && $_data->review_memtype=="Y") {
			echo "<html></head><body onload=\"alert('로그인을 하셔야 사용후기 등록이 가능합니다.');location.href='./login.php?chUrl=".getUrl()."'\"></body></html>";exit;
		}
		if(strlen($review_filter)>0) {	//사용후기 내용 필터링
			if(ReviewFilter($review_filter,$rcontent,$findFilter)) {
				echo "<html></head><body onload=\"alert('사용하실 수 없는 단어를 입력하셨습니다.(".$findFilter.")\\n\\n다시 입력하시기 바랍니다.');history.go(-1);\"></body></html>";exit;
			}
		}

		$sql = "INSERT tblproductreview SET ";
		$sql.= "productcode	= '".$productcode."', ";
		$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
		$sql.= "name		= '".$rname."', ";
		$sql.= "marks		= '".$rmarks."', ";
		$sql.= "date		= '".date("YmdHis")."', ";
		$sql.= "content		= '".$rcontent."' ";
		mysql_query($sql,get_db_conn());

		if($_data->review_type=="A") $msg="관리자 인증후 등록됩니다.";
		else $msg="등록되었습니다.";
		$rqry="productcode=".$productcode;
		if(strlen($code)>0) $rqry.="&code=".$code;
		if(strlen($sort)>0) $rqry.="&sort=".$sort;
		if(strlen($brandcode)>0) $rqry.="&brandcode=".$brandcode;
		echo "<html></head><body onload=\"alert('".$msg."');location='".$_SERVER["PHP_SELF"]."?".$rqry."'\"></body></html>";exit;
	}

	//이전/다음 상품 관련
	$qry = "WHERE 1=1 ";
	if(eregi("T",$_cdata->type)) {	//가상분류
		$sql = "SELECT productcode FROM tblproducttheme WHERE code LIKE '".$likecode."%' ";
		$result=mysql_query($sql,get_db_conn());
		$t_prcode="";
		while($row=mysql_fetch_object($result)) {
			$t_prcode.=$row->productcode.",";
			$i++;
		}
		mysql_free_result($result);
		$t_prcode=substr($t_prcode,0,-1);
		$t_prcode=ereg_replace(',','\',\'',$t_prcode);
		$qry.= "AND a.productcode IN ('".$t_prcode."') ";

		$add_query="&code=".$code;
	} else {	//일반분류
		$qry.= "AND a.productcode LIKE '".$likecode."%' ";
	}
	$qry.= "AND a.display='Y' ";

	$tmp_sort=explode("_",$sort);
	if($brandcode>0) {
		$qry.="AND a.brand='".$brandcode."' ";
		$add_query.="&brandcode=".$brandcode;
		$brand_link = "brandcode=".$brandcode."&";

		$sql ="SELECT SUBSTRING(a.productcode, 1, 3) AS code FROM tblproduct AS a ";
		$sql.="LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.="WHERE a.display='Y' AND a.brand='".$brandcode."' ";
		$sql.="AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.="GROUP BY code ";
		$result=mysql_query($sql,get_db_conn());
		$brand_qry = "";
		$leftcode = array();
		while($row=mysql_fetch_object($result)) {
			$leftcode[] = $row->code;
		}
		if(count($leftcode)>0) {
			$brand_qry = "AND codeA IN ('".implode("','",$leftcode)."') ";
		}

		if($tmp_sort[0]=="reserve") {
			$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.option_price ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else $sql.= "ORDER BY a.productname ";
	} else {
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
		$sql.= "a.tinyimage, a.etctype, a.option_price ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="sellprice") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else {
			if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
				$sql.= "ORDER BY date DESC ";
			} else if($_cdata->sort=="productname") {
				$sql.= "ORDER BY a.productname ";
			} else if($_cdata->sort=="production") {
				$sql.= "ORDER BY a.production ";
			} else if($_cdata->sort=="price") {
				$sql.= "ORDER BY a.sellprice ";
			}
		}
	}
	$result=mysql_query($sql,get_db_conn());
	unset($arr_productcode);
	$isprcode=false;
	while($row=mysql_fetch_object($result)) {
		if($productcode==$row->productcode) {
			$isprcode=true;
		} else {
			if($isprcode==false) {
				$arr_productcode["prev"]=$row->productcode;
			} else {
				$arr_productcode["next"]=$row->productcode;
				break;
			}
		}
	}
	mysql_free_result($result);

	#####################상품별 회원할인율 적용 시작#######################################
	$discountprices = getProductDiscount($productcode);
	if($discountprices>0){
		$memberprice = $_pdata->sellprice - $discountprices;
		$mempricestr = number_format($memberprice);
		$strikeStart = "<strike>";
		$strikeEnd = "</strike> ▶ ";
	}else{
		$memberprice = '';
		$mempricestr = '';
	}
	#####################상품별 회원할인율 적용 끝 #######################################

	//현재위치
	//$codenavi=($brandcode>0?getBCodeLoc($brandcode,$code):getCodeLoc($code));

	//상품QNA 게시판 존재여부 확인 및 설정정보 확인
	$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
	if(strlen($prqnaboard)>0) {
		$sql = "SELECT * FROM tblboardadmin WHERE board='".$prqnaboard."' ";
		$result=mysql_query($sql,get_db_conn());
		$qnasetup=mysql_fetch_object($result);
		mysql_free_result($result);
		if($qnasetup->use_hidden=="Y") unset($qnasetup);
	}

	//페이스북 이미지
	if(strlen($_pdata->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->tinyimage)) {
		$fbThumb = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/product/".$_pdata->tinyimage;
	}else{
		$fbThumb = "http://".$_ShopInfo->getShopurl()."images/no_img/no_img.gif";
	}

	//sns 설정
	$arSnsType = explode("", $_data->sns_reserve_type);
	$odrChk = true;

	$delipriceTxt = '';
	$deliRangeStr = ((intval($_data->deli_setperiod) > 0)?$_data->deli_setperiod+2:3).'일 이내 배송가능(토,일 공휴일 제외)';
	if(($_pdata->deli=="Y" || $_pdata->deli=="N") && $_pdata->deli_price>0) {
		$delipriceTxt = '[개별유료배송] '.number_format($_pdata->deli_price).'원';
		if($_pdata->deli=="Y") $delipriceTxt .= '(수량대비증가)';
	} else if($_pdata->deli=="F" || $_pdata->deli=="G") {
		if($_pdata->deli=="F") {
			$delipriceTxt = '[개별무료배송]';
		} else {
			$delipriceTxt = '[개별착불배송]';
		}
	}else{
		$_vdinfo = false;
		if($_pdata->vender >0){
			$sql = "select * from tblvenderinfo where vender = '".$_pdata->vender."' limit 1";
			if(false !== $result = mysql_query($sql,get_db_conn())){
				if(mysql_num_rows($result)){
					$_vdinfo = mysql_fetch_assoc($result);
				}
				mysql_free_result($result);
			}
		}
		if($_vdinfo && $_vdinfo['deli_super'] != 'S'){
			if($_vdinfo['deli_type'] == 'F'){
				$delipriceTxt = '[입점사무료배송]';
			}else if($_vdinfo['deli_type'] == 'Y'){
				$delipriceTxt = '[입점사착불]';
			}else{
				if($_vdinfo['deli_price'] == 0){
					if(($pRow->deli_price<=0 && $pRow->deli=="N") || !$pRow){
						$delipriceTxt = '기본 배송비';
					}else{
						$delipriceTxt = '배송비 무료';
					}
				}else{
					if( $_vdinfo['deli_price'] > 0 ) {
						$delipriceTxt = '[유료배송] '.number_format($_vdinfo['deli_price']).'원';
						if( $_vdinfo[deli_mini] > 99 ) {
							$delipriceTxt .= '<br /> ('.number_format($_vdinfo[deli_mini]).'원이상 무료배송)';
						}
					} else{
						$delipriceTxt = '배송비 무료';
					}
				}
			}
		}else{
			if($_data->deli_type == 'F'){
				$delipriceTxt = '[무료배송]';
			}else if($_data->deli_type == 'Y'){
				$delipriceTxt = '[착불]';
			}else{
				if($_pdata->sellprice >= $_data->deli_miniprice){
					$delipriceTxt = '배송비 무료';
				}else{
					if( $_data->deli_basefee > 0 ) {
						$delipriceTxt = '[유료배송] '.number_format($_data->deli_basefee).'원';
						if( $_data->deli_miniprice > 0 ) {
							$delipriceTxt .= '<br /> ('.number_format($_data->deli_miniprice).'원이상 무료배송)';
						}
					} else{
						$delipriceTxt = '배송비 무료';
					}
				}
			}
		}
	}
	

	//상품평 수
	$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$_GET[productcode]'";
	$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
	$row_cnt3=mysql_fetch_object($result_cnt3);
	$t_cnt3 = (int)$row_cnt3->t_count;

	//상품문의 수
	$pridx=$_pdata->pridx;
	$sql_cnt4 = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='$prqnaboard' and pridx = '$pridx'";
	$result_cnt4=mysql_query($sql_cnt4,get_db_conn());
	$row_cnt4=mysql_fetch_object($result_cnt4);
	$t_cnt4 = (int)$row_cnt4->t_count;
	
	
	//주문서 상품에 대한 표시
	$aoidx=$_REQUEST["aoidx"];
	$ao_cnt = 0;
	if(strlen($aoidx)>0) {
		$sql="SELECT 
				aop.aopidx
				,aop.sellprice
				,aop.deli_price
				,aop.prodnum
			FROM
				auction_order_proposal AS aop
			LEFT JOIN
				auction_order AS ao
			ON
				aop.aoidx = ao.aoidx
			WHERE
				aop.userid = '".$_ShopInfo->getMemid()."'
				AND aop.pridx = '".$_pdata->pridx."'
				AND aop.del_flag=false
				AND ao.aoidx = '".$aoidx."'
				AND DATE_FORMAT( CONCAT( ao.receiveDate, ' ', ao.receivetime ) , '%Y-%m-%d %H:%i:%s' ) >= DATE_ADD(NOW(), INTERVAL 3 HOUR)";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$ao_cnt++;
			$ao_sellprice = $row->sellprice;
			$ao_deli_price = $row->deli_price;
			$ao_prodnum = $row->prodnum;
			$aopidx = $row->aopidx;
		}
		if($ao_cnt==0){
			echo "<script>alert('해당 제안은 주문이 불가능합니다. 일반상품으로 처리됩니다.')</script>";
		}
		mysql_free_result($result);
	}
	
	if($ao_cnt>0) {
		if($ao_deli_price > 0){
			$delipriceTxt = '[유료배송] '.number_format($ao_deli_price).'원';
		}
		else{
			$delipriceTxt = '배송비 무료';
		}
	}
?>

<form name="codeNaviForm" id="codeNaviForm" action="productlist.php">
	<input type="hidden" name="code" value="" />
</form>

<script language="javascript">
	<!--
	function chgNaviCode(dp){
		var code = '';
		dp = parseInt(dp);
		if(dp > 4) dp = 4
		for(i=0;i<=dp;i++){
			var el = document.getElementById('code'+String.fromCharCode(65+i));
			if(el){
				code += el.options[el.selectedIndex].value;
			}else{
				break;
			}
		}
		document.codeNaviForm.code.value = code;
		document.codeNaviForm.submit();
	}
	//-->
	
	$(document).ready(function() {
		$('.btn_prev').click(function() {
			var vidx = "<?=$vidx?>";
			var refURL = "<?=$refURL?>";
			var refPage = "<?=$refPage?>";
			if (refPage.length > 0) {
				location.href = "vender_timesale.php?vidx=" + vidx + "&refURL=timesale_product_map";
			} else {
				if (refURL == "timesale_product" || refURL == "productdetail_today_sale" || refURL == "productdetail_today_flower") {
					callTimesale('list');
				} else {
					history.back();
				}
			}
		});
	});
</script>
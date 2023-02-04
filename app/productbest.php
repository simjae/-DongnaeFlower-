<?php
include_once("./header.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."app/inc/paging_inc.php");

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1;

$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];
if($listnum<=0) $listnum=$_data->prlist_num;

$itemcount = 12; // 페이지당 게시글 리스트 수
$pagePerBlock = 5; // 블록 갯수

$displaymode = isset($_GET['list_type']) ? trim($_GET['list_type']) : "gallery";

#화면 모드 관련 파라메터#
$displaygallery=$displaywebzine=$displaylist="";
switch($displaymode){
    case "list":;
        $displaylist="on";
        break;

    case "webzine":
        $displaywebzine="on";
        break;

    case "gallery":
    default:
        $displaygallery="on";
        break;
}
?>

    <div id="list">
        <h1 class="list_title">인기상품</h1>
        <div class="wrapper">
            <div class="list_sort">
                <div class="options">
				<span class="basic_select">
					<select onChange="ChangeSort(this.value)">
						<option value="">최근등록순</option>
						<option value="price_desc" <?if($_GET[sort]=="price_desc") {echo "selected";}?>>높은가격순</option>
						<option value="price" <?if($_GET[sort]=="price") {echo "selected";}?>>낮은가격순</option>
						<option value="name" <?if($_GET[sort]=="name") {echo "selected";}?>>상품명 순</option>
						<option value="name_desc" <?if($_GET[sort]=="name_desc") {echo "selected";}?>>상품명 역순</option>
						<option value="reserve_desc" <?if($_GET[sort]=="reserve_desc") {echo "selected";}?>>적립금 높은순</option>
						<option value="reserve" <?if($_GET[sort]=="reserve") {echo "selected";}?>>적립금 낮은순</option>
					</select>
				</span>
                </div>
                <div class="sort_view">
                    <ul>
                        <li class="sort_gallery <?=$displaygallery?>" onClick="changeDisplayMode('gallery','<?=$code?>','<?=$sort?>')">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
							<rect width="9" height="9"/>
                                <rect x="11" width="9" height="9"/>
                                <rect x="11" y="11" width="9" height="9"/>
                                <rect y="11" width="9" height="9"/>
						</svg>
                        </li>
                        <li class="sort_webzine <?=$displaywebzine?>" onClick="changeDisplayMode('webzine','<?=$code?>','<?=$sort?>')">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
							<rect class="st0" width="4" height="2"/>
                                <rect x="7" y="0" width="13" height="2"/>
                                <rect x="7" y="8" width="13" height="2"/>
                                <rect y="8" width="4" height="2"/>
                                <rect y="16" width="4" height="2"/>
                                <rect x="7" y="16" width="13" height="2"/>
						</svg>
                        </li>
                        <li class="sort_list <?=$displaylist?>" onClick="changeDisplayMode('list','<?=$code?>','<?=$sort?>')">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="24px" height="20px" viewBox="0 0 24 20" style="enable-background:new 0 0 24 20;" xml:space="preserve">
							<rect width="20" height="9"/>
                                <rect y="12"  width="20" height="10"/>
						</svg>
                        </li>
                    </ul>
                </div>
            </div>

            <?
            $sql = "SELECT special_list FROM tblspecialmain WHERE special='2'";
            $result=mysql_query($sql,get_db_conn());
            $sp_prcode="";
            if($row=mysql_fetch_object($result)) {
                $sp_prcode=ereg_replace(',','\',\'',$row->special_list);
            }
            mysql_free_result($result);

            $tmp_sort=explode("_",$sort);

            $sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.discountRate, a.quantity, a.reserve, a.reservetype, a.production, a.prmsg, a.tinyimage, a.minimage, a.maximage, a.wideimage, a.etctype, a.option_price, a.reservation, a.tag, a.selfcode, a.vender, a.option1, a.option2, a.option_quantity, a.youtube_url, a.youtube_prlist, a.youtube_prlist_file, a.youtube_prlist_imgtype ";
            $sql.= "FROM tblproduct AS a ";
            $sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
            $sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
            $sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";

            if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
            else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
            else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
            else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY a.reserve ".$tmp_sort[1]." ";
            else {
                if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
                    if(eregi("T",$_cdata->type) && strlen($t_prcode)>0) {
                        $sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),a.date DESC ";
                    } else {
                        $sql.= "ORDER BY a.date DESC ";
                    }
                } else if($_cdata->sort=="productname") {
                    $sql.= "ORDER BY a.productname ";
                } else if($_cdata->sort=="production") {
                    $sql.= "ORDER BY a.production ";
                } else if($_cdata->sort=="price") {
                    $sql.= "ORDER BY a.sellprice ";
                }
            }

            $result=mysql_query($sql,get_db_conn());
            $rowcount=mysql_num_rows($result);

            switch($displaymode){
                case "gallery":
                    ?>
                    <!-- 상품리스트-타입1 -->
                    <div class="product_a">
                        <ul class="product_list">
                            <?
                            $sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;

                            if(false !== $gelleryRes = mysql_query($sql,get_db_conn())){
                                $gelleryNumRows = mysql_num_rows($gelleryRes);

                                $i = 0;
                                if($gelleryNumRows > 0){
                                    while($gelleryRow = mysql_fetch_assoc($gelleryRes)){
                                        $maximage = $gelleryRow['maximage'];
                                        $wholeSaleIcon = ( $gelleryRow['isdiscountprice'] == 1 ) ? '<img src="/images/common/wholeSaleIcon.gif"/>':"";
                                        $memberpriceValue = $gelleryRow['sellprice'];
                                        $strikeStart = $strikeEnd = '';
                                        $memberprice = 0;
                                        $reservation = $gelleryRow['reservation'];

                                        $productname=_strCut($gelleryRow['productname'],26,4,$charset);
                                        $productmsg=$gelleryRow['prmsg'];
                                        $prconsumerprice=number_format($gelleryRow['consumerprice']);
                                        $discountRate=$gelleryRow['discountRate'];
                                        $productcode = $gelleryRow['productcode'];

                                        $option1 = $gelleryRow['option1'];
                                        $option2 = $gelleryRow['option2'];
                                        $optionquantity = $gelleryRow['option_quantity'];


                                        #####################상품별 회원할인율 적용 시작#######################################
                                        $discountprices = getProductDiscount($productcode);
                                        if($discountprices > 0 AND isSeller() != 'Y' ){
                                            $memberprice = $gelleryRow['sellprice'] - $discountprices;
                                            $gelleryRow['sellprice'] = $memberprice;
                                        }
                                        #####################상품별 회원할인율 적용 끝 #######################################

                                        $viewPrice="";
                                        $dicker=new_dickerview($gelleryRow['etctype'],$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원",1);
                                        if($memberprice > 0) {	// 회원 로그인(및 등급 할인) 일 경우에만
                                            $viewPrice.="<img src='/images/common/memsale_icon.gif' /> ";
                                        }

                                        if (count($dicker['memOpenData']) > 0) {
                                            if ($dicker['memOpenData']['type'] == "img") {
                                                $viewPrice.="<img src='".$dicker['memOpenData']['value']."' />";
                                            } else {
                                                $viewPrice.="<span class=''>".$dicker['memOpenData']['value']."</span>";
                                            }
                                        } else if (strlen($_data->proption_price) == 0) {
                                            $viewPrice.=$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원";
                                        } else {
                                            if (strlen($gelleryRow['option_price']) == 0) {
                                                $viewPrice.=$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원";
                                            } else {
                                                $viewPrice.=ereg_replace("\[PRICE\]",number_format($gelleryRow['sellprice']),$_data->proption_price);
                                            }
                                        }

                                        //상품평 수
                                        $sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
                                        $result_cnt3=mysql_query($sql_cnt3,get_db_conn());
                                        $row_cnt3=mysql_fetch_object($result_cnt3);
                                        $t_cnt3 = (int)$row_cnt3->t_count;


                                        if(strlen($gelleryRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$gelleryRow[tinyimage])==true){
                                            $background_url=$Dir.DataDir."shopimages/product/".urlencode($gelleryRow[tinyimage]);
                                        }else{
                                            $background_url=$Dir."images/no_img.gif";
                                        }

                                        $prdetail_link="productdetail_tab01.php?productcode=".$productcode."";


                                        $youtube_url=$gelleryRow['youtube_url'];
                                        $youtube_prlist=$gelleryRow['youtube_prlist'];
                                        $youtube_prlist_file=$gelleryRow['youtube_prlist_file'];
                                        $youtube_prlist_imgtype=$gelleryRow['youtube_prlist_imgtype'];

                                        //동영상(유튜브) 등록일 때 상품이미지 교체
                                        if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='Y'){
                                            $youtube_code=str_replace("https://youtu.be/","",$youtube_url);
                                            $prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
                                            $background_image=str_replace("https://youtu.be/","",$youtube_url);
                                            $background_url="https://img.youtube.com/vi/".$background_image."/sddefault.jpg";

                                        }else if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='D'){
                                            $youtube_code=str_replace("https://youtu.be/","",$youtube_url);
                                            $prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
                                            $background_image=$youtube_prlist_file;
                                            $background_url=$Dir.DataDir."shopimages/product/".$background_image;
                                        }

                                        $width=getimagesize($background_url);
                                        if($width[1]>$width[0]){ //세로가 가로보다 길 때
                                            $background_size="100% auto";
                                        }else{ //가로가 세로보다 길 때
                                            $background_size="auto 100%";
                                        }
                                        ?>
                                        <li class="product_item">
                                            <div class="product_view">
                                                <div class="product_img">
                                                    <?
                                                    if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
                                                        echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
                                                    }
                                                    ?>
                                                    <a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;">
                                                        <img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
                                                    </a>
                                                </div>
                                                <? if($discountRate>0){ ?><div class="product_sale"><?=$discountRate?>%</div><? } ?>
                                            </div><!-- product_view -->

                                            <? if($productlist_basket=="Y") { ?>
                                                <? $opti=0; ?>
                                                <form name="bfrm<?=$productcode?>" id="bfrm<?=$productcode?>" method="post" action="./basket.php">
                                                    <? if(strlen($option1)>0){ ?>
                                                        <input type="hidden" id="opt_idx_<?=$productcode?>" name="opt_idx[]" <?=$oneoption1?>/>
                                                        <input type="hidden" id="opt_idx2_<?=$productcode?>" name="opt_idx2[]" <?=$oneoption2?>/>
                                                        <input type="hidden" id="opt_quantity_<?=$productcode?>" name="opt_quantity[]" />
                                                    <? } ?>
                                                    <input type="hidden" name="price" />
                                                    <input type="hidden" name="dollarprice" />
                                                    <input type="hidden" name="code" />
                                                    <input type="hidden" name="productcode" value="<?=$productcode?>" />
                                                    <input type="hidden" name="ordertype" />
                                                    <input type="hidden" name="opts" />

                                                    <? if(!$dicker['memOpen']){ ?>
                                                        <div class="product_amount">
                                                            <div class="product_minus" onclick="change_quantity(bfrm<?=$pm_idx.$productcode?>.name,'dn');">-</div>
                                                            <div class="product_quantity"><input type="text" name="quantity" value="1" /></div>
                                                            <div class="product_plus" onclick="change_quantity(bfrm<?=$pm_idx.$productcode?>.name,'up');">+</div>

                                                            <?
                                                            //옵션유무 체크
                                                            $optClass->setOptUse($productcode);
                                                            $optClass->setOptType($productcode);

                                                            if($optClass->optUse){ //옵션이 있는 상품일 때
                                                                ?>
                                                                <div class="product_basket" onclick="optChecker('<?=$productcode?>','basket');">&nbsp;</div>
                                                                <div class="product_buy" onclick="optChecker('<?=$productcode?>','ordernow');">&nbsp;</div>
                                                            <? }else{ ?>
                                                                <div class="product_basket" onclick="CheckForm(document.bfrm<?=$pm_idx.$productcode?>,'<?=$productcode?>','');">&nbsp;</div>
                                                                <div class="product_buy" onclick="CheckForm(document.bfrm<?=$pm_idx.$productcode?>,'<?=$productcode?>','ordernow');">&nbsp;</div>
                                                            <? } ?>
                                                        </div>
                                                    <? } ?>
                                                </form>
                                            <? } ?>

                                            <div class="product_info" <?=($productlist_quick=='Y'?"style=\"margin-bottom:65px;\"":"")?>>
                                                <div class="product_name"><a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external"><?=$msgreservation?><?=$productname?></a></div>
                                                <? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
                                                <? if($prconsumerprice>0){ ?><div class="product_discount"><?=$prconsumerprice?>원</div><? } ?>
                                                <div class="product_price">
                                                    <?
                                                    echo $viewPrice;
                                                    if($gelleryRow['quantity']=="0") echo soldout();
                                                    ?>
                                                </div>
                                                <? if(strlen($reservation)>0 && $reservation != "0000-00-00"){ ?>
                                                    <div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
                                                <? } ?>
                                                <? if(strlen($vendername)>0){ ?>
                                                    <div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');">판매자 : <?=$vendername?></a></div>
                                                <? } ?>
                                                <? if($gelleryRow['etctype']){ ?><div>
                                                    <!--모바일 메인 아이콘-->
                                                    <?
                                                    $icoi = strpos(" ".$gelleryRow['etctype'],"ICON=");
                                                    if($icoi>0){
                                                        $icon = substr($gelleryRow['etctype'],strpos($gelleryRow['etctype'],"ICON="));
                                                        $icon = substr($icon,5,strpos($icon,"")-5);
                                                        $num=strlen($icon);
                                                        for($j=0;$j<$num;$j+=2){
                                                            $temp=$icon[$j].$icon[$j+1];
                                                            /*
                                                                        if($temp=='04'){
                                                                            $icon_name="NEW";
                                                                        }else if($temp=='13'){
                                                                            $icon_name="BEST";
                                                                        }else{
                                                                            $icon_name="HOT";
                                                                        }
                                                            */
                                                            if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
                                                                echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
                                                            } else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
                                                                echo "<span class='icon".$temp."'></span>";
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                    <!--모바일 아이콘-->



                                                    <?// echo viewproductname('',$gelleryRow['etctype'],'');?>

                                                    </div><? } ?>
                                            </div><!-- product_info -->

                                            <? if($productlist_quick=="Y") { ?>
                                                <div class="product_communicate">
                                                    <ul>
                                                        <li><a href="productdetail_tab03.php?productcode=<?=$productcode?>&sort=#tapTop" rel="external"><span class="product_coment"><?=$t_cnt3?></span></a></li>

                                                        <?
                                                        $wish_chk = true;
                                                        $wish_sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='".$productcode."' ";
                                                        $wish_result = mysql_query($wish_sql, get_db_conn());
                                                        $wish_row = mysql_fetch_object($wish_result);

                                                        if($wish_row->cnt>0)
                                                            $wish_chk = false;

                                                        if(strlen($_ShopInfo->getMemid())>0){
                                                            if($wish_chk){
                                                                ?>
                                                                <li><a href="javascript:wishAjax('<?=$productcode?>', 'im<?=$i?>')" id="im<?=$i?>" class="btn_wishlist off"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
                                                            <? }else{ ?>
                                                                <li><a href="javascript:wishAjax('<?=$productcode?>', 'im<?=$i?>')" id="im<?=$i?>" class="btn_wishlist on"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
                                                                <?
                                                            }
                                                        }else{
                                                            ?>
                                                            <li><a href="javascript:check_login()" id="im<?=$i?>" class="btn_wishlist off"><span class="product_like"><?=$wish_row->cnt?></span></a></li>
                                                        <? } ?>
                                                        <li><a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external"><span class="product_more"></span></a></li>
                                                    </ul>
                                                </div>
                                            <? } ?>
                                        </li>
                                        <?
                                        if($i>$gelleryNumRows-2 AND ($i+1)%2 != 0) {	//상품 전체 갯수가 홀수이면 비어있는 li 추가하기
                                            echo "<li class='product_item'></li>";
                                        }

                                        if($i>0 && $i%2){	//가로 2개 줄바꿈 처리
                                            echo "</ul><div style='height:40px;'></div><ul class='product_list'>";
                                        }

                                        $i++;
                                    }
                                } else {
                                    ?>
                                    <li style="margin:0px;padding:0px;text-align:center;width:100%;">진열된 상품이 없습니다.</li>
                                    <?
                                }
                                mysql_free_result($gelleryRes);
                            } else {
                                ?>
                                <li style="margin:0px;padding:0px;text-align:center;width:100%;">연결이 지연되었습니다 다시 시도해주세요.</li>
                            <? } ?>
                        </ul>
                    </div>
                    <!-- 상품리스트-타입1 -->

                    <?
                    break;
                case "webzine":
                    ?>
                    <!-- 상품리스트-타입2 -->
                    <div class="product_c">
                        <ul class="product_list">
                            <?
                            $itemcount = 12; // 페이지당 게시글 리스트 수
                            $sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;

                            if(false !== $listRes = mysql_query($sql,get_db_conn())){
                                $listNumRows = mysql_num_rows($listRes);
                                if($listNumRows > 0){
                                    while($listRow = mysql_fetch_assoc($listRes)){
                                        $productcode=$listRow['productcode'];
                                        $maximage=$listRow['maximage'];
                                        $productname=_strCut($listRow['productname'],24,4,$charset);
                                        $productmsg=_strCut($listRow['prmsg'],34,4,$charset);
                                        $prconsumerprice = number_format($listRow['consumerprice']);
                                        $discountRate=$listRow['discountRate'];
                                        $wholeSaleIcon = ( $listRow['isdiscountprice'] == 1 ) ? '<img src="/images/common/wholeSaleIcon.gif"/>':"";
                                        $memberpriceValue = $listRow['sellprice'];
                                        $strikeStart = $strikeEnd = '';
                                        $memberprice = 0;
                                        $reservation = $listRow['reservation'];
                                        if(strlen($reservation)>0 && $reservation != "0000-00-00"){
                                            $msgreservation = "<span class=\"font-orange\">(예약)</span> ";
                                            $datareservation = $reservation;
                                        }else{
                                            $msgreservation = $datareservation = "";
                                        }
                                        $vendername=$listRow['com_name'];
                                        $venderidx=$listRow['vender'];

                                        #####################상품별 회원할인율 적용 시작#######################################
                                        $discountprices = getProductDiscount($productcode);
                                        if($discountprices > 0 AND isSeller() != 'Y' ){
                                            $memberprice = $listRow['sellprice'] - $discountprices;
                                            $listRow['sellprice'] = $memberprice;
                                        }
                                        #####################상품별 회원할인율 적용 끝 #######################################

                                        $viewPrice="";
                                        $dicker=new_dickerview($listRow['etctype'],$wholeSaleIcon.number_format($listRow['sellprice'])."원",1);
                                        if($memberprice > 0) {	// 회원 로그인(및 등급 할인) 일 경우에만
                                            $viewPrice.="<img src='/images/common/memsale_icon.gif' /> ";
                                        }

                                        if (count($dicker['memOpenData']) > 0) {
                                            if ($dicker['memOpenData']['type'] == "img") {
                                                $viewPrice.="<img src='".$dicker['memOpenData']['value']."' />";
                                            } else {
                                                $viewPrice.="<span class=''>".$dicker['memOpenData']['value']."</span>";
                                            }
                                        } else if (strlen($_data->proption_price) == 0) {
                                            $viewPrice.=$wholeSaleIcon.number_format($listRow['sellprice'])."원";
                                        } else {
                                            if (strlen($listRow['option_price']) == 0) {
                                                $viewPrice.=$wholeSaleIcon.number_format($listRow['sellprice'])."원";
                                            } else {
                                                $viewPrice.=ereg_replace("\[PRICE\]",number_format($listRow['sellprice']),$_data->proption_price);
                                            }
                                        }

                                        //상품평 수
                                        $sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
                                        $result_cnt3=mysql_query($sql_cnt3,get_db_conn());
                                        $row_cnt3=mysql_fetch_object($result_cnt3);
                                        $t_cnt3 = (int)$row_cnt3->t_count;


                                        if(strlen($listRow[tinyimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$listRow[tinyimage])==true){
                                            $background_url=$Dir.DataDir."shopimages/product/".urlencode($listRow[tinyimage]);
                                        }else{
                                            $background_url=$Dir."images/no_img.gif";
                                        }

                                        $prdetail_link="productdetail_tab01.php?productcode=".$productcode."";


                                        $youtube_url=$listRow['youtube_url'];
                                        $youtube_prlist=$listRow['youtube_prlist'];
                                        $youtube_prlist_file=$listRow['youtube_prlist_file'];
                                        $youtube_prlist_imgtype=$listRow['youtube_prlist_imgtype'];

                                        //동영상(유튜브) 등록일 때 상품이미지 교체
                                        if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='Y'){
                                            $youtube_code=str_replace("https://youtu.be/","",$youtube_url);
                                            $prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
                                            $background_image=str_replace("https://youtu.be/","",$youtube_url);
                                            $background_url="https://img.youtube.com/vi/".$background_image."/sddefault.jpg";

                                        }else if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='D'){
                                            $youtube_code=str_replace("https://youtu.be/","",$youtube_url);
                                            $prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
                                            $background_image=$youtube_prlist_file;
                                            $background_url=$Dir.DataDir."shopimages/product/".$background_image;
                                        }

                                        $width=getimagesize($background_url);
                                        if($width[1]>$width[0]){ //세로가 가로보다 길 때
                                            $background_size="100% auto";
                                        }else{ //가로가 세로보다 길 때
                                            $background_size="auto 100%";
                                        }
                                        ?>
                                        <li class="product_item">
                                            <div class="product_view">
                                                <div class="product_img">
                                                    <?
                                                    if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
                                                        echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
                                                    }
                                                    ?>
                                                    <a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;">
                                                        <img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
                                                    </a>
                                                </div>
                                                <? if($discountRate>0){ ?><div class="product_sale"><?=$discountRate?>%</div><? } ?>
                                            </div>
                                            <div class="product_info">
                                                <div class="product_name"><?=$msgreservation?><?=$productname?></div>
                                                <? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
                                                <div class="product_price">
                                                    <?
                                                    echo $viewPrice;
                                                    if($listRow['quantity']=="0") echo soldout();
                                                    ?>
                                                </div>
                                                <? if (!$dicker['memOpen']) { ?>
                                                    <? if($prconsumerprice>0){ ?><div class="product_discount"><?=$prconsumerprice?>원</div><? } ?>
                                                <? } ?>
                                                <div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
                                                <? if(strlen($vendername)>0){ ?>
                                                    <div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');">판매자 : <?=$vendername?></a></div>
                                                <? } ?>
                                                <? if($listRow['etctype']){ ?><div>
                                                    <!--모바일 메인 아이콘-->
                                                    <?
                                                    $icoi = strpos(" ".$listRow['etctype'],"ICON=");
                                                    if($icoi>0){
                                                        $icon = substr($listRow['etctype'],strpos($listRow['etctype'],"ICON="));
                                                        $icon = substr($icon,5,strpos($icon,"")-5);
                                                        $num=strlen($icon);
                                                        for($j=0;$j<$num;$j+=2){
                                                            $temp=$icon[$j].$icon[$j+1];
                                                            /*
                                                                        if($temp=='04'){
                                                                            $icon_name="NEW";
                                                                        }else if($temp=='13'){
                                                                            $icon_name="BEST";
                                                                        }else{
                                                                            $icon_name="HOT";
                                                                        }
                                                            */
                                                            if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
                                                                echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
                                                            } else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
                                                                echo "<span class='icon".$temp."'></span>";
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                    <!--모바일 아이콘-->



                                                    <?// echo viewproductname('',$listRow['etctype'],'');?>

                                                    </div><? } ?>
                                                <div><span class="product_coment"><?=$t_cnt3?></span></div>
                                            </div>
                                        </li>
                                        <?
                                    }
                                }else{
                                    ?>
                                    <li>진열된 상품이 없습니다.</li>
                                    <?
                                }
                                mysql_free_result($listRes);
                            }else{
                                ?>
                                <li>연결이 지연되었습니다 다시 시도해주세요.</li>
                                <?
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- //상품리스트-타입2 -->
                    <?
                    break;
                case "list":
                    ?>

                    <!-- 상품리스트-타입3 -->
                    <div class="product_b">
                        <ul class="product_list">
                            <?
                            $itemcount = 5; // 페이지당 게시글 리스트 수
                            $sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;
                            if(false !== $listRes = mysql_query($sql,get_db_conn())){
                                $listNumRows = mysql_num_rows($listRes);
                                if($listNumRows > 0){
                                    while($listRow = mysql_fetch_assoc($listRes)){
                                        $productcode=$listRow['productcode'];
                                        $maximage=$listRow['maximage'];
                                        $productname=_strCut($listRow['productname'],34,4,$charset);
                                        $productmsg = $listRow['prmsg'];
                                        $consumerprice = number_format($listRow['consumerprice']);
                                        $discountRate  = $listRow['discountRate'];
                                        $wholeSaleIcon = ( $listRow['isdiscountprice'] == 1 ) ? '<img src="/images/common/wholeSaleIcon.gif"/>':"";
                                        $memberpriceValue = $listRow['sellprice'];
                                        $strikeStart = $strikeEnd = '';
                                        $memberprice = 0;
                                        $reservation = $listRow['reservation'];
                                        $vendername = $listRow['com_name'];
                                        $venderidx=$listRow['vender'];

                                        if(strlen($reservation)>0 && $reservation != "0000-00-00"){
                                            $msgreservation = "<span class=\"font-orange\">(예약)</span> ";
                                            $datareservation = $reservation;
                                        }else{
                                            $msgreservation = $datareservation = "";
                                        }

                                        #####################상품별 회원할인율 적용 시작#######################################
                                        $discountprices = getProductDiscount($productcode);
                                        if($discountprices > 0 AND isSeller() != 'Y' ){
                                            $memberprice = $listRow['sellprice'] - $discountprices;
                                            $listRow['sellprice'] = $memberprice;
                                        }
                                        #####################상품별 회원할인율 적용 끝 #######################################

                                        $viewPrice = "";
                                        $dicker = new_dickerview($listRow['etctype'],$wholeSaleIcon.number_format($listRow['sellprice'])."원",1);
                                        if($memberprice > 0) {	// 회원 로그인(및 등급 할인) 일 경우에만
                                            $viewPrice .= "<img src='/images/common/memsale_icon.gif' /> ";
                                        }

                                        if (count($dicker['memOpenData']) > 0) {
                                            if ($dicker['memOpenData']['type'] == "img") {
                                                $viewPrice .= "<img src='".$dicker['memOpenData']['value']."' />";
                                            } else {
                                                $viewPrice .= "<span class=''>".$dicker['memOpenData']['value']."</span>";
                                            }
                                        } else if (strlen($_data->proption_price) == 0) {
                                            $viewPrice .= $wholeSaleIcon.number_format($listRow['sellprice'])."원";
                                        } else {
                                            if (strlen($listRow['option_price']) == 0) {
                                                $viewPrice .= $wholeSaleIcon.number_format($listRow['sellprice'])."원";
                                            } else {
                                                $viewPrice .= ereg_replace("\[PRICE\]",number_format($listRow['sellprice']),$_data->proption_price);
                                            }
                                        }

                                        //상품평 수
                                        $sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$productcode'";
                                        $result_cnt3=mysql_query($sql_cnt3,get_db_conn());
                                        $row_cnt3=mysql_fetch_object($result_cnt3);
                                        $t_cnt3 = (int)$row_cnt3->t_count;


                                        $background_url="";
                                        if(is_file($savewideloc.$wideimage)>0){
                                            $background_url=$savewideloc.$wideimage;
                                        }else if(strlen($listRow[minimage])>0 && file_exists($Dir.DataDir."shopimages/product/".$listRow[minimage])==true){
                                            $background_url=$Dir.DataDir."shopimages/product/".urlencode($listRow[minimage]);
                                        }else{
                                            $background_url=$Dir."images/no_img.gif";
                                        }

                                        $prdetail_link="productdetail_tab01.php?productcode=".$productcode."";


                                        $youtube_url=$listRow['youtube_url'];
                                        $youtube_prlist=$listRow['youtube_prlist'];
                                        $youtube_prlist_file=$listRow['youtube_prlist_file'];
                                        $youtube_prlist_imgtype=$listRow['youtube_prlist_imgtype'];

                                        //동영상(유튜브) 등록일 때 상품이미지 교체
                                        if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='Y'){
                                            $youtube_code=str_replace("https://youtu.be/","",$youtube_url);
                                            $prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
                                            $background_image=str_replace("https://youtu.be/","",$youtube_url);
                                            $background_url="https://img.youtube.com/vi/".$background_image."/sddefault.jpg";

                                        }else if(strlen($youtube_url)>0 && $youtube_prlist=='Y' && $youtube_prlist_imgtype=='D'){
                                            $youtube_code=str_replace("https://youtu.be/","",$youtube_url);
                                            $prdetail_link="'http://www.youtube.com/watch?v=".$youtube_code."' class='modal_movie' ";
                                            $background_image=$youtube_prlist_file;
                                            $background_url=$Dir.DataDir."shopimages/product/".$background_image;
                                        }

                                        $width=getimagesize($background_url);
                                        if($width[1]>$width[0]){ //세로가 가로보다 길 때
                                            $background_size="100% auto";
                                        }else{ //가로가 세로보다 길 때
                                            $background_size="auto 100%";
                                        }
                                        ?>
                                        <li class="product_item">
                                            <div class="product_view">
                                                <div class="product_img">
                                                    <?
                                                    if(strlen($youtube_url)>0 && $youtube_prlist=='Y'){ //동영상(유튜브) 이미지는 퀵툴 미출력
                                                        echo "<div style='position:absolute;top:50%;left:50%;width:80px;height:80px;margin-left:-40px;margin-top:-40px;font-size:0px;line-height:0%;'><A HREF=".$prdetail_link."><img src='/images/movie_icon.png' alt='' /></a></div>";
                                                    }
                                                    ?>
                                                    <a href=<?=$prdetail_link?> rel="external" style="display:block;width:100%;height:100%;background:url('<?=$background_url?>') no-repeat;background-size:<?=$background_size?>;background-position:center;font-size:0px;" />
                                                    <? if($discountRate>0){ ?><div class="product_sale"><?=$discountRate?>%</div><? } ?>
                                                    <img src="/images/common/trans.gif" width="100%" alt="상품 이미지" class="pr_pt" />
                                                    </a>
                                                </div>
                                                <? if($discountRate>0){ ?><div class="product_sale"><?=$discountRate?>%</div><? } ?>
                                            </div>
                                            <div class="product_info">
                                                <div class="product_name"><a href="productdetail_tab01.php?productcode=<?=$listRow['productcode']?><?=$add_query?>&sort=<?=$sort?>" rel="external"><?=$msgreservation?><?=$productname?></a></div>
                                                <? if(strlen($productmsg)>0){ ?><div class="product_caption"><?=$productmsg?></div><? } ?>
                                                <div class="product_price">
                                                    <?
                                                    echo $viewPrice;
                                                    if ($listRow['quantity']=="0") echo soldout();
                                                    ?>
                                                </div>
                                                <? if($consumerprice>0){ ?><div class="product_discount"><?=$consumerprice?>원</div><? } ?>
                                                <? if(strlen($reservation)>0 && $reservation != "0000-00-00"){ ?>
                                                    <div class="product_reserve"><span><?=$msgreservation?></span> <?=$datareservation?></div>
                                                <? } ?>
                                                <? if(strlen($vendername)>0){ ?>
                                                    <div class="product_seller"><a href="javascript:venderInfo('<?=$venderidx?>');">판매자 : <?=$vendername?></a></div>
                                                <? } ?>
                                                <? if($listRow['etctype']){ ?><div>
                                                    <!--모바일 메인 아이콘-->
                                                    <?
                                                    $icoi = strpos(" ".$listRow['etctype'],"ICON=");
                                                    if($icoi>0){
                                                        $icon = substr($listRow['etctype'],strpos($listRow['etctype'],"ICON="));
                                                        $icon = substr($icon,5,strpos($icon,"")-5);
                                                        $num=strlen($icon);
                                                        for($j=0;$j<$num;$j+=2){
                                                            $temp=$icon[$j].$icon[$j+1];
                                                            /*
                                                                        if($temp=='04'){
                                                                            $icon_name="NEW";
                                                                        }else if($temp=='13'){
                                                                            $icon_name="BEST";
                                                                        }else{
                                                                            $icon_name="HOT";
                                                                        }
                                                            */
                                                            if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
                                                                echo "<img src=\"http://".$_ShopInfo->shopurl.DataDir."shopimages/etc/icon".$temp.".gif\" align=\"absmiddle\" border=\"0\" />";
                                                            } else if(strlen($temp)>0 && !preg_match("/^(U)[1-6]$/",$temp)) {
                                                                echo "<span class='icon".$temp."'></span>";
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                    <!--모바일 아이콘-->



                                                    <?// echo viewproductname('',$listRow['etctype'],'');?>

                                                    </div><? } ?>
                                            </div>

                                            <div class="product_communicate">
                                                <ul>
                                                    <li>
                                                        <a href="#"><span class="product_coment"><?=$t_cnt3?></span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <?
                                    }
                                }else{
                                    ?>
                                    <li>진열된 상품이 없습니다.</li>
                                    <?
                                }
                                mysql_free_result($listRes);
                            }else{
                                ?>
                                <li>연결이 지연되었습니다 다시 시도해주세요.</li>
                                <?
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- //상품리스트-타입3 -->
                    <?
                    break;
            }
            ?>

            <? if($displaymode != "webzine"){ ?>
                <div id="page_wrap">
                    <?
                    $pageLink =$_SERVER['PHP_SELF']."?sort=".$sort."&list_type=".$displaymode."&page=%u";
                    $pagePerBlock = ceil($rowcount/$itemcount);
                    $paging = new pages($pageparam);
                    $paging->_init(array('page'=>$currentPage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
                    echo $paging->_result('fulltext');
                    ?>
                </div>
            <? } ?>

        </div><!-- wrapper -->
    </div><!-- list -->

    <form name="form2" method="get" action="<?=$_SERVER[PHP_SELF]?>">
        <input type="hidden" name="sort" value="<?=$sort?>" />
        <input type="hidden" name="list_type" value="<?=$displaymode?>" />
    </form>

    <script type="text/javascript" src="./js/wishlist_ajax.js"></script>
    <script type="text/javascript">
        <!--
        function ChangeSort(val) {
            document.form2.sort.value=val;
            document.form2.submit();
        }

        function changeDisplayMode(displaymode,sort){
            location.href="productbest.php?list_type="+displaymode+"&sort="+sort;
            return;
        }
        //-->
    </script>

    <!-- 옵션바로담기 -->
    <script>
        function optChecker(prcode,type){
            $('#show_contents').html("");
            $.post('productlist_opt_checker.php?productcode='+prcode+'&buy_type='+type, function(data){
                $('#show_contents').html(data);
            });

            setTimeout(function(){
                $('#wrap_layer_popup').dialog({
                    create:function(){
                        $(this).parent().css({position:"fixed"});
                    },
                    title: '상품 옵션선택',
                    modal: true,
                    width: '90%',
                    height: 'auto'
                });
            },200);
        }
    </script>

    <script language="javascript">
        <!--

        function displayUL(target,page_cnt,k){
            kk = k -1;
            for(i=0;i<page_cnt;i++){
                document.getElementById(target+"_main_product_"+i).style.display = 'none';
                document.getElementById(target+"_page_"+i).style.display = 'none';
            }
            document.getElementById(target+"_main_product_"+kk).style.display = '';
            document.getElementById(target+"_page_"+kk).style.display = '';
        }

        function change_quantity(theform,gbn) {
            var frm = document.getElementById(theform);

            tmp=frm.quantity.value;
            if(gbn=="up") {
                tmp++;
            } else if(gbn=="dn") {
                if(tmp>1) tmp--;
            }
            if(frm.quantity.value!=tmp) {
                <? if($_pdata->assembleuse=="Y") { ?>
                if(getQuantityCheck(tmp)) {
                    if(frm.assemblequantity) {
                        frm.assemblequantity.value=tmp;
                    }
                    frm.quantity.value=tmp;
                    setTotalPrice(tmp);
                } else {
                    alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
                    return;
                }
                <? } else { ?>
                frm.quantity.value=tmp;
                <? } ?>
            }
        }

        function CheckForm(theform, productcode, type) {
            if(theform.quantity.value.length==0 || theform.quantity.value==0) {
                alert("주문수량을 입력하세요.");
                theform.quantity.focus();
                return;
            }
            if(isNaN(theform.quantity.value)) {
                alert("주문수량은 숫자만 입력하세요.");
                theform.quantity.focus();
                return;
            }
            if(typeof(theform.option1)!="undefined" && theform.option1.selectedIndex<1) {
                alert('해당 상품의 옵션을 선택하세요.');
                theform.option1.focus();
                return;
            }
            if(typeof(theform.option2)!="undefined" && theform.option2.selectedIndex<1) {
                alert('해당 상품의 옵션을 선택하세요.');
                theform.option2.focus();
                return;
            }

            if(typeof(theform.option1)!="undefined") {
                document.getElementById("opt_idx_"+productcode).value = theform.option1.value;
                document.getElementById("opt_quantity_"+productcode).value = theform.quantity.value;
                theform.option1.value = "";
            }

            if(typeof(theform.option2)!="undefined") {
                document.getElementById("opt_idx2_"+productcode).value = theform.option2.value;
            } else if(typeof(theform.option1)!="undefined" && typeof(theform.option2)=="undefined") {
                document.getElementById("opt_idx2_"+productcode).value = 1;
            }

            theform.ordertype.value=type;
            theform.submit();
        }


        function check_login() {
            if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
                document.location.href="login.php?chUrl=<?=getUrl()?>";
            }
        }
        //-->
    </script>

<? include "footer.php"; ?>
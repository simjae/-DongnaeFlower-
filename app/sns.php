<div class="sns_wrap">
    <?
    $appid = $_SERVER['HTTP_HOST'];
    $appname = $_data->shopname;
    if(empty($appname)) $appname = $_data->companyname;

    $mobilesns_sql = "SELECT use_mobile_sns FROM tblmobileconfig";
    $mobilesns_result = mysql_query($mobilesns_sql,get_db_conn());
    $mobilesns_row = mysql_fetch_object($mobilesns_result);

    $sns_set = explode('|',$mobilesns_row->use_mobile_sns);

    $set_kakaotalk = $sns_set[0];
    $set_kakaostory = $sns_set[1];
    $set_facebook = $sns_set[2];
    $set_twitter = $sns_set[3];
    $set_pinterest = $sns_set[4];
    $set_googleplus = $sns_set[5];


    $imagesrc =$Dir."/data/shopimages/product/".$_pdata->maximage;
    $imgsize = array();
    $imgsize = getimagesize($imagesrc);
    $imagecapacity = filesize($imagesrc);
    $sendmaxcapacity ="512000";

    $shareWidth=!_empty($imgsize[0])?trim($imgsize[0]):"";
    $shareHeight = !_empty($imgsize[1])?trim($imgsize[1]):"";
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
    $kakao_primagesrc = $protocol.'://'.$_SERVER['HTTP_HOST']."/data/shopimages/product/".$_pdata->maximage;
    $kakao_returnurl = $protocol."://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?productcode=".$_pdata->productcode;
    $kakao_prname = $_pdata->productname;
    $kakao_prcontents = trim(strip_tags($_pdata->content));

    $kakaoinfoSQL = "SELECT state, secret FROM tblshopsnsinfo WHERE type ='k' ";

    $kakaousestate = $kakaousekey = "";
    if(false !== $kakaoinfoRes = mysql_query($kakaoinfoSQL,get_db_conn())){
        $kakaoinfocount = mysql_num_rows($kakaoinfoRes);
        if($kakaoinfocount>0){
            $kakaousestate = trim(mysql_result($kakaoinfoRes,0,0));
            $kakaousekey = trim(mysql_result($kakaoinfoRes,0,1));
        }
    }
    ?>

    <!-- QR CODE -->
    <div onclick="qrcodeView()" style="display:inline-block;width:32px;height:32px;line-height:32px;border:1px solid #ddd;box-sizing:border-box;font-size:0.9em;text-align:center">QR<!--qrcode--></div>

    <div id="qrCodeView" onclick="qrcodeClose()" style="display:none;position:fixed;top:0px;left:0px;width:100%;height:100%;margin:0px;padding:0px;background:rgba(0,0,0,0.7);font-size:0px;line-height:0%;z-index:910;">
        <div style="position:absolute;top:50%;left:50%;width:200px;height:200px;margin-top:-100px;margin-left:-100px;background:url('/data/qrcode/<?=$_pdata->productcode?>.png') no-repeat;background-position:center;background-size:100% auto;"></div>
    </div>

    <script>
        //QR코드 보기
        function qrcodeView(){
            $('#qrCodeView').fadeIn('300');
            $('body').css('overflow','hidden');
        }

        //QR코드 닫기
        function qrcodeClose(){
            $('#qrCodeView').fadeOut('300');
            $('body').css('overflow','');
        }
    </script>
    <!-- QR CODE -->

    <? if(!$_SESSION[sellvidx]){ ?>

        <!-- 카카오 링크 V2 -->
        <script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
        <a id="kakao-link-btn" href="javascript:;" style="display:inline-block;*display:inline;*zoom:1;margin-left:4px;"><img src="/images/design/icon_kakaolink.png" style="width:32px;height:32px" /></a>

        <script type='text/javascript'>
            //<![CDATA[
            // // 사용할 앱의 JavaScript 키를 설정해 주세요.
            Kakao.init('<?=$kakaousekey?>');
            // // 카카오링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
            Kakao.Link.createDefaultButton({
                container: '#kakao-link-btn',
                objectType: 'commerce',
                content: {
                    title: '<?=$kakao_prname?>',
                    imageUrl: "<?=$kakao_primagesrc?>",
                    link: {
                        mobileWebUrl: 'http://<?=$_data->shopurl?>m/productdetail_tab01.php?productcode=<?=$_pdata->productcode?>',
                        webUrl: 'http://<?=$_data->shopurl?>front/productdetail.php?productcode=<?=$_pdata->productcode?>'
                    }
                },
                commerce: {
                    regularPrice: <?=$_pdata->consumerprice?>,
                    discountPrice: <?=$_pdata->sellprice?>,
                    discountRate: <?=$_pdata->discountRate?>
                },
                buttons: [
                    {
                        title: '구매하기',
                        link: {
                            mobileWebUrl: 'http://<?=$_data->shopurl?>m/productdetail_tab01.php?productcode=<?=$_pdata->productcode?>',
                            webUrl: 'http://<?=$_data->shopurl?>front/productdetail.php?productcode=<?=$_pdata->productcode?>'
                        }
                    },
                    {
                        title: '공유하기',
                        link: {
                            mobileWebUrl: 'http://<?=$_data->shopurl?>m/productdetail_tab01.php?productcode=<?=$_pdata->productcode?>',
                            webUrl: 'http://<?=$_data->shopurl?>front/productdetail.php?productcode=<?=$_pdata->productcode?>'
                        }
                    }
                ]
            });
            //]]>
        </script>
        <!-- 카카오 링크 V2 -->
    <? } ?>



    <? /*
	<form name="snsreseveForm" action="promotion_payreserve_proc.php" method="post" >
		<input type="hidden" name="prcode" value="<?=$_pdata->productcode?>"/>
		<input type="hidden" name="promotiontype" value=""/>
	</form>

	<iframe id="PROMOTION" name="PROMOTION" style="display:none"></iframe>
	<iframe id="SNSPROC" name="SNSPROC" style="display:none"></iframe>

	<?if($set_kakaotalk == 'Y'){?>
		<div id="kakao-link-btn" class="snskakaotalk" onclick="snsSendProc('KT')" /></div>
	<?}?>
	<?if($set_kakaostory == 'Y'){?>
		<div class="snskakaostory" onclick="snsSendProc('KS')" /></div>
	<?}?>
	<?if($set_pinterest == 'Y'){?>
		<div class="snspinterest" onclick="snsSendProc('PI');" /></div>
	<?}?>
	<?if($set_googleplus == 'Y'){?>
		<div class="snsgoogleplus" onclick="snsSendProc('GO');" /></div>
	<?}?>
	<?if($set_twitter == 'Y'){?>
		<div class="snstwitter" onclick="snsSendProc('TW');" /></div>
	<?}?>
	<?if($set_facebook == 'Y'){?>
		<div class="snsfacebook" onclick="snsSendProc('FB');" /></div>
	<?}?>
	*/ ?>
</div>
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
	<form name="snsreseveForm" action="promotion_payreserve_proc.php" method="post" >
		<input type="hidden" name="prcode" value="<?=$_pdata->productcode?>"/>
		<input type="hidden" name="promotiontype" value=""/>
	</form>

	<form name="snsprocForm" action="prsns_proc.php" method="post" target="SNSPROC">
		<input type="hidden" name="prcode" value="<?=$productcode?>"/>
		<input type="hidden" name="snstype" value=""/>
	</form>
	<iframe id="PROMOTION" name="PROMOTION" style="display:none"></iframe>
	<iframe id="SNSPROC" name="SNSPROC" style="display:none"></iframe>
	<?if($set_kakaotalk == 'Y'){?>
		<div class="snskakaotalk" onclick="kakaocall('KT')" /></div>
	<?}?>
	
	<?if($set_kakaostory == 'Y'){?>
	<div class="snskakaostory" onclick="kakaocall('KS')" /></div>
	<?}?>

	<?
		//Twitter, Facebook
		if($_data->sns_ok == "Y"){
			if(TWITTER_ID !="TWITTER_ID")
				echo "<input type=\"hidden\" name=\"tLoginBtnChk\" id=\"tLoginBtnChk\">";
			if(FACEBOOK_ID !="FACEBOOK_ID")
				echo "<input type=\"hidden\" name=\"fLoginBtnChk\" id=\"fLoginBtnChk\">";
		}
	?>
		<?if($set_twitter == 'Y'){?>
			<div class="snstwitter" onclick="snsSendProc('TW');" id="tLoginBtn0" /></div>
		<?}?>
		<?if($set_facebook == 'Y'){?>
			<div class="snsfacebook" onclick="snsSendProc('FB');" id="tLoginBtn0" /></div>
		<?}?>
	
</div>
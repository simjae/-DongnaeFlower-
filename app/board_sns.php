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
		$set_naverblog = $sns_set[6];

		$imgurl = "";//'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/product/".$_pdata->minimage;

		$sns_content = trim(strip_tags($view_content));

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

	<?if($set_kakaotalk == 'Y'){?>
		<div id="kakao-link-btn" class="snskakaotalk" onclick="snsSendProc('KT');" /></div>
	<?}?>
	<?if($set_kakaostory == 'Y'){?>
		<div class="snskakaostory" onclick="snsSendProc('KS');" /></div>
	<?}?>
	<?if($set_pinterest == 'Y'){?>
		<div class="snspinterest" onclick="snsSendProc('PI');" ></div>
	<?}?>
	<?if($set_googleplus == 'Y'){?>
		<div class="snsgoogleplus" onclick="snsSendProc('GO');" ></div>
	<?}?>
	<?if($set_twitter == 'Y'){?>
		<div class="snstwitter" onclick="snsSendProc('TW');" ></div>
	<?}?>
	<?if($set_facebook == 'Y'){?>
		<div class="snsfacebook" onclick="snsSendProc('FB');" ></div>
	<?}?>
	<?if($set_naverblog == 'Y'){?>
		<div class="snsnaverblog" onclick="snsSendProc('NB');"></div>
	<?}?>
</div>


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

		$imgurl  = "";//'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/product/".$_pdata->minimage;
		

		$sns_content = trim(strip_tags($view_content));
	?>

	<?if($set_kakaotalk == 'Y'){?>
		<a href="javascript:executeKakaoLink('<?=$view_title?>','<?='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?num='.$board_num?>&board=<?=$board_name?>','<?=$appid?>','<?=$appname?>')"><div class="snskakaotalk"></div></a>
	<?}?>
	
	<?if($set_kakaostory == 'Y'){?>
		<a href="javascript:executeKakaoStoryLink('<?='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?num='.$board_num?>&board=<?=$board_name?>','<?=$appid?>','<?=$appname?>','<?=$view_title?>','<?=$sns_content?>','<?=$imgurl?>')"><div class="snskakaostory"></div></a>
	<?}?>

	<?
		//Twitter, Facebook
		if($_data->sns_ok == "Y"){
			if(TWITTER_ID !="TWITTER_ID")
				echo "<input type=\"hidden\" name=\"tLoginBtnChk\" id=\"tLoginBtnChk\">";
			if(FACEBOOK_ID !="FACEBOOK_ID")
				echo "<input type=\"hidden\" name=\"fLoginBtnChk\" id=\"fLoginBtnChk\">";
			if(ME2DAY_ID !="ME2DAY_ID")
				echo "<input type=\"hidden\" name=\"mLoginBtnChk\" id=\"mLoginBtnChk\">";
		}
	?>
		<?if($set_twitter == 'Y'){?>
			<a href="javascript:SendSMS('t');"><div class="snstwitter"></div></a>
		<?}?>
		<?if($set_facebook == 'Y'){?>
			<a href="javascript:SendSMS('f');" ><div class="snsfacebook"></div></a>
		<?}?>
</div>


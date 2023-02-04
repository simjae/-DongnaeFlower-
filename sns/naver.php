<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

// 네이버 API 연결 상태
$state	= $naver->getConnectState();

// 네이버 로그인 API 에서 유저 정보 취득.
$result	= $naver->getUserProfile();
$result	= json_decode($result);
$info	= $result->response;

$id		= $naver->getSocialId().$info->id;
// 네이버 로그인 API 에서 취득한 유저 정보를 솔루션 회원 정보에서 비교.
$sql	= sprintf("select * from tblmember where member_out ='N' AND loginType = 'naver' AND id = '%s'", $id);
$result	= mysql_query($sql,get_db_conn());
$cnt	= mysql_num_rows($result);

// 팝업이나 아니냐 설정. 160331 지금은 설정이 없음.
$opener	= "opener.";
$close	= "self.close();";

if ($state && $cnt) {
	if($row = mysql_fetch_object($result)) {
		$memid		 = $row->id;
		$memname	 = !_empty($row->name) ? $row->name : $row->nickname;
		$mememail	 = $row->email;
		$memgroup	 = $row->group_code;
		$memreserve	 = $row->reserve;

		$authidkey = md5(uniqid(""));

		$_ShopInfo->setMemid($memid);
		$_ShopInfo->setAuthidkey($authidkey);
		$_ShopInfo->setMemgroup($memgroup);
		$_ShopInfo->setMemname($memname);
		$_ShopInfo->setMemreserve($memreserve);
		$_ShopInfo->setMememail($mememail);
		$_ShopInfo->Save();

		$sql = "UPDATE tblmember SET ";
		$sql.= "authidkey		= '".$authidkey."', ";
		$sql.= "ip				= '".getenv("REMOTE_ADDR")."', ";
		$sql.= "logindate		= '".date("YmdHis")."', ";
		$sql.= "logincnt		= logincnt+1 ";
		$sql.= "WHERE id = '".$_ShopInfo->getMemid()."'";
		mysql_query($sql,get_db_conn());

		$loginday = date("Ymd");
		$sql = "SELECT id_list FROM tblshopcountday ";
		$sql.= "WHERE date='".$loginday."'";
		$result = mysql_query($sql,get_db_conn());
		if($row3 = mysql_fetch_object($result)){
			if(!strpos(" ".$row3->id_list,"".$_ShopInfo->getMemid()."")){
				$id_list=$row3->id_list.$_ShopInfo->getMemid()."";
				$sql = "UPDATE tblshopcountday SET id_list='".$id_list."',login_cnt=login_cnt+1 ";
				$sql.= "WHERE date='".$loginday."'";
				mysql_query($sql,get_db_conn());
			}
		} else {
			$id_list="".$_ShopInfo->getMemid()."";
			$sql = "INSERT INTO tblshopcountday (date,count,login_cnt,id_list) VALUES ('".$loginday."',1,1,'".$id_list."')";
			mysql_query($sql,get_db_conn());
		}

		if (!empty($chUrl)) {
			echo "<script>".$opener."location.href = '{$chUrl}';".$close."</script>";
			exit;
		} else {
			echo "<script>".$opener."location.href = '/m/';".$close."</script>";
			exit;
		}
	}
} else {
	echo '<script>'.$opener.'location.href = "/m/member_join.php?loginType=naver";'.$close.'</script>';
	exit;
}
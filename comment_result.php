<?php
//블로그형 게시물 location 변경 2016-03-17 Seul
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include ($Dir."board/lib.inc.php");
include ($Dir."board/file.inc.php");

$up_name=$_POST["up_name"];
$up_passwd=$_POST["up_passwd"];
$up_comment=$_POST["up_comment"];
$pageIndex=$_POST["pageindex"]; //블로그형 게시물 페이지 번호 2016-03-16 Seul



if ($setup[use_comment] != "Y") {
	$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if ($member[grant_comment]!="Y") {
	$errmsg="댓글쓰기 권한이 없습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if(!eregi($_SERVER[HTTP_HOST],$_SERVER[HTTP_REFERER])) {
	$errmsg="잘못된 경로로 접근하셨습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}


if(isNull($up_comment)) {
	$errmsg="내용을 입력하셔야 합니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if(strlen($member[name])==0) {
	if(isNull($up_name)) {
		$errmsg="이름을 입력하셔야 합니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
	if(isNull($up_passwd)) {
		$errmsg="비밀번호를 입력하셔야 합니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
} else {
	$up_name = $member[name];
}


$up_name = addslashes($up_name);
$up_comment = autoLink($up_comment);
$up_comment = addslashes($up_comment);

if ($setup[use_filter] == "1") {
	if (isFilter($setup[filter],$up_comment,$findFilter)) {
		$errmsg="사용 제한된 불량단어를 사용하셨습니다.\\n\\n다시 확인 하십시오.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
}

$check = mysql_fetch_array(mysql_query("SELECT num FROM tblboard WHERE board='$board' AND num = '$num'",get_db_conn()));
if(!$check[0]) {
	$errmsg="원본 글이 존재하지 않습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}


// 파일 등록

$filename = "";
if( $_FILES['img']['error'] == 0 AND $_FILES['img']['size'] > 0 ) {
	if( !eregi("image/", $_FILES['img']['type'] ) ) {
		echo "<html><head><title></title></head><body onload=\"alert('이미지 형태의 파일만 업로드 가능합니다.');history.go(-1);\"></body></html>";exit;
	}
	
	$filename = "cmt_".time()."_".preg_replace("^[a-zA-Z0-9\-]+$","",$_FILES['img']['name']);
	ProcessBoardDir($board,"create");
	move_uploaded_file($_FILES['img']['tmp_name'],DirPath.DataDir."shopimages/board/".$board."/".$filename);
}




$sql  = "INSERT INTO tblboardcomment (board,parent,name,passwd,ip,writetime,comment,id,file) VALUES ";
$sql .= "('".$board."','".$num."','".$up_name."','".$up_passwd."','".$_SERVER[REMOTE_ADDR]."','".time()."','".$up_comment."', '".$_ShopInfo->getMemid()."','".$filename."')";
$insert = mysql_query($sql,get_db_conn());

// 코멘트 갯수를 구해서 정리
$total=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."'",get_db_conn()));
mysql_query("UPDATE tblboard SET total_comment='".$total[0]."' WHERE board='".$board."' AND num='".$num."'",get_db_conn());

$boardTypeSQL = "SELECT * FROM tblboardadmin WHERE board='".$board."'";
$boardTypeResult = mysql_query($boardTypeSQL,get_db_conn());
$boardTypeRow = mysql_fetch_object($boardTypeResult);
$boardType = $boardTypeRow->board_skin;


if(strpos($boardType, "B")!==false) //블로그형 게시물은 location 주소 달라짐 2016-03-16 Seul
{
	header("Location:board_list.php?page=$pageIndex&board=$board");exit;
}
else
{
	header("Location:board_view.php?num=$num&board=$board");exit;
}

?>
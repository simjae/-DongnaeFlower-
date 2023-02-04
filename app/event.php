<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$num=$_REQUEST["num"];
$type=$_REQUEST["type"];
$no=$_REQUEST["no"];

$sql = "SELECT * FROM tblmobilepopup WHERE num='".$num."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	if($type=="close") {
		if($no=="yes") {
			$cookiename="eventpopup_".$row->num;
			if($row->cookietime==2)			//다시열지않음
				setcookie($cookiename,$row->end_date,time()+(60*60*24*30),"/".RootPath);
			else if($row->cookietime==1)	//하루동안 열지않음
				setcookie($cookiename,$row->end_date,time()+(60*60*24*1),"/".RootPath);
			else							//브라우저 종료때까지 열지않음
				setcookie($cookiename,$row->end_date,0,"/".RootPath);
		}
		mysql_free_result($result);
		echo "<script>window.close();</script>";
		exit;
	} else {
		if($row->frame_type=="2") {	//투프레임일 경우
			if($row->scroll_yn=="Y") $scroll="yes";
			else $scroll="no";

			echo "<!DOCTYPE HTML>\n";
			echo "<html>\n";
			echo "<head>\n";
			echo "<meta charset=\"UTF-8\"\n>";
			echo "<title>".$row->title."</title>\n";
			echo "<META http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n";
			echo "</head>\n";
			echo "<frameset rows=\"*,26\" border=0 MARGINWIDTH=0 MARGINHEIGHT=0 noresize>\n";
			echo "<frame src=\"".$Dir.MobileDir."event_frame.php?type=".$row->design."&one=1&num=".$row->num."\" name=event MARGINWIDTH=0 MARGINHEIGHT=0 scrolling=".$scroll.">\n";
			echo "<frame src=\"".$Dir.MobileDir."event_bottom.php?num=".$row->num."\" name=bottom MARGINWIDTH=0 MARGINHEIGHT=0 scrolling=no>\n";
			echo "</frameset>\n";
			echo "</html>";
			mysql_free_result($result);
		} else if($row->frame_type=="1") {	//원프레임일 경우
			include ($Dir.TempletDir."event/event".$row->design.".php");
		} else {	//레이어 타입은 그냥 닫는다.
			echo "<script>window.close();</script>";
			exit;
		}
	}
} else {
	echo "<script>window.close();</script>";
	exit;
}
?>
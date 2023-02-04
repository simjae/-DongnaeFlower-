<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$type = $_REQUEST[type];	//design
$one = $_REQUEST[one];		//원프레임인지 투프레임인지...
$num = $_REQUEST[num];		//이벤트 고유번호

include ($Dir.TempletDir."event/mobileevent".$type.".php");
?>

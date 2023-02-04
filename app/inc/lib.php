<?
function gotoPage($url) 
{ echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">"; }

function alert($s)
{ echo "<script language=javaScript>alert('$s');</script>"; }

function selfClose()
{ echo "<script language=javaScript>self.close();</script>"; }

function alertHistoryBack($s)
{	echo"	<script language=javaScript>alert('$s');	history.back();	</script>"; }

function historyBack()
{	echo"	<script language=javaScript>history.back();	</script>"; }

function locationHref($u)
{	echo"	<script language=javaScript>location.href='$u';</script>";  }

function alertLocationHref($s,$u)
{	echo"<script language=javaScript>alert('$s');location.href='$u';</script>"; }

function confirm($s)
{	echo"<script language=javaScript>	if(!confirm('$s')) {	history.back();	} </script>";  }

function openerReload()
{	echo"<script language=javaScript>opener.location.reload();</script>"; }

function parentReload()
{	echo"<script language=javaScript>parent.location.reload();</script>"; }

function parentOpenerReload()
{	echo"<script language=javaScript>parent.opener.location.reload();</script>"; }


function parentLocationHref($s)
{	echo"<script language=javaScript>parent.location.href='$s';</script>"; }


// 문자열 자르기

?>
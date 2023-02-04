	<!-- 리뷰 내용바로보기 -->
	<DIV id="reviewContents" style="position:absolute; display:none; background-color:#545454; z-index:1000;" >
		<DIV id="reviewContentsView"></DIV>
		<div><button class="button white bigrounded" style="width:100%; border:0px;padding:3px;color:#ffffff" onclick="reviewContentsClose();">X</button></div>
	</DIV>

	<script type="text/javascript">
	<!--
		function abspos(e){
			return e.clientY + (document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop);
		}

		function reviewOpen ( productcode, num, e ) {
			$.post( "reviewContents.php", { "productcode":productcode, "num": num }).done(function( data ) {
				$( "#reviewContentsView" ).html( data );
				pos = abspos(e);
				$( "#reviewContents" ).css( "left", 9 );
				$( "#reviewContents" ).css( "top", pos );
				$( "#reviewContents" ).css( "width", "95%" );
				$( "#reviewContents" ).css( "display","block" );
			});
		}

		function reviewContentsClose() {
			$( "#reviewContents" ).css( "display","none" );
		}
	//-->
	</script>
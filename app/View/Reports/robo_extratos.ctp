<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

<script type="text/javascript">
	function resize_iframe() {
		var height = window.innerWidth; //Firefox
		if (document.body.clientHeight) {
			height = document.body.clientHeight; //IE
		}
		height += 1170;
		document.getElementById("frame").style.height = parseInt(height -
			document.getElementById("frame").offsetTop) + "px";
		frame
	}
</script>

<iframe src="http://robo.berh.com.br/extratos" scrolling="auto" id="frame" onload="resize_iframe()" style="width: 100%;" frameborder="0"></iframe>
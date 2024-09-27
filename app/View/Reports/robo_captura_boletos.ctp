<script type="text/javascript">
	function resize_iframe() {
		var height = window.innerWidth; //Firefox
		if (document.body.clientHeight) {
			height = document.body.clientHeight; //IE
		}
		document.getElementById("frame").style.height = parseInt(height -
			document.getElementById("frame").offsetTop) + "px";
		frame
	}
</script>

<iframe src="https://robo.berh.com.br/captura_boletos" allow="clipboard-read; clipboard-write" scrolling="auto" id="frame" onload="resize_iframe()" style="width: 100%;" frameborder="0"></iframe>
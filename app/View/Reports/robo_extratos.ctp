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

<iframe src="https://robo.berh.com.br/extratos/set-cookie?hash=6eb0fed6ec2700a0ecabe9752644c8d4b43942f6f0193a6b6da7babef9e56841" allow="clipboard-read; clipboard-write" scrolling="auto" id="frame" onload="resize_iframe()" style="width: 100%;" frameborder="0"></iframe>
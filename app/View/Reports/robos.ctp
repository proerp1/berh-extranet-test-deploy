<script type="text/javascript">
	$(document).ready(function() {
		setTimeout(function() {
			$("#frame_cookie").hide();
		}, 1000);

		setTimeout(function() {
			$("#frame_robos").attr("src", "<?php echo $url_iframe; ?>");
		}, 4000);
	});

	function resize_iframe() {
		var height = window.innerWidth; //Firefox
		if (document.body.clientHeight) {
			height = document.body.clientHeight; //IE
		}
		document.getElementById("frame_robos").style.height = parseInt(height -
			document.getElementById("frame_robos").offsetTop) + "px";
	}
</script>

<iframe src="<?php echo $url_cookie; ?>" id="frame_cookie" style="width: 0%;" frameborder="0"></iframe>

<iframe allow="clipboard-read; clipboard-write" scrolling="auto" id="frame_robos" onload="resize_iframe()" style="width: 100%;" frameborder="0"></iframe>
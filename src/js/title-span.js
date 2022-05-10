(function () {
	var heading = document.querySelector(".site-header .wp-block-site-title a");
	if (heading !== null) {
		var split = heading.innerHTML.split(" ");
		var words = ["of", "Of", "and", "And", "is", "Is", "&"];

		for (var i = 0; i < split.length; i++) {
			for (var j = 0; j < words.length; j++) {
				if (split[i] == words[j]) {
					split[i] = "<span class='flourish'>" + split[i] + "</span>";
				}
			}
		}

		var newHeading = split.join(" ");

		document.querySelector(".site-header .wp-block-site-title a").innerHTML =
			newHeading;
	}
})();

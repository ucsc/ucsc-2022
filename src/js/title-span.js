(function() {
var heading = document.querySelector('.main-header .wp-block-site-title a').innerHTML;
var split = heading.split(" ");
var words = ["of", "Of", "and", "And", "is", "Is"];

for (var i = 0; i < split.length; i++) {
    for (var j = 0; j < words.length; j++) {
        if (split[i] == words[j]) {
            split[i] = "<span class='flourish'>" + split[i] + "</span>";
        }
    }
}

var newHeading = split.join(' ');

document.querySelector('.main-header .wp-block-site-title a').innerHTML = newHeading;
// console.log(split);
})();

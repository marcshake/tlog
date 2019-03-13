

function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return false;
}

var setCookie = function (cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	console.log(document.cookie);
}

var accept_cookies=function() {
	document.getElementById('cookiebox').style.display = 'none';
	setCookie('consent', 'accepted', 365);
	return false;
}

var cookie_consent=function () {
	var cookie_read;
	cookie_read = getCookie('consent');
	if (cookie_read == false) {
		document.getElementById('cookiebox').style.display = 'block';
	}
}


window.cookie_consent = cookie_consent;
window.accept_cookies = accept_cookies;
window.setCookie = setCookie;


window.addEventListener('load', function() {
	/* document.getElementById('daPage').className+='lDone'; */
	cookie_consent();
	youtube_replacer();
	//para();
	scrollout();
	var allimages = document.getElementsByTagName('img');
	for (var i = 0; i < allimages.length; i++) {
		if (allimages[i].getAttribute('data-src')) {
			allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
		}
	}
}, false);

function youtube_replacer() {}

function scrollout() {
	window.onscroll = function() {
		var scrollBarPosition = window.pageYOffset | document.body.scrollTop;
		var intro = document.getElementById('parallax');
		if (intro === null) {
			return false;
		}
		intro.style.opacity = 1 - scrollBarPosition / 950;
	};
}

function para() {
	window.onscroll = function() {
		var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
		var scrollBarPosition = window.pageYOffset | document.body.scrollTop;
		if (w > 550) {
			var intro = document.getElementById('parallax');
			if (intro === null) {
				return false;
			}
			var tmpHeight = (intro.clientHeight) / 2;
			var scroll = (scrollBarPosition) * 0.25;
			intro.style.backgroundPosition = '50% -' + scroll + 'px';
		}
	};
}


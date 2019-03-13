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

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    console.log(document.cookie);
}

function accept_cookies() {
    document.getElementById('cookiebox').style.display = 'none';
    setCookie('consent', 'accepted', 365);

}


function cookie_consent() {
    var cookie_read;
    cookie_read = getCookie('consent');
    if (cookie_read == false) {
        document.getElementById('cookiebox').style.display = 'block';
    }
}

window.addEventListener('load', function () {
    /* document.getElementById('daPage').className+='lDone'; */
    cookie_consent();

    var allimages = document.getElementsByTagName('img');
    for (var i = 0; i < allimages.length; i++) {
        if (allimages[i].getAttribute('data-src')) {
            allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
        }
    }
}, false);
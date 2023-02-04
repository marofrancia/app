window.onload = function() {
    var links = document.getElementsByTagName("a");
    var currentUrl = window.location.href;
    for (var i = 0; i < links.length; i++) {
        if (currentUrl.indexOf(links[i].href) !== -1) {
            links[i].classList.add("active");
        }
    }
}
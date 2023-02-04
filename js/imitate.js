function imitate(id) {
    // make an AJAX call to a PHP script that sets the session for the selected user
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "imitate.php?id=" + id, true);
    xhr.send();

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // reload the page after setting the session
            location.reload();
        }
    }
}

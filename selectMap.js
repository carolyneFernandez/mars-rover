function getMap(){
    var xhr = new XMLHttpRequest();
    var urlJSON = "../Front/DumbDatas/map.json";

// Called whenever the readyState attribute changes
    xhr.onreadystatechange = function() {

        // Check if fetch request is done
        if (xhr.readyState == 4 && xhr.status == 200) {

            // Parse the JSON string
            var jsonData = JSON.parse(xhr.responseText);

            // Call the showArtists(), passing in the parsed JSON string
            test();
        }
    };

// Do the HTTP call using the url variable we specified above

    xhr.open("GET", urlJSON);
    xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
    xhr.send();
}
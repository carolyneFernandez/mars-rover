createRequestMapInit();

function createRequestMapInit() {
    // loadJSON(function(){
    //     let data = JSON.parse(response);
    //         console.log(data);
    //     //     for(let i = 0; i < data.length; i++){
    //     //         createRover(data[i].roverName, data[i].type, data[i].pathImage);
    //     //     }
    // });

    const map = JSON.parse(localStorage.getItem('mapSelected'));
    const difficulty = JSON.parse(localStorage.getItem('difficultySelected'));

    const mapSelected = map.name;
    const difficultySelected = difficulty.name;

    let nameMap;

    console.log(mapSelected);
    if (mapSelected === 'DefaultMap') {
        let url = url_api_carte + `?parameters_map[difficulty]=${difficultySelected}&parameters_map[glace]=1&parameters_map[fer]=1&parameters_map[argile]=1&parameters_map[minerai]=1&parameters_map[sable]=1&parameters_map[roche]=1`;
        url = encodeURI(url);
        const request = new Request(url);

        fetch(request)
            .then(function (data) {
                // console.log(data);
                return data.json();
            })
            .then(function (response) {
                // console.log(response);
                localStorage.setItem('mapData', JSON.stringify(response));
                window.location.replace("./game.html");
            });
    } else if (mapSelected === 'CustomMap') {
        const quantity = JSON.parse(localStorage.getItem('quantity'));
        let sRequest = url_api_carte + '?parameters_map[difficulty]=${difficultySelected}';

        console.log(quantity);

        for (let i = 0; i < quantity.length; i++) {
            if (quantity[i].quantity === 'moins') {
                sRequest += `&parameters_map[${quantity[i].name}]=0`;
            } else if (quantity[i].quantity === 'plus') {
                sRequest += `&parameters_map[${quantity[i].name}]=1`;
            }
        }

        fetch(sRequest)
            .then(function (data) {
                // console.log(data);
                return data.json();
            })
            .then(function (response) {
                // console.log(response);
                localStorage.setItem('mapData', JSON.stringify(response));
                window.location.replace("./game.html");
            });
    }
}


function loadJSON(callback) {

    let xobj = new XMLHttpRequest();
    xobj.overrideMimeType("application/json");
    xobj.open('GET', url_api_carte + 'api/getIceCase?mapName=map', true); // Replace 'my_data' with the path to your file
    xobj.onreadystatechange = function () {
        if (xobj.readyState == 4 && xobj.status == "200") {
            // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
            callback(xobj.responseText);
        }
    };
    xobj.send(null);
}
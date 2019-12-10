document.addEventListener('DOMContentLoaded', getRovers, false);

function getRovers(){


    // loadJSON(function(response) {
    //     // Parse JSON string into object
    //     let data = JSON.parse(response);
    //     console.log(data);
    //     for(let i = 0; i < data.length; i++){
    //         createRover(data[i].roverName, data[i].type, data[i].pathImage);
    //     }
    // });

    const request = new Request('Front/DumpDatas/rovers.json');

    fetch(request)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            for(let i = 0; i < data.length; i++){
                createRover(data[i].roverName, data[i].type, data[i].pathImage);
            }
        });
}

function getRoversSelected(){
    let rovers = document.getElementsByClassName('roverSelected');
    console.log(rovers);
    let roversSelected = [];

    for(let i = 0; i < rovers.length; i++){
        roversSelected.push(new roverObject(rovers[i].parentElement.attributes.name.value, rovers[i].parentElement.attributes.value.value));
    }
    window.localStorage.setItem('roversSelected', JSON.stringify(roversSelected));

    console.log(roversSelected);
    if(roversSelected.length !== 0){
        movePage('roversSelected', './selectMap.html');
    }
}

function createRover(name, type, path) {
    const newContainer = document.createElement('div');
    const container = document.getElementById('roverContainer');

    newContainer.classList.add('buttonSelect-column', 'smallAction');
    newContainer.setAttribute('name', name);
    newContainer.setAttribute('value', type);
    newContainer.addEventListener('click', getName);
    newContainer.addEventListener('click', selectedRover);
    container.appendChild(newContainer);

    const newContainerImage = document.createElement('div');
    newContainerImage.classList.add('wrapper-imageItem');
    newContainerImage.addEventListener('click', getName);
    newContainer.appendChild(newContainerImage);

    const newImage = document.createElement('img');
    newImage.classList.add('imageItem');
    newImage.setAttribute('src', path);
    newImage.setAttribute('alt', 'rover');
    newImage.addEventListener('click', getName);
    newContainerImage.appendChild(newImage);

    // create the name
    const newContainerName = document.createElement('div');
    newContainerName.classList.add('nameItem', 'usableButton', 'title');
    newContainerName.addEventListener('click', getName);

    newContainerName.innerHTML = name;
    newContainer.appendChild(newContainerName);
}

function getName(e) {
    const containerSelectedRoverName = document.getElementById('selectedRoverName');
    containerSelectedRoverName.innerText = e.target.getAttribute('name');
}

function selectedRover(e){
    if(!e.target.classList.contains('roverSelected')){
        e.target.classList.add('roverSelected');
    } else {
        e.target.classList.remove('roverSelected');
    }
}

function loadJSON(callback) {

    let xobj = new XMLHttpRequest();
    xobj.overrideMimeType("application/json");
    xobj.open('GET', 'Front/DumpDatas/rovers.json', true); // Replace 'my_data' with the path to your file
    xobj.onreadystatechange = function () {
        if (xobj.readyState == 4 && xobj.status == "200") {
            // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
            callback(xobj.responseText);
        }
    };
    xobj.send(null);
}
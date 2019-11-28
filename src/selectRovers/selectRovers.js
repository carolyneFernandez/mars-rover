document.addEventListener('DOMContentLoaded', getRovers, false);

function getRovers(){
    const request = new Request('http://localhost:80/rovers.json');

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
        roversSelected.push(new roverObject(rovers[i].attributes.name.value, rovers[i].attributes.value.value));
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
document.addEventListener('DOMContentLoaded', getRovers, false);

function getRovers(){
    const request = new Request('http://localhost:80/rovers.json');

    fetch(request, {
        Origin : 'http://localhost:8888/'
    })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            for(let i = 0; i < data.length; i++){
                createRover(data[i].roverName, data[i].pathImage);
            }
        });
}

function createRover(name, path) {
    const newContainer = document.createElement('div');
    const container = document.getElementById('roverContainer');

    newContainer.classList.add('buttonSelect-column', 'smallAction');
    newContainer.setAttribute('name', name);
    newContainer.addEventListener('click', getName);
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
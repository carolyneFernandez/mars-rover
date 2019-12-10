function createRequestMapInit() {
    const map = JSON.parse(localStorage.getItem('mapSelected'));
    const difficulty = JSON.parse(localStorage.getItem('difficultySelected'));

    const mapSelected = map.name;
    const difficultySelected = difficulty.name;

    let nameMap;

    console.log(mapSelected);
    if (mapSelected === 'DefaultMap'){
        const request = new Request(`http://localhost:80/?parameters_map[difficulty]=${difficultySelected}&parameters_map[ice]=1&parameters_map[iron]=1&parameters_map[clay]=1&parameters_map[minerals]=1&parameters_map[sand]=1&parameters_map[rocks]=1`);

        fetch(request)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            // add affichage de la carte
            // renvoi un name (nameMap)
        });
    } else if (mapSelected === 'CustomMap'){
        const quantity = JSON.parse(localStorage.getItem('quantity'));
        let sRequest = 'http://localhost:80/?parameters_map[difficulty]=${difficultySelected}';

        console.log(quantity);

        for(let i = 0; i < quantity.length; i++){
            if(quantity[i].quantity === 'moins'){
                sRequest += `&parameters_map[${quantity[i].name}]=0`;
            } else if (quantity[i].quantity === 'plus'){
                sRequest += `&parameters_map[${quantity[i].name}]=1`;
            }
        }

        fetch(sRequest)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            // add affichage de la carte
            // renvoi un name (nameMap)
        });
    }
}
function getSelectedMap(){
    let maps = document.getElementsByClassName('buttonSelect-row');
    let mapSelected;

    for(let i = 0; i < maps.length; i++){
        if(maps[i].classList.contains('logoSelected')){
            mapSelected = new mapObject('modSelected', maps[i].id, maps[i].lastElementChild.textContent);
        }
    }
    window.localStorage.setItem('mapSelected', JSON.stringify(mapSelected));

    if(mapSelected !== undefined){
        if(mapSelected.name === 'CustomMap'){
            movePage('mapSelected', './custom_map.html');
        } else {
            movePage('mapSelected', './selectDifficulty.html');
        }
    }
}

function selectedButtonMap(id){
    const buttonSelect = document.querySelectorAll('.buttonSelect-row');
    const svg0 = document.querySelectorAll('.logoSelect0');
    const svg1 = document.querySelectorAll('.logoSelect1');

    if(!buttonSelect[id].classList.contains('logoSelected')){
        buttonSelect[id].classList.add('logoSelected');
    } else {
        buttonSelect[id].classList.remove('logoSelected');
    }
    if (id === 0){
        if(svg0[0].classList.contains('logoSelectedFill') === false){
            for(let i = 0; i < svg0.length; i++){
                svg0[i].classList.add('logoSelectedFill');
            }
        } else {
            for(let i = 0; i < svg0.length; i++){
                svg0[i].classList.remove('logoSelectedFill');
            }
        }

        if (buttonSelect[id+1].classList.contains('logoSelected')){
            buttonSelect[id+1].classList.remove('logoSelected');
            for(let i = 0; i < svg1.length; i++){
                svg1[i].classList.remove('logoSelectedFill');
            }
        } else if (buttonSelect[id+2].classList.contains('logoSelected')) {
            buttonSelect[id+2].classList.remove('logoSelected');
            for(let i = 0; i < svg2.length; i++){
                svg2[i].classList.remove('logoSelectedFill');
            }
        }
    } else if (id === 1){
        if(!svg1[0].classList.contains('logoSelectedFill')){
            for(let i = 0; i < svg1.length; i++){
                svg1[i].classList.add('logoSelectedFill');
            }
        } else {
            for(let i = 0; i < svg1.length; i++){
                svg1[i].classList.remove('logoSelectedFill');
            }
        }

        if (buttonSelect[id-1].classList.contains('logoSelected')){
            buttonSelect[id-1].classList.remove('logoSelected');
            for(let i = 0; i < svg0.length; i++){
                svg0[i].classList.remove('logoSelectedFill');
            }
        } else if (buttonSelect[id+1].classList.contains('logoSelected')) {
            buttonSelect[id+1].classList.remove('logoSelected');
            for(let i = 0; i < svg2.length; i++){
                svg2[i].classList.remove('logoSelectedFill');
            }
        }
    }
}
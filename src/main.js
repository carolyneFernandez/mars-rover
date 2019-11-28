function selectedButton(id) {
    const buttonSelect = document.querySelectorAll('.buttonSelect-row');
    const svg = document.querySelectorAll('.logoSelect');
    if(!buttonSelect[id].classList.contains('logoSelected')){
        buttonSelect[id].classList.add('logoSelected');
    } else {
        buttonSelect[id].classList.remove('logoSelected');
    }
    if(!svg[id].classList.contains('logoSelectedFill') && id === 0){
        svg[id].classList.add('logoSelectedFill');
        svg[id+1].classList.add('logoSelectedFill');
    } else if (!svg[id+1].classList.contains('logoSelectedFill') && id === 1) {
        svg[id+1].classList.add('logoSelectedFill');
        svg[id+2].classList.add('logoSelectedFill');
    } else if (svg.classList.contains('logoSelectedFill') && id === 0) {
        svg[id].classList.remove('logoSelectedFill');
        svg[id+1].classList.remove('logoSelectedFill');
    } else if (svg[id+1].classList.contains('logoSelectedFill') && id === 1) {
        svg[id+1].classList.remove('logoSelectedFill');
        svg[id+2].classList.remove('logoSelectedFill');
    } 
    if (id === 0) {
        if (buttonSelect[id+1].classList.contains('logoSelected')){
            buttonSelect[id+1].classList.remove('logoSelected');
            svg[id+2].classList.remove('logoSelectedFill');
            svg[id+3].classList.remove('logoSelectedFill');
        }
    } else if (id === 1) {
        if (buttonSelect[id-1].classList.contains('logoSelected')){
            buttonSelect[id-1].classList.remove('logoSelected');
            svg[id-1].classList.remove('logoSelectedFill');
            svg[id].classList.remove('logoSelectedFill');
        }
    }
}

function selectedButton3items(id){
    const buttonSelect = document.querySelectorAll('.buttonSelect-row');
    const svg0 = document.querySelectorAll('.logoSelect0');
    const svg1 = document.querySelectorAll('.logoSelect1');
    const svg2 = document.querySelectorAll('.logoSelect2');

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
    } else if (id === 2){
        if(!svg2[0].classList.contains('logoSelectedFill')){
            for(let i = 0; i < svg2.length; i++){
                svg2[i].classList.add('logoSelectedFill');
            }
        } else {
            for(let i = 0; i < svg2.length; i++){
                svg2[i].classList.remove('logoSelectedFill');
            }
        }

        if (buttonSelect[id-1].classList.contains('logoSelected')){
            buttonSelect[id-1].classList.remove('logoSelected');
            for(let i = 0; i < svg1.length; i++){
                svg1[i].classList.remove('logoSelectedFill');
            }
        } else if (buttonSelect[id-2].classList.contains('logoSelected')) {
            buttonSelect[id-2].classList.remove('logoSelected');
            for(let i = 0; i < svg0.length; i++){
                svg0[i].classList.remove('logoSelectedFill');
            }
        }
    }
}

function movePage(item, path){
    if(localStorage.getItem(item) !== null || item !== undefined){
        window.location.replace(path);
    }
}
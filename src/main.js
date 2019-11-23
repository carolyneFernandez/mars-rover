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
    } else if (svg[id].classList.contains('logoSelectedFill') && id === 0) {
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
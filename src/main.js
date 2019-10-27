document.addEventListener('DOMContentLoaded', run, false);

function run() {

}

function selectedButton(id) {
    console.log('passed1');
    let buttonSelect = document.querySelectorAll('.buttonSelect-row')
    if(!buttonSelect[id].classList.contains('logoSelected')){
        
        console.log('passed2');
        buttonSelect[id].classList.add('logoSelected');
    } else {
        buttonSelect[id].classList.remove('logoSelected');
    }
}
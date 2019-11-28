function getSelectedDifficulty(){
    let difficulties = document.getElementsByClassName('buttonSelect-row');
    let difficultySelected;
        
    for(let i = 0; i < difficulties.length; i++){
        if(difficulties[i].classList.contains('logoSelected')){
            difficultySelected = new difficultyObject('difficultySelected', difficulties[i].id, difficulties[i].lastElementChild.textContent);
        }
    }
    window.localStorage.setItem('difficultySelected', JSON.stringify(difficultySelected));

    if(difficultySelected !== undefined){
        movePage('difficultySelected', './loader.html');
    }
}
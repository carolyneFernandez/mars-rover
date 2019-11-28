function getSelectedMod(){
    let mods = document.getElementsByClassName('buttonSelect-row');
    let modSelected;
        
    for(let i = 0; i < mods.length; i++){
        if(mods[i].classList.contains('logoSelected')){
            modSelected = new modObject('modSelected', mods[i].id, mods[i].lastElementChild.textContent);
        }
    }
     window.localStorage.setItem('modSelected', JSON.stringify(modSelected));

     if(modSelected !== undefined){
         movePage('modSelected', './selectRovers.html');
     }
}
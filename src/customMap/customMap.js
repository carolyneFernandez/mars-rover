function getQuantity(){
    let input = document.querySelectorAll('input');
    let inputQuantity = [];
        
    for(let i = 0; i < input.length; i++){
        if(input[i].checked === true){
            inputQuantity.push(new quantityObject(input[i].name, input[i].value));
        }
    }
    window.localStorage.setItem('quantity', JSON.stringify(inputQuantity));
    console.log(localStorage.getItem('quantity'));
}
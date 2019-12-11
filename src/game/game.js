$(document).ready(function () {

    // Récupération des variables stockés en local :
    const mapConf = jQuery.parseJSON(localStorage.getItem('mapData'));
    const modSelected = jQuery.parseJSON(localStorage.getItem('modSelected'));
    const roversSelected = jQuery.parseJSON(localStorage.getItem('roversSelected'));

    const CONTENTS = {
            '1':
                'glace',
            '2':
                'roche',
            '3':
                'sable',
            '4':
                'minerai',
            '5':
                'argile',
            '6':
                'fer',
            '7':
                'inconnue'
        }
    ;

    // Construction du jeu
    let game = constructGame(modSelected, mapConf, roversSelected);

    console.log(mapConf);
    console.log(modSelected);
    console.log(game);

    // Affichage de la map à l'écran
    displayMap(mapConf.map);


    console.log(game.rovers[0]);



});

function initRovers(game){

    $.each(game.rovers, function(index, rover){
        if( rover.pos_x == null || rover.pos_y == null ) {
            $("#game-inscructions").html("Sélectionner la position de départ du rover "+rover.name);
            $("td").on("click", setPosRover, {'rover':rover} );
        }else{
            $("td").off("click", setPosRover, {'rover':rover} );
        }
    });


}

function setPosRover(rover){
    console.log(this);
    const coordonnees = $(this).attr('data-coor').split('_');
    console.log(coordonnees);
    rover.pos_x(coordonnees[0]).pos_y(coordonnees[1]);

}


function constructGame(modSelected, mapConf, roversSelected){
    // Création du jeu
    let game = new Game();
    game.mode = modSelected.name;
    game.map = mapConf.mapName;

    // Affectation des rovers sélectionnés au jeu
    $.each(roversSelected, function (rover, value) {
        game.addRover(value);
    });

    return game;

}


function displayMap(map){
    $.each(map, function (y, resteY) {
        let chained = "<tr>";
        $.each(resteY, function (x, caseMap) {
            chained += '<td class="'+caseMap.material+'" data-coor="'+x+'_'+y+'"></td>';
        });
        chained += "</tr>";
        $(".map-display").append(chained);
    });


}





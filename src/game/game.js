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
    console.log(game.rovers.length);
    // Affichage de la map à l'écran
    displayMap(mapConf.map);
    initRovers(game);


    console.log(game.rovers[0]);


});

function gameRace(game) {
    console.log("racccceeeeee");

    while (game.round < 3) {

        $.each(game.rovers, function (index, rover) {
                const data =
                    {
                        typeRover: rover.type,
                        posX: parseInt(rover.pos_x),
                        posY: parseInt(rover.pos_y),
                        destX: parseInt(game.finish[0]),
                        destY: parseInt(game.finish[1]),
                        energy: parseInt(rover._energy),
                        map: game._map,
                        memory: rover._memory
                    };
                console.log(data);
                $.post(url_api_rover,   // url
                    JSON.stringify(data), // data to be submit
                    function(data, status, jqXHR) {// success callback
                        console.log(data);
                        // $('p').append('status: ' + status + ', data: ' + data);
                    });

        });

        game.nextRound();

    }

}

function gameFlag(game) {

}


function launchGame(game) {
    if (game.mode === 'Race') {
        gameRace(game);
    } else {
        gameFlag(game);
    }


}


function initGame(game) {
    if (game.finish == null) {
        if (game.mode === "Race") {
            setInstruction("Sélectionner l'arrivée");
        } else {
            setInstruction("Sélectionner l'emplacement du drapeau");
        }
        $("td").on("click", function () {
            setPosFinish(this, game);
        });
    }
}

function initRovers(game) {
    let roverParam = 0;
    $.each(game.rovers, function (index, rover) {
        if (rover.pos_x == null || rover.pos_y == null) {

            // rover.num = index;
            setInstruction("Sélectionner la position de départ du rover " + rover.name);
            $("td").on("click", function () {
                setPosRover(this, rover, game);
            });
            return false;
        } else {
            roverParam++;
            $("td").off("click");
        }
    });
    if (roverParam === game.rovers.length) initGame(game);


}

function setPosFinish(evt, game) {
    console.log(evt);
    const coordonnees = $(evt).attr('data-coor').split('_');
    console.log(coordonnees);
    game.finish = [coordonnees[0], coordonnees[1]];
    $(evt).addClass("case-finish");
    setInstruction("C'est parti !!");

    //lancement du jeu :
    launchGame(game);
}

function setPosRover(evt, rover, game) {
    console.log(evt);
    const coordonnees = $(evt).attr('data-coor').split('_');
    console.log(coordonnees);
    rover.pos_x = coordonnees[0];
    rover.pos_y = coordonnees[1];
    rover.originPosX = coordonnees[0];
    rover.originPosY = coordonnees[1];
    rmInstruction();
    displayRover(rover);
    initRovers(game);
}


function constructGame(modSelected, mapConf, roversSelected) {
    // Création du jeu
    let game = new Game();
    game.mode = modSelected.name;
    game.map = mapConf.mapName;

    // Affectation des rovers sélectionnés au jeu
    $.each(roversSelected, function (index, rover) {
        rover.num = index;
        let chained = '<div class="tableau-bord-rover" data-rover="' + rover.num + '" >';
        chained += '<h2 class="name-rover title">' + rover.name + '</h2>';
        chained += '<div class="coordonnees-rover text-blue-color">Coordonnées : <span id="coordonnees-rover-' + rover.num + '"></span></div>';
        chained += '<div class="ernegy-rover text-blue-color">Energie : <span id="energy-rover-' + rover.num + '">' + rover._energy + '</span></div>';
        chained += '</div>';
        $(".tableau-bord-rovers").append(chained);

        game.addRover(rover);
    });

    return game;

}


function displayMap(map) {
    $.each(map, function (y, resteY) {
        let chained = "<tr>";
        $.each(resteY, function (x, caseMap) {
            chained += '<td class="' + caseMap.material + '" data-coor="' + x + '_' + y + '"></td>';
        });
        chained += "</tr>";
        $(".map-display").append(chained);
    });

}


function displayRover(rover) {
    const coordonnees = rover.pos_x + '_' + rover.pos_y;
    $(".posRover" + rover.type).removeClass("posRover-" + rover.type);
    $("td[data-coor='" + coordonnees + "']").addClass("visited-rover-" + rover.type).addClass("posRover-" + rover.type);
}

function displayRovers(game) {
    $.each(game.rovers, function (index, rover) {
        displayRover(rover);
    });
}

function setInstruction(instruction) {
    $("#game-inscructions").html(instruction);
}

function rmInstruction() {
    setInstruction("&nbsp;");
}





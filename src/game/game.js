$(document).ready(function () {

    // Récupération des variables stockés en local :
    const mapConf = jQuery.parseJSON(localStorage.getItem('mapData'));
    const modSelected = jQuery.parseJSON(localStorage.getItem('modSelected'));
    const roversSelected = jQuery.parseJSON(localStorage.getItem('roversSelected'));
    if(mapConf === null) window.location.replace("./index.html");
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
        rmInstruction();
    // setTimeout(function(){
    // }, 2000);
    console.log("racccceeeeee");

    while (game.winner === null) {
        newRoundRover(game);
    }



}

function newRoundRover(game) {
    $.each(game.rovers, function (index, rover) {

        $("#tableau-bord-rover-"+rover.num).addClass("rover-round");
        let data =
            {
                "posX": parseInt(rover._posX),
                "posY": parseInt(rover._posY),
                "typeRover": rover.type,
                "energy": parseInt(rover._energy),
                "destX": parseInt(game.finish[0]),
                "destY": parseInt(game.finish[1]),
                "map": game._map,
                "memory": rover._memory
            };

        data = JSON.stringify(data);
        console.log(data);
        $.ajax({
            url: url_api_rover,
            async: false,
            type: "POST",
            data: data,
            contentType: "application/json",
            dataType: "json",
            success: function (data, status) {
                data = JSON.parse(data);
                console.log(data);
                rover._posX = data.nextX;
                rover._posY = data.nextY;
                rover._memory = data.memory;
                rover._energy = data.energyRest;
                displayRover(rover);
            },
            error : function(data, status, erreur){
                console.log(erreur);
            },
            complete: function(data, status){
                // console.log(erreur);

            }
        });
        $("#tableau-bord-rover-"+rover.num).removeClass("rover-round");
    });
    game.nextRound();
    console.log(game._round);
    return verifWinner(game);

}

function verifWinner(game){

    if(game.mode === "Race"){

        $.each(game.rovers, function(index, rover){
           if(parseInt(rover._posX) === parseInt(game._finish[0]) && parseInt(rover._posY) === parseInt(game._finish[1]) ) {

               // Vainqueur de la course !
               game.winner = rover;
               setInstruction("Le rover "+game.winner.name+" a gagné !!!");
               $("#resetGame").removeClass("hidden");
               $("#resetGame").on("click", function(){
                   resetGame(game);
               });
               return false;

           }
           while(game.winner !== null){

               newRoundRover(game);

           }

        });

        // if(parseInt(game._round) < 3){
        //     newRoundRover(game);
        // }else{
        //     return false;
        // }

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
        if (rover._posX === null || rover._posY === null) {

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
    $("td").off("click");
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
    rover._posX = coordonnees[0];
    rover._posY = coordonnees[1];
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
        rover._energy = energyMax;
        let chained = '<div class="tableau-bord-rover" id="tableau-bord-rover-' + rover.num + '" >';
        chained += '<h2 class="name-rover title">' + rover.name + '</h2>';
        chained += '<div class="coordonnees-rover text-blue-color">Coordonnées : <span id="coordonnees-rover-' + rover.num + '"></span></div>';
        chained += '<div class="ernegy-rover text-blue-color">Energie : <span id="energy-rover-' + rover.num + '">' + rover._energy + '</span></div>';
        chained += '</div>';
        // $(".tableau-bord-rovers").append(chained);
        $(chained).insertBefore("#resetGame");

        game.addRover(rover);
    });

    return game;

}


function displayMap(map) {
    $.each(map, function (y, resteY) {
        let chained = "<tr>";
        $.each(resteY, function (x, caseMap) {
            if(-100 < caseMap.z && caseMap.z < -90){
                chained += '<td class="'+caseMap.material+' z-100_-90" data-coor="'+x+'_'+y+'"></td>';
            } else if(-90 < caseMap.z && caseMap.z < -80){
                chained += '<td class="'+caseMap.material+' z-90_-80" data-coor="'+x+'_'+y+'"></td>';
            } else if(-80 < caseMap.z && caseMap.z < -70){
                chained += '<td class="'+caseMap.material+' z-80_-70" data-coor="'+x+'_'+y+'"></td>';
            } else if(-70 < caseMap.z && caseMap.z < -60){
                chained += '<td class="'+caseMap.material+' z-70_-60" data-coor="'+x+'_'+y+'"></td>';
            } else if(-60 < caseMap.z && caseMap.z < -50){
                chained += '<td class="'+caseMap.material+' z-60_-50" data-coor="'+x+'_'+y+'"></td>';
            } else if(-50 < caseMap.z && caseMap.z < -40){
                chained += '<td class="'+caseMap.material+' z-50_-40" data-coor="'+x+'_'+y+'"></td>';
            } else if(-40 < caseMap.z && caseMap.z < -30){
                chained += '<td class="'+caseMap.material+' z-40_-30" data-coor="'+x+'_'+y+'"></td>';
            } else if(-30 < caseMap.z && caseMap.z < -20){
                chained += '<td class="'+caseMap.material+' z-30_-20" data-coor="'+x+'_'+y+'"></td>';
            } else if(-20 < caseMap.z && caseMap.z < -10){
                chained += '<td class="'+caseMap.material+' z-20_-10" data-coor="'+x+'_'+y+'"></td>';
            } else if(-10 < caseMap.z && caseMap.z < 0){
                chained += '<td class="'+caseMap.material+' z-10_0" data-coor="'+x+'_'+y+'"></td>';
            } else if(0 < caseMap.z && caseMap.z < 10){
                chained += '<td class="'+caseMap.material+' z0_10" data-coor="'+x+'_'+y+'"></td>';
            } else if(10 < caseMap.z && caseMap.z < 20){
                chained += '<td class="'+caseMap.material+' z10_20" data-coor="'+x+'_'+y+'"></td>';
            } else if(20 < caseMap.z && caseMap.z < 30){
                chained += '<td class="'+caseMap.material+' z20_30" data-coor="'+x+'_'+y+'"></td>';
            } else if(30 < caseMap.z && caseMap.z < 40){
                chained += '<td class="'+caseMap.material+' z30_40" data-coor="'+x+'_'+y+'"></td>';
            } else if(40 < caseMap.z && caseMap.z < 50){
                chained += '<td class="'+caseMap.material+' z40_50" data-coor="'+x+'_'+y+'"></td>';
            } else if(50 < caseMap.z && caseMap.z < 60){
                chained += '<td class="'+caseMap.material+' z50_60" data-coor="'+x+'_'+y+'"></td>';
            } else if(60 < caseMap.z && caseMap.z < 70){
                chained += '<td class="'+caseMap.material+' z60_70" data-coor="'+x+'_'+y+'"></td>';
            } else if(70 < caseMap.z && caseMap.z < 80){
                chained += '<td class="'+caseMap.material+' z70_80" data-coor="'+x+'_'+y+'"></td>';
            } else if(80 < caseMap.z && caseMap.z < 90){
                chained += '<td class="'+caseMap.material+' z80_90" data-coor="'+x+'_'+y+'"></td>';
            } else if(90 < caseMap.z && caseMap.z < 100){
                chained += '<td class="'+caseMap.material+' z90_100" data-coor="'+x+'_'+y+'"></td>';
            } else {
                chained += '<td class="'+caseMap.material+'" data-coor="'+x+'_'+y+'"></td>';
            }
        });
        chained += "</tr>";
        $(".map-display").append(chained);
    });

}


function displayRover(rover) {
    const coordonnees = rover._posX + '_' + rover._posY;
    $(".posRover" + rover.type).removeClass("posRover-" + rover.type);
    $("td[data-coor='" + coordonnees + "']").addClass("visited-rover-" + rover.type).addClass("posRover-" + rover.type);
    $("#energy-rover-"+rover.num).html(rover._energy);
    $("#coordonnees-rover-"+rover.num).html('('+rover._posX+','+rover._posY+')');
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

function resetGame(game){

    $.each(game.rovers, function(index, rover){

        rover._hasFlag = false;
        rover._originPosX = null;
        rover._originPosY = null;
        rover._posX = null;
        rover._posY = null;
        rover._destX = null;
        rover._destY = null;
        rover._energy = energyMax;
        rover._memory = {};
        $(".visited-rover-" + rover.type).removeClass("visited-rover-" + rover.type);

    });

    game._round = 0;
    game._winner = null;
    game._finish = null;
    game._flag = null;
    $(".case-finish").removeClass("case-finish");
    $("#resetGame").addClass('hidden');
    displayRovers(game);

    initRovers(game);



}



** Mise en place des 3 API **

** Pré-requis **
    * docker
    * docker-compose
    * composer
    * npm
    
** Configuration globale **

    * Créer un dossier qui contiendra les 3 api et positionnez-vous dedans.
    * Entrer cette commande dans un terminal pour créer un réseau interne à docker afin que les 3 api puissent communiquer :
    `docker network create --internal marsAPI`
    

** API ROVER **

    * Cloner le repo `git clone ssh://git@gitlab.flecuziat.fr/flodu141/mars-rover.git rover`, puis aller dans le dossier
    * Se mettre sur la branche "rover-carte"
    * Lancer la commande `composer update`, (s'il y a une erreur en vidant le cache : `composer require doctrine/doctrine-cache-bundle`)
    * Lancer le container : `docker-compose up -d`
    
** API CARTE **

    * Cloner le repo `git clone ssh://git@gitlab.flecuziat.fr/flodu141/mars-rover.git cartes`, puis aller dans le dossier
    * Se mettre sur la branche "cartes_ryan"
    * Lancer la commande `composer update`, (s'il y a une erreur en vidant le cache : `composer require doctrine/doctrine-cache-bundle`)
    * Lancer le container : `docker-compose up -d`
    
** LE FRONT **

    * Cloner le repo `git clone ssh://git@gitlab.flecuziat.fr/flodu141/mars-rover.git front`, puis aller dans le dossier
    * Se mettre sur la branche "front-controller"
    * Lancer la commande dans un terminal : `npm install`
    * Lancer le container : `docker-compose up -d`
    
** TESTER **

    * Installer une extension sur votre navigateur qui désactive le cors :
        * Firefox : https://addons.mozilla.org/fr/firefox/addon/access-control-allow-origin/
        * Chrome : https://chrome.google.com/webstore/detail/allow-cors-access-control/lhobafahddgcelffkeicbaginigeejlf
        
        * Après l'installation, un petit icon apparaitra en haut à droite de votre navigateur. Cliquez dessus pour autoriser les requête multi-origin
    
        
    * Aller sur la page http://localhost:3000/index.html
    * Amusez-vous !!
    
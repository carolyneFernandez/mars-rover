#!/bin/bash

read -p "Make sure git, composer, npm and docker/docker-compose are installed and added to your PATH. (press any key to continue) : " res

echo ""

while true; do
    read -p "Clone git project via SSH or HTTPS ? (SSH/HTTPS) : " res
    case $res in
        SSH) 
            echo "Cloning via SSH : ";
            git clone git@gitlab.flecuziat.fr:flodu141/mars-rover.git rover;
            git clone git@gitlab.flecuziat.fr:flodu141/mars-rover.git carte;
            git clone git@gitlab.flecuziat.fr:flodu141/mars-rover.git front;
            break;;
        HTTPS) 
            echo "Cloning via HTTPS : ";
            git clone https://gitlab.flecuziat.fr/flodu141/mars-rover.git rover;
            git clone https://gitlab.flecuziat.fr/flodu141/mars-rover.git carte;
            git clone https://gitlab.flecuziat.fr/flodu141/mars-rover.git front;
            break;;
        ssh) 
            echo "Cloning via SSH : ";
            git clone git@gitlab.flecuziat.fr:flodu141/mars-rover.git rover;
            git clone git@gitlab.flecuziat.fr:flodu141/mars-rover.git carte;
            git clone git@gitlab.flecuziat.fr:flodu141/mars-rover.git front;
            break;;
        https) 
            echo "Cloning via HTTPS : ";
            git clone https://gitlab.flecuziat.fr/flodu141/mars-rover.git rover;
            git clone https://gitlab.flecuziat.fr/flodu141/mars-rover.git carte;
            git clone https://gitlab.flecuziat.fr/flodu141/mars-rover.git front;
            break;;
        *) 
            echo "Please answer SSH or HTTPS.";;
    esac
done

(cd rover; git checkout rover-carte && composer install)
(cd carte; git checkout cartes_ryan && composer install)
(cd front; git checkout front-controller && npm install)

docker-compose up -d

echo ""
echo ""
echo ""
echo ""
echo "-> project is up to date"
echo "-> docker containers are running"
echo "-> server is listening on http://localhost:3000"
read -p "press any key to continue : " input
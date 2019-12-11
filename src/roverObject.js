class roverObject {
    name = '';
    type = '';
    _map = '';
    _posX = null;
    _posY = null;
    _destX = null;
    _destY = null;
    _energy = 500;
    _memory = [];


    constructor(name, type){
        this.name = name;
        this.type = type;
    }

    get map() {
        return this._map;
    }

    set map(value) {
        this._map = value;
    }

    get posX() {
        return this._posX;
    }

    set posX(value) {
        this._posX = value;
    }

    get posY() {
        return this._posY;
    }

    set posY(value) {
        this._posY = value;
    }

    get destX() {
        return this._destX;
    }

    set destX(value) {
        this._destX = value;
    }

    get destY() {
        return this._destY;
    }

    set destY(value) {
        this._destY = value;
    }

    get energy() {
        return this._energy;
    }

    set energy(value) {
        this._energy = value;
    }

    get memory() {
        return this._memory;
    }

    set memory(value) {
        this._memory = value;
    }
}
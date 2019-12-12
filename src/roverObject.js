class roverObject {
    name = '';
    type = '';
    _map = '';
    _originPosX = null;
    _originPosY = null;
    _posX = null;
    _posY = null;
    _destX = null;
    _destY = null;
    _energy = null;
    _memory = {};
    _numRover = 0;
    _hasFlag = false;
    _arrived = false;
    _image = "";


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

    get numRover() {
        return this._numRover;
    }

    set numRover(value) {
        this._numRover = value;
    }

    get originPosX() {
        return this._originPosX;
    }

    set originPosX(value) {
        this._originPosX = value;
    }

    get originPosY() {
        return this._originPosY;
    }

    set originPosY(value) {
        this._originPosY = value;
    }

    get hasFlag() {
        return this._hasFlag;
    }

    set hasFlag(value) {
        this._hasFlag = value;
    }
}
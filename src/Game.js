class Game{

    _mode = 'race';
    _map = [];
    _rovers = [];
    _finish = null; // sera un tableau [x, y]
    _flag = null; // sera un tableau [x, y]
    _winner = null; // sera un objet Rover
    _round = 0;


    get mode() {
        return this._mode;
    }

    set mode(value) {
        this._mode = value;
    }

    get map() {
        return this._map;
    }

    set map(value) {
        this._map = value;
    }

    get rovers() {
        return this._rovers;
    }

    set rovers(value) {
        this._rovers = value;
    }

    addRover(rover) {
        this._rovers.push(rover);
    }

    get finish() {
        return this._finish;
    }

    set finish(value) {
        this._finish = value;
    }

    get flag() {
        return this._flag;
    }

    set flag(value) {
        this._flag = value;
    }

    get winner() {
        return this._winner;
    }

    set winner(value) {
        this._winner = value;
    }

    get round() {
        return this._round;
    }

    set round(value) {
        this._round = value;
    }

    nextRound(){
        this._round++;
    }
}
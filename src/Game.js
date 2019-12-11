class Game{

    _mode = 'race';
    _map = [];
    _rovers = [];
    finish = null; // sera un tableau [x, y]
    flag = null; // sera un tableau [x, y]

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

}
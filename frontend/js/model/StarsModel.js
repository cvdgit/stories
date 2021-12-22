class StarsModel {

    constructor(data) {
        this.data = data;
    }

    getTotal() {
        return parseInt(this.data.total);
    }

    getCurrent() {
        return parseInt(this.data.current);
    }

    setCurrent(value) {
        this.data.current = parseInt(value);
    }

    incCurrent() {
        this.data.current++;
    }

    decCurrent() {
        this.data.current--;
    }
}

export default StarsModel;
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
        if (!Number.isInteger(value)) {
            throw 'StarsModel.setCurrent value is not integer';
        }
        this.data.current = value;
    }

    incCurrent() {
        this.data.current++;
    }

    decCurrent() {
        this.data.current--;
    }
}

export default StarsModel;
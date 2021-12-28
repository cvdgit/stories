class ProgressModel {

    constructor(data) {
        this.data = data;
    }

    getTotal() {
        return parseInt(this.data.total);
    }

    getCurrent() {
        return parseInt(this.data.current);
    }

    incCurrent() {
        let current = this.getCurrent();
        current++;
        this.setCurrent(current);
    }

    decCurrent() {
        let current = this.getCurrent();
        current--;
        this.setCurrent(current);
    }

    setCurrent(value) {
        if (!Number.isInteger(value)) {
            throw 'ProgressModel.setCurrent value is not integer';
        }
        this.data.current = value;
    }
}

export default ProgressModel;
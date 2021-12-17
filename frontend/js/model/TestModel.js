class TestModel {

    constructor(data) {
        this.data = data;
    }

    getId() {
        return parseInt(this.data.id);
    }
}

export default TestModel;
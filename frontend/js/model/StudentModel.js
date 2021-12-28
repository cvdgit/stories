class StudentModel {

    constructor(data) {
        this.data = data;
    }

    getId() {
        return parseInt(this.data.id);
    }

    getName() {
        return this.data.name;
    }

    getProgress() {
        return parseInt(this.data.progress);
    }

    isDone() {
        return this.getProgress() === 100;
    }
}

export default StudentModel;
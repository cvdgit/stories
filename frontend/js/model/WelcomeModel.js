import StudentModel from "./StudentModel";

class WelcomeModel {

    constructor(data) {
        this.data = data;
        this.students = data.students.map(student => new StudentModel(student));
    }

    getStudents() {
        return this.students;
    }

    getTestHeader() {
        return this.data.test.header;
    }

    getTestDescription() {
        return this.data.test.description;
    }
}

export default WelcomeModel;
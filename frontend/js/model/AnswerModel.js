class AnswerModel {

    constructor(data) {
        this.data = data;
    }

    getId() {
        return parseInt(this.data.id);
    }

    getName() {
        return this.data.name;
    }

    isCorrect() {
        return parseInt(this.data.is_correct) === 1;
    }

    getImage() {
        return this.data.image;
    }

    haveImage() {
        const value = this.getImage();
        return (typeof value !== 'undefined' && value);
    }

    getOrigImage() {
        return this.data.orig_image;
    }
}

export default AnswerModel;
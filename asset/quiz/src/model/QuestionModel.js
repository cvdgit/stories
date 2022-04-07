import AnswerModel from "./AnswerModel";
import StarsModel from "./StarsModel";

class QuestionModel {

    constructor(data, answersPropName) {
        this.data = data;
        this.answers = data[answersPropName].map(answer => new AnswerModel(answer));
        this.stars = new StarsModel(this.data.stars);
    }

    getId() {
        return parseInt(this.data.id);
    }

    getName() {
        return this.data.name;
    }

    getType() {
        return parseInt(this.data.type);
    }

    getAnswers() {
        return this.answers;
    }

    getAnswerById(id) {
        return this.getAnswers().find(element => element.getId() === id);
    }

    getCorrectAnswers() {
        return this.answers.filter((answer) => {
            return answer.isCorrect();
        });
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

    isMixAnswers() {
        return parseInt(this.data.mix_answers) === 1;
    }

    getStars() {
        return this.stars;
    }

    lastAnswerIsCorrect() {
        return Boolean(this.data['lastAnswerIsCorrect']);
    }

    setLastAnswersIsCorrect(value) {
        this.data['lastAnswerIsCorrect'] = Boolean(value);
    }

    getCorrectNumber() {
        return parseInt(this.data.correct_number);
    }
}

export default QuestionModel;
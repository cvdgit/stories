import WrongAnswer from "./WrongAnswer";

class DefaultWrongAnswer extends WrongAnswer {

    constructor(question) {
        super();
        this.question = question;
    }

    render() {
        const baseElements = super.render();

        this.question.getCorrectAnswers().forEach((answer) => {

            const rowElement = document.createElement('div');
            rowElement.classList.add('row');
            rowElement.innerHTML =
                `<div class="col-xs-12 col-md-offset-3 col-md-9">
                     <p>${answer.getName()}</p>
                 </div>`;

            baseElements.querySelector('.wikids-test-correct-answer-answers')
                .appendChild(rowElement);
        });

        return baseElements;
    }
}

export default DefaultWrongAnswer;
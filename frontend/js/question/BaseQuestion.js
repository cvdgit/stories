class BaseQuestion {

    constructor(model) {
        this.model = model;
        this.userAnswers = new Set();
    }

    createAnswer(answer) {

        const inputElement = document.createElement('input');
        let inputId = 'answer' + answer.getId();
        const singleValue = false;
        inputElement.setAttribute('type', 'checkbox');
        inputElement.setAttribute('id', inputId);
        inputElement.setAttribute('name', 'qwe');
        inputElement.setAttribute('value', answer.getId());

        const answerElement = document.createElement('div');
        answerElement.classList.add('wikids-test-answer');
        answerElement.addEventListener('click', (e) => {
            let tagName = e.target.tagName;
            let tags = ['INPUT'];
            //if (originalImageExists) {
            //    tags.push('IMG');
            //}
            let input = e.target.querySelector('input');
            if (!tags.includes(tagName)) {
                input.checked = !input.checked;
            }

            if (singleValue) {
                this.userAnswers.clear();
                this.userAnswers.add(input.value);
            }
            else {
                if (input.checked) {
                    this.userAnswers.add(input.value);
                }
                else {
                    this.userAnswers.delete(input.value);
                }
            }
        }, false);
        answerElement.appendChild(inputElement);

        const labelElement = document.createElement('label');
        labelElement.setAttribute('for', inputId);
        labelElement.textContent = answer.getName();
        answerElement.appendChild(labelElement);

        return answerElement;
    }

    renderAnswers(answers) {

        const mainElement = document.createElement('div');
        mainElement.innerHTML =
            `<div class="row row-no-gutters">
                 <div class="col-md-4 question-image"></div>
                 <div class="col-md-8 question-wrapper">
                     <div class="wikids-test-answers"></div>
                 </div>
             </div>`;

        const answersElement = mainElement.querySelector('.wikids-test-answers');
        answers.forEach((answer) => {
            answersElement.appendChild(this.createAnswer(answer));
        });

        return mainElement;
    }

    render() {

        const titleElement = document.createElement('p');
        titleElement.classList.add('question-title');
        titleElement.textContent = this.model.getName();

        const questionElement = document.createElement('div');
        questionElement.classList.add('wikids-test-question');
        questionElement.setAttribute('data-question-id', this.model.getId());
        questionElement.appendChild(titleElement);

        questionElement.appendChild(this.renderAnswers(this.model.getAnswers()));

        return questionElement;
    }

    getUserAnswers() {
        return Array.from(this.userAnswers);
    }
}

export default BaseQuestion;
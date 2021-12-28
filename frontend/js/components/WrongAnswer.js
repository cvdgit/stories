const EventEmitter = require('events');

class WrongAnswer extends EventEmitter {

    render() {

        const pageElement = document.createElement('div');
        pageElement.classList.add('wikids-test-correct-answer-page');

        let titleElement = 'Правильный ответ';
        pageElement.innerHTML =
            `<div class="wikids-test-correct-answer-answers">
                 <h2>${titleElement}</h2>
             </div>
             <div class="wikids-test-correct-answer-page-action"></div>`;

        const nextButtonElement = document.createElement('button');
        nextButtonElement.classList.add('btn');
        nextButtonElement.classList.add('correct-answer-page-next')
        nextButtonElement.textContent = 'Продолжить';
        nextButtonElement.addEventListener('click', () => {
            this.emit('wrongAnswerNext');
        });

        pageElement.querySelector('.wikids-test-correct-answer-page-action')
            .appendChild(nextButtonElement);

        return pageElement;
    }
}

export default WrongAnswer;
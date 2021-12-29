export default class WelcomeGuestPage {

    render(testBeginCallback) {

        const element = document.createElement('div');
        element.classList.add('wikids-test-begin-page');

        const buttonElement = document.createElement('button');
        buttonElement.classList.add('btn');
        buttonElement.classList.add('wikids-test-begin');
        buttonElement.textContent = 'Ответить на вопросы';
        buttonElement.addEventListener('click', (e) => {
            testBeginCallback();
        }, false);

        element.appendChild(buttonElement);

        return element;
    }
}
class ErrorPage {

    render() {

        const element = document.createElement('div');
        element.classList.add('wikids-test-error-page')
        element.innerHTML = `<h3>При загрузке теста произошла ошибка</h3>`;

        return element;
    }
}

export default ErrorPage;
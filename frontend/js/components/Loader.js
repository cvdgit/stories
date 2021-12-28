class Loader {

    constructor(text) {
        this.text = text || 'Загрузка вопросов...';
    }

    render() {

        const elem = document.createElement('div');
        elem.classList.add('wikids-test-loader');
        elem.innerHTML =
            `<p>${this.text}<br/><img src="/img/loading.gif" alt="Loading..." /></p>`;

        return elem;
    }
}

export default Loader;
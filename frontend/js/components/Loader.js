class Loader {

    constructor(text) {
        this.text = text || 'Загрузка вопросов';
    }

    render() {
        let elem = document.createElement('div');
        elem.classList.add('wikids-test-loader');
        let textElem = document.createElement('p');
        textElem.textContent = this.text;
        elem.appendChild(textElem);
        let imgElem = document.createElement('img');
        imgElem.setAttribute('src', '/img/loading.gif');
        elem.appendChild(imgElem);
        return elem;
    }
}

export default Loader;
class Testing {

    /**
     *
     * @param element
     * @param data
     * @param options
     */
    constructor(element, data, options) {

        if (!(element && element.nodeType && element.nodeType === 1)) {
            throw "Element must be an HTMLElement, not ".concat({}.toString.call(element));
        }

        this.element = element;
        this.options = options = Object.assign({}, options);

        element['_wikids_test'] = this;

        this.testConfig = data.getTestModel();
        this.dom = {};
    }

    initialize() {
        let wrapper = document.createElement('div');
        wrapper.classList.add('wikids-test');
        this.dom.wrapper = wrapper;
    }

    setQuestions(questions) {
        // new Question(question)
    }

    start() {

        this.setupDOM();
    }

    setupDOM() {

    }
}

export default Testing;
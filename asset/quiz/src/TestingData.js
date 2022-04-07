import TestModel from './model/TestModel'

class TestingData {

    /**
     *
     * @param data
     * @param options
     * @param options.testPropName
     * @param options.questionsPropName
     * @param options.answersPropName
     */
    constructor(data, options) {
        this.testModel = new TestModel(data[options.testPropName], data[options.questionsPropName], options.answersPropName);
    }

    getTestModel() {
        return this.testModel;
    }
}

export default TestingData;
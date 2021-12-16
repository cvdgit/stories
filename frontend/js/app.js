import TestingData from "./TestingData";
import Testing from './Testing';

const data = require('./testing-data.json');
const testingData = new TestingData(data[0], {
    testPropName: 'test',
    questionsPropName: 'storyTestQuestions',
    answersPropName: 'storyTestAnswers'
});

const testing = new Testing(document.getElementById('mobile-testing'), testingData, {});

import "../scss/style.scss";
import Testing from './Testing';
import TestModel from "./model/TestModel";
import QuestionsData from "./model/QuestionsData";

const testing = new Testing(document.getElementById('mobile-testing'), {});

const data = require('./testing-data.json');
const testConfig = new TestModel(data[0]['test']);
const questionsData = new QuestionsData(data[0]['storyTestQuestions'], 'storyTestAnswers');
testing.initialize(testConfig, questionsData);
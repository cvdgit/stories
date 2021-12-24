import "../scss/style.scss";
import Testing from './Testing';
import TestModel from "./model/TestModel";
import QuestionsData from "./model/QuestionsData";
import WelcomeModel from "./model/WelcomeModel";

const element = document.getElementById('mobile-testing');
const testId = element.getAttribute('data-test-id');
const testing = new Testing(element, {
    welcome: (welcomeCallback, errorCallback) => {
        fetch('/test-mobile/init?test_id=' + testId)
            .then(response => response.json())
            .then(response => welcomeCallback(new WelcomeModel(response)))
            .catch(error => errorCallback(error));
    },
    initialize: (initCallback, errorCallback) => {
        fetch('/test-mobile/get-data?test_id=' + testId)
            .then(response => response.json())
            .then(data => {
                const testConfig = new TestModel(data[0]['test']);
                const questionsData = new QuestionsData(data[0]['storyTestQuestions'], 'storyTestAnswers');
                initCallback(testConfig, questionsData);
            })
            .catch(error => errorCallback(error));
    }
});

//const data = require('./testing-data.json');

/*
let response = await fetch('/test-mobile/get-data?test_id=' + testId);
let data = await response.json();
const testConfig = new TestModel(data[0]['test']);
const questionsData = new QuestionsData(data[0]['storyTestQuestions'], 'storyTestAnswers');
testing.initialize(testConfig, questionsData);*/

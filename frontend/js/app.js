import "../scss/style.scss";
import Testing from './Testing';
import TestModel from "./model/TestModel";
import QuestionsData from "./model/QuestionsData";
import WelcomeModel from "./model/WelcomeModel";

const elements = document.querySelectorAll("[data-toggle='mobile-testing']");
elements.forEach((element) => {

    const testId = element.getAttribute('data-test-id');
    const studentId = element.getAttribute('data-student-id');
    const isGuestMode = element.getAttribute('data-guest-mode') === '1';

    const options = {
        initialize: (initCallback, errorCallback, studentId) => {
            let url = '/test-mobile/get-data?test_id=' + testId;
            if (studentId) {
                url = url + '&student_id=' + studentId;
            }
            if (isGuestMode) {
                url = url + '&fast_mode=true';
            }
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const testConfig = new TestModel(data[0]['test']);
                    const questionsData = new QuestionsData(data[0]['storyTestQuestions'], 'storyTestAnswers');
                    initCallback(testConfig, questionsData);
                })
                .catch(error => errorCallback(error));
        }
    };

    if (isGuestMode) {
        options['welcomeGuest'] = true;
    }
    else {

        options['welcome'] = (welcomeCallback, errorCallback) => {
            fetch('/test-mobile/init?test_id=' + testId)
                .then(response => response.json())
                .then(response => welcomeCallback(new WelcomeModel(response), studentId))
                .catch(error => errorCallback(error));
        };

        /**
         * @param {HistoryModel} history
         */
        options['history'] = (history) => {
            fetch('/question/answer', {
                method: 'post',
                body: history.asQueryString(),
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
                .then(response => response.json())
                .then(data => console.log(data));
        };
    }

    const testing = new Testing(element, options);
});
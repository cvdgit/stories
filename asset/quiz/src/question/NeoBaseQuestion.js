import BaseQuestion from "./BaseQuestion";
import {shuffle} from "../utils";

export default class NeoBaseQuestion extends BaseQuestion {

    generateAnswerList(answers, num) {

        if (answers.length <= num) {
            return shuffle(answers);
        }

        const list = this.getCorrectAnswers();

        function sample(population, k){
            if (!Array.isArray(population)) {
                throw new TypeError("Population must be an array.");
            }
            var n = population.length;
            if (k < 0 || k > n) {
                throw new RangeError("Sample larger than population or is negative");
            }
            var result = new Array(k);
            var setsize = 21;   // size of a small set minus size of an empty list
            if (k > 5) {
                setsize += Math.pow(4, Math.ceil(Math.log(k * 3) / Math.log(4)))
            }
            if (n <= setsize) {
                // An n-length list is smaller than a k-length set
                var pool = population.slice();
                for (var i = 0; i < k; i++) {          // invariant:  non-selected at [0,n-i)
                    var j = Math.random() * (n - i) | 0;
                    result[i] = pool[j];
                    pool[j] = pool[n - i - 1];       // move non-selected item into vacancy
                }
            } else {
                var selected = new Set();
                for (var i = 0; i < k; i++) {
                    var j = Math.random() * n | 0;
                    while (selected.has(j)) {
                        j = Math.random() * n | 0;
                    }
                    selected.add(j);
                    result[i] = population[j];
                }
            }
            return result;
        }

        var max = num - list.length;
        sample(answers.filter(function(answer) {
            return !answer.isCorrect();
        }), max).map(function(elem) {
            list.push(elem);
        });

        return shuffle(list);
    }

    renderAnswers(answers) {

        const answersElement = document.createElement('div');
        answersElement.classList.add('.wikids-test-answers');

        const num = this.model.getAnswerNumber();
        if (num > 0) {
            answers = this.generateAnswerList(answers, num);
        }

        answers.forEach((answer) => {
            answersElement.appendChild(this.createAnswer(answer));
        });

        return answersElement;
    }
}
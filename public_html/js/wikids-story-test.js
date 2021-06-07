(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
        typeof define === 'function' && define.amd ? define(factory) :
            (global = global || self, global.WikidsStoryTest = factory());
}(this, function () { 'use strict';

    function extend(a, b) {
        for (var i in b) {
            a[i] = b[i];
        }
        return a;
    }

    function shuffle(array) {
        var counter = array.length;
        // While there are elements in the array
        while (counter > 0) {
            // Pick a random index
            var index = Math.floor(Math.random() * counter);
            // Decrease counter by 1
            counter--;
            // And swap the last element with it
            var temp = array[counter];
            array[counter] = array[index];
            array[index] = temp;
        }
        return array;
    }

    function combineArraysRecursively(array_of_arrays) {
        if (!array_of_arrays) {
            return [];
        }
        if (!Array.isArray(array_of_arrays)) {
            return [];
        }
        if (array_of_arrays.length == 0) {
            return [];
        }
        for (let i = 0; i < array_of_arrays.length; i++) {
            if (!Array.isArray(array_of_arrays[i]) || array_of_arrays[i].length == 0) {
                return [];
            }
        }
        let outputs = [];
        function permute(arrayOfArrays, whichArray=0, output="") {
            arrayOfArrays[whichArray].forEach((array_element) => {
                if (whichArray == array_of_arrays.length - 1) {
                    outputs.push([output, array_element]);
                }
                else{
                    permute(arrayOfArrays, whichArray + 1, output + array_element);
                }
            });
        }
        permute(array_of_arrays);
        return outputs;
    }

    function textDiff(a, b) {
        var diff = patienceDiff(a.split(''), b.split(''));
        var diffAnswer = '';
        diff.lines.forEach(function(line) {
            var char = '',
                color = 'red';
            if (line.aIndex >= 0) {
                char = line.line;
                if (line.bIndex === -1) {
                    color = 'red';
                }
                if (line.aIndex === line.bIndex) {
                    color = 'green';
                }
            }
            if (char.length) {
                diffAnswer += '<span style="color: ' + color + '">' + char + '</span>';
            }
        });
        return diffAnswer;
    }

    var QuestionSuccess = function() {

        function create(action, title, image) {
            var $action = $('<button/>')
                .addClass('btn')
                .text('Продолжить')
                .on('click', action);
            return $('<div/>')
                .addClass('wikids-test-success-question-page')
                .hide()
                .append(
                    $('<div/>').addClass('wikids-test-success-question-page-content')
                        .append('<i class="glyphicon glyphicon-star"></i>')
                        .append('<i class="glyphicon glyphicon-star"></i>')
                        .append('<i class="glyphicon glyphicon-star"></i>')
                        .append('<i class="glyphicon glyphicon-star"></i>')
                        .append('<i class="glyphicon glyphicon-star"></i>')
                        .append(
                            $('<h4/>').text('Вы заработали 5 звезд!')
                        )
                        .append($('<p/>').text(title))
                        .append($('<img/>').attr('src', image))
                )
                .append($('<div/>').addClass('wikids-test-success-question-page-action').append($action));
        }

        return {
            'create': create
        }
    }

    var AnswerTypeNumPad = function() {

    };

    AnswerTypeNumPad.prototype.create = function(callback) {

        var html = '<div class="keyboard-wrapper"><ul id="keyboard" class="clearfix">' +
            '<li class="letter">0</li>' +
            '<li class="letter-empty"></li>' +
            '<li class="letter">1</li>' +
            '<li class="letter">2</li>' +
            '<li class="letter">3</li>' +
            '<li class="letter">4</li>' +
            '<li class="letter">5</li>' +
            '<li class="letter">6</li>' +
            '<li class="letter">7</li>' +
            '<li class="letter">8</li>' +
            '<li class="letter">9</li>' +
            '<li class="letter">10</li>' +
            '<li class="letter-empty clearl"></li>' +
            '<li class="letter-empty"></li>' +
            '<li class="letter">11</li>' +
            '<li class="letter">12</li>' +
            '<li class="letter">13</li>' +
            '<li class="letter">14</li>' +
            '<li class="letter">15</li>' +
            '<li class="letter">16</li>' +
            '<li class="letter">17</li>' +
            '<li class="letter">18</li>' +
            '<li class="letter">19</li>' +
            '<li class="letter">20</li>' +
            '</ul>' +
            '<p></p></div>',
            $html = $(html);

        $html.find('li.letter').on('click', function() {
            $(this).parent().parent().find('p').text($(this).text());
            callback($(this).text());
        });

        return $html;
    }

    AnswerTypeNumPad.prototype.reset = function(element) {
        element.find('#keyboard + p').text('');
    }

    var answerTypeInput = {};
    answerTypeInput.create = function(action) {
        //var $html = $('<input type="text" class="answer-input" style="width: 80%" />');
        var $html = $('<textarea class="answer-input" style="width: 80%" rows="5" />');
        $html.keypress(function(e) {
            if (e.which == 13) {
                action();
                return false;
            }
        });
        return $html;
    };

    /*var SlideLoader = (function() {

        var $element = $('<div/>')
            .addClass('wikids-test-loader')
            .append($('<p/>').text('Загрузка вопросов'))
            .append($('<img/>').attr('src', '/img/loading.gif'));

        function show() {
            WikidsStoryTest.getCurrentQuestionElement().append($element);
        }

        function hide() {

        }

        return {
            'show': show,
            'hide': hide
        };
    })();*/

    var TestLinked = function(data) {

        var stories = [];

        function init() {
            if (!data || !data.length) {
                return;
            }
            stories = data;
        }

        init();

        function getHtml() {
            if (!stories.length) {
                return '';
            }
            var $wrapper = $('<div/>')
                .addClass('test-linked-stories-wrapper');
            $wrapper.append($('<p/>').text('Посмотрите историю'))
            stories.forEach(function(story) {
                $('<a/>')
                    .attr('href', story['url'])
                    .css('display', 'block')
                    .append(
                        $('<img/>').attr('src', story['image'])
                    )
                    .append($('<p/>').text(story['title']))
                    .appendTo($wrapper);
            });
            return $wrapper;
        }

        var API = {};
        API.getHtml = getHtml;
        return API;
    }

    function RegionQuestion(test) {

        this.test = test;

        var answers = [];
        this.addAnswer = function(answer) {
            if (answers.indexOf(answer) === -1) {
                answers.push(answer);
            }
        };
        this.getAnswers = function() {
            return answers;
        };
        this.resetAnswers = function() {
            answers = [];
        }

        this.getAnswerByRegion = function(questionAnswers, region) {
            return questionAnswers.filter(function(answer) {
                return answer.region_id === region;
            });
        };
    }

    RegionQuestion.prototype.create = function(question, questionAnswers) {

        var params = question.params;

        var $img = $('<img/>')
            .attr('src', params.image)
            .css({'position': 'absolute', 'left': 0, 'top': 0, 'width': '100%', 'height': '100%'});

        function getScale() {
            var scale = 1;
            if (window['Reveal']) {
                scale = Reveal.getScale();
            }
            return scale;
        }

        function getRelativeCoordinates(event, target) {
            var position = {
                x: event.clientX,
                y: event.clientY
            };
            var container = $('.reveal .slides')[0]
            var scaleX = parseFloat(target.offsetWidth  / target.getBoundingClientRect().width).toFixed(2);
            var scaleY = parseFloat(target.offsetHeight  / target.getBoundingClientRect().height).toFixed(2);
            var offset = $(target).offset();
            var canvasOffsetLeft = offset.left;
            var canvasOffsetTop = offset.top;
            return {
                x: (position.x - canvasOffsetLeft + $(window).scrollLeft()) / getScale(),
                y: (position.y - canvasOffsetTop + $(window).scrollTop()) / getScale()
            };
        }

        var that = this;
        var $wrapper = $('<div/>')
            .addClass('question-region')
            .css({'width': params.imageWidth + 'px', 'height': params.imageHeight + 'px', 'position': 'relative', 'margin': '0 auto'})
            .on('click', function(e) {
                var rect = getRelativeCoordinates(e, $wrapper[0]);
                $('<span/>')
                    .addClass('answer-point')
                    .css({
                        'position': 'absolute',
                        'left': rect.x,
                        'top': rect.y,
                        'shape-outside': 'circle()',
                        'clip-path': 'circle()',
                        'background': 'orangered',
                        'width': '3rem',
                        'height': '3rem'
                    })
                    .appendTo(this);

                var $target  = $(e.target);
                var isRect = $target[0].tagName === 'DIV' && $target.hasClass('answer-rect');

                setTimeout(function() {
                    if (isRect) {
                        var regionID = $target.attr('data-answer-id');
                        var answer = that.getAnswerByRegion(questionAnswers, regionID);
                        that.addAnswer(answer[0].id);
                    }
                    else {
                        that.addAnswer('no correct');
                    }

                    if (that.getAnswers().length === parseInt(question.correct_number)) {
                        that.test.nextQuestion(that.getAnswers());
                        that.resetAnswers();
                        $wrapper.find('span.answer-point').remove();
                    }
                }, 500);
            })
            .append($img);

        question.params.regions.forEach(function(region) {
            $('<div/>')
                .addClass('answer-rect')
                .attr('data-answer-id', region.id)
                .css({
                    'position': 'absolute',
                    'left': parseInt(region.rect.left) + 'px',
                    'top': parseInt(region.rect.top) + 'px',
                    'width': parseInt(region.rect.width) + 'px',
                    'height': parseInt(region.rect.height) + 'px'
                })
                .appendTo($wrapper);
        });
        return $wrapper;
    };

    RegionQuestion.prototype.createSuccess = function(question) {
        var params = question.params;
        var $img = $('<img/>')
            .attr('src', params.image)
            .css({'position': 'absolute', 'left': 0, 'top': 0, 'width': '100%', 'height': '100%'});
        var $wrapper = $('<div/>')
            .addClass('question-region')
            .css({'width': params.imageWidth + 'px', 'height': params.imageHeight + 'px', 'position': 'relative', 'margin': '0 auto'})
            .append($img);
        params.regions.forEach(function(region) {
            if (region.correct) {
                $('<div/>')
                    .addClass('answer-rect')
                    .css({
                        'position': 'absolute',
                        'left': parseInt(region.rect.left) + 'px',
                        'top': parseInt(region.rect.top) + 'px',
                        'width': parseInt(region.rect.width) + 'px',
                        'height': parseInt(region.rect.height) + 'px',
                        'backgroundColor': 'rgba(153, 205, 80, 0.3)',
                        'border': '3px #808080 solid'
                    })
                    .appendTo($wrapper);
            }
        });
        return $wrapper;
    };

    _extends(RegionQuestion, {
        pluginName: 'regionQuestion'
    });

    var TestConfig = function(data) {

        function getSource() {
            return parseInt(data.source);
        }

        function getAnswerType() {
            return parseInt(data.answerType);
        }

        return {
            'getSource': getSource,
            'sourceIsLocal': function() {
                return getSource() === 1;
            },
            'sourceIsNeo': function() {
                return getSource() === 2;
            },
            'sourceIsWord': function() {
                return getSource() === 3;
            },
            'answerTypeIsDefault': function() {
                return getAnswerType() === 0;
            },
            'answerTypeIsNumPad': function() {
                return getAnswerType() === 1;
            },
            'answerTypeIsInput': function() {
                return getAnswerType() === 2;
            },
            'answerTypeIsRecording': function() {
                return getAnswerType() === 3;
            },
            'answerTypeIsMissingWords': function() {
                return getAnswerType() === 4;
            },
            'isStrictAnswer': function() {
                return parseInt(data.strictAnswer);
            },
            'getInputVoice': function() {
                return data.inputVoice;
            },
            'getRecordingLang': function() {
                return data.recordingLang;
            },
            'isRememberAnswers': function() {
                return data.rememberAnswers;
            },
            'getTestID': function() {
                return parseInt(data.id);
            },
            'isAskQuestion': function() {
                return data.askQuestion;
            },
            'getAskQuestionLang': function() {
                return data.askQuestionLang;
            }
        }
    }

    var Morphy = function() {
        var API = {};
        API.correctResult = function(match, result) {
            return $.post('/morphy/root', {
                match, result
            });
        }
        return API;
    };

    function MissingWords(test) {

        var control;
        this.control = control = new RecognitionControl(test);
        this.recognition = null;
        this.test = test;

        this.init = function(question, answer) {
            var element = $('<div/>', {
                'class': 'missing-words test-recognition'
            });
            var that = this;
            element.data('correctAnswer', answer.name);
            element
                .append($('<p/>', {
                    'class': 'missing-words-text',
                    'html': createMaskedString(question.name)
                }));
            element.on('click', 'span.label', function (e) {
                that.start(e, question.id, $(this).attr('data-match'));
            });
            element
                .append($('<div/>')
                    .addClass('recognition-result-wrapper')
                    .append($('<span/>').addClass('recognition-result').css('background-color', 'inherit'))
                    .append($('<span/>').addClass('recognition-result-interim'))
                );
            $('<div/>')
                .addClass('wikids-test-loader')
                .append($('<img/>')
                    .attr('src', '/img/loading.gif')
                    .attr('width', '60px')
                )
                .hide()
                .appendTo(element);
            element
                .append($('<p/>').addClass('recognition-status'));
            $('<a/>')
                .attr('href', '#')
                .attr('title', 'Остановить')
                .addClass('recognition-stop')
                .on('click', function(e) {
                    e.preventDefault();
                    that.recognition.stop();
                })
                .append($('<i/>').addClass('glyphicon glyphicon-stop'))
                .hide()
                .appendTo(element);
            return element;
        };

        this.addRecognitionListeners = function() {
            if (this.recognition === null) {
                return;
            }
            var that = this;
            this.recognition.addEventListener('onStart', function() {
                test.hideNextButton();
                control.setStatus('Идет запись с микрофона');
            });
            this.recognition.addEventListener('onResult', function(event) {
                var args = event.args;
                var elem = $(args.target);
                var match = elem.attr('data-match')
                var result = $.trim(args.result);
                elem.text(result);
                if (result.length >= match.length) {
                    that.recognition.stop();
                }
            });
            this.recognition.addEventListener('onEnd', function(event) {
                control.hideLoader();
                control.hideStopButton();
                control.setStatus();
                var args = event.args,
                    elem = $(args.target),
                    match = elem.attr('data-match');
                var result = control.getMissingWordsText();
                if (checkResult(result)) {
                    that.resetMatchElements();
                    test.nextQuestion([result]);
                }
                else {
                    correctResult(match, args.result).done(function(response) {
                        elem.text(response.result);
                        result = control.getMissingWordsText();
                        if (checkResult(result)) {
                            that.resetMatchElements();
                            test.nextQuestion([result]);
                        }
                        else {
                            test.showNextButton();
                        }
                    });
                }
            });
        }

        function correctResult(match, result) {
            return $.post('/morphy/root', {
                match, result
            });
        }

        function createMaskedString(string) {
            var re = /\{([\wа-яА-ЯёЁ\s]+)\}/igm;
            var match;
            while ((match = re.exec(string)) !== null) {
                string = string.replace(match[0], '<span style="cursor:pointer" class="label label-primary" data-match="'+match[1]+'">' + createRepeatString(match[1]) + '</span>')
            }
            return string;
        }

        function createRepeatString(string) {
            return string.split(' ').map(function(word) {
                return '*'.repeat(word.length);
            }).join('_');
        }

        this.resetMatchElements = function() {
            control.getMissingWordsElement().find('span.label').each(function() {
                var match = $(this).attr('data-match');
                $(this).text(createRepeatString(match));
            });
        };

        function checkResult(result) {
            return test.checkAnswerCorrect(
                test.getCurrentQuestion(),
                [result],
                function(elem) {
                    return elem.name.toLowerCase();
                },
                false);
        }
    }

    MissingWords.prototype = {
        start: function(event, questionID, match) {
            this.control.setStatus();
            this.control.showLoader();
            this.control.showStopButton();
            this.recognition.start(event, match);
        },
        getResult: function() {
            return this.control.getMissingWordsText();
        },
        setRecognition: function(recognition) {
            this.recognition = recognition;
            this.addRecognitionListeners();
        }
    };

    _extends(MissingWords, {
        pluginName: 'missingWords'
    });

    var RecognitionControl = function(test) {

        function getElement() {
            return test.getCurrentQuestionElement();
        }

        var API = {}

        API.showLoader = function() {
            getElement().find('.wikids-test-loader').show();
        };

        API.hideLoader = function() {
            getElement().find('.wikids-test-loader').hide();
        };

        API.setStatus = function(status) {
            status = status || '';
            getElement().find('.recognition-status').text(status);
        };

        API.showStopButton = function() {
            getElement().find('.recognition-stop').show();
        }

        API.hideStopButton = function() {
            getElement().find('.recognition-stop').hide();
        }

        API.getResult = function() {
            return getElement().find('.recognition-result').text();
        }

        API.setResult = function(text) {
            text = text || '';
            getElement().find('.recognition-result').text(text).trigger('input');
        }

        API.disableResult = function() {
            getElement().find('.recognition-result').prop('contenteditable', false);
        };

        API.enableResult = function() {
            getElement().find('.recognition-result').prop('contenteditable', true);
        };

        API.setFragmentResult = function(fragment, range) {
            var result = API.getResult();
            var match = result.substring(0, range.startOffset)
                + fragment
                + result.substring(range.endOffset);
            API.setResult(match);
        }

        API.showRepeatWord = function() {
            return getElement().find('.recognition-repeat-word').show();
        };

        API.hideRepeatWord = function() {
            return getElement().find('.recognition-repeat-word').hide();
        };

        API.getQuestionTitle = function() {
            return $.trim(getElement().find('.question-title').text());
        };

        API.repeatButtonShow = function() {
            getElement().find('.recognition-repeat').show();
        };

        API.repeatButtonHide = function() {
            getElement().find('.recognition-repeat').hide();
        };

        API.getCurrentCorrectAnswer = function() {
            return test.getCorrectAnswer(test.getCurrentQuestion())
                .map(function(elem) {
                    return $.trim(elem.name);
                })
                .join('');
        }

        API.resultSetFocus = function() {
            getElement().find('.recognition-result').focus();
        };

        API.getMissingWordsText = function() {
            return $.trim(getElement().find('.missing-words-text').text()).toLowerCase();
        }

        API.getMissingWordsElement = function() {
            return getElement().find('.missing-words-text');
        }

        return API;
    }

    function RecordingAnswer(test) {

        var control;
        this.control = control = new RecognitionControl(test);
        this.recognition = null;
        this.test = test;

        function checkResultLength(result, match) {
            return result.length >= match.replaceAll(/(\d+)#([\wа-яА-ЯёЁ]+)/uig, "$1").length;
        }

        this.addRecognitionListeners = function() {
            if (this.recognition === null) {
                return;
            }
            this.recognition.addEventListener('onStart', function() {
                test.hideNextButton();
                control.setStatus('Идет запись с микрофона');
                control.showStopButton();
                control.disableResult();
            });

            var that = this;
            this.recognition.addEventListener('onEnd', function() {
                control.hideLoader();
                control.hideStopButton();
                control.setStatus();
                control.enableResult();

                var result = that.getResult();
                if (result.length === 0) {
                    control.repeatButtonShow();
                    control.resultSetFocus();
                    return;
                }

                if (that.checkResult(result)) {
                    that.resetResult();
                    test.nextQuestion([result]);
                }
                else {
                    var morphy = new Morphy();
                    morphy.correctResult(control.getCurrentCorrectAnswer(), result).done(function(response) {
                        result = response.result;
                        control.setResult(result);
                        if (that.checkResult(result)) {
                            that.resetResult();
                            test.nextQuestion([result]);
                        }
                        else {
                            test.showNextButton();
                        }
                    })
                    control.repeatButtonShow();
                    control.resultSetFocus();
                }
            });

            this.recognition.addEventListener('onError', function(event) {
                control.hideLoader();
                control.setStatus(event.args.error);
            });

            this.recognition.addEventListener('onResult', function(event) {
                var args = event.args;
                var result = $.trim(args.result);
                control.setResult(result);
                var match = control.getCurrentCorrectAnswer();
                if (checkResultLength(result, match)) {
                    that.recognition.stop();
                }
            });
        };
    }

    RecordingAnswer.prototype = {
        create: function (question, answer) {
            var that = this;
            var element = $('<div/>');
            element.addClass('test-recognition');
            element
                .append($('<div/>')
                    .addClass('recognition-result-wrapper')
                    .append(
                        $('<div/>')
                            .prop('contenteditable', true)
                            .addClass('recognition-result')
                            .on('input', function (e) {
                                var value = $(this).text();
                                value.length > 0
                                    ? that.test.showNextButton()
                                    : that.test.hideNextButton();
                            })
                            .on('keydown', function (e) {
                                if (e.key === "Enter") {
                                    e.preventDefault();
                                    var value = $(this).text();
                                    if (value.length > 0) {
                                        that.resetResult();
                                        that.test.nextQuestion([value]);
                                    }
                                }
                            })
                    )
                    .append($('<span/>').addClass('recognition-result-interim'))
                );
            $('<div/>')
                .addClass('wikids-test-loader')
                .append($('<img/>')
                    .attr('src', '/img/loading.gif')
                    .attr('width', '60px')
                )
                .hide()
                .appendTo(element);
            element
                .append($('<p/>').addClass('recognition-status'));
            $('<a/>')
                .attr('href', '#')
                .attr('title', 'Повторить ввод с микрофона')
                .addClass('recognition-repeat')
                .on('click', function (e) {
                    e.preventDefault();
                    that.start(e);
                })
                .append($('<i/>').addClass('glyphicon glyphicon-refresh'))
                .hide()
                .appendTo(element);
            $('<a/>')
                .attr('href', '#')
                .attr('title', 'Остановить')
                .addClass('recognition-stop')
                .on('click', function (e) {
                    e.preventDefault();
                    that.recognition.stop();
                })
                .append($('<i/>').addClass('glyphicon glyphicon-stop'))
                .hide()
                .appendTo(element);
            return element;
        },
        autoStart: function (event, timeout) {
            this.control.setStatus();
            this.control.repeatButtonHide();
            timeout = timeout || 1000;
            var that = this;
            setTimeout(function() {
                that.control.showLoader();
                that.recognition.start(event);
            }, timeout);
        },
        setRecognition: function(recognition) {
            this.recognition = recognition;
            this.addRecognitionListeners();
        },
        getResult: function() {
            return this.control.getResult();
        },
        resetResult: function() {
            this.control.setResult();
        },
        start: function(event) {
            this.control.setStatus();
            this.control.setResult();
            this.control.repeatButtonHide();
            this.control.showLoader();
            this.recognition.start(event);
        },
        checkResult: function(result) {
            return this.test.checkAnswerCorrect(
                this.test.getCurrentQuestion(),
                [result],
                function(elem) {
                    return elem.name.toLowerCase();
                },
                false);
        }
    };

    _extends(RecordingAnswer, {
        pluginName: 'recordingAnswer'
    });

    var MissingWordsRecognition = function(config) {

        var recorder = new webkitSpeechRecognition();
        recorder.continuous = true;
        recorder.interimResults = true;
        recorder.lang = config.getRecordingLang() || 'ru-RU';

        var recognizing = false;
        var startTimestamp = null;
        var finalTranscript = '';
        var targetElement;

        var eventListeners = [];

        recorder.onstart = function() {
            recognizing = true;
            dispatchEvent({type: 'onStart'});
        };

        recorder.onresult = function(event) {

            var interimTranscript = '';
            if (typeof(event.results) === 'undefined') {
                recorder.onend = null;
                recorder.stop();
                return;
            }

            for (var i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    finalTranscript = event.results[i][0].transcript;
                } else {
                    interimTranscript += event.results[i][0].transcript;
                }
            }

            if (finalTranscript.length) {
                finalTranscript = lowerCase(finalTranscript);
                dispatchEvent({
                    type: 'onResult',
                    args: {
                        target: targetElement,
                        result: linebreak(finalTranscript),
                        interim: linebreak(interimTranscript)
                    }
                });
            }
        };

        recorder.onend = function() {
            recognizing = false;
            dispatchEvent({
                type: 'onEnd',
                args: {
                    target: targetElement,
                    result: linebreak(finalTranscript)
                }
            });
        }

        function errorString(error) {
            var result = '';
            switch (error) {
                case 'no-speech': result = 'Речи не обнаружено'; break;
                case 'audio-capture': result = 'Не удалось захватить звук'; break;
                case 'not-allowed': result = 'Пользовательский агент запретил ввод речи из соображений безопасности, конфиденциальности или предпочтений пользователя'; break;
                default: result = error;
            }
            return result
        }

        recorder.onerror = function(event) {

            dispatchEvent({
                type: 'onError',
                args: {
                    error: errorString(event.error)
                }
            });
        };

        function start(event, text) {
            if (recognizing) {
                recorder.stop();
                return;
            }
            finalTranscript = '';
            recorder.start();
            startTimestamp = event.timeStamp;
            targetElement = event.target;
        }

        function stop() {
            recorder.stop();
        }

        function dispatchEvent(event) {
            for (var i = 0; i < eventListeners.length; i++) {
                if (event.type === eventListeners[i].type) {
                    eventListeners[i].eventHandler(event);
                }
            }
        }

        function linebreak(s) {
            var two_line = /\n\n/g;
            var one_line = /\n/g;
            return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
        }

        function capitalize(s) {
            var first_char = /\S/;
            return s.replace(first_char, function(m) { return m.toUpperCase(); });
        }

        function lowerCase(s) {
            return s.toLowerCase();
        }

        return {
            'start': start,
            'stop': stop,
            'addEventListener': function(type, eventHandler) {
                var listener = {};
                listener.type = type;
                listener.eventHandler = eventHandler;
                eventListeners.push(listener);
            }
        }
    }

    var TestSpeech = function(options) {

        var defaultOptions = {
            pitch: 1,
            rate: 0.8
        };
        options = options || {};
        options = Object.assign(defaultOptions, options);

        var synthesis = window.speechSynthesis;

        function setSpeech() {
            return new Promise(function(resolve, reject) {
                var handle;
                handle = setInterval(function() {
                    if (synthesis.getVoices().length > 0) {
                        resolve(synthesis.getVoices());
                        clearInterval(handle);
                    }
                }, 50);
            });
        }

        var voices = [];
        setSpeech().then(function(speech) {
            voices = speech;
        });

        return {
            'readText': function(text, voice, onEnd) {

                var utterance = new SpeechSynthesisUtterance(text);

                voice = voice || 'Google русский';
                for (var i = 0; i < voices.length; i++) {
                    if (voices[i].name === voice) {
                        utterance.voice = voices[i];
                        break;
                    }
                }

                for (var [key, value] of Object.entries(options)) {
                    utterance[key] = value;
                }

                if (typeof onEnd === 'function') {
                    utterance.onend = onEnd;
                }

                synthesis.speak(utterance);
            }
        }
    }

    function SequenceQuestion(test) {

        var $list = $('<div/>', {
            class: 'list-group'
        });

/*        function checkResult(result) {
            return test.checkAnswerCorrect(test.getCurrentQuestion(), result);
        }*/

        Sortable.create($list[0], {
            ghostClass: 'wikids-sortable-ghost',
            handle: '.wikids-sortable-handle'
/*            onUpdate: function(e) {
                var answers = getAnswerIDs(e.srcElement);
                if (checkResult(answers)) {
                    test.nextQuestion(answers);
                }
            }*/
        });

        this.createAnswer = function(answers) {
            $list.empty();
            var _answers = [];
            _extends(_answers, answers);
            _answers = shuffle(_answers);
            _answers.forEach(function(answer) {
                var move = $('<i/>', {
                    class: 'glyphicon glyphicon-move wikids-sortable-handle'
                });
                $('<div/>', {
                    'class': 'list-group-item',
                    'text': answer.name,
                    'data-answer-id': answer.id
                }).prepend(move).appendTo($list);
            });
            return $list;
        }

        this.getAnswerIDs = function() {
            return $list.find('[data-answer-id]').map(function() {
                return parseInt($(this).attr('data-answer-id'));
            }).get();
        }
    }

    SequenceQuestion.prototype = {
        createAnswers: function(answers) {
            var $answers = $('<div/>', {
                'class': 'wikids-test-answers'
            });
            $answers.append(this.createAnswer(answers));
            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-4 col-md-offset-4 question-wrapper"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }
    };

    _extends(SequenceQuestion, {
        pluginName: 'sequenceQuestion'
    });

    function _extends() {
        _extends = Object.assign || function (target) {
            for (var i = 1; i < arguments.length; i++) {
                var source = arguments[i];
                for (var key in source) {
                    if (Object.prototype.hasOwnProperty.call(source, key)) {
                        target[key] = source[key];
                    }
                }
            }
            return target;
        };
        return _extends.apply(this, arguments);
    }

    var plugins = [];
    var defaults = {
        initializeByDefault: true
    };
    var PluginManager = {
        mount: function mount(plugin) {
            // Set default static properties
            for (var option in defaults) {
                if (defaults.hasOwnProperty(option) && !(option in plugin)) {
                    plugin[option] = defaults[option];
                }
            }
            plugins.forEach(function (p) {
                if (p.pluginName === plugin.pluginName) {
                    throw "WikidsStoryTest: Cannot mount plugin ".concat(plugin.pluginName, " more than once");
                }
            });
            plugins.push(plugin);
        },
        initializePlugins: function initializePlugins(test, el, defaults, options) {
            plugins.forEach(function (plugin) {
                var pluginName = plugin.pluginName;
                //if (!test.options[pluginName] && !plugin.initializeByDefault) return;
                if (!plugin.initializeByDefault) return;
                var initialized = new plugin(test, el, {});
                initialized.test = test;
                initialized.options = {};
                test[pluginName] = initialized; // Add default options from plugin
                _extends(defaults, initialized.defaults);
            });
        }
    };

    var tests = [];

    function WikidsStoryTest(el, options) {

        if (!(el && el.nodeType && el.nodeType === 1)) {
            throw "Element must be an HTMLElement, not ".concat({}.toString.call(el));
        }

        options = options || {};
        this.options = Object.assign({}, options);

        var that = this;

        el['_wikids_test'] = this;

        this.recordingAnswer = null;
        this.recognition = null;
        this.sequenceQuestion = null;
        this.missingWords = null;
        this.regionQuestion = null;

        setElementHtml(createLoader('Инициализация'));

        var numQuestions,
            currentQuestionIndex = 0,
            correctAnswersNumber = 0,
            testData = {},
            dom = {};

        var questionHistory = [],
            skipQuestion = [],
            questions = [],
            questionsRepeat = [];

        var questionAnswers = {};

        function reset()
        {
            numQuestions = 0;
            currentQuestionIndex = 0;
            correctAnswersNumber = 0;
            testData = {};
            dom = {};
            questionHistory = [];
            skipQuestion = [];
            questions = [];
            questionsRepeat = [];
            questionAnswers = {};
            testQuestions = [];
        }

        var questionSuccess;

        function getTestData() {
            return testData;
        }

        function getQuestionsData() {
            return testData.storyTestQuestions.filter(function(el) {
                return (skipQuestion.indexOf(el.id) === -1);
            });
        }

        function getAnswersData(question) {
            return question.storyTestAnswers;
        }

        function getProgressData() {
            var progress = testData['test'] || {};
            progress = progress['progress'] || {};
            return progress;
        }

        function getStudentsData() {
            return testData['students'] || [];
        }

        var QuestionsRepeat = function(questions, starsTotal) {
            this.starsTotal = starsTotal;
            questions.map(function(question) {
                if (question['stars']) {
                    question.stars.number = parseInt(question.stars.total) - parseInt(question.stars.current);
                }
            });
        };

        QuestionsRepeat.prototype.inc = function(question) {
            question.stars.number++;
            var increased = true;
            if (question.stars.number > this.starsTotal) {
                question.stars.number = this.starsTotal;
                increased = false;
            }
            return increased;
        };

        QuestionsRepeat.prototype.dec = function(question) {
            question.stars.number--;
        };

        QuestionsRepeat.prototype.done = function(question) {
            return parseInt(question.stars.number) <= 0;
        };

        QuestionsRepeat.prototype.number = function(question) {
            var number = this.starsTotal - question.stars.number;
            return number < 0 ? 0 : number;
        };

        QuestionsRepeat.prototype.stars = function(question) {
            return question.stars.number;
        };

        var testProgress;
        var TestProgress = function(progress) {
            this.progress = progress;
        };

        TestProgress.prototype.getProgress = function() {
            return this.progress;
        };

        TestProgress.prototype.getCurrent = function() {
            return this.progress.current;
        };

        TestProgress.prototype.getTotal = function() {
            return this.progress.total;
        };

        TestProgress.prototype.calcPercent = function() {
            return Math.round(this.getCurrent() * 100 / this.getTotal());
        }

        TestProgress.prototype.inc = function() {
            this.progress.current++;
        }

        TestProgress.prototype.dec = function() {
            this.progress.current--;
            if (this.progress.current < 0) {
                this.progress.current = 0;
            }
        }

        var testConfig;

        function setElementHtml(html) {
            $(el).html(html);
        }

        function testIsRequired() {
            return parseInt(that.options.required) === 1;
        }

        function createContainer() {
            if (that.options.forSlide) {
                setElementHtml($("<section/>")
                    .attr("data-background-color", "#ffffff")
                    .append(dom.wrapper));
            }
            else {
                setElementHtml(dom.wrapper);
            }
        }

        function init(testResponse) {
            console.debug('WikidsStoryTest.init');

            reset();
            dom.wrapper = $("<div/>").addClass("wikids-test");

            if (App.userIsGuest()) {
                dom.beginPage = createGuestBeginPage(testResponse);
            }
            else {
                dom.beginPage = createBeginPage(testResponse);
            }

            dom.wrapper.append(dom.beginPage);
            createContainer();
        }

        function incorrectAnswerAction() {
            return testData['incorrectAnswerAction'] || '';
        }

        function incorrectAnswerActionRelated() {
            return incorrectAnswerAction() === 'related';
        }

        var testQuestions = [];

        function makeTestQuestions() {
            var end = false;
            var max = getQuestionRepeat();
            while (!end && testQuestions.length < max) {
                end = questions.length === 0;
                if (!end) {
                    testQuestions.push(questions.shift());
                }
            }
        }

        var incorrectAnswerText = '';
        var showAnswerImage = true,
            showAnswerText = true,
            showQuestionImage = true;
        var numPad;
        var linked;
        var speech;

        function getQuestionRepeat() {
            return that.options.fastMode ? 1 : 5;
        }

        function load(data) {
            console.debug('WikidsStoryTest.load');

            questionSuccess = new QuestionSuccess();
            testData = data[0];
            questions = getQuestionsData();
            numQuestions = questions.length;

            if (testData['test']) {
                incorrectAnswerText = testData['test']['incorrectAnswerText'] || '';
            }

            if (testData['test']) {
                showAnswerImage = testData['test']['showAnswerImage'];
                showAnswerText = testData['test']['showAnswerText'];
                showQuestionImage = testData['test']['showQuestionImage'];
            }

            testConfig = new TestConfig(testData['test']);
            linked = new TestLinked(testData['stories']);
            questionsRepeat = new QuestionsRepeat(questions, getQuestionRepeat());
            testProgress = new TestProgress(getProgressData());
            numPad = new AnswerTypeNumPad();
            speech = new TestSpeech();

            if (testConfig.answerTypeIsMissingWords()) {
                that.missingWords.setRecognition(new MissingWordsRecognition(testConfig));
            }

            if (testConfig.answerTypeIsRecording()) {
                that.recordingAnswer.setRecognition(new MissingWordsRecognition(testConfig));
            }

            makeTestQuestions();
            setupDOM();
            addEventListeners();

            start();
            createContainer();
        }

        function createLoader(text) {
            text = text || 'Загрузка вопросов';
            return $('<div/>')
                .addClass('wikids-test-loader')
                .append($('<p/>').text(text))
                .append($('<img/>').attr('src', '/img/loading.gif'));
        }

        function loadData() {
            console.debug('WikidsStoryTest.loadData');

            setElementHtml(createLoader());
            var dataParams = Object.assign(that.options.dataParams || {}, {
                studentId: activeStudent.getID(),
                fastMode: that.options.fastMode
            });
            $.getJSON(that.options.dataUrl, dataParams)
                .done(function(response) {
                    load(response);
                    if (that.options.forSlide) {
                        Reveal.sync();
                        Reveal.slide(0);
                    }
                })
                .fail(function(response) {
                    setElementHtml(createErrorPage());
                });
        }

        var currentStudent;
        var activeStudent = (function() {
            var stud = {};
            return {
                'set': function(student) {
                    stud = student;
                },
                'getID': function() {
                    return stud['id'] || null;
                },
                'getName': function() {
                    return stud.name;
                },
                'getProgress': function() {
                    return stud.progress;
                }
            }
        })();

        function setActiveStudentElement(element) {
            element.siblings().removeClass('active');
            element.addClass('active');
            currentStudent = element.data('student');
            activeStudent.set(element.data('student'));
            $('.wikids-test-student-info', dom.header).text(currentStudent.name);
        }

        function createGuestBeginPage(testResponse) {

            var $beginButton = $('<button/>')
                .addClass('btn wikids-test-begin')
                .text('Начать тест')
                .on('click', function() {
                    that.options.fastMode = $('#test-fast-mode').is(':checked');
                    loadData();
                });

            var $options = $('<div/>', {
                class: 'wikids-test-begin-page-options'
            });
            $options.append('<label for="test-fast-mode"><input id="test-fast-mode" type="checkbox" /> быстрый режим</label>');

            var $col = $('<div/>').addClass('col-md-12');
            $col.append($('<p/>', {'class': 'wikids-test-description'}).html(testResponse.test.description));
            $col.append($beginButton);

            return $('<div/>')
                .addClass('wikids-test-begin-page row')
                .append($('<div/>', {'class': 'col-md-12'}).append($('<h3/>').text(testResponse.test.header)))
                .append($col);
        }

        function createBeginPage(testResponse) {

            var $listGroup = $('<div/>').addClass('list-group');
            $listGroup.on('click', 'a', function(e) {
                e.preventDefault();
                setActiveStudentElement($(this));
            });
            testResponse.students.forEach(function(student) {
                var $item = $('<a/>')
                    .attr('href', '#')
                    .addClass('list-group-item')
                    .data('student', student)
                    .append($('<h4/>').addClass('list-group-item-heading').text(student.name));
                if (student['progress'] && student.progress > 0) {
                    $item.append(
                        $('<p/>').addClass('list-group-item-text').text(student.progress + '% завершено')
                    );
                }
                $item.appendTo($listGroup);
            });
            setActiveStudentElement($listGroup.find('a:eq(0)'));

            var $beginButton = $('<button/>')
                .addClass('btn wikids-test-begin')
                .text('Начать тест')
                .on('click', function() {
                    var fastMode = $('#test-fast-mode').is(':checked');
                    that.options.fastMode = fastMode;
                    loadData();
                });

            var $options = $('<div/>', {
                class: 'wikids-test-begin-page-options'
            });
            $options.append('<label for="test-fast-mode"><input id="test-fast-mode" type="checkbox" /> быстрый режим</label>');

            var $col = $('<div/>').addClass('col-md-6')
                .append($('<h3/>').text('Выберите ученика:'))
                .append($listGroup);

            if (App.userIsModerator()) {
                $col.append($options);
            }

            $col.append($beginButton);

            return $('<div/>')
                .addClass('wikids-test-begin-page row')
                .append($('<h3/>').text(testResponse.test.header))
                .append($col)
                .append($('<div/>').addClass('col-md-6')
                    .append($('<p/>').addClass('wikids-test-description').html(testResponse.test.description)));
        }

        function createErrorPage() {
            return $('<div/>')
                .addClass('wikids-test-error-page')
                .append($('<h3/>').text('При загрузке теста произошла ошибка'));
        }

        function correctAnswerPageNext() {
            dom.correctAnswerPage.hide();
            showNextQuestion();
            dom.results.hide();
            showNextButton();
        }

        function createCorrectAnswerPage() {
            var $action = $('<button/>')
                .addClass('btn correct-answer-page-next')
                .text('Продолжить')
                .on('click', function() {
                    correctAnswerPageNext();
                });
            return $('<div/>')
                .addClass('wikids-test-correct-answer-page')
                .hide()
                //.append($('<p/>').addClass('wikids-test-correct-answer-page-header'))
                .append($('<div/>').addClass('wikids-test-correct-answer-answers'))
                .append($('<div/>').addClass('wikids-test-correct-answer-page-action').append($action));
        }

        function setupDOM() {
            console.debug('WikidsStoryTest.setupDOM');
            questionSuccess.create();
            dom.header = createHeader(getTestData());
            dom.questions = createQuestions(getQuestionsData());
            dom.controls = createControls();
            dom.nextButton = $("<button/>")
                .addClass("btn btn-small btn-test wikids-test-next")
                .hide()
                .text('Следующий вопрос')
                .appendTo($(".wikids-test-buttons", dom.controls));
            dom.finishButton = $("<button/>")
                .addClass("wikids-test-finish")
                .hide()
                .text('Закончить тест')
                .appendTo($(".wikids-test-buttons", dom.controls));
            dom.restartButton = $("<button/>")
                .addClass("btn wikids-test-reset")
                .hide()
                .text('Пройти еще раз')
                .appendTo($(".wikids-test-buttons", dom.controls));
            dom.backToStoryButton = $("<button/>")
                .addClass("btn wikids-test-back")
                .hide()
                .text('Вернуться к истории')
                .appendTo($(".wikids-test-buttons", dom.controls));
            dom.continueButton = $("<button/>")
                .addClass("wikids-test-continue")
                .hide()
                .text('Продолжить')
                .appendTo($(".wikids-test-buttons", dom.controls));
            dom.nextSlideButton = $("<button/>")
                .addClass("btn wikids-test-next-slide")
                .hide()
                .html('Продолжить <i class="icomoon-chevron-right"></i>')
                .appendTo($(".wikids-test-buttons", dom.controls));
            dom.questions = createQuestions(getQuestionsData());
            dom.results = createResults();
            dom.correctAnswerPage = createCorrectAnswerPage();
            dom.wrapper
                .empty()
                .append(dom.header)
                .append(dom.questions)
                .append(dom.results)
                .append(dom.controls)
                .append(dom.correctAnswerPage);
        }

        function createStudentInfo() {
            return $('<div/>')
                .addClass('wikids-test-student-info')
                .text(currentStudent.name);
        }

        function addEventListeners() {
            //dom.nextButton.off("click").on("click", nextQuestion);
            dom.finishButton.off("click").on("click", finish);
            dom.restartButton.off("click").on("click", restart);
            dom.backToStoryButton.off("click").on("click", backToStory);
            dom.continueButton.off("click").on("click", continueTestAction);
            dom.nextSlideButton.off("click").on("click", nextSlideAction);
        }

        function showOriginalImage(url, elem) {
            $('<div/>')
                .addClass('wikids-test-image-original')
                .append(
                    $('<div/>')
                        .addClass('wikids-test-image-original-inner image-loader')
                        .on('click', function() {
                            $(this).parent().remove();
                            //if (elem) {
                            //    $(elem).parent()[0].click();
                            //}
                        })
                        .append(
                            $('<img/>')
                                .attr('src', url + '/original')
                                .on('load', function() {
                                    $(this).parent().removeClass('image-loader');
                                    $(this).show();
                                })
                        )
                )
                .appendTo(dom.wrapper);
        }

        function createAnswer(answer, question) {

            var questionType = question.type;
            var type = "radio";
            if (parseInt(questionType) === 1) {
                type = "checkbox";
            }

            var $element = $("<input/>")
                .attr("id", "answer" + answer.id)
                .attr("type", type)
                .attr("name", "qwe")
                .attr("value", answer.id)
                .data("answer", answer);

            var originalImageExists = answer['original_image'] === undefined ? true : answer['original_image'];

            var $answer = $("<div/>").addClass("wikids-test-answer")
                .on("click", function(e) {
                    var tagName = e.target.tagName;
                    var tags = ['INPUT'];
                    if (originalImageExists) {
                        tags.push('IMG');
                    }
                    if ($.inArray(tagName, tags) === -1) {
                        var $input = $(this).find("input");
                        $input.prop("checked", !$input.prop("checked"));
                    }

                    var key = 'q' + question.id;
                    questionAnswers[key] = getQuestionAnswers($(this).parent());
                    if (questionAnswers[key].length === parseInt(question.correct_number)) {
                        nextQuestion();
                    }
                })
                .append($element);

            if (showAnswerImage && answer.image) {
                var $image = $("<img/>")
                    .attr("src", answer.image)
                    .attr('height', 100);
                if (originalImageExists) {
                    $image
                        .css('cursor', 'zoom-in')
                        .on('click', function () {
                            showOriginalImage($(this).attr('src'), this);
                        });
                }
                $answer.append($image);
            }

            if (showAnswerText) {
                var $label = $("<label/>")
                    .attr("for", "answer" + answer.id)
                    .text(answer.name);
                $answer.append($label);
            }

            return $answer;
        }

        function generateAnswerList(answers, num) {

            if (answers.length <= num) {
                return shuffle(answers);
            }

            var list = answers.filter(function(answer) {
                return answer.is_correct === 1;
            });

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
                return answer.is_correct !== 1;
            }), max).map(function(elem) {
                list.push(elem);
            });

            return shuffle(list);
        }

        function getQuestionAnswerNumber(question) {
            return parseInt(question.answer_number);
        }

        function createAnswers(answers, question) {

            var num = getQuestionAnswerNumber(question);
            if (testConfig.sourceIsNeo() && num > 0) {
                answers = generateAnswerList(answers, num);
            }
            else {
                var mixAnswers = question.mix_answers || 0;
                if (parseInt(mixAnswers) === 1 || testConfig.sourceIsNeo()) {
                    answers = shuffle(answers);
                }
            }

            var $answers = $("<div/>").addClass("wikids-test-answers");
            answers.forEach(function(answer) {
                $answers.append(createAnswer(answer, question));
            });

            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-4 question-image"></div><div class="col-md-8 question-wrapper"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }

        function createSvgAnswer(question, answers) {
            var $object = $('<object/>')
                .attr({
                    data: '/upload/Continents.svg?t=' + (new Date().getMilliseconds()),
                    type: 'image/svg+xml',
                    id: 'svg' + question.id
                })
                .css('width', '100%');
            var getAnswersIDs = function(dom) {
                var answers = [];
                $('.continent.selected', dom).each(function() {
                    var id = $(this).attr('id');
                    answers.push(id);
                });
                return answers;
            }
            $object[0].addEventListener('load', function() {
                var svgDOM = $object[0].contentDocument;
                $('.continent', svgDOM).on('click', function() {
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    }
                    else {
                        if (parseInt(question.correct_number) === 1) {
                            $('.selected', svgDOM).removeClass('selected');
                        }
                        $(this).addClass('selected');
                    }
                    var key = 'q' + question.id;
                    questionAnswers[key] = getAnswersIDs(svgDOM);
                    if (questionAnswers[key].length === parseInt(question.correct_number)) {
                        nextQuestion();
                    }
                });
            }, true);
            return $object;
        }

        function createSvgAnswers(question, answers) {
            var $answers = $("<div/>").addClass("wikids-test-answers");
            $answers.append(createSvgAnswer(question, answers));
            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-4 question-image"></div><div class="col-md-8 question-wrapper"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }

        function createNumPadAnswer(question, answer) {
            return numPad.create(function(text) {
                nextQuestion();
            });
        }

        function createNumPadAnswers(question, answers) {
            var $answers = $("<div/>").addClass("wikids-test-answers");
            answers.forEach(function(answer) {
                $answers.append(createNumPadAnswer(question, answer));
            });
            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }

        function createInputAnswer(question, answer) {
            var $html = '<a href="#" title="Повторить слово" class="glyphicon glyphicon-repeat synthesis-question" style="top: 5px; right: 10px"><i></i></a>';
            return $('<div/>').append($html).append(answerTypeInput.create(nextQuestion));
        }

        function createInputAnswers(question, answers) {
            var $answers = $("<div/>").addClass("wikids-test-answers");
            answers.forEach(function(answer) {
                $answers.append(createInputAnswer(question, answer));
            });
            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper text-center"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }

        function createRecordingAnswer(question, answer) {
            return that.recordingAnswer.create(question, answer);
        }

        function createRegionAnswer(question, answers) {
            return that.regionQuestion.create(question, answers);
        }

        function createRecordingAnswers(question, answers) {
            var $answers = $("<div/>").addClass("wikids-test-answers");
            answers.forEach(function(answer) {
                $answers.append(createRecordingAnswer(question, answer));
            });
            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }

        function createRegionAnswers(question, answers) {
            var $answers = $("<div/>").addClass("wikids-test-answers");
            $answers.append(createRegionAnswer(question, answers));
            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }

        function createMissingWordsAnswer(question, answer) {
            return that.missingWords.init(question, answer);
        }

        function createMissingWordsAnswers(question, answers) {
            var $answers = $("<div/>").addClass("wikids-test-answers");
            answers.forEach(function(answer) {
                $answers.append(createMissingWordsAnswer(question, answer));
            });
            var $wrapper = $('<div class="row row-no-gutters"><div class="col-md-12 question-wrapper"></div></div>');
            $wrapper.find(".question-wrapper").append($answers);
            return $wrapper;
        }

        function appendStars($elem, total, current) {
            $elem.empty();
            for (var i = 0, $star, className; i < total; i++) {
                $star = $('<i/>');
                className = 'star-empty';
                if (i + 1 <= current) {
                    className = 'star';
                }
                $star.addClass('glyphicon glyphicon-' + className);
                $star.appendTo($elem);
            }
        }

        function createStars(stars) {
            var $elem = $('<p/>');
            $elem.addClass('question-stars');
            $elem.css('textAlign', 'right');
            appendStars($elem, getQuestionRepeat(), stars.current);
            return $elem;
        }

        function getCurrentProgressStateText() {
            return 'Вопрос ' + testProgress.getCurrent() + ' из ' + testProgress.getTotal();
        }

        function progressValue(value) {
            return ' ' + value + '% ';
        }

        function createProgress() {
            var progress = testProgress.calcPercent();
            return $('<div/>')
                .addClass('wikids-progress')
                .attr('title', getCurrentProgressStateText())
                .attr('data-toggle', 'tooltip')
                .append(
                    $('<div/>')
                        .addClass('progress-bar progress-bar-info')
                        .css('width', progress + '%')
                        .css('minWidth', '2em')
                        .text(progressValue(progress))
                )[0].outerHTML;
        }

        function updateProgress() {
            var progress = testProgress.calcPercent();
            $('.wikids-progress', dom.header).attr('title', getCurrentProgressStateText()).tooltip('fixTitle');
            $('.wikids-progress .progress-bar', dom.header)
                .css('width', progress + '%')
                .text(progressValue(progress));
        }

        function createQuestion(question) {

            var questionName = question.name;
            if (question['correct_number'] && question.correct_number > 1) {
                questionName += ' (верных ответов: ' + question.correct_number + ')';
            }

            if (testConfig.answerTypeIsMissingWords()) {
                questionName = 'Заполните пропущенные части';
            }

            if (questionViewSequence(question)) {
                questionName = question.name;
            }

            var titleElement = $('<p/>')
                .addClass('question-title')
                .append(questionName);

            var stars = '';
            if (question['stars']) {
                stars = createStars(question.stars);
            }
            return $("<div/>")
                .hide()
                .addClass("wikids-test-question")
                .append(stars)
                .append(titleElement)
                .attr("data-question-id", question.id)
                .data("question", question);
        }

        function createQuestions(questions) {
            var $questions = $("<div/>").addClass("wikids-test-questions");
            questions.forEach(function(question) {

                var $question = createQuestion(question);

                var view = question['view'] ? question.view : '';
                if (testConfig.answerTypeIsNumPad()) {
                    view = 'numpad';
                }
                if (testConfig.answerTypeIsInput()) {
                    view = 'input';
                }
                if (testConfig.answerTypeIsRecording()) {
                    view = 'recording';
                }
                if (testConfig.answerTypeIsMissingWords()) {
                    view = 'missing_words';
                }

                var $answers;
                switch (view) {
                    case 'svg':
                        $answers = createSvgAnswers(question, getAnswersData(question));
                        break;
                    case 'numpad':
                        $answers = createNumPadAnswers(question, getAnswersData(question));
                        break;
                    case 'input':
                        $answers = createInputAnswers(question, getAnswersData(question));
                        break;
                    case 'recording':
                        $answers = createRecordingAnswers(question, getAnswersData(question));
                        break;
                    case 'region':
                        $answers = createRegionAnswers(question, getAnswersData(question));
                        break;
                    case 'missing_words':
                        $answers = createMissingWordsAnswers(question, getAnswersData(question));
                        break;
                    case 'sequence':
                        $answers = that.sequenceQuestion.createAnswers(getAnswersData(question));
                        break;
                    default:
                        $answers = createAnswers(getAnswersData(question), question);
                }
                $answers.appendTo($question);

                if (showQuestionImage && question.image) {
                    var $image = $('<img/>')
                        .attr("src", question.image)
                        .css('max-width', '330px');
                    var originalImageExists = question['original_image'] === undefined ? true : question['original_image'];
                    if (originalImageExists) {
                        $image
                            .css('cursor', 'zoom-in')
                            .on('click', function () {
                                showOriginalImage($(this).attr('src'));
                            });
                    }
                    $image.appendTo($(".question-image", $question));
                }

                $questions.append($question);
            });

            return $questions;
        }

        function createControls() {
            var $controls = $("<div/>").addClass("wikids-test-controls"),
                $buttons = $("<div/>").addClass("wikids-test-buttons");
            $buttons.appendTo($controls);
            return $controls;
        }

        function createResults() {
            return $("<div/>")
                .addClass("wikids-test-results")
                .hide()
                .append($("<p/>"));
        }

        function createHeader(test) {
            var $header = $("<div/>")
                .addClass("wikids-test-header");

            if (test.title) {
                $header.append($("<h3/>").text(test.title));
            }
            if (test.description) {
                $header.append($("<p/>").text(test.description));
            }
            if (!App.userIsGuest()) {
                $header.append(createStudentInfo());
            }
            $header.append(createProgress());

            $('[data-toggle="tooltip"]', $header).tooltip();

            return $header;
        }

        function questionIsVisible(questionElement) {
            var object = $('object', questionElement);
            if (object.length) {
                var domSVG = object.contents();
                $('.continent', domSVG).removeClass('selected');
            }
        }

        function setTestResults(title) {
            title = title || 'Тест пройден';
            dom.results
                .empty()
                .append("<h2>" + title + "</h2>");
            var linkedStories = linked.getHtml();
            console.log(linkedStories);
            if (linkedStories.length) {
                dom.results.append(linkedStories);
            }
            return dom.results.show();
        }

        function nextSlideAction() {
            dispatchEvent("nextSlide", {
                "testID": getTestData().id
            });
        }

        function start() {
            console.debug('WikidsStoryTest.start');

            correctAnswersNumber = 0;
            currentQuestionIndex = 0;

            if (numQuestions === 0) {
                if (currentStudent.progress === 100) {
                    setTestResults();
                    if (that.options.forSlide) {
                        dom.backToStoryButton.show();
                    }
                    dom.nextSlideButton.show();
                }
                else {
                    setTestResults('В тесте нет вопросов');
                }
                return;
            }

            showNextQuestion();
            dom.beginPage.hide();
            dom.header.show();
            showNextButton();
        }

        function finish() {
            $('.wikids-test-active-question').hide().removeClass('wikids-test-active-question');
            dom.finishButton.hide();
            setTestResults();
            currentStudent['finish'] = true;
            dispatchEvent("finish", {
                "testID": getTestData().id,
                "correctAnswers": correctAnswersNumber
            });
        }

        function restart() {
/*            var nextQuestion = testQuestions.shift();
            $('.wikids-test-question[data-question-id=' + nextQuestion.id + ']', dom.questions)
                .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
                .addClass('wikids-test-active-question');

            dom.results.hide();
            correctAnswersNumber = 0;
            currentQuestionIndex = 0;
            dom.nextButton.show();
            dom.restartButton.hide();
            dom.backToStoryButton.hide();
            dom.continueButton.hide();*/
        }

        function backToStory() {
            dispatchEvent("backToStory", {});
        }

        function getCorrectAnswers(question) {
            return getAnswersData(question).filter(function(elem) {
                return parseInt(elem.is_correct) === 1;
            });
        }

        function createAnswerSteps(answers) {
            var steps = [];
            answers.map(function(item) {
                if (/(\d+)#([\wа-яА-ЯёЁ]+)/ui.test(item.name)) {
                    var match;
                    var re = /(\d+)#([\wа-яА-ЯёЁ]+)/uig;
                    var parts = [];
                    var key = 0;
                    var line = item.name;
                    while ((match = re.exec(item.name)) !== null) {
                        parts.push({match, values: [match[1], match[2]], key: '{' + key + '}'});
                        line = line.replace(match[0], '{' + key + '}');
                        key++;
                    }

                    var variants = [];
                    if (parts.length === 1) {
                        variants.push([parts[0].values[0]]);
                        variants.push([parts[0].values[1]]);
                    }
                    else {
                        variants = combineArraysRecursively(parts.map(function (item) {
                            return item.values;
                        }));
                    }

                    var str = '';
                    var j = 0;
                    variants.forEach(function(value) {
                        str = line;
                        j = 0
                        parts.forEach(function(partValue) {
                            str = str.replace(partValue.key, value[j]);
                            j++;
                        });
                        steps.push(str);
                    });
                }
            });
            return steps;
        }

        function correctAnswerSteps(steps, userAnswers) {
            return userAnswers.every(function(userAnswer) {
                return steps.some(function(stepAnswer) {
                    return userAnswer === stepAnswer;
                });
            });
        }

        function checkAnswerCorrect(question, answer, correctAnswersCallback, convertAnswerToInt) {
            console.debug('WikidsStoryTest.checkAnswerCorrect');
            var correctAnswers = getAnswersData(question).filter(function(elem) {
                return parseInt(elem.is_correct) === 1;
            });
            var steps = createAnswerSteps(correctAnswers);
            var correct = false;
            if (steps.length > 0) {
                correct = correctAnswerSteps(steps, answer);
            }
            else {
                if (questionViewSequence(question)) {
                    correct = checkAnswersCorrect(question, answer);
                }
                else {
                    correctAnswers = correctAnswers.map(correctAnswersCallback);
                    var answerCheckCallback = function (value, index) {
                        if (convertAnswerToInt) {
                            value = parseInt(value)
                        }
                        return value === correctAnswers.sort()[index];
                    };
                    if (answer.length === correctAnswers.length && answer.sort().every(answerCheckCallback)) {
                        correctAnswersNumber++;
                        correct = true;
                    }
                }
            }
            return correct;
        }

        function getQuestionView(question) {
            return question['view'] ? question.view : '';
        }

        function questionViewSequence(question) {
            return getQuestionView(question) === 'sequence';
        }

        function questionViewDefault(question) {
            return getQuestionView(question) === 'default';
        }

        function questionViewSvg(question) {
            return getQuestionView(question) === 'svg';
        }

        function questionViewRegion(question) {
            return getQuestionView(question) === 'region';
        }

        function checkAnswersCorrect(question, userAnswers) {
            var correctAnswers = getCorrectAnswers(question);
            correctAnswers.sort(function(a, b) {
                return a.order - b.order;
            });
            correctAnswers = correctAnswers.map(function(answer) {
                return parseInt(answer.id);
            });
            return (JSON.stringify(correctAnswers) === JSON.stringify(userAnswers));
        }

        function getQuestionAnswers(element) {
            var answer = [];
            element.find(".wikids-test-answer input:checked").each(function(i, elem) {
                answer.push($(elem).val());
            });
            return answer;
        }

        function getSvgQuestionAnswers(question) {
            if (Object.keys(questionAnswers).length === 0) {
                return [];
            }
            var questionAnswerNames = [];
            $.each(questionAnswers, function(key, value) {
                var questionID = key.replace(/\D+/, '');
                if (parseInt(questionID) === parseInt(question.id)) {
                    questionAnswerNames = value;
                    return;
                }
            });
            var answers = [];
            if (questionAnswerNames.length > 0) {
                var questionParams = question.svg.params;
                questionAnswerNames.forEach(function (answerName) {
                    var param = questionParams.filter(function (value) {
                        return (value.param_name === answerName);
                    });
                    if (param.length > 0) {
                        answers.push(param[0].entity_id);
                    }
                });
            }
            return answers;
        }

        function getNumPadQuestionAnswers(element) {
            var answer = [parseInt(element.find('#keyboard + p').text())];
            return answer;
        }

        function getInputQuestionAnswers(element) {
            var val = element.find('.answer-input').val();
            if (!val.length) {
                return [];
            }
            if (testConfig.isStrictAnswer()) {
                return [val];
            }
            return [val.toLowerCase()];
        }

        var answerIsCorrect,
            currentQuestion,
            currentQuestionElement;

        function getCurrentQuestion() {
            return currentQuestion;
        }

        function updateStars($question, current) {
            var $stars = $('.question-stars', $question);
            appendStars($stars, getQuestionRepeat(), current);
        }

        function answerByID(question, id) {
            return getAnswersData(question).filter(function(answer) {
                return parseInt(answer.id) === parseInt(id);
            })[0];
        }

        function showQuestionSuccessPage(answer) {

            var action = function() {
                $(this).parent().parent().remove();
                continueTestAction(answer);
            };
            var text = currentQuestion.name;
            if (testConfig.answerTypeIsInput()) {
                text = answer[0];
            }
            var $content = questionSuccess.create(action, text, currentQuestion.image);
            dom.wrapper.append($content)
            $content.fadeIn();
        }

        function getQuestionRememberAnswers(question) {
            return question['rememberAnswer'] || false;
        }

        function changeQuestionRememberAnswers(question, answer) {
            question.rememberAnswer = false;
            question.storyTestAnswers[0].name = answer[0];
        }

        // Ответ на вопрос
        function nextQuestion(preparedAnswers) {

            console.debug('WikidsStoryTest.nextQuestion');
            if (!Array.isArray(preparedAnswers)) {
                preparedAnswers = false;
            }
            preparedAnswers = preparedAnswers || false;

            var $activeQuestion = $('.wikids-test-active-question');
            currentQuestion = $activeQuestion.data('question');

            var view = currentQuestion['view'] ? currentQuestion.view : '';
            if (testConfig.answerTypeIsNumPad()) {
                view = 'numpad';
            }
            if (testConfig.answerTypeIsInput()) {
                view = 'input';
            }
            if (testConfig.answerTypeIsRecording()) {
                view = 'recognition';
            }

            var answer = [];
            if (!preparedAnswers) {
                switch (view) {
                    case 'svg':
                        answer = getSvgQuestionAnswers(currentQuestion);
                        questionIsVisible($activeQuestion);
                        break;
                    case 'numpad':
                        answer = getNumPadQuestionAnswers($activeQuestion);
                        numPad.reset($activeQuestion);
                        break;
                    case 'input':
                        answer = getInputQuestionAnswers($activeQuestion);
                        break;
                    default:
                        answer = getQuestionAnswers($activeQuestion);
                }
            }
            else {
                answer = preparedAnswers;
            }

            if (answer.length === 0) {
                return;
            }

            var convertAnswerToInt = true;
            var correctAnswersCallback = function(elem) {
                return parseInt(elem.id);
            };
            if (view === 'numpad') {
                correctAnswersCallback = function(elem) {
                    return parseInt(elem.name);
                };
            }
            if (view === 'input' || view === 'recognition' || testConfig.answerTypeIsMissingWords()) {
                correctAnswersCallback = function(elem) {
                    if (testConfig.isStrictAnswer()) {
                        return elem.name;
                    }
                    else {
                        return elem.name.toLowerCase();
                    }
                };
                convertAnswerToInt = false;
            }

            var rememberAnswer = getQuestionRememberAnswers(currentQuestion);
            if (!rememberAnswer) {
                answerIsCorrect = checkAnswerCorrect(currentQuestion, answer, correctAnswersCallback, convertAnswerToInt);
            }
            else {
                changeQuestionRememberAnswers(currentQuestion, answer);
                answerIsCorrect = true;
            }

            if (answerIsCorrect) {
                if (currentQuestion['stars']) {
                    if (currentQuestion.lastAnswerIsCorrect) {
                        questionsRepeat.dec(currentQuestion);
                        testProgress.inc();
                    }
                    else {
                        currentQuestion.lastAnswerIsCorrect = true;
                    }
                }
                else {
                    skipQuestion.push(currentQuestion.id);
                }
            }
            else {
                currentQuestion.lastAnswerIsCorrect = false;
                if (currentQuestion['stars']) {
                    var increased = questionsRepeat.inc(currentQuestion);
                    if (increased) {
                        testProgress.dec();
                    }
                }
            }

            if (currentQuestion['stars']) {
                updateStars($activeQuestion, questionsRepeat.number(currentQuestion));
            }
            updateProgress();

            var done = false;
            if (!answerIsCorrect) {
                testQuestions.unshift(currentQuestion);
            }
            else {
                done = questionsRepeat.done(currentQuestion);
                if (done) {
                    makeTestQuestions();
                }
                else {
                    testQuestions.push(currentQuestion);
                }
            }

            if (!App.userIsGuest() && !that.options.fastMode) {
                var answerParams = {};
                var answerList = [];
                if (testConfig.sourceIsNeo()) {
                    answerList = answer.map(function (entity_id) {
                        return {
                            'answer_entity_id': entity_id,
                            'answer_entity_name': answerByID(currentQuestion, entity_id).name
                        };
                    });
                    answerParams = {
                        'source': testConfig.getSource(),
                        'test_id': testConfig.getTestID(),
                        'student_id': currentStudent.id,
                        'question_topic_id': currentQuestion.topic_id,
                        'question_topic_name': currentQuestion.name,
                        'entity_id': currentQuestion.id,
                        'entity_name': currentQuestion.entity_name,
                        'relation_id': currentQuestion.relation_id,
                        'relation_name': currentQuestion.relation_name,
                        'correct_answer': answerIsCorrect ? 1 : 0,
                        'answers': answerList,
                        'progress': testProgress.calcPercent(),
                        'stars': questionsRepeat.number(currentQuestion)
                    };
                    $.post('/question/answer', answerParams);
                }
                if (testConfig.sourceIsWord() && !testConfig.answerTypeIsInput()) {
                    answerList = answer.map(function (answerText) {
                        return {
                            'answer_entity_id': currentQuestion.id,
                            'answer_entity_name': answerText
                        };
                    });
                    answerParams = {
                        'source': testConfig.getSource(),
                        'test_id': testConfig.getTestID(),
                        'student_id': currentStudent.id,
                        'entity_id': currentQuestion.id,
                        'entity_name': currentQuestion.name,
                        'correct_answer': answerIsCorrect ? 1 : 0,
                        'answers': answerList,
                        'progress': testProgress.calcPercent(),
                        'stars': questionsRepeat.number(currentQuestion)
                    };
                    $.post('/question/answer', answerParams);
                }
                if (testConfig.sourceIsWord() && testConfig.answerTypeIsInput()) {
                    answerList = answer.map(function (answerText) {
                        return {
                            'answer_entity_id': currentQuestion.id,
                            'answer_entity_name': answerText
                        };
                    });
                    answerParams = {
                        'source': testConfig.getSource(),
                        'test_id': testConfig.getTestID(),
                        'student_id': currentStudent.id,
                        'entity_id': currentQuestion.id,
                        'entity_name': currentQuestion.name,
                        'correct_answer': answerIsCorrect ? 1 : 0,
                        'answers': answerList,
                        'progress': testProgress.calcPercent(),
                        'stars': questionsRepeat.number(currentQuestion)
                    };
                    $.post('/question/answer', answerParams);
                }
                if (testConfig.sourceIsLocal()) {
                    answerList = answer.map(function (entity_id) {
                        var answer = answerByID(currentQuestion, entity_id);
                        return {
                            'answer_entity_id': entity_id,
                            'answer_entity_name': answer ? answer.name : 'no correct'
                        };
                    });
                    answerParams = {
                        'source': testConfig.getSource(),
                        'test_id': testConfig.getTestID(),
                        'student_id': currentStudent.id,
                        'entity_id': currentQuestion.id,
                        'entity_name': currentQuestion.name,
                        'correct_answer': answerIsCorrect ? 1 : 0,
                        'answers': answerList,
                        'progress': testProgress.calcPercent(),
                        'stars': questionsRepeat.number(currentQuestion)
                    };
                    $.post('/question/answer', answerParams);
                }
            }

            $activeQuestion
                .slideUp()
                .hide()
                .removeClass('wikids-test-active-question');

            dom.nextButton.hide();

            if (!answerIsCorrect) {
                if (testConfig.sourceIsWord()
                    && !testConfig.answerTypeIsNumPad()
                    && !testConfig.answerTypeIsInput()
                    && !testConfig.answerTypeIsMissingWords()) {
                    continueTestAction(answer);
                }
                else {
                    dom.results
                        .html("<p>Ответ не верный.</p>")
                        .show()
                        .delay(1000)
                        .fadeOut('slow', function () {
                            continueTestAction(answer);
                        });
                }
            }
            else {
                if (done && !that.options.fastMode) {
                    showQuestionSuccessPage(answer);
                }
                else {
                    continueTestAction(answer);
                }
            }
        }

        function showNextQuestion() {

            console.debug('WikidsStoryTest.showNextQuestion');

            var nextQuestionObj = testQuestions.shift();
            currentQuestion = nextQuestionObj;

            if (nextQuestionObj === undefined) {
                return;
            }

            dom.nextButton.off("click").on("click", nextQuestion);

            currentQuestionElement = $('.wikids-test-question[data-question-id=' + nextQuestionObj.id + ']', dom.questions);

            if (getQuestionView(currentQuestion) !== 'svg' && testConfig.sourceIsNeo()) {
                $('.wikids-test-answers', currentQuestionElement)
                    .empty()
                    .append(createAnswers(getAnswersData(currentQuestion), currentQuestion)
                        .find('.wikids-test-answers > div'));
            }

            if (questionViewSequence(currentQuestion)) {
                $('.wikids-test-answers', currentQuestionElement)
                    .empty()
                    .append(that.sequenceQuestion.createAnswers(getAnswersData(currentQuestion))
                        .find('.wikids-test-answers > div'));

                dom.nextButton.off("click").on("click", function() {
                    var result = that.sequenceQuestion.getAnswerIDs();
                    nextQuestion(result);
                });
            }

            if (testConfig.answerTypeIsMissingWords()) {
                dom.nextButton.off("click").on("click", function() {
                    var result = that.missingWords.getResult();
                    that.missingWords.resetMatchElements();
                    nextQuestion([result]);
                });
            }
            if (testConfig.answerTypeIsRecording()) {
                dom.nextButton.off("click").on("click", function() {
                    var result = that.recordingAnswer.getResult();
                    that.recordingAnswer.resetResult();
                    nextQuestion([result]);
                });
            }

            currentQuestionElement
                .find('input[type=checkbox],input[type=radio]').prop('checked', false).end()
                .slideDown()
                .addClass('wikids-test-active-question');

            if (testConfig.sourceIsWord()) {
                dom.nextButton.hide();
            }

            if (testConfig.answerTypeIsInput()) {

                var text = getAnswersData(nextQuestionObj)[0].name;
                var q = $('.wikids-test-active-question .answer-input', dom.questions);
                setTimeout(function () {
                    speech.readText(text, testConfig.getInputVoice());
                    q.focus();
                }, 500);

                setTimeout(function() {
                    q
                        .val('')
                        .focus();
                }, 100);

                $('.wikids-test-active-question .synthesis-question', dom.questions)
                    .off('click')
                    .on('click', function (e) {
                        e.preventDefault();
                        speech.readText(text, testConfig.getInputVoice());
                    });
            }

            if (testConfig.answerTypeIsRecording()) {
                if (testConfig.isAskQuestion()) {
                    var text = currentQuestion.name;
                    setTimeout(function() {
                        speech.readText(text, testConfig.getAskQuestionLang(), function () {
                            that.recordingAnswer.autoStart(new Event('autoStart'), 500);
                        });
                    }, 500);
                }
                else {
                    that.recordingAnswer.autoStart(new Event('autoStart'));
                }
            }
        }

        function goToRelatedSlide(goToSlideCallback, otherCallback) {
            var params = {
                'entity_id': getCurrentQuestion().entity_id,
                'relation_id': getCurrentQuestion().relation_id,
            };
            $.getJSON('/question/get-related-slide', params).done(function (data) {
                if (data && data['slide_id'] && data['story_id']) {
                    goToSlideCallback(data);
                } else {
                    otherCallback();
                }
            });
        }

        function showCorrectAnswerPage(question, answer) {
            console.debug('WikidsStoryTest.showCorrectAnswerPage');
            var $elements = $('<div/>');

            var text = incorrectAnswerText || 'Правильный ответ';
            text = text.replace('{1}', question.entity_name);
            $elements.append($('<h4/>').text(text + ':'));

            if (questionViewRegion(question)) {
                $elements.append(that.regionQuestion.createSuccess(question));
            }
            else {
                var $element;
                var answerText = '';
                var userAnswer = answer[0];
                getAnswersData(question).forEach(function (questionAnswer) {
                    $element = $('<div/>').addClass('row');
                    var $content = $('<div/>').addClass('col-md-offset-3 col-md-9');
                    if (parseInt(questionAnswer.is_correct) === 1) {

                        answerText = questionAnswer.name;

                        if (questionAnswer.image) {
                            var $image = $('<img/>')
                                .attr("src", questionAnswer.image)
                                .attr("width", 180)
                                .css('cursor', 'zoom-in')
                                .on('click', function () {
                                    showOriginalImage($(this).attr('src'));
                                });
                            $content.append($image);
                        }

                        var $answerElement;
                        if (testConfig.answerTypeIsRecording()) {
                            $answerElement = $('<p/>')
                                .append($('<span/>').text(answerText))
                                .append($('<a/>')
                                    .attr('href', '#')
                                    .attr('title', 'Прослушать')
                                    .css('font-size', '3rem')
                                    .on('click', function (e) {
                                        e.preventDefault();
                                        speech.readText(questionAnswer.name, testConfig.getInputVoice());
                                    })
                                    .html('<i class="glyphicon glyphicon-volume-up" style="left: 10px; top: 6px"></i>')
                                );
                        } else {
                            if (testConfig.answerTypeIsInput()) {
                                $answerElement = $('<p/>').html(textDiff(answerText, userAnswer));
                            } else {
                                $answerElement = $('<p/>').text(answerText);
                            }
                        }
                        $content.append($answerElement);

                        if (testConfig.answerTypeIsInput()) {
                            $('<p/>').html('&nbsp;').appendTo($content);
                            $('<p/>')
                                .text('Ваш ответ:')
                                .appendTo($content);
                            $('<p/>')
                                .html(userAnswer)
                                .appendTo($content);
                        }

                        $elements.append($element.append($content));
                    }
                });
            }

            if (testConfig.answerTypeIsRecording()) {
                dom.correctAnswerPage.find('.correct-answer-page-next').hide();
            }

            dom.correctAnswerPage
                .find('.wikids-test-correct-answer-answers').empty().html($elements[0].childNodes).end()
                .show();

            if (testConfig.answerTypeIsRecording()) {
                setTimeout(function() {
                    speech.readText(answerText, testConfig.getInputVoice(), correctAnswerPageNext);
                }, 600);
            }
        }

        function showNextButton() {
            if (!testConfig.sourceIsWord()
                && !questionViewDefault(currentQuestion)
                && !questionViewSvg(currentQuestion)
                && !questionViewRegion(currentQuestion)
                && !testConfig.sourceIsNeo()) {
                dom.nextButton.show();
            }
        }

        function hideNextButton() {
            dom.nextButton.hdie();
        }

        function continueTestAction(answer) {
            console.debug('continueTestAction');

            dom.continueButton.hide();
            var isLastQuestion = (testQuestions.length === 0);
            // var actionRelated = incorrectAnswerActionRelated();
            var showCorrectAnswerPageCondition = testConfig.sourceIsWord()
                && !testConfig.answerTypeIsNumPad()
                && !testConfig.answerTypeIsRecording()
                && !testConfig.answerTypeIsInput()
                && !testConfig.answerTypeIsMissingWords();

            if (isLastQuestion) {

                if (!answerIsCorrect) {
                    if (showCorrectAnswerPageCondition) {
                        showNextQuestion();
                        dom.results.hide();
                        showNextButton();
                    }
                    else {
                        showCorrectAnswerPage(currentQuestion, answer);
                    }
                }
                else {
                    if (that.options.forSlide) {
                        dispatchEvent("backToStory", {});
                    }
                    else {
                        finish();
                    }
                }
            }
            else {
                if (!answerIsCorrect) {
                    if (showCorrectAnswerPageCondition) {
                        showNextQuestion();
                        dom.results.hide();
                        showNextButton();
                    }
                    else {
                        showCorrectAnswerPage(currentQuestion, answer);
                    }
                }
                else {
                    showNextQuestion();
                    dom.results.hide();
                    showNextButton();
                }
            }
        }

        function dispatchEvent(type, args) {
            var event = document.createEvent("HTMLEvents", 1, 2);
            event.initEvent(type, true, true);
            extend(event, args);
            dom.wrapper[0].dispatchEvent(event);
        }

        function restore() {

            //init(true);
            dom.wrapper = $("<div/>").addClass("wikids-test");

            setupDOM();
            addEventListeners();
            start();

            var elem = $("div.new-questions", WikidsPlayer.getCurrentSlide());
            elem.html(dom.wrapper);
        }

        PluginManager.initializePlugins(this, el, {});

        this.getCurrentQuestionElement = function() {
            return currentQuestionElement;
        }

        this.getCorrectAnswer = function(question) {
            return getCorrectAnswers(question);
        };

        this.getCurrentQuestion = getCurrentQuestion;

        this.hideNextButton = function() {
            dom.nextButton.hide();
        };

        this.showNextButton = function() {
            dom.nextButton.show();
        };

        this.nextQuestion = nextQuestion;

        this.checkAnswerCorrect = checkAnswerCorrect;

        tests.push(el);

        this.canNext = function() {
            var canNext = currentStudent && (currentStudent.progress === 100 || currentStudent['finish']);
            return (testIsRequired() && canNext) || (!testIsRequired());
        };

        return {
            "init": init,
            "load": load,
            "restore": restore,
            "addEventListener": function(type, listener, useCapture) {
                if ('addEventListener' in window) {
                    dom.wrapper[0].addEventListener(type, listener, useCapture);
                }
            },
            "getTestConfig": function() {
                return testConfig;
            },
            "isTestSlide": function() {
                return ($('[data-test-id]', Reveal.getCurrentSlide()).length > 0);
            }
        };
    }

    WikidsStoryTest.create = function(el, options) {
        return new WikidsStoryTest(el, options);
    };

    WikidsStoryTest.mount = function () {
        for (var _len = arguments.length, plugins = new Array(_len), _key = 0; _key < _len; _key++) {
            plugins[_key] = arguments[_key];
        }
        if (plugins[0].constructor === Array) plugins = plugins[0];
        plugins.forEach(function (plugin) {
            if (!plugin.prototype || !plugin.prototype.constructor) {
                throw "WikidsStoryTest: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
            }
            PluginManager.mount(plugin);
        });
    };

    WikidsStoryTest.mount(RecordingAnswer);
    WikidsStoryTest.mount(SequenceQuestion);
    WikidsStoryTest.mount(MissingWords);
    WikidsStoryTest.mount(RegionQuestion);

    WikidsStoryTest.getTests = function() {
        return tests;
    }

    return WikidsStoryTest;
}));
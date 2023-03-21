const TestConfig = function (data) {

  function getSource() {
    return parseInt(data.source);
  }

  function getAnswerType() {
    return parseInt(data.answerType);
  }

  return {
    'getSource': getSource,
    'sourceIsLocal': function () {
      return getSource() === 1;
    },
    'sourceIsNeo': function () {
      return getSource() === 2;
    },
    'sourceIsWord': function () {
      return getSource() === 3;
    },
    'sourceIsTests': function () {
      return getSource() === 4;
    },
    'answerTypeIsDefault': function () {
      return getAnswerType() === 0;
    },
    'answerTypeIsNumPad': function (q) {
      return getAnswerType() === 1 || parseInt(q['type']) === 4;
    },
    'answerTypeIsInput': function (q) {
      return getAnswerType() === 2 || parseInt(q['type']) === 5;
    },
    'answerTypeIsRecording': function (q) {
      q = q || {};
      return getAnswerType() === 3 || parseInt(q['type']) === 6;
    },
    'answerTypeIsMissingWords': function (q) {
      q = q || {};
      return getAnswerType() === 4 || parseInt(q['type']) === 7;
    },
    'isStrictAnswer': function () {
      return parseInt(data.strictAnswer);
    },
    'getInputVoice': function () {
      return data.inputVoice;
    },
    'getRecordingLang': function () {
      return data.recordingLang;
    },
    'isRememberAnswers': function () {
      return data.rememberAnswers;
    },
    'getTestID': function () {
      return parseInt(data.id);
    },
    'isAskQuestion': function () {
      return data.askQuestion;
    },
    'getAskQuestionLang': function () {
      return data.askQuestionLang;
    },
    'hideQuestionName': function () {
      return data.hideQuestionName;
    },
    'isSayCorrectAnswer': function () {
      return data.sayCorrectAnswer;
    },
    'isVoiceResponse': function () {
      return data.voiceResponse;
    },
    getDescription: () => data.description,
    showDescriptionInQuestions: () => data.showDescriptionInQuestions,
    answerTypeIsPassTest: (q) => parseInt(q['type']) === 8
  }
}

export default TestConfig;

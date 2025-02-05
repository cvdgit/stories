
const RecognitionControl = function(test) {

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
    getElement().find('.recognition-result').attr('contenteditable', '');
  };

  API.enableResult = function() {
    getElement().find('.recognition-result').attr('contenteditable', 'plaintext-only');
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

  API.addVoiceAnimation = () => {
    $(`<div id="voice-anim-wrap" class="recognition-voice-anim-wrap"><div id="recognition-voice-anim" class="no-anim">
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
  <div class="bar"></div>
</div></div>`).appendTo(getElement().find('.recognition-result-wrapper'))
  }

  API.removeVoiceAnimation = () => {
    getElement().find('#voice-anim-wrap').remove()
  }

  API.voiceAnimOn = () => {
    if (getElement().find('#recognition-voice-anim').hasClass('no-anim')) {
      getElement().find('#recognition-voice-anim').removeClass('no-anim')
    }
  }

  API.voiceAnimOff = () => {
    if (!getElement().find('#recognition-voice-anim').hasClass('no-anim')) {
      getElement().find('#recognition-voice-anim').addClass('no-anim')
    }
  }

  API.requestLoaderShow = (text) => {
    $(`<div id="request-loader" class="recognition-voice-anim-wrap">
<p style="margin-bottom: 0; margin-right: 20px">${text}</p>
<img style="height: 30px" src="/img/loading.gif" alt="">
</div>`).appendTo(getElement().find('.recognition-result-wrapper'))
  }

  API.requestLoaderHide = () => getElement().find('#request-loader').remove()

  API.setRetellingResponse = (text) => getElement().find('.retelling-response').html(text)

  API.getRetellingResponse = () => getElement().find('.retelling-response').text()

  return API;
}

export default RecognitionControl;

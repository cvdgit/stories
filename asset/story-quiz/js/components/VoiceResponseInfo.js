
const VoiceResponseInfo = function() {

};

VoiceResponseInfo.create = function(content) {

  var $wrap = $(`<div class="quiz-modal-wrapper">
                     <div class="quiz-modal-background"></div>
                     <div class="quiz-modal-inner">
                       <div class="quiz-modal-header">
                         <div class="quiz-modal-header__title">
                           <h4>Информация</h4>
                         </div>
                         <div class="quiz-modal-actions">
                           <button type="button" class="quiz-modal-action-close">&times;</button>
                         </div>
                       </div>
                       <div class="quiz-modal-body"></div>
                     </div>
                   </div>`);

  $wrap.find('.quiz-modal-action-close').on('click', function() {
    $(this).parents('.quiz-modal-wrapper:eq(0)').hide().remove();
  });

  $wrap.find('.quiz-modal-body').append(content);

  return $wrap;
}

VoiceResponseInfo.remove = function(container) {
  var elem = container.find('.quiz-modal-wrapper');
  if (elem.length) {
    elem.fadeOut().remove();
  }
};

VoiceResponseInfo.setContent = function(container, content) {
  if (!container.find('.quiz-modal-wrapper').length) {
    return;
  }
  container.find('.quiz-modal-body')
    .empty()
    .append(content);
}

VoiceResponseInfo.send = function(question_id, answer, onSuccess) {
  $.post('/answer/create', {question_id, answer})
    .done(function(response) {
      if (response && response.success) {
        if (typeof onSuccess === 'function') {
          onSuccess(response);
        }
      }
    });
}

export default VoiceResponseInfo;

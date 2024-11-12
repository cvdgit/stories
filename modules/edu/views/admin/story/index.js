(function() {

  function clearSelectize(selectize) {
    selectize.clear()
    selectize.clearOptions()
  }

  const defaultOptions = {
    maxItems: 1,
    valueField: 'id',
    labelField: 'name',
    searchField: 'name',
    create: false,
    options: [],
  }

  $('#addstoryform-class_id').selectize({
    ...defaultOptions,
    options: [
      ...window.itemsData.classItems
    ],
    onChange: (value) => {
      const classProgramSelectize = $('#addstoryform-class_program_id')[0].selectize;
      const topicSelectize = $('#addstoryform-topic_id')[0].selectize;
      const lessonSelectize = $('#addstoryform-lesson_id')[0].selectize;
      clearSelectize(classProgramSelectize)
      clearSelectize(topicSelectize)
      clearSelectize(lessonSelectize)
      const options = window.itemsData.classProgramItems.filter(i => Number(i.class_id) === Number(value))
      options.map(o => classProgramSelectize.addOption(o))
    },
  });

  $('#addstoryform-class_program_id').selectize({
    ...defaultOptions,
    onChange: (value) => {
      const topicSelectize = $('#addstoryform-topic_id')[0].selectize;
      const lessonSelectize = $('#addstoryform-lesson_id')[0].selectize;
      clearSelectize(topicSelectize)
      clearSelectize(lessonSelectize)
      const options = window.itemsData.topicItems.filter(i => Number(i.class_program_id) === Number(value))
      options.map(o => topicSelectize.addOption(o))
    },
  });

  $('#addstoryform-topic_id').selectize({
    ...defaultOptions,
    onChange: (value) => {
      const lessonSelectize = $('#addstoryform-lesson_id')[0].selectize;
      clearSelectize(lessonSelectize)
      const options = window.itemsData.lessonItems.filter(i => Number(i.topic_id) === Number(value))
      options.map(o => lessonSelectize.addOption(o))
    },
  });

  $('#addstoryform-lesson_id').selectize({
    ...defaultOptions,
  });

  attachBeforeSubmit(document.getElementById('edu-select-form'), (form) => {
    const formData = new FormData(form)
    sendForm($(form).attr('action'), $(form).attr('method'), formData)
      .then((r) => {
        if (r.success) {
          $('#story-list').find(`[data-story-id=${r.storyId}]`).replaceWith(r.links)
          $('#edu-select-modal').modal('hide')
        }
      })
  })

  $('#story-list').on('click', '[data-story-id]', e => {
    e.preventDefault()
    const storyId = e.target.dataset.storyId
    $('#addstoryform-story_id').val(storyId)
    $('#edu-select-modal')
      .on('show.bs.modal', (e) => {

      })
      .modal('show')
  })
})();

(function() {
  $('.history-content').on('change', '#historyfilterform-hours', function() {
    $('#history-filter-form').submit();
  })

  $('.history-nav').on('click', 'a', e => {
    $.pjax.reload('#pjax-day-history', {
      replace: false,
      push: true,
      async: false,
      url: e.target.getAttribute('href')
    })
  })

  $('.history-content').on('submit', '#history-filter-form', e => {
    console.log('subbbb')
    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);
    $.pjax.reload('#pjax-day-history', {
      replace: false,
      push: false,
      async: true,
      url: '?' + params.toString()
    })
    return false
  })
})();

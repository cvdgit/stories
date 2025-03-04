(function() {

  $('.history-nav').on('click', 'a', e => {
    $.pjax.reload('#pjax-week-history', {
      replace: false,
      push: true,
      async: false,
      url: e.target.getAttribute('href')
    })
  })

  $('.history-content').on('submit', '#week-filter-form', e => {
    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);
    $.pjax.reload('#pjax-week-history', {
      replace: false,
      push: false,
      async: true,
      url: 'week?' + params.toString()
    })
    return false
  })
})();

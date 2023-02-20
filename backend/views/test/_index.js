(function() {
  $('#testing-filter-form input[type=checkbox]').on('click', function() {
    $.pjax.reload({
      container: '#pjax-tests',
      replace: false,
      url: $('#testing-filter-form').attr('action'),
      method: 'post',
      data: $('#testing-filter-form').serialize()
    });
  });
})();

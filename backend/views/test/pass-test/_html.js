(function() {
  $('#content_html').on('change keyup paste', e => {
    $('#content').html(e.target.value);
  });
  $('#content-as-html').click(e => {
    $('#content_html').toggle('fast', () => {
      $('#content_html').text($('#content').html());
    });
  });
})();

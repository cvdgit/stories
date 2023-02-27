(function() {
  $('#testing-filter-form input[type=checkbox]').on('click', function() {
    $('.tests-wrap .table-responsive').yiiGridView('applyFilter');
  });

  $(document).on('beforeFilter', function(e) {
    const $form = $(".tests-wrap .table-responsive").find('form.gridview-filter-form');
    $form.find('input[name="TestSearch[with_repetition]"]').remove();
    $form.append(
      $('<input>').attr({
        type:  'hidden',
        name:  'TestSearch[with_repetition]'
      }).val($('#testsearch-with_repetition').is(':checked') ? '1' : '')
    );
  });
})();

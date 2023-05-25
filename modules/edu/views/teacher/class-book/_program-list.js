(function() {

  function RemoteModal({id, title, dialogClassName}) {

    const content = `
    <div class="modal fade" tabindex="-1" id="${id}">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="display: flex; justify-content: space-between">
            <h5 class="modal-title" style="margin-right: auto">${title}</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">...</div>
        </div>
      </div>
    </div>
    `;

    if ($('body').find(`div#${id}`).length) {
      $('body').find(`div#${id}`).remove();
    }

    $('body').append(content);

    const element = $('body').find(`div#${id}`);

    if (dialogClassName) {
      element.find('.modal-dialog').addClass(dialogClassName);
    }

    element.on('hide.bs.modal', function() {
      $(this).removeData('bs.modal');
      $(this).find('.modal-body').html('');
    });

    return {
      show({url, callback}) {
        element
          .off('show.bs.modal')
          .on('show.bs.modal', function() {
            $(this).find('.modal-body').load(url, callback);
          });
        element.modal('show');
      },
      hide() {
        element.modal('hide');
      }
    };
  }

  function attachBeforeSubmit(form, callback) {
    $(form)
      .on('beforeSubmit', function(e) {
        e.preventDefault();
        callback(form);
        return false;
      })
      .on('submit', function(e) {
        e.preventDefault();
      });
  }

  const modal = new RemoteModal({
    id: 'manage-topics',
    title: 'Управление доступом к темам',
    dialogClassName: 'modal-lg'
  })

  $('#class-book-list').on('click', '.manage-topics', (e) => {
    e.preventDefault();
    const url = e.target.getAttribute('href');
    modal.show({
      url,
      callback: function() {

        const $form = $(this).find('form');

        attachBeforeSubmit($form[0], form => {

          const formData = new FormData(form)

          $(this).find('#topic-access-list').find('input[type=checkbox]:checked').each((i, elem) => {
            formData.append('TopicAccessForm[' + i + '][class_program_id]', $(elem).attr('data-program-id'));
            formData.append('TopicAccessForm[' + i + '][topic_id]', $(elem).val());
          });

          sendForm(formData, $form.attr('action'), $form.attr('method'))
            .done(response => {
              if (response && response.success) {
                toastr.success(response.message);
                modal.hide();
              }
              if (response && response.success === false) {
                toastr.error(response.message || 'Произошла ошибка');
              }
            });
        });
      }
    })
  });
})();

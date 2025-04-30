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
      if (typeof hideCallback === 'function') {
        hideCallback()
      }
    });

    let hideCallback;

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
      },
      onHide(callback) {
        hideCallback = callback
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

  const accessModal = new RemoteModal({
    id: 'teacher-access',
    title: 'Доступ к классу',
  })

  function createTableRow({name, classBookId, teacherId}) {
    const $tr = $('<tr>')
    $tr.append($('<td/>').text(name))
    $tr.append($('<td/>').html(`<a data-class-book-id="${classBookId}" data-teacher-id="${teacherId}" href="" class="delete-teacher"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em;pointer-events: none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg></a>`))
    return $tr
  }

  accessModal.onHide(() => $.pjax.reload({container: '#pjax-class-books', async: false}))

  $('#class-book-list').on('click', '.teacher-access', e => {
    e.preventDefault()
    const url = e.target.getAttribute('href')
    accessModal.show({
      url,
      callback: function() {
        const $form = $(this).find('form');
        const $table = $(this).find('#teacher-list tbody');

        (window.teacherAccessItems || []).map(item => {
          const {id: teacherId, name} = item
          $table.append(createTableRow({name, classBookId: window.classBookId, teacherId}))
        })

        $table.on('click', '.delete-teacher', e => {
          e.preventDefault()
          if (!confirm('Подтверждаете?')) {
            return
          }

          const teacherId = e.target.dataset.teacherId
          const classBookId = e.target.dataset.classBookId

          if (!teacherId || !classBookId) {
            return
          }

          fetch('/edu/teacher/class-book/revoke-access', {
            method: 'post',
            body: JSON.stringify({
              teacherId,
              classBookId
            }),
            cache: 'no-cache',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
          })
            .then(response => response.json())
            .then(response => {
              console.log(response)
              if (response?.success) {
                $(e.target).parents('tr:eq(0)').remove()
              }
            })
            .catch(response => toastr.error(response.statusText))
        })

        attachBeforeSubmit($form[0], form => {
          const formData = new FormData(form)
          sendForm(formData, $form.attr('action'), $form.attr('method'))
            .done(response => {
              if (response && response.success) {

                const selectize = $(form).find('#teacheraccessform-teacher_id')[0].selectize;
                selectize.setValue('')
                // toastr.success(response.message || 'OK');

                const $tr = $('<tr>')
                $tr.append($('<td/>').text(response.data?.name))
                $tr.append($('<td/>').html(`<a data-class-book-id="${response.data?.class_book_id}" data-teacher-id="${response.data?.teacher_id}" href="" class="delete-teacher"><svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em;pointer-events: none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg></a>`))
                $table.append($tr)

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

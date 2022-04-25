export default class {

  constructor() {
    this.$element = $('#update-block-modal');
    this.onSuccessCallback = undefined;
    this.init();
  }

  init() {
    this.$element
      .on('hide.bs.modal', (e) => {
        $(e.target).removeData('bs.modal');
        $(e.target).find('.modal-content').html('');
      })
      .on('loaded.bs.modal', (e) => {
        $(e.target).find('form')
          .on('beforeSubmit', (e) => {
            e.preventDefault();

            const $form = $(e.target);

            fetch($form.attr('action'), {
              method: $form.attr('method'),
              body: new FormData($form[0]),
              cache: 'no-cache',
              headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
              }
            })
              .then((response) => {
                this.$element.modal('hide');
                if (response.ok) {
                  return response.json();
                }
                throw new Error(response.statusText);
              })
              .then((responseJson) => {
                if (responseJson.success) {
                  if (typeof this.onSuccessCallback === 'function') {
                    this.onSuccessCallback(responseJson);
                  }
                  toastr.success(responseJson.message || 'Успешно');
                }
                else {
                  toastr.error(responseJson.message || 'Ошибка');
                }
              })
              .catch((error) => {
                toastr.error(error);
              });

          })
          .on('submit', e => e.preventDefault());
      });
  }

  modalRemote(remote, onSuccess) {
    this.onSuccessCallback = onSuccess;
    this.$element.modal({remote});
  }
}

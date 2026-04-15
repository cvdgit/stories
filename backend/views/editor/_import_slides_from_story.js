function ImportSlidesFromStory() {
}

ImportSlidesFromStory.showModal = function({storyId, slideId}) {

  const modal = new RemoteModal({
    id: 'import-slides-from-story-modal',
    title: 'Импортировать слайды из истории'
  });

  let url = `/admin/index.php?r=editor/import-slides/form&storyId=${storyId}`;
  if (slideId) {
    url += `&currentSlideId=${slideId}`
  }
  modal.show({
    url,
    callback: (body) => {

      attachBeforeSubmit($(body).find('form')[0], (form) => {
        const formData = new FormData(form);
        sendForm($(form).attr('action'), $(form).attr('method'), formData)
          .done((response) => {
            if (response.success) {
              toastr.success('Успешно');
              location.reload()
            } else {
              toastr.error(response.message || 'Ошибка')
            }
          });
      })
    }
  })
};

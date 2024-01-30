function CreatePassTest() {

  async function createQuestions(storyId, slideId, content) {
    const response = await fetch(`/admin/index.php?r=editor/create-pass-test&current_slide_id=${slideId}&story_id=${storyId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': yii.getCsrfToken(),
      },
      body: JSON.stringify({content})
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    return await response.json();
  }

  this.create = ({storyId, slideId, content, processCallback}) => {
    const response = createQuestions(storyId, slideId, content)
    response.then(json => {
      if (json) {
        if (json.success) {
          if (typeof processCallback === "function") {
            processCallback();
          }
          toastr.success("Успешно");
        } else {
          toastr.error(json.message || "Ошибка");
        }
      } else {
        toastr.error("Неизвестная ошибка");
      }
    });
  }
}

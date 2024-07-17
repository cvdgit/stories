function MentalMapSlide() {

  async function createSlide(storyId, slideId, content, image) {
    const response = await fetch(`/admin/index.php?r=editor/mental-map&current_slide_id=${slideId}&story_id=${storyId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': yii.getCsrfToken(),
      },
      body: JSON.stringify({
        content,
        image
      })
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    return await response.json();
  }

  this.createSlide = (storyId, slideId, content, image) => {
    return createSlide(storyId, slideId, content, image)
  }
}

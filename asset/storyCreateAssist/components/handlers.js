import api from "../Api";

export async function createStoryHandler(threadId, title, payload) {
  const data = {
    title,
    threadId,
    ...payload
  }
  return await api.post('/admin/index.php?r=story-ai/create-story-handler', data, {
    'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
  })
}

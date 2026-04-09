export default async function saveUserResult(payload) {
  return await window.Api.post('/mental-map/save', payload);
}

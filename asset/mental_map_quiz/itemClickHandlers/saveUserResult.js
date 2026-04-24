
/**
 * @typedef {Object} Payload
 * @property {Number} story_id
 * @property {Number} slide_id
 * @property {String} mental_map_id
 * @property {String} image_fragment_id
 * @property {Number} overall_similarity
 * @property {Number} text_hiding_percentage
 * @property {Number} text_target_percentage
 * @property {String} content
 * @property {Boolean} repetition_mode
 * @property {Number} threshold
 * @property {Object} payload
 * @property {String} location
 * @property {Number} seconds
 */

/**
 * @param {Payload} payload
 * @return {Promise<*>}
 */
export default async function saveUserResult(payload) {
  return await window.Api.post('/mental-map/save', payload);
}

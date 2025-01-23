import TreeViewBody from "./TreeViewBody";

/**
 *
 * @param name
 * @param tree
 * @param history
 * @param {{story_id: number|null, slide_id: number|null, mental_map_id: string}} params
 * @param {VoiceResponse} voiceResponse
 * @returns {HTMLDivElement}
 * @constructor
 */
export default function TreeView({name, tree, history, params}, voiceResponse) {

  const wrap = document.createElement('div')
  wrap.style.display = 'flex'
  wrap.style.height = '100%'
  wrap.style.overflow = 'hidden'
  wrap.style.flexDirection = 'column'

  const header = document.createElement('div')
  header.innerHTML = `<h2 class="h3 text-center">${name}</h2>`
  wrap.appendChild(header)

  wrap.appendChild(TreeViewBody(tree, voiceResponse, history, params))

  return wrap
}

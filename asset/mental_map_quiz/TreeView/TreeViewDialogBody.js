import "./TreeViewBody.css";
import "./TreeViewDialogBody.css";
import MapImageStatus from "../components/MapImageStatus";

function createRow(node, status, done) {

  const row = document.createElement('div')
  row.dataset.nodeId = node.id
  row.dataset.level = node.level
  row.classList.add('node-row')

  if (done) {
    row.classList.add('node-row-done');
  }

  row.innerHTML = `<div class="node-status">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
     stroke="currentColor" class="retelling-success">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg>
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
     class="retelling-failed">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg>
</div>
<div class="node-body">
<div class="node-title" style="cursor: pointer">${node.title.replace(/\r?\n/g, "\r\n")}</div>
</div>
  `;

  row.querySelector('.node-body').appendChild(status);

  return row;
}

function flatten(nodes, level = 0) {
  return nodes.flatMap((node, index) => [
    {...node, level, index, hasChildren: (node.children || []).length > 0},
    ...flatten(node.children || [], level + 1)
  ])
}

async function getHistoryLog(id) {
  return await window.Api.get(`/mental-map/item-log?id=${id}`);
}

export default function TreeViewDialogBody({tree, voiceResponse, history, itemClickHandler, params, onEndHandler, isPlanTreeView, settingsPromptId}) {

  function init() {
    const body = document.createElement('div');
    body.classList.add('tree-body', 'tree-dialog-body');
    return body;
  }

  let body = init();

  voiceResponse.onError(({args}) => {
    const row = body.querySelector('.node-row.current-row.pending')
    if (row) {
      body.classList.remove('do-recording')
      const id = row.getAttribute('data-node-id')
      const historyItem = history.find(h => h.id === id)
      historyItem.pending = false
      body.innerHTML = ''
      API.init()
    }
  })

  const API = {
    getElement() {
      return body
    },
    init() {
      const list = flatten(tree);
      list.map(
        node => {

          const historyItem = history.find(({id}) => id === node.id);
          const row = createRow(node, MapImageStatus.render({
            hiding: historyItem.hiding || 0,
            seconds: historyItem.seconds || 0,
            hidingPrev: historyItem.hidingPrev || 0,
          }), historyItem.done);

          row.querySelector('.node-title').addEventListener('click', e => {
            itemClickHandler({
              id: node.id,
              text: node.description,
              description: node.description
            });
          });

          const stat = row.querySelector('.map-user-status-hiding');
          if (stat) {
            stat.addEventListener('click', async () => {
              console.log(await getHistoryLog(node.id));
            });
          }

          body.appendChild(row);
        }
      );
    },
    restart() {
      body.remove()
      history = history.map(h => ({...h, done: false, all: 0, pending: false, repeat: false}))
      body = init()
      this.init()
      return body
    },
    on(type, listener, useCapture) {
      body.addEventListener(type, listener, useCapture)
    }
  }

  return API;
}

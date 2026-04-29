import "./TreeViewBody.css";
import "./TreeViewDialogBody.css";
import MapImageStatus from "../components/MapImageStatus";

function buildTree(nodes, renderNodeContent) {
  const container = document.createElement("div");

  nodes.forEach(node => {
    const nodeEl = document.createElement("div");
    nodeEl.className = "tree-node";
    nodeEl.dataset.nodeId = node.id;

    const struct = document.createElement("div");
    struct.className = "node-struct";

    const slot = document.createElement("div");
    slot.className = "node-slot";

    slot.appendChild(renderNodeContent(node));

    struct.appendChild(slot);
    nodeEl.appendChild(struct);

    if (node.children && node.children.length) {
      const children = document.createElement("div");
      children.className = "children";

      children.appendChild(buildTree(node.children, renderNodeContent));
      nodeEl.appendChild(children);

      const toggle = document.createElement("div");
      toggle.className = "toggle toggle__collapseButton";
      struct.appendChild(toggle);
      toggle.addEventListener("click", () => {
        nodeEl.classList.toggle("collapsed");
        toggle.classList.remove('toggle__collapseButton', 'toggle__expandButton');
        toggle.classList.add(
          nodeEl.classList.contains('collapsed') ? 'toggle__expandButton' : 'toggle__collapseButton'
        )
      });
    }

    container.appendChild(nodeEl);
  });

  return container;
}

function createRow(node, status, done) {

  const row = document.createElement('div')
  row.dataset.nodeId = node.id
  row.dataset.level = node.level
  row.classList.add('node-row')

  if (done) {
    row.classList.add('node-row-done');
  }

  row.innerHTML = `
<div class="node-status"></div>
<div class="node-body">
<div class="node-title" style="cursor: pointer">${node.title.replace(/\r?\n/g, "\r\n")}</div>
</div>
  `;

  row.querySelector('.node-status').appendChild(status);

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

      body.appendChild(
        buildTree(tree, (node) => {
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

          return row;
        })
      );

      /*const list = flatten(tree);
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
      );*/
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

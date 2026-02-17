import {v4 as uuidv4} from 'uuid';

function processNodes(nodes, nodeCallback) {
  for (const node of nodes) {
    nodeCallback(node)
    if (node.children?.length) {
      const found = processNodes(node.children, nodeCallback);
      if (found) return found;
    }
  }
}

export default function storyPayloadBuilder(tree) {
  const payload = {
    title: null,
    contents: [],
    fragments: []
  };

  let currentGroup;
  let currentLevel;
  processNodes(
    tree,
    ({level, header, content, children}) => {
      if (payload.title === null && level === 1) {
        payload.title = header;
      }

      if (level < currentLevel) {
        currentGroup = null;
      }
      currentLevel = level;

      if (!currentGroup) {
        currentGroup = {
          name: header,
          cards: [],
        };
        payload.contents.push(currentGroup);
        return;
      }

      if (content === '') {
        currentGroup.name = header;
        return;
      }

      const slideId = uuidv4();
      currentGroup.cards.push({
        name: header,
        slides: [slideId]
      });
      payload.fragments.push({
        id: slideId,
        text: `<h2>${header}</h2><p>${content}</p>`
      });
    }
  )

  /*tree.map(({header, content, children}) => {
    const group = {
      name: header,
      cards: [],
    };
    (children || []).map(({header, content}) => {
      const slideId = uuidv4();
      payload.fragments.push({
        id: slideId,
        text: `<h2>${header}</h2><p>${content}</p>`
      });
      group.cards.push({
        name: header,
        slides: [slideId]
      });
    });
    payload.contents.push(group);
  });*/
  return payload;
}

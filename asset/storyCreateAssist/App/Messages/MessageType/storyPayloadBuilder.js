import {v4 as uuidv4} from 'uuid';
export default function storyPayloadBuilder(tree) {
  const payload = {
    contents: [],
    fragments: []
  };
  tree.map(({header, content, children}) => {
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
  });
  return payload;
}

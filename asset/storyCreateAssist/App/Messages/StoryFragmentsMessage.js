import React from "react";
import styles from "./Messages.module.css";
import {v4 as uuidv4} from "uuid";
import api from "../../Api";

async function createStory(threadId, title, payload) {
  const data = {
    title,
    threadId,
    ...payload
  }
  return await api.post('/admin/index.php?r=story-ai/create-story-handler', data, {
    'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
  })
}

export default function StoryFragmentsMessage({message, threadId}) {
  const {metadata} = message;
  const {tree} = metadata;

  const createStoryHandler = async () => {
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

    const response = await createStory(
      threadId,
      'Новая история',
      payload
    );
    console.log(response);
  }

  return <div>
    {tree.map(({header, content, children}, i) =>
      <div key={i}>
        <h3>{header}</h3>
        {content && <div>{content}</div>}
        {children.length && children.map(({header, content}, j) => <div key={j} style={{marginBottom: '20px', paddingBottom: '20px', borderBottom: '1px #808080 solid'}}>
          <h4>{header}</h4>
          <div>{content}</div>
        </div>)}
      </div>
    )}
    <div>
      <button className={styles.itemButton} onClick={createStoryHandler} type="button">Создать историю</button>
    </div>
  </div>;
}

import React, {forwardRef, useCallback, useEffect, useMemo, useState} from "react";
import Dialog from "../Dialog";
import ContentEditable from "react-contenteditable";
import styles from "./Editor.module.css";

function splitTextIntoChunks(text, chunksCount) {
  if (typeof text !== 'string' || chunksCount <= 0) {
    return [];
  }

  const abbreviations = [
    'т.е.',
    'и т.д.',
    'и т.п.',
    'т.к.',
    'т.н.',
    'и др.',
    'e.g.',
    'i.e.',
    'etc.',
    'vs.',
    'mr.',
    'mrs.',
    'dr.'
  ];

  const DOT_MARKER = '__DOT__';
  let prepared = text;
  abbreviations.forEach(abbr => {
    const safe = abbr.replace(/\./g, DOT_MARKER);
    const re = new RegExp(abbr.replace(/\./g, '\\.'), 'gi');
    prepared = prepared.replace(re, safe);
  });

  const sentenceEndRegex =
    /(\.\.\.|[.!?])(["»”')\]]*)/g;

  const sentenceEnds = [];
  let match;

  while ((match = sentenceEndRegex.exec(prepared)) !== null) {
    const endIndex = match.index + match[0].length;
    sentenceEnds.push(endIndex);
  }

  if (sentenceEnds.length === 0) {
    return [text];
  }

  const totalLength = text.length;
  const targetLength = Math.ceil(totalLength / chunksCount);

  const result = [];
  let chunkStart = 0;
  let lastAcceptedEnd = sentenceEnds[0];

  for (let i = 0; i < sentenceEnds.length; i++) {
    const end = sentenceEnds[i];
    const projectedLength = end - chunkStart;
    if (result.length === chunksCount - 1) {
      break;
    }
    if (projectedLength >= targetLength) {
      result.push(text.slice(chunkStart, end));
      chunkStart = end;
    }
    lastAcceptedEnd = end;
  }
  result.push(text.slice(chunkStart));
  return result;
}


const Page = ({text, pageIndex, onInput}) => {
  return <div style={{backgroundColor: '#fff'}}>
    <ContentEditable html={text} onChange={e => {
      onInput(e, pageIndex);
    }}/>
  </div>
}

const TextEditorDialog = forwardRef(({dialogProps, open, setOpen, text, createStoryFromEditorHandler}, ref) => {
  const [allText, setAllText] = useState(text);
  const [pages, setPages] = useState([]);
  const [chunkSize, setChunkSize] = useState(1500);

  const paragraphs = useMemo(() => {
    return allText.split('\n\n').filter(t => t.trim() !== '');
  }, [allText]);

  const oneParagraphOnPageHandler = () => {
    setPages(paragraphs);
  }

  const twoParagraphsOnPageHandler = () => {
    const newPages = [];
    let pageTexts = [];
    let paragraphIndex = 0;
    let paragraphsMax = 2;
    paragraphs.map((paragraphText, i) => {
      if (paragraphIndex < paragraphsMax) {
        pageTexts.push(paragraphText);
        paragraphIndex++;
      }
      if (paragraphIndex === paragraphsMax || i + 1 === paragraphs.length) {
        newPages.push(pageTexts.join('\n\n'));
        paragraphIndex = 0;
        pageTexts = [];
      }
    });
    setPages(newPages);
  }

  const splitByChunksHandler = () => {
    const newPages = [];
    splitTextIntoChunks(text, Math.round(text.length / chunkSize))
      .map(chunkText => newPages.push(chunkText.trim()));
    setPages(newPages);
  }

  const createRepetitionStoryHandler = () => {
    if (!pages.length) {
      return;
    }
    setOpen(false);
    createStoryFromEditorHandler(pages.filter(text => text.trim() !== ''));
  };

  const handleInput = useCallback((e, pageIndex) => {
    const pageText = e.currentTarget.innerText;
    setPages(prevState => {
      const values = [...prevState];
      values[pageIndex] = pageText;
      return values.filter(t => t.trim() !== '');
    });
  }, [pages]);

  useEffect(() => {
    if (!pages.length) {
      return
    }
    setAllText(pages.join('\n\n'));
  }, [pages])

  return (
    <Dialog nodeRef={ref} hideHandler={() => setOpen(false)} addContentClassName="item-content">
      <div style={{
        color: 'black',
        padding: '10px 0',
        display: 'flex',
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        borderBottom: '1px #808080 solid'
      }}>
        <div style={{display: 'flex', flexDirection: 'row', gap: '10px'}}>
          <button onClick={oneParagraphOnPageHandler} className={styles.itemButton}
                  type="button">По абзацу на страницу
          </button>
          <button onClick={twoParagraphsOnPageHandler} className={styles.itemButton}
                  type="button">По два абзаца на страницу
          </button>
          <button onClick={splitByChunksHandler} className={styles.itemButton}
                  type="button">Разбить на фрагменты
          </button>
          <input type="number" value={chunkSize} onChange={e => setChunkSize(Number(e.target.value))}/>
        </div>
        <button onClick={createRepetitionStoryHandler} className="button button--default button--outline"
                type="button">Создать историю для чтения
        </button>
      </div>
      <div style={{display: 'flex', flexDirection: 'column', height: '100%', overflowY: 'auto', flex: '1'}}>
        {pages.length === 0 ?
          <div style={{color: 'black', width: '100%', height: '100%', flexDirection: 'column', display: 'flex', alignItems: 'center', justifyContent: 'center'}}>
            <p>Выберите как разбить текст</p>
            <ul style={{display: 'flex', flexDirection: 'column', gap: '10px'}}>
              <li><b>По абзацу на страницу</b> - подойдет для текстов (стихов) где каждый абзац уже отделен дополнительным переносом строки</li>
              <li><b>По два абзаца на страницу</b> - работает также, как и первый вариант, только на страницу (слайд) добавляется по два абзаца. Подойдет если абзацы небольшие. Когда нужно уменьшить количество слайдов/ментальных карт</li>
              <li><b>Разбить на фрагменты</b> - подойдет для сплошного текста, когда нет разделения на абзацы. Можно указать количество символов (1500 по умолчанию)</li>
            </ul>
            <p>Текст можно редактировать. Таким способом можно обработать большой текст за раз. Из разбитого на страницы текста можно сформировать историю для чтения, нажав на соответствующую кнопку.</p>
          </div>
        : <div style={{
          flex: '1',
          display: 'flex',
          backgroundColor: '#eee',
          padding: '10px',
          flexDirection: 'column',
          gap: '10px',
          color: 'black',
          whiteSpace: "pre-wrap",
        }}>
          {pages.map((pageText, i) =>
            <Page
              key={i}
              text={pageText}
              pageIndex={i}
              onInput={handleInput}
            />)}
        </div>
        }
      </div>
    </Dialog>
  );
});

export default TextEditorDialog;

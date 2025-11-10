import React from "react";
import styles from './Sidebar.module.css'
import {useThreadContext} from "../../Context/ThreadProvider";
import {useQueryState} from "nuqs";

function SidebarComponent() {
  const {threadsData, messagesData} = useThreadContext();
  const {userThreads, deleteThread} = threadsData;
  const {switchSelectedThread, setMessages} = messagesData;
  const [_threadId, setThreadId] = useQueryState('id');

  const changeThreadHandler = thread => switchSelectedThread(thread);

  const deleteThreadHandler = async (threadId) => {
    if (!confirm('Подтверждаете?')) {
      return;
    }
    setMessages([]);
    deleteThread(threadId, () => setMessages([]));
  };

  const createThreadHandler = () => {
    setThreadId(null);
    setMessages([]);
  }

  return (
    <span>
    <div className={styles.sidebar}>
      <div className={styles.inner}>
        <div className={styles.header}>
          <div
            style={{display: 'flex', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center'}}>
            <div>
              <a style={{
                color: 'oklch(95% 0 0)', margin: '8px 0', fontWeight: 500, fontSize: '18px', lineHeight: '28px'
              }} href="/admin/index.php?r=story">Назад</a>
            </div>
          </div>
          <div style={{
            display: 'flex', width: '100%', alignItems: 'center', justifyContent: 'center', padding: '10px 0'
          }}>
            <button onClick={createThreadHandler} className={styles.itemButton} type="button">Новая история</button>
          </div>
        </div>
        <div className={styles.content}>
          <div className={styles.contentItem}>
            {userThreads.length === 0 ? <div style={{
              color: 'oklch(60% 0 0)',
              textAlign: 'center',
              fontSize: '14px',
              marginInline: '12px',
              lineHeight: '20px',
              paddingBlock: '32px',
              paddingInline: '24px'
            }}>Нет историй</div> : <>
              <h3 className={styles.datePeriod}>Last 7 days</h3>
              <div style={{display: 'flex', flexDirection: 'column', gap: '4px'}}>
                {userThreads.map((thread, i) => <div key={i}
                                                     className={styles.itemWrap + ' ' + (thread.id === _threadId ? styles.active : '')}>
                  <div title={thread.title} style={{flex: '1', minWidth: '0'}}
                       onClick={() => changeThreadHandler(thread)}>
                    <div
                      style={{
                        fontWeight: '500', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap'
                      }}>
                      {thread.title}
                    </div>
                    <div
                      style={{color: 'oklch(60% 0 0)', fontSize: '12px', lineHeight: '16px', marginTop: '2px'}}>
                      {thread.updatedAt}
                    </div>
                  </div>
                  <button onClick={() => deleteThreadHandler(thread.id)} type="button"
                          className={styles.trashBtn}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                         className={styles.trashSvg}>
                      <path d="M3 6h18"></path>
                      <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                      <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                      <line x1="10" x2="10" y1="11" y2="17"></line>
                      <line x1="14" x2="14" y1="11" y2="17"></line>
                    </svg>
                  </button>
                </div>)}
              </div>
            </>}
          </div>
        </div>
      </div>
    </div>
    </span>
  );
}

export const Sidebar = React.memo(SidebarComponent);

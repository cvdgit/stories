import React, {useEffect, useRef} from "react";
import "./ChatSidebar.css"

function ChatSidebar({ isOpen, onClose, messages, draft, onDraftChange, onSubmit, onAttach, onOpenFilePicker, fileInputRef, onCreateMap, isLoading, error }) {
  const messagesContainerRef = useRef(null);

  useEffect(() => {
    if (messagesContainerRef.current) {
      messagesContainerRef.current.scrollTop = messagesContainerRef.current.scrollHeight;
    }
  }, [messages, isLoading, error]);

  return (
    <>
      <div className={`sidebar-backdrop ${isOpen ? 'active' : ''}`} onClick={onClose} />

      <aside className={`chat-sidebar ${isOpen ? 'open' : ''}`} aria-hidden={!isOpen}>
        <div className="chat-sidebar__header">
          <div>
            <p className="eyebrow">ИИ-ассистент</p>
            <h2>Чат</h2>
          </div>
          <button className="icon-btn" type="button" aria-label="Закрыть чат" onClick={onClose}>
            ×
          </button>
        </div>

        <div className="chat-sidebar__messages" ref={messagesContainerRef}>
          {messages.map((message, index) => (
            <div key={`${message.role}-${index}`} className={`message-bubble ${message.role}`}>
              {message.text}
            </div>
          ))}
          {isLoading && <div className="message-bubble ai">Идёт ответ…</div>}
          {error && <div className="message-bubble ai">{error}</div>}
        </div>

        <div className="chat-sidebar__composer">
          <form onSubmit={onSubmit}>
            <label className="sr-only" htmlFor="messageInput">Введите сообщение</label>
            <textarea
              id="messageInput"
              rows="3"
              placeholder="Введите сообщение..."
              value={draft}
              onChange={onDraftChange}
            />
            <div className="composer__actions">
              <button className="ghost-btn" type="button" onClick={onOpenFilePicker}>
                📎 Файл
              </button>
              <input ref={fileInputRef} type="file" hidden onChange={onAttach} />
              <button className="primary-btn" type="submit">
                Отправить
              </button>
            </div>
          </form>
          <div className="composer__footer">
            <button className="secondary-btn" type="button" onClick={onCreateMap}>
              Создать ментальную карту
            </button>
          </div>
        </div>
      </aside>
    </>
  );
}

export default ChatSidebar;

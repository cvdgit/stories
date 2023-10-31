import React, {useEffect} from 'react'
import {Avatar, Icon, Textarea, Loading, Tooltip, Button} from '../../ui'
import {useGlobal} from "../../context";
import {useMessage} from "../../hooks/useMessage";
import {useOptions} from "../../hooks/useOptions";
import avatar from '../../images/avatar-gpt.png'
import {ScrollView} from "../ScrollView/ScrollView";
import {useSendKey} from "../../hooks/useSendKey";
import "./ChatMessage.css";
import {MessageRender} from "../MessageRender/MessageRender";
import {dateFormat} from "../../utils";
import {ChatError} from "../ChatError/ChatError";

export function MessageHeader() {
  const {is, setIs, clearMessage, options} = useGlobal()
  const {message} = useMessage()
  const {messages = []} = message || {}
  const columnIcon = is?.sidebar ? 'column-close' : 'column-open'
  const {setGeneral} = useOptions()
  return (
    <div className="message-header">
      <div className="header-title">
        {message?.title || "Conversation name"}
        <div className="messages-number">{messages.length} сообщений</div>
      </div>
    </div>
  )
}

export function EditorMessage() {
  return (
    <div>
      <Textarea rows="3"/>
    </div>
  )
}

export function MessageItem(props) {
  const {content, sentTime, role, index} = props
  const {removeMessage} = useGlobal()
  return (
    <div className={`message-item role-${role}`}>
      <Avatar src={role !== 'user' && avatar}/>
      <div className={`message-item__content message-item__content--${role}`}>
        <div className="message-item__inner">
          <div className="message-item__tool">
            <div className="message-item__date">{dateFormat(sentTime)}</div>
            <div className="message-item__bar">
              <Tooltip text="Remove Messages">
                <Icon className="icon" type="trash" onClick={() => removeMessage(index)}/>
              </Tooltip>
            </div>
          </div>
          <MessageRender>
            {content}
          </MessageRender>
        </div>
      </div>
    </div>
  )
}

export function MessageBar() {
  const {sendMessage, setMessage, is, options, setIs, typingMessage, clearTyping, stopResponse} = useGlobal()
  useSendKey(sendMessage, options.general.command)
  return (
    <div className="message-bar">
      {is.thinking && (
        <div className="message-bar__tool">
          <div className="message-bar__loading">
            <div className="flex-c"><span>Thinking</span> <Loading/></div>
            <Button size="min" className="stop" onClick={stopResponse} icon="stop">Отменить</Button>
          </div>
        </div>
      )}
      <div className="message-bar__inner">
        <div className="message-bar__type">
          <Textarea transparent={true} rows="3" value={typingMessage?.content || ''}
                    onFocus={() => setIs({inputting: true})} onBlur={() => setIs({inputting: false})}
                    placeholder="Введите что-нибудь...." onChange={setMessage}/>
        </div>
        <div className="message-bar__icon">
          {typingMessage.content && (
            <Tooltip text="clear">
              <Icon className="icon" type="cancel" onClick={clearTyping}/>
            </Tooltip>
          )}
          <Icon className="icon" type="send" onClick={sendMessage}/>
        </div>
      </div>
    </div>
  )
}

export function MessageContainer() {
  const {options} = useGlobal()
  const {message} = useMessage()
  const {messages = []} = message || {}
  if (options?.openai?.apiKey) {
    return (
      <>
        {
          messages.length ? (
            <div className="messages-container">
              {messages.map((item, index) => <MessageItem index={index} key={index} {...item} />)}
              {message?.error && <ChatError/>}
            </div>
          ) : (
            <div className="no-messages">Нет сообщений</div>
          )
        }
      </>
    )
  } else {
    return (
      <div>No API key</div>
    )
  }
}

export function ChatMessage() {
  const {is, chat} = useGlobal();
  return (
    <div className="message">
      {chat.length ? (
        <>
          <MessageHeader/>
          <ScrollView>
            <MessageContainer/>
            {is?.thinking && <Loading/>}
          </ScrollView>
          <MessageBar/>
        </>
      ) : (
        <div className="no-conversions">
          <div>Создайте новый разговор</div>
        </div>
      )}
    </div>
  )
}

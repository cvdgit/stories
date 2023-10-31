import React, {useEffect, useState} from 'react'
import {Icon, Title, Textarea, Button} from '../../ui'
import {useGlobal} from "../../context";
import "./ChatList.css";

export function ListEmpty() {
  return (
    <div className="flex-column">
      <Icon type="message"/>
      <Title type="h3">Разговоры не найдены<br/>Начните новый разговор</Title>
    </div>
  )
}

export function ListTool(props) {
  const {removeChat, setState} = useGlobal();
  return (
    <div className="conversation-tool">
      <Icon className="icon" type="editor" onClick={() => setState({currentEditor: props.index})}/>
      <Icon className="icon" type="close" onClick={() => removeChat(props.index)}/>
    </div>
  )
}

export function CreateNew() {
  const {newChat} = useGlobal();
  return (
    <div className="conversation-new" onClick={newChat}>
      <Icon type="add"/>Новый разговор
    </div>
  );
}

export const TagIco = React.forwardRef(({ico, color, ...rest}, ref) => <div ref={ref} {...rest}
                                                                            className={`conversation-icon ico-${ico}`}
                                                                            style={{
                                                                              color: '#fff',
                                                                              backgroundColor: `var(--tag-color-${color})`
                                                                            }}/>)

export function EditItem(props) {
  const {modifyChat} = useGlobal();
  const [title, setVal] = useState(props.title);
  const [icon, setIcon] = useState(props.icon);
  const [color, ico] = icon || [1, 'files'];
  return (
    <div className="conversation-edit">
      <h2 className="conversation-edit__title">
        <TagIco ico={ico} color={color}/>
        <span className="conversation-edit__text">Редактировать</span>
      </h2>
      <div className="conversation-edit__wrap">
        <Textarea rows={'3'} className="editor_text" value={title} onChange={value => setVal(value)}/>
      </div>
      <div className="conversation-edit__bar">
        <Button onClick={() => modifyChat({title, icon}, props.index)} block={true} type="primary">Сохранить</Button>
      </div>
    </div>
  )
}

export function ChatItem(props) {
  const {icon} = props;
  const [color, ico] = icon || [1, 'files'];
  const {setState, currentChat, currentEditor} = useGlobal();
  const item = (
    <>
      <TagIco ico={ico} color={color}/>
      <div className="title-container">
        <div className="conversation-title">
          <div className="conversation-title__text">{props.title}</div>
        </div>
        <div className="conversation-title__messages">{props.messages.length} сообщений</div>
      </div>
      <ListTool index={props.index}/>
    </>
  );

  return (
    <div className={`conversation-item ${currentChat === props.index && "current"}`}
         onClick={() => setState({currentChat: props.index})}>
      {currentEditor === props.index ? <EditItem {...props} /> : item}
    </div>
  )
}

export function ChatList() {
  const {chat} = useGlobal();
  return (
    <div className="conversations">
      {chat?.length ? chat.map((item, index) => <ChatItem key={index} index={index} {...item} />) : <ListEmpty/>}
      <CreateNew/>
    </div>
  )
}

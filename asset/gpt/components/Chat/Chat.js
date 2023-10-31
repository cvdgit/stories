import React from "react";
import "./Chat.css";
import {ScrollView} from "../ScrollView/ScrollView";
import {ChatList} from "../ChatList/ChatList";
import {ChatMessage} from "../ChatMessage/ChatMessage";
import {Avatar} from "../../ui";

function Chat() {

  return (
    <div className="chat chat-full">
      <div className="chat_inner">
        <div className="panel flex-c-sb flex-column">
          <Avatar />
        </div>
        <div className="sider">
          <div className="search">
            <div className="search-inner">
              <div className="search-container"></div>
            </div>
          </div>
          <ScrollView>
            <ChatList/>
          </ScrollView>
        </div>
        <ChatMessage/>
      </div>
    </div>
  );
}

export default Chat;

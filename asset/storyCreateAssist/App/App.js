import React, {useEffect, useRef} from "react";
import styles from './App.module.css'
import {Sidebar} from "./Sidebar";
import Content from "./Content";
import {useThreadContext} from "../Context/ThreadProvider";
import {useQueryState} from "nuqs";

function AppComponent() {
  const {threadsData, messagesData} = useThreadContext();
  const {getThreadById} = threadsData;
  const {messages, switchSelectedThread} = messagesData;
  const [threadId, setThreadId] = useQueryState('id');

  const hasCheckedThreadIdParam = useRef(false);
  useEffect(() => {
    if (typeof window === "undefined" || hasCheckedThreadIdParam.current) {
      return;
    }

    if (!threadId) {
      hasCheckedThreadIdParam.current = true;
      return;
    }

    hasCheckedThreadIdParam.current = true;

    try {
      getThreadById(threadId).then((thread) => {
        if (!thread) {
          setThreadId(null);
          return;
        }
        switchSelectedThread(thread);
        hasCheckedThreadIdParam.current = false;
      });
    } catch (e) {
      console.error("Failed to fetch thread in query param", e);
      setThreadId(null);
    }
  }, [/*getThreadById, setThreadId, switchSelectedThread,*/ threadId]);

  return (
    <>
      <Sidebar/>
      <Content messages={messages}/>
    </>
  )
}

export const App = React.memo(AppComponent);

import React, {
  useRef,
  useEffect,
  useReducer,
  useContext,
  createContext, useState,
} from "react";
import {initState} from "./initState";
import reducer from "./reducer";
import action from "./actions";

export const ChatContext = createContext(null);
export const MessagesContext = createContext(null);

export const ChatProvider = ({children}) => {

  const init = {...initState};
  const [state, dispatch] = useReducer(reducer, init);
  const actionList = action(state, dispatch);
  const latestState = useRef(state);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    latestState.current = state;
  }, [state]);

  useEffect(() => {

    const fetchData = async () => {
      const response = await fetch(`/admin/index.php?r=gpt/chat/get-data`, {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return response.json();
    }

    fetchData().then(data => {
      dispatch({type: "SET_STATE", payload: data});
      setLoading(true);
    })

  }, []);

  /*useEffect(() => {
    const stateToSave = latestState.current;
  }, [latestState.current]);*/

  return (
    <ChatContext.Provider value={{...state, ...actionList}}>
      <MessagesContext.Provider value={dispatch}>
        {children}
      </MessagesContext.Provider>
    </ChatContext.Provider>
  );
};

export const useGlobal = () => useContext(ChatContext);
export const useMessages = () => useContext(MessagesContext);

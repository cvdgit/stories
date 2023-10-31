import React, {Suspense} from "react";
import {ChatProvider} from "../context";
import Chat from "../components/Chat";
//const Chat = React.lazy(() => import("../components/Chat"))

function App() {
  return (
    <Suspense fallback={"Загрузка..."}>
      <ChatProvider>
        <Chat/>
      </ChatProvider>
    </Suspense>
  );
}

export default App;

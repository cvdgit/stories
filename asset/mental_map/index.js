import ReactDOM from "react-dom/client";
import React from "react";
import App from "./components/App/App"
import "./index.css"

const root = ReactDOM.createRoot(
  document.getElementById('app')
);
root.render(
  <App mentalMapId={window?.mentalMapId}/>
);

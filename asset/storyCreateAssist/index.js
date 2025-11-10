import React from 'react';
import {createRoot} from 'react-dom/client';
import './index.css';
import styles from "./App/App.module.css";
import {ThreadProvider} from "./Context/ThreadProvider";
import {App} from "./App";
import {NuqsAdapter} from "nuqs/adapters/react";

const root = createRoot(document.getElementById('app'));
root.render(
  <NuqsAdapter>
    <div className={styles.inner}>
      <main className={styles.main}>
        <div className={styles.wrap}>
          <React.Suspense fallback={null}>
            <ThreadProvider>
              <App/>
            </ThreadProvider>
          </React.Suspense>
        </div>
      </main>
    </div>
  </NuqsAdapter>
);

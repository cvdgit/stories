<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 */

$this->registerCss(
    <<<CSS

html {
    height: 100%;
}

body {
    height: 100%;
    min-height: 100%;
    position: relative;
    margin: 0;
    font-family: "Roboto", "sans-serif";
}

button,input,optgroup,select,textarea {
    font-family: inherit;
    font-feature-settings: inherit;
    font-variation-settings: inherit;
    font-size: 100%;
    font-weight: inherit;
    line-height: inherit;
    color: inherit;
    margin: 0;
    padding: 0
}

.textarea-input {
    width: 100%;
    /*height: 40px;*/
    font-size: 16px;
    -webkit-padding-start: 16px;
    padding-inline-start: 16px;
    -webkit-padding-end: 16px;
    padding-inline-end: 16px;
    border-radius: 6px;
    min-width: 0px;
    outline: 2px solid transparent;
    outline-offset: 2px;
    position: relative;
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    appearance: none;
    transition-property: background-color,border-color,color,fill,stroke,opacity,box-shadow,transform;
    transition-duration: 200ms;
    padding-top: 8px;
    padding-bottom: 8px;
    min-height: unset;
    line-height: 1.375;
    vertical-align: top;
    border: 1px solid;
    background: inherit;
    overflow: auto;
    resize: none;
    margin-right: 56px;
    border-color: rgb(58, 58, 61);
}
.message-item {
    display: flex;
    -webkit-box-align: start;
    align-items: start;
    flex-direction: column;
    gap: 20px;
    padding-bottom: 20px;
}
.message-images {
    display: flex;
    flex-direction: row;
    gap: 20px;
}
@keyframes bounce {
  0% {
    transform: translateY(0%);
    opacity: 0;
  }
  50% {
    transform: translateY(100%);
    opacity: 0.5;
  }
  100% {
    transform: translateY(0%);
    opacity: 1;
  }
}

.loading {
  color: var(--text-color);
}

.loading-inner {
  display: flex;
  align-items: center;
  justify-content: center;
}

.loading-line {
  display: flex;
  align-items: center;
}

.loading-bar {
  background: var(--text-color-gray);
  margin: 0 2px;
}

.loading-inner.circle .loading-bar {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  animation: bounce 0.6s ease-in-out infinite;
  opacity: 0;
}

.loading-inner.circle .loading-bar:nth-last-child(4) {
  animation-delay: 0.4s;
}
.loading-inner.circle .loading-bar:nth-last-child(3) {
  animation-delay: 0.3s;
}
.loading-inner.circle .loading-bar:nth-last-child(2) {
  animation-delay: 0.2s;
}
.loading-inner.circle .loading-bar:nth-last-child(1) {
  animation-delay: 0.1s;
}

.textarea-wrap {
    width: 100%;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    position: relative;
    isolation: isolate;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    box-sizing: border-box;
    word-wrap: break-word;
    border: 0 solid #e5e7eb;
}
.offer-line {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: grid;
    gap: 32px;
    -webkit-box-flex: 1;
    -webkit-flex-grow: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    margin-top: 25px;
    max-width: 800px;
    width: 100%;
    grid-template-columns: 1fr 1fr;
}
.offer-line-item {
    display: flex;
    padding: 20px;
    border: 1px #E2E8F0 solid;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1),0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border-radius: 6px;
    color: #E2E8F0;
    background-color: rgb(58, 58, 61);
    cursor: pointer;
    font-size: 20px;
    font-weight: 500;
    line-height: 1.2;
    justify-content: center;
    align-items: center;
}
.offer-line-item:hover {
    background-color: rgb(78,78,81);
}

.send-btn {
    right: 0;
    width: 40px;
    height: 100%;
    font-size: 16px;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    -webkit-justify-content: center;
    justify-content: center;
    position: absolute;
    top: 0px;
    z-index: 2;
}
.send-btn button {
    display: inline-flex;
    appearance: none;
    align-items: center;
    justify-content: center;
    user-select: none;
    position: relative;
    white-space: nowrap;
    vertical-align: middle;
    outline: 2px solid transparent;
    outline-offset: 2px;
    line-height: 1.2;
    border-radius: 9999px;
    font-weight: 600;
    transition-property: background-color,border-color,color,fill,stroke,opacity,box-shadow,transform;
    transition-duration: 200ms;
    height: 40px;
    min-width: 40px;
    font-size: 16px;
    padding-inline-start: 16px;
    padding-inline-end: 16px;
    background: #3182ce;
    color: #ffffff;
    padding: 0px;
    border: 0 none;
    cursor: pointer;
}
.send-btn button:hover {
    background-color: #2b6cb0;
}
.send-btn button svg {
    width: 16px;
    height: 16px;
    display: inline-block;
    line-height: 1em;
    flex-shrink: 0;
    color: currentColor;
    vertical-align: middle;
}
CSS
);

$this->registerJs($this->renderFile("@frontend/views/story/_chat.js"));
?>
<div style="display: flex; padding: 32px; flex-direction: column; height: 100%">
    <div
        style="display: flex; flex-grow: 1; align-items: center; flex-direction: column; max-height: 100%; padding: 32px">
        <div
            style="display: flex; flex-direction: column; -webkit-box-align: center; align-items: center; padding-bottom: 20px">
            <h2 style="line-height: 1.2">Задай вопрос по истории Руси</h2>
        </div>
        <div id="message-container"
             style="overflow: auto; display: flex; flex-direction: column-reverse; width: 100%; margin-bottom: 8px">
            <div id="message-offers"
                 style="display: flex; flex-direction: column; max-width: 100%; align-items: center; padding: 32px">
                <div class="offer-line">
                    <div class="offer-line-item">Где жили словене?</div>
                    <div class="offer-line-item">Расскажи про ладожское озеро</div>
                    <div class="offer-line-item">Где жили словене?</div>
                    <div class="offer-line-item">Где жили словене?</div>
                </div>
            </div>
        </div>
        <div class="textarea-wrap">
            <textarea rows="1" id="send-message" class="textarea-input" placeholder="Какой город построили ильменские словене?"></textarea>
            <div class="send-btn">
                <button type="submit" id="send-message-btn">
                    <svg viewBox="0 0 24 24" focusable="false" class="chakra-icon css-onkibi" aria-hidden="true">
                        <path fill="currentColor"
                              d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

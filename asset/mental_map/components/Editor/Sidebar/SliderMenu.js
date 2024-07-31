import React, {useState} from "react";
import {MenuToggleButton, ToggleMenu, ToggleMenuContent, ToggleMenuItem} from "../../Menu";

function SliderMenu({lessonId, createQuestionHandler, setActiveQuestion}) {

  const [toggle, setToggle] = useState(false);

  const toggleHandler = () => {
    setToggle(!toggle);
  }

  const multipleChoiceIcon = (
    <svg viewBox="0 0 16 16" width="16" height="16" className="i i-multipleChoice" focusable="false"><title>Multiple
      Choice</title>
      <desc>Stacked circles to the left of stacked lines</desc>
      <path d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16zm0-2A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path>
      <circle cx="7.904" cy="7.904" r="1.904"></circle>
    </svg>
  );

  const multipleResponseIcon = (
    <svg viewBox="0 0 16 16" width="16" height="16" className="i i-multipleResponse" focusable="false"><title>Multiple
      Response</title>
      <desc>A box with a checkmark in it</desc>
      <path
        d="M15.142.332c.6 0 1 .4 1 1v14c0 .6-.4 1-1 1h-14c-.6 0-1-.4-1-1v-14c0-.6.4-1 1-1h14zm-1 14v-12h-12v12h12zm-7.978-3.308l-2-2-.707-.708L4.87 6.902l.707.707 1.3 1.3 3.317-3.33.707-.708 1.414 1.414-.707.707-4.03 4.032-.708.707-.707-.707z"></path>
    </svg>
  );

  const addMultipleChoiceQuestionHandler = () => {
    /*const question = CreateMultipleChoiceQuestion();
    createQuestionHandler(question);
    setActiveQuestion(question);*/
    setToggle(false);
  }

  const addMultipleResponseQuestionHandler = () => {
    /*const question = CreateMultipleResponseQuestion();
    createQuestionHandler(question);
    setActiveQuestion(question);*/
    setToggle(false);
  }

  return (
    <ToggleMenu toggle={toggle} classNames="menu--full">
      <MenuToggleButton classNames="button button--sidebar-basic"
                        toggleHandler={toggleHandler}>Загрузить изображения</MenuToggleButton>
      <div>
        <ToggleMenuContent classNames="menu__content--top menu__content--centerX sidebar-list__menu">
          <ToggleMenuItem
            title="Из файла"
            icon={multipleChoiceIcon}
            clickHandler={addMultipleChoiceQuestionHandler}
          />
        </ToggleMenuContent>
      </div>
    </ToggleMenu>
  );
}

export default SliderMenu;

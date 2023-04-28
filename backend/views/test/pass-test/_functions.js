
function fragmentElementBuilder(type) {

  const createDefaultElement = () => {
    const element = document.createElement("span");
    element.className = "dropdown";
    element.setAttribute('contenteditable', false);
    element.innerHTML = '<button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown"></button><ul class="dropdown-menu"></ul>';
    return element;
  };

  const createRegionElement = () => {
    const element = document.createElement("span");
    element.className = "";
    element.setAttribute('contenteditable', false);
    element.innerHTML = '<button type="button" class="btn btn-default highlight select-region"></button>';
    return element;
  };

  if (type === 'region') {
    return createRegionElement();
  }

  return createDefaultElement();
}

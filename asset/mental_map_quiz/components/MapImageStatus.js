function MapImageStatus() {
}

/**
 * @typedef {Object} Props
 * @property {int|null} hiding
 * @property {int|null} hidingPrev
 * @property {int|null} seconds
 */

/**
 *
 * @param {number} seconds
 * @return {string}
 */
function formatTime(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  const paddedSecs = secs.toString().padStart(2, '0');
  return mins > 0 ? `${mins}:${paddedSecs}` : secs.toString();
}

/**
 * @param {Props} param0
 */
MapImageStatus.render = function ({hiding, seconds, hidingPrev}) {

  let hidingLabel = '';
  hiding = Number(hiding);
  if (hiding > 0) {
    hidingLabel = `${hiding}%`;
  }

  let secondsLabel = '';
  seconds = Number(seconds);
  if (seconds > 0) {
    secondsLabel = formatTime(seconds);
  }

  let hidingAddLabel = '';
  const hidingAdd = hiding - Number(hidingPrev);
  if (hidingAdd > 0) {
    hidingAddLabel = `+${hidingAdd}%`;
  }

  const element = document.createElement('div');
  element.classList.add('map-user-status');
  element.innerHTML = `
<div class="map-user-status-hiding-wrap">
<span class="map-user-status-hiding" data-value="${hiding}">${hidingLabel}</span>
<span class="map-user-status-hiding-add">${hidingAddLabel}</span>
</div>
<span class="map-user-status-time" data-value="${seconds}">${secondsLabel}</span>
`;
  return element;
}

/**
 * @param {HTMLElement} container
 * @param {Props} param1
 */
MapImageStatus.update = function (container, {hiding, seconds, hidingPrev, statClickHandler}) {
  const hidingElem = container.querySelector('.map-user-status-hiding');
  if (hidingElem) {
    hidingElem.innerHTML = '';
    hidingElem.setAttribute('data-value', '');
    const currentHiding = Number(hidingElem.getAttribute('data-value'));
    if (Number(hiding) > currentHiding) {
      hidingElem.setAttribute('data-value', hiding);
      hidingElem.innerHTML = hiding + '%';
    }

    /*$(container.querySelector('.map-user-status-hiding-wrap')).off('click');
    if (hidingElem.innerHTML !== '' && typeof statClickHandler === 'function') {
      container.classList.add('map-user-status-stat')
      $(container.querySelector('.map-user-status-hiding-wrap')).on('click', statClickHandler);
    }*/
  }
  const hidingAddElem = container.querySelector('.map-user-status-hiding-add');
  if (hidingAddElem) {
    hidingAddElem.innerHTML = '';
      let hidingAddLabel = '';
    const hidingAdd = hiding - Number(hidingPrev);
    if (hidingAdd > 0) {
      hidingAddLabel = `+${hidingAdd}%`;
    }
    hidingAddElem.innerHTML = hidingAddLabel;
  }
  const secondsElem = container.querySelector('.map-user-status-time');
  if (secondsElem) {
    secondsElem.innerHTML = '';
    secondsElem.setAttribute('data-value', '');
    const currentSeconds = Number(secondsElem.getAttribute('data-value'));
    if (Number(seconds) > currentSeconds) {
      secondsElem.setAttribute('data-value', seconds);
      secondsElem.innerHTML = formatTime(seconds);
    }
  }
}

MapImageStatus.updateAllClosedValue = function(container, {hiding, hidingPrev}) {
  const allElem = container.querySelector('.map-user-status-all');
  if (allElem) {
    allElem.innerHTML = '';
    allElem.setAttribute('data-value', '');
    const currentHiding = Number(allElem.getAttribute('data-value'));
    if (Number(hiding) > currentHiding) {
      allElem.setAttribute('data-value', hiding);
      allElem.innerHTML = hiding + '%';
    }
  }
  const allAddElem = container.querySelector('.map-user-status-all-add');
  if (allAddElem) {
    allAddElem.innerHTML = '';
    let hidingAddLabel = '';
    const hidingAdd = hiding - Number(hidingPrev);
    if (hidingAdd > 0) {
      hidingAddLabel = `+${hidingAdd}%`;
    }
    allAddElem.innerHTML = hidingAddLabel;
  }
}

MapImageStatus.renderDialog = function({hiding, hidingPrev, hidingClickHandler}, {all, allPrev, allClickHandler}) {

  let hidingLabel = '0%';
  hiding = Number(hiding);
  if (hiding > 0) {
    hidingLabel = `${hiding}%`;
  }

  let hidingAddLabel = '';
  const hidingAdd = hiding - Number(hidingPrev);
  if (hidingAdd > 0) {
    hidingAddLabel = `+${hidingAdd}%`;
  }

  let allLabel = '0%';
  all = Number(all);
  if (all > 0) {
    allLabel = `${all}%`;
  }

  let allAddLabel = '';
  const allAdd = all - Number(allPrev);
  if (allAdd > 0) {
    allAddLabel = `+${allAdd}%`;
  }

  const element = document.createElement('div');
  element.classList.add('map-user-status');
  element.innerHTML = `
<div class="map-user-status-hiding-wrap" data-toggle="tooltip" title="Проговорить текст и диалогом">
<span class="map-user-status-hiding" data-value="${hiding}">${hidingLabel}</span>
<span class="map-user-status-hiding-add">${hidingAddLabel}</span>
</div>
<div class="map-user-status-all-wrap" data-toggle="tooltip" title="Проговорить в режиме презентации">
<span class="map-user-status-all" data-value="${all}">${allLabel}</span>
<span class="map-user-status-all-add">${allAddLabel}</span>
</div>
`;

  element.querySelector('.map-user-status-hiding-wrap')
    .addEventListener('click', hidingClickHandler)
  element.querySelector('.map-user-status-all-wrap')
    .addEventListener('click', allClickHandler)

  return element;
}

export default MapImageStatus;

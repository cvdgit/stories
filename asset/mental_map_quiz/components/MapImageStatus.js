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
<div>
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
MapImageStatus.update = function (container, {hiding, seconds, hidingPrev}) {
  const hidingElem = container.querySelector('.map-user-status-hiding');
  if (hidingElem) {
    const currentHiding = Number(hidingElem.getAttribute('data-value'));
    if (Number(hiding) > currentHiding) {
      hidingElem.setAttribute('data-value', hiding);
      hidingElem.innerHTML = hiding + '%';
    }
  }
  const hidingAddElem = container.querySelector('.map-user-status-hiding-add');
  if (hidingAddElem) {
    let hidingAddLabel = '';
    const hidingAdd = hiding - Number(hidingPrev);
    if (hidingAdd > 0) {
      hidingAddLabel = `+${hidingAdd}%`;
    }
    hidingAddElem.innerHTML = hidingAddLabel;
  }
  const secondsElem = container.querySelector('.map-user-status-time');
  if (secondsElem) {
    const currentSeconds = Number(secondsElem.getAttribute('data-value'));
    if (Number(seconds) > currentSeconds) {
      hidingElem.setAttribute('data-value', seconds);
      secondsElem.innerHTML = formatTime(seconds);
    }
  }
}

export default MapImageStatus;

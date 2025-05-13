function findBgClassName(bg) {
  if (!bg || bg === '') {
    return '';
  }
  return `fragment-bg-${bg.toLowerCase()}`;
}

export default function MentalMapImage(
  mapImageUrl,
  mapImageWidth,
  mapImageHeight,
  images,
  imageClickHandler,
  history
) {

  const zoomWrap = document.createElement('div')
  zoomWrap.classList.add('zoom-wrap')
  zoomWrap.style.width = mapImageWidth
  zoomWrap.style.height = mapImageHeight

  const img = document.createElement('img')
  img.src = mapImageUrl
  img.style.height = '100%'
  zoomWrap.appendChild(img)

  images.map(image => {

    const mapImgWrap = document.createElement('div')
    mapImgWrap.dataset.imgId = image.id
    mapImgWrap.classList.add('mental-map-img')
    mapImgWrap.style.position = 'absolute'
    mapImgWrap.style.width = image.width + 'px'
    mapImgWrap.style.height = image.height + 'px'
    mapImgWrap.style.left = '0px'
    mapImgWrap.style.top = '0px'
    mapImgWrap.style.transform = `translate(${image.left}px, ${image.top}px)`
    mapImgWrap.addEventListener('click', () => {
      imageClickHandler(image)
    })

    let mapFragment;
    if (image.url) {
      mapFragment = document.createElement('img')
      mapFragment.src = image.url
    } else {
      mapFragment = document.createElement('div')
      mapFragment.classList.add('map-fragment')
      mapFragment.style.height = '100%'
      const bgClassName = findBgClassName(image.bg)
      if (bgClassName) {
        mapFragment.classList.add(bgClassName)
      }
    }

    mapFragment.dataset.trigger = 'hover'
    mapFragment.dataset.placement = 'auto'
    mapFragment.dataset.container = 'body'
    mapFragment.setAttribute('title', image.text.replace(/<[^>]*>?/gm, ''))
    mapFragment.classList.add('map-img')

    const doneElem = document.createElement('div')
    doneElem.classList.add('done-elem')
    doneElem.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
</svg>`
    mapImgWrap.appendChild(doneElem)

    const historyItem = history.find(h => h.id === image.id)
    if (historyItem?.done) {
      mapImgWrap.classList.add('fragment-item-done')
    }

    mapImgWrap.appendChild(mapFragment)

    zoomWrap.appendChild(mapImgWrap)
  })

  return zoomWrap
}

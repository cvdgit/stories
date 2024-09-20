export default function MentalMapImage(
  mapImageUrl,
  mapImageWidth,
  mapImageHeight,
  images,
  imageClickHandler
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
    const mapImg = document.createElement('img')
    mapImg.setAttribute('title', image.text.replace(/<[^>]*>?/gm, ''))
    mapImg.dataset.trigger = 'hover'
    mapImg.dataset.placement = 'auto'
    mapImg.dataset.container = 'body'
    mapImg.src = image.url
    mapImgWrap.appendChild(mapImg)
    zoomWrap.appendChild(mapImgWrap)
  })

  return zoomWrap
}

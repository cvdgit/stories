import './MentalMapProgress.css'

export default function MentalMapProgress(initProgress = 0) {
  const element = document.createElement('div')
  element.className = 'progress-wrap'
  element.innerHTML = `<div class="progress-wrap">
                <div class="map-progress">
                    <div class="progress__container">
                        <div class="progress__container-indicator" style="transform: translate3d(${initProgress}%, 0px, 0px);"></div>
                    </div>
                </div>
            </div>`
  return {
    render() {
      return element
    },
    setProgress(progress) {
      if (isNaN(progress)) {
        throw new Error('Progress value must be number')
      }
      console.log('setProgress', progress)
      element.querySelector('.progress__container-indicator').style.transform = `translate3d(${progress}%, 0px, 0px)`
    }
  }
}

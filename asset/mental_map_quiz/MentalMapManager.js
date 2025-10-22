import MentalMap from "./MentalMap";

export default function MentalMapManager() {
  const instances = {}
  return {
    create(element, deck, params, slideId, microphoneChecker) {
      const instance = new MentalMap(element, deck, params, microphoneChecker)
      instances[slideId] = instance
      return instance
    },
    getInstance(slideId) {
      return instances[slideId]
    }
  }
}

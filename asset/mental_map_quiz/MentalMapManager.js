import MentalMap from "./MentalMap";

export default function MentalMapManager() {

  const instances = {}

  return {
    create(element, deck, params, slideId) {
      const instance = new MentalMap(element, deck, params)
      instances[slideId] = instance
      return instance
    },
    getInstance(slideId) {
      return instances[slideId]
    }
  }
}

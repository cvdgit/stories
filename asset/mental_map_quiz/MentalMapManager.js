import MentalMap from "./MentalMap";

export default function MentalMapManager() {

  const instances = {}

  return {
    create(element, deck, params) {
      const instance = new MentalMap(element, deck, params)

      return instance
    }
  }
}

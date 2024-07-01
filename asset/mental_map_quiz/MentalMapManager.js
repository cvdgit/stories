import MentalMap from "./MentalMap";

export default function MentalMapManager() {

  const instances = {}

  return {
    create(element, params) {
      const instance = new MentalMap(element, params)

      return instance
    }
  }
}

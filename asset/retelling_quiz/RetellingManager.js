import Retelling from "./Retelling";

export default function RetellingManager() {
  const instances = {}
  return {
    create(element, deck, params, slideId) {
      const instance = new Retelling(element, deck, params)
      instances[slideId] = instance
      return instance
    },
    getInstance(slideId) {
      return instances[slideId]
    }
  }
}

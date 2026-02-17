import Retelling from "./Retelling";

export default function RetellingManager() {
  const instances = {}
  return {
    create(element, deck, params, slideId, microphoneChecker) {
      const instance = new Retelling(
        element,
        deck,
        params,
        microphoneChecker
      );
      instances[slideId] = instance;
      return instance;
    },
    getInstance(slideId) {
      return instances[slideId];
    },
    destroyInstances() {
      Object.keys(instances).forEach(key => {
        instances[key].destroy();
      })
    }
  }
}

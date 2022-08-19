
function getConfigValue(name) {
  const config = window.WikidsRevealConfig || {};
  return config[name];
}

export default () => {

  return {
    get: (name) => {
      return getConfigValue(name);
    }
  }
};

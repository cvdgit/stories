
function getConfigValue(name) {
  const config = window.WikidsRevealConfig || {};
  return config[name];
}

function SlidesConfig() {
  return {
    get: (name) => {
      return getConfigValue(name);
    }
  }
}

export default SlidesConfig;

import {_extends} from "./common";

const plugins = []
const defaults = {
  initializeByDefault: true
}

export const PluginManager = {
  mount(plugin) {
    // Set default static properties
    for (let option in defaults) {
      if (defaults.hasOwnProperty(option) && !(option in plugin)) {
        plugin[option] = defaults[option]
      }
    }
    plugins.forEach(p => {
      if (p.pluginName === plugin.pluginName) {
        throw "WikidsStoryTest: Cannot mount plugin ".concat(plugin.pluginName, " more than once");
      }
    })
    plugins.push(plugin)
  },
  initializePlugins(test, el, defaults, options) {
    plugins.forEach(plugin => {
      const pluginName = plugin.pluginName
      //if (!test.options[pluginName] && !plugin.initializeByDefault) return
      if (!plugin.initializeByDefault) {
        return
      }
      const initialized = new plugin(test, el, defaults)
      initialized.test = test
      //initialized.options = defaults
      test[pluginName] = initialized // Add default options from plugin
      _extends(defaults, initialized.defaults)
    })
  }
}

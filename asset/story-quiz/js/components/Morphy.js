const Morphy = function() {
  var API = {};
  API.correctResult = function(match, result) {
    return $.post('/morphy/root', {
      match, result
    });
  }
  return API;
}

export default Morphy;

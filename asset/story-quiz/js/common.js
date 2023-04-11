import {patienceDiff} from "./PatienceDiff";

export function extend(a, b) {
  for (var i in b) {
    a[i] = b[i];
  }
  return a;
}

export function shuffle(array) {
  var counter = array.length;
  // While there are elements in the array
  while (counter > 0) {
    // Pick a random index
    var index = Math.floor(Math.random() * counter);
    // Decrease counter by 1
    counter--;
    // And swap the last element with it
    var temp = array[counter];
    array[counter] = array[index];
    array[index] = temp;
  }
  return array;
}

export function combineArraysRecursively(array_of_arrays) {
  if (!array_of_arrays) {
    return [];
  }
  if (!Array.isArray(array_of_arrays)) {
    return [];
  }
  if (array_of_arrays.length === 0) {
    return [];
  }
  for (let i = 0; i < array_of_arrays.length; i++) {
    if (!Array.isArray(array_of_arrays[i]) || array_of_arrays[i].length === 0) {
      return [];
    }
  }
  let outputs = [];
  function permute(arrayOfArrays, whichArray=0, output="") {
    arrayOfArrays[whichArray].forEach((array_element) => {
      if (whichArray === array_of_arrays.length - 1) {
        outputs.push([output, array_element]);
      }
      else{
        permute(arrayOfArrays, whichArray + 1, output + array_element);
      }
    });
  }
  permute(array_of_arrays);
  return outputs;
}

export function textDiff(a, b) {
  var diff = patienceDiff(a.split(''), b.split(''));
  var diffAnswer = '';
  diff.lines.forEach(function(line) {
    var char = '',
      color = 'red';
    if (line.aIndex >= 0) {
      char = line.line;
      if (line.bIndex === -1) {
        color = 'red';
      }
      if (line.aIndex === line.bIndex) {
        color = 'green';
      }
    }
    if (char.length) {
      diffAnswer += '<span style="color: ' + color + '">' + char + '</span>';
    }
  });
  return diffAnswer;
}

export function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}

/**
 * @param element
 */
export function show (element) {
  element
    .removeClass('hide flex-show')
    .addClass('flex-show');
}

/**
 * @param element
 */
export function hide (element) {
  element
    .removeClass('hide flex-show')
    .addClass('hide');
}

import {stripTags} from "../common";

const {diffChars} = require('diff');

export function SimilarityChecker(threshold) {
  let similarityPercentage = 0;
  return {
    check(rawText, rawUserResponse) {
      const text = removePunctuation(stripTags(rawText).toLowerCase()).trim();
      const userResponse = removePunctuation(stripTags(rawUserResponse).toLowerCase()).trim();
      similarityPercentage = calcSimilarityPercentage(text, userResponse);
      if (similarityPercentage < threshold) {
        return false
      }
      return allImportantWordsIncluded(rawText.toLowerCase().trim(), userResponse)
    },
    getSimilarityPercentage() {
      return similarityPercentage;
    }
  }
}

export function calcSimilarityPercentage(text, userResponse) {
  if (userResponse.length === 0) {
    return 0
  }
  const diff = diffChars(text, userResponse)
  let diffCounter = 0
  diff.forEach((part) => {
    if (part.added || part.removed) {
      diffCounter += part.count
    }
  })
  if (diffCounter === 0) {
    return 100
  }
  return 100 - Math.round(diffCounter * 100 / text.length)
}

const removePunctuation = text => text.replace(/[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}–«»~]/g, '').replace(/\s{2,}/g, " ")

export function allImportantWordsIncluded(text, userResponse) {
  const importantWords = $(`<div>${text}</div>`)
    .find('span.target-text')
    .map((i, el) => removePunctuation($(el).text()))
    .get()
  if (importantWords.length === 0) {
    return true
  }
  userResponse = removePunctuation(userResponse)
  return importantWords.every(word => userResponse.toString().indexOf(word) !== -1)
}

export function diffRetelling(text, userResponse) {
  const diff = diffChars(text, userResponse);
  const fragment = document.createDocumentFragment();
  diff.forEach((part) => {
    const color = part.added ? 'green' :
      part.removed ? 'red' : 'grey';
    const span = document.createElement('span');
    span.style.color = color;
    span.appendChild(document
      .createTextNode(part.value));
    fragment.appendChild(span);
  });
  return fragment
}

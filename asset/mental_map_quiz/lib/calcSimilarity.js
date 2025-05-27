const {diffChars} = require('diff');

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

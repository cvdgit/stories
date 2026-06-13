export default function calcMapProgress(history) {
  let total = 0
  let hidden = 0
  let percent = 0
  history.map(({words, hiddenWords}) => {
    total += Number(words)
    hidden += Number(hiddenWords)
  })
  if (total === 0) {
    return 0
  }
  percent = Math.round(hidden * 100 / total)
  return percent
}

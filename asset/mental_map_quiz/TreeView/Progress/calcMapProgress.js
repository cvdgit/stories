export default function calcMapProgress(history) {
  let total = 0
  let hidden = 0
  history.map(({words, hiddenWords}) => {
    total += Number(words)
    hidden += Number(hiddenWords)
  })
  const result = {percent: 0, content: '0'}
  if (total === 0) {
    return result
  }
  result.percent = Math.round(hidden * 100 / total)
  result.content = `Слов ${hidden} из ${total} - ${result.percent}%`
  return result
}

export default function calcAllMapProgress(history) {
  let total = 0
  let all = 0
  history.map(({allTextClosed}) => {
    total++
    all += Number(allTextClosed)
  })
  const result = {percent: 0, content: '0%'}
  if (total === 0) {
    return result
  }
  result.percent = Math.round(all * 100 / (total * 100))
  result.content = `${result.percent}%`
  return result
}

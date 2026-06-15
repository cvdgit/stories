export default function calcAllMapProgress(history) {
  console.log(history)
  let total = 0
  let all = 0
  let percent = 0
  history.map(({allTextClosed}) => {
    total++
    all += Number(allTextClosed)
  })
  if (total === 0) {
    return 0
  }
  console.log(all, total)
  percent = Math.round(all * 100 / (total * 100))
  return {
    percent,
    content: `${percent}%`
  }
}

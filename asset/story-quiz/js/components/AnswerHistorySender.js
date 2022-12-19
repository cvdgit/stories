
const AnswerHistorySender = url => {
  return {
    send(data) {
      return $.post(url, data);
    }
  }
}

export default AnswerHistorySender;

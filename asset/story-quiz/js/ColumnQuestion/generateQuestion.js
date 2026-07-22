import multiplyColumnSteps from "./multiplyColumnSteps";

function randomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function randomNumberWithLength(length) {
  const min = length === 1 ? 0 : 10 ** (length - 1);
  const max = 10 ** length - 1;
  return randomInt(min, max);
}

export default function generateQuestion(payload) {
  const firstLength = payload.firstDigit.length;
  const secondLength = payload.secondDigit.length;

  let first;
  let second;

  switch (payload.sign) {
    case '+':
      first = randomNumberWithLength(firstLength);
      second = randomNumberWithLength(secondLength);
      break;

    case '-':
      do {
        first = randomNumberWithLength(firstLength);
        second = randomNumberWithLength(secondLength);
      } while (first <= second);
      break;

    case '*':
      first = randomNumberWithLength(firstLength);
      second = randomNumberWithLength(secondLength);
      break;

    default:
      throw new Error(`Неизвестный знак: ${payload.sign}`);
  }

  let result;

  switch (payload.sign) {
    case '+':
      result = first + second;
      break;
    case '-':
      result = first - second;
      break;
    case '*':
      result = first * second;
      break;
  }

  const newSteps = []
  if (payload.sign === '*') {
    if (firstLength > 1 || secondLength > 1) {
      const steps = multiplyColumnSteps(String(first), String(second))
      steps.map(s => newSteps.push(s))
    }
  }

  return {
    firstDigit: String(first),
    secondDigit: String(second),
    sign: payload.sign,
    result: String(result),
    steps: newSteps
  };
}

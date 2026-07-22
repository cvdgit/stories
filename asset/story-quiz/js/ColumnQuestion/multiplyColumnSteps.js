export default function multiplyColumnSteps(a, b) {
  const num1 = String(a);
  const num2 = String(b);

  const n1 = num1.length;
  const n2 = num2.length;

  const steps = [];

  for (let i = n2 - 1; i >= 0; i--) {
    let partial = '';
    let carry = 0;
    const digit2 = Number(num2[i]);

    for (let j = n1 - 1; j >= 0; j--) {
      const digit1 = Number(num1[j]);
      const mul = digit1 * digit2 + carry;

      carry = Math.floor(mul / 10);
      partial = String(mul % 10) + partial;
    }

    if (carry > 0) {
      partial = String(carry) + partial;
    }

    const partialInt = Number(partial);

    partial += '0'.repeat(n2 - 1 - i);

    steps.push({
      step: n2 - i,
      firstDigit: Number(num1),
      secondDigit: digit2,
      result: Number(partial),
      resultInt: partialInt,
    });
  }

  return steps;
}

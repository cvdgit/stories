import 'mathlive';
import {MathfieldElement} from "mathlive";
import Quill from 'quill';
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import Embed from "quill/blots/embed";

MathfieldElement.fontsDirectory = '/build/fonts';

class MathFormula extends Embed {
  static blotName = 'math';
  static className = 'ql-math-element';
  static tagName = 'span';

  static create(value) {
    let node
    if (typeof value === 'string') {
      node = super.create(value);
      node.innerHTML = `<math-field data-id="" read-only>${value}</math-field>`;
      node.setAttribute('data-value', value);
    } else {
      node = super.create(value.value);
      node.dataset.id = value.id
      node.innerHTML = `<math-field data-id="${value.id}" read-only>${value.value}</math-field>`;
      node.setAttribute('data-value', value.value);
    }
    return node;
  }

  static value(domNode) {
    return domNode.dataset;
  }

  /*html() {
    const {math} = this.value();
    return `<span>${math}</span>`;
  }*/
}

Quill.register({
  "formats/math": MathFormula,
}, true)

window.Quill = Quill;

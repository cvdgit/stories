export function trimRanges(selection) {
  for (let i = 0, range = selection.getRangeAt(0); i < selection.rangeCount; range = selection.getRangeAt(i++)) {

    const text = selection.toString();
    const startOffset = text.length - text.trimStart().length;
    const endOffset = text.length - text.trimEnd().length;

    if (startOffset) {
      const offset = range.startOffset + startOffset;
      if (offset < 0) {
        // If the range will underflow the current element, then it belongs in the previous element
        const start = range.startContainer.parentElement.previousSibling;
        range.setStart(start, start.textContent.length + offset);
      } else if (offset > range.startContainer.textContent.length) {
        // If the range will overflow the current element, then it belongs in the next element
        const start = range.startContainer.parentElement.nextSibling;
        range.setStart(start, offset - range.startContainer.textContent.length);
      } else {
        range.setStart(range.startContainer, offset);
      }
    }
    if (endOffset) {
      const offset = range.endOffset - endOffset;
      if (offset < 0) {
        // If the range will underflow the current element, then it belongs in the previous element
        const end = range.endContainer.parentElement.previousSibling;
        range.setEnd(end, end.textContent.length + offset);
      } else if (offset > range.endContainer.textContent.length) {
        // If the range will overflow the current element, then it belongs in the next element
        const end = range.endContainer.parentElement.nextSibling;
        range.setEnd(end, offset - range.endContainer.textContent.length);
      } else {
        range.setEnd(range.endContainer, offset);
      }
    }
  }
}

export function surroundRangeContents(range, callback) {
  splitRangeBoundaries(range);
  var textNodes = getTextNodesInRange(range);
  if (textNodes.length === 0) {
    return;
  }

  callback(textNodes);

  range.setStart(textNodes[0], 0);
  var lastTextNode = textNodes[textNodes.length - 1];
  range.setEnd(lastTextNode, lastTextNode.length);
}

function splitRangeBoundaries(range) {
  var sc = range.startContainer,
    so = range.startOffset,
    ec = range.endContainer,
    eo = range.endOffset;
  var startEndSame = (sc === ec);

  // Split the end boundary if necessary
  if (isCharacterDataNode(ec) && eo > 0 && eo < ec.length) {
    splitDataNode(ec, eo);
  }

  // Split the start boundary if necessary
  if (isCharacterDataNode(sc) && so > 0 && so < sc.length) {
    sc = splitDataNode(sc, so);
    if (startEndSame) {
      eo -= so;
      ec = sc;
    } else if (ec === sc.parentNode && eo >= getNodeIndex(sc)) {
      ++eo;
    }
    so = 0;
  }

  range.setStart(sc, so);
  range.setEnd(ec, eo);
}

function getNextNode(node) {
  var next = node.firstChild;
  if (next) {
    return next;
  }
  while (node) {
    if ( (next = node.nextSibling) ) {
      return next;
    }
    node = node.parentNode;
  }
}

function insertAfter(node, precedingNode) {
  var nextNode = precedingNode.nextSibling, parent = precedingNode.parentNode;
  if (nextNode) {
    parent.insertBefore(node, nextNode);
  } else {
    parent.appendChild(node);
  }
  return node;
}

function getNodesInRange(range) {
  var start = range.startContainer;
  var end = range.endContainer;
  var commonAncestor = range.commonAncestorContainer;
  var nodes = [];
  var node;

  // Walk parent nodes from start to common ancestor
  /*for (node = start.parentNode; node; node = node.parentNode) {
    nodes.push(node);
    if (node === commonAncestor) {
      break;
    }
  }
  nodes.reverse();*/

  // Walk children and siblings from start until end is found
  for (node = start; node; node = getNextNode(node)) {
    nodes.push(node);
    if (node.textContent === end.textContent) {
      break;
    }
  }

  return nodes;
}

function getTextNodesInRange(range) {
  var textNodes = [];
  var nodes = getNodesInRange(range);
  //console.log('nodes', nodes);
  for (var i = 0, node, el; node = nodes[i++]; ) {
    //if (node.nodeType === 3) {
    textNodes.push(node);
    //}
  }
  return textNodes;
}

function isCharacterDataNode(node) {
  var t = node.nodeType;
  return t === 3 || t === 4 || t === 8 ; // Text, CDataSection or Comment
}

function splitDataNode(node, index) {
  var newNode = node.cloneNode(false);
  newNode.deleteData(0, index);
  node.deleteData(index, node.length - index);
  insertAfter(newNode, node);
  return newNode;
}

function getNodeIndex(node) {
  var i = 0;
  while ( (node = node.previousSibling) ) {
    ++i;
  }
  return i;
}

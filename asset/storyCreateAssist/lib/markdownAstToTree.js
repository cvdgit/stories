export function findFirstHeader(tree) {
  if (!Array.isArray(tree) || tree.length === 0) {
    return null;
  }

  function dfs(nodes) {
    for (const node of nodes) {
      if (node.header) {
        return node.header;
      }
      if (node.children?.length) {
        const found = dfs(node.children);
        if (found) return found;
      }
    }
    return null;
  }

  return dfs(tree);
}

export default function markdownAstToTree(tokens) {
  const result = [];
  const stack = [];

  let pendingLevel = null;
  let hasHeadings = false;
  let plainText = [];

  for (let i = 0; i < tokens.length; i++) {
    const token = tokens[i];

    // открытие заголовка
    if (token.type === 'heading_open') {
      pendingLevel = Number(token.tag.replace('h', ''));
      hasHeadings = true;
      continue;
    }

    // текст заголовка
    if (token.type === 'inline' && pendingLevel) {
      const node = {
        header: token.content.trim(),
        content: '',
        children: []
      };

      while (stack.length >= pendingLevel) {
        stack.pop();
      }

      if (stack.length === 0) {
        result.push(node);
      } else {
        stack[stack.length - 1].children.push(node);
      }

      stack.push(node);
      pendingLevel = null;
      continue;
    }

    // обычный текст
    if (token.type === 'inline') {
      const text = token.content.trim();
      if (!text) continue;

      if (stack.length) {
        const current = stack[stack.length - 1];
        if (current.content) current.content += '\n\n';
        current.content += text;
      } else {
        // текст без заголовков
        plainText.push(text);
      }
    }
  }

  // fallback: вообще нет заголовков
  if (!hasHeadings && plainText.length) {
    return [
      {
        header: null,
        content: plainText.join('\n\n'),
        children: []
      }
    ];
  }

  return result;
}


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

  let i = 0;

  while (i < tokens.length) {
    const token = tokens[i];

    if (token.type === "heading_open") {
      const level = parseInt(token.tag.replace("h", "")) + 1;

      const inlineToken = tokens[i + 1];
      const header = inlineToken.content;

      const node = {
        header,
        content: "",
        level,
        children: []
      };

      // определяем родителя по level
      while (stack.length && stack[stack.length - 1].level >= level) {
        stack.pop();
      }

      if (stack.length === 0) {
        result.push(node);
      } else {
        stack[stack.length - 1].children.push(node);
      }

      stack.push(node);

      i += 3; // пропускаем heading_open + inline + heading_close

      // собираем content до следующего заголовка
      let contentParts = [];

      while (i < tokens.length && tokens[i].type !== "heading_open") {
        const t = tokens[i];

        if (t.type === "inline" && t.content) {
          contentParts.push(t.content);
        }

        i++;
      }

      node.content = contentParts.join(" ").trim();
      continue;
    }

    i++;
  }

  return result;
}

/*export default function markdownAstToTree(tokens) {
  const roots = [];
  const stack = [];

  let pendingLevel = null;
  let hasHeadings = false;
  let plainText = [];

  for (const token of tokens) {

    if (token.type === 'heading_open') {
      pendingLevel = Number(token.tag.replace('h', ''));
      hasHeadings = true;
      continue;
    }

    if (token.type === 'inline' && pendingLevel) {
      const level = pendingLevel;

      const node = {
        header: token.content.trim(),
        content: '',
        children: [],
        level
      };

      // поднимаемся пока уровень родителя >= текущего
      while (stack.length && stack[stack.length - 1].level >= level) {
        stack.pop();
      }

      if (stack.length === 0) {
        roots.push(node);
      } else {
        stack[stack.length - 1].children.push(node);
      }

      stack.push(node);
      pendingLevel = null;
      continue;
    }

    if (token.type === 'inline') {
      const text = token.content.trim();
      if (!text) continue;

      if (stack.length) {
        const current = stack[stack.length - 1];
        if (current.content) current.content += '\n\n';
        current.content += text;
      } else {
        plainText.push(text);
      }
    }
  }

  // fallback без заголовков
  if (!hasHeadings && plainText.length) {
    return {
      tree: [
        {
          header: null,
          content: plainText.join('\n\n'),
          children: []
        }
      ],
      empty: []
    };
  }

  function normalize(nodes) {
    const result = [];
    for (const node of nodes) {
      const children = normalize(node.children);
      const hasContent = node.content && node.content.trim();
      if (hasContent) {
        node.children = children;
        result.push(node);
      } else {
        result.push(...children);
      }
    }
    return result;
  }

  return normalize(roots);
}*/

/*export default function markdownAstToTree(tokens) {
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
        level: pendingLevel,
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
}*/


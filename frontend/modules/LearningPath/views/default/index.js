(function() {


  const container = document.querySelector('div[data-learning-path-id]')
  const learningPathId = container.dataset.learningPathId

  async function init() {
    const response = await fetch(`/learning-path/init/${learningPathId}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      }
    })
    return await response.json()
  }

  function renderTree(items, container) {

    function renderItem(item, level = 0) {
      const commentElement =
        document.createElement('div');
      commentElement.classList.add('lp-column-item');
      commentElement.style.marginLeft = `${level * 20}px`;
      commentElement.innerHTML = `
        <div class="comment-author">${item.title}</div>
        <div class="lp-column-item-children"></div>
      `;

      const repliesContainer =
        commentElement.querySelector('.lp-column-item-children');
      (item.children || []).forEach(child => {
        repliesContainer
          .appendChild(renderItem(child, level + 1));
      });

      return commentElement;
    }

    items.forEach(item => {
      container.appendChild(renderItem(item));
    });
  }

  async function initLeaningPath() {
    const json = await init()

    const wrap = document.createElement('div')
    wrap.classList.add('lp-container')

    for (const [treeName, treeValue] of Object.entries(json.data)) {
      const col = document.createElement('div')
      col.classList.add('lp-column')
      col.innerHTML = `<div class="lp-column-title">${treeValue.name}</div>`
      wrap.appendChild(col)
      renderTree(treeValue.items, col)
    }

    container.appendChild(wrap)
  }

  initLeaningPath()

})()

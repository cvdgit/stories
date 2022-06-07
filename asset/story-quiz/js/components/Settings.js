
export default function createSettings(items) {

  const element = document.createElement('div');
  element.classList.add('quiz-settings');

  element.innerHTML =
    `<div class="dropdown pull-right">
       <a href="#" data-toggle="dropdown" class="dropdown-toggle" title="Настройки">
         <svg xmlns="http://www.w3.org/2000/svg" style="width:30px;height:30px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
           <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
           <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
         </svg>
       </a>
       <ul class="dropdown-menu"></ul>
     </div>`;

  const menu = element.querySelector('.dropdown-menu');
  items.forEach((item) => {
    const linkElement = document.createElement('a');
    linkElement.textContent = item.title;
    linkElement.setAttribute('href', '#');
    linkElement.setAttribute('tabindex', '-1');
    linkElement.addEventListener('click', (e) => {
      e.preventDefault();
      item.callback();
    });
    const itemElement = document.createElement('li');
    itemElement.appendChild(linkElement);
    menu.appendChild(itemElement);
  });

  return element;
}

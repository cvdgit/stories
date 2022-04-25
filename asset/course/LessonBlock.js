export default class LessonBlock {

  /**
   *
   * @param {BlockModel} block
   */
  constructor(block) {
    this.block = block;
  }

  createSlide(data) {
    const elem = document.createElement('div');
    elem.classList.add('thumb-reveal-wrapper');
    elem.innerHTML =
      `<div class="thumb-reveal-inner">
         <div class="thumb-reveal reveal" style="width: 1280px; height: 720px; transform: scale(0.28)">
           <div class="slides">
             ${data}
           </div>
         </div>
       </div>`;
    return elem;
  }

  makeReveal(elem) {
    const deck = new Reveal(elem, {
      embedded: true
    });
    deck.initialize({
      'width': 1280,
      'height': 720,
      'margin': 0.01,
      'transition': 'none',
      'disableLayout': true,
      'controls': false,
      'progress': false,
      'slideNumber': false
    });
    return deck;
  }

  render() {
    const element = document.createElement('div');
    element.classList.add('lesson-block');
    element.setAttribute('data-block-id', this.block.getId());
    element.innerHTML =
      `<div class="lesson-block__content"></div>
       <div class="lesson-block__action">
           <div class="dropdown">
               <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><i class="glyphicon glyphicon-plus"></i> <span class="caret"></span></button>
               <ul class="dropdown-menu dropdown-menu-right"></ul>
           </div>
       </div>`;
    element.querySelector('.lesson-block__content')
      .appendChild(this.createSlide(this.block.getData()));

    this.makeReveal(element.querySelector('.reveal'));

    return element;
  }
}

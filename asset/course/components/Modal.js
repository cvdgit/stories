export default class Modal {

  constructor(title, dialog) {

    this.id = 'modal' + new Date().getTime();
    this.element = this.createModalElement(title, dialog);

    this.onShowCallback = null;
    this.onHideCallback = null;
  }

  createModalElement(title, dialog) {
    const element = document.createElement('div');
    element.classList.add('modal');
    element.classList.add('fade');
    element.setAttribute('id', this.id);
    element.setAttribute('tabindex', '-1');
    element.setAttribute('role', 'dialog');
    element.innerHTML =
      `<div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
             <h4 class="modal-title">${title}</h4>
           </div>
           <div class="modal-body"></div>
           <!--div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
             <button type="button" class="btn btn-primary">Save changes</button>
           </div-->
         </div>
       </div>`;
    if (dialog) {
      element.querySelector('.modal-dialog').classList.add('modal-' + dialog);
    }
    return element;
  }

  setContent(content) {
    this.element.querySelector('.modal-body')
      .appendChild(content);
  }

  onShow(callback) {
    this.onShowCallback = callback;
  }

  onHide(callback) {
    this.onHideCallback = callback;
  }

  show() {
    $(this.element).on('show.bs.modal', (e) => {
      if (typeof this.onShowCallback === 'function') {
        this.onShowCallback(e);
      }
    });
    $(this.element).on('hidden.bs.modal', (e) => {
      document.body.querySelector('#' + this.id).remove();
      if (typeof this.onHideCallback === 'function') {
        this.onHideCallback(e);
      }
    });
    $(this.element).modal('show');
  }

  hide() {
    $(this.element).modal('hide');
  }
}

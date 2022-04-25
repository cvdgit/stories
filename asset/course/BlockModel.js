export default class BlockModel {

  constructor(data) {
    this.uuid = this.uuidv4();
    this.id = data.slide_id;
    this.data = data.data;
    this.order = data.order;
  }

  getUUID() {
    return this.uuid;
  }

  getId() {
    return this.id;
  }

  getOrder() {
    return this.order;
  }

  setOrder(order) {
    if (!Number.isInteger(order)) {
      throw 'BlockModel.setOrder error ' + order;
    }
    this.order = order;
  }

  uuidv4() {
    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
      (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    );
  }

  getData() {
    return this.data;
  }
}

const blockTypes = ['text', 'image'];

export function showDialogHandler(deck) {
  if (!deck) {
    return
  }
  deck.configure({keyboard: false});
  $('.reveal .story-controls').hide();
  blockTypes.map(blockType => {
    $(deck.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', '-1')
  })
}

export function hideDialogHandler(deck, voiceResponse) {
  if (!deck) {
    return
  }
  if ($(deck.getCurrentSlide()).find('.slide-hints-wrapper').length) {
    return
  }
  if (voiceResponse.getStatus()) {
    voiceResponse.stop();
  }
  deck.configure({keyboard: true})
  $('.reveal .story-controls').show();
  blockTypes.map(blockType => {
    $(deck.getCurrentSlide()).find(`div.sl-block[data-block-type=${blockType}]`).css('zIndex', 'auto')
  })
}

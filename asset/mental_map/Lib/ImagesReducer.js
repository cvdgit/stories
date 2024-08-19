export default function ImagesReducer(state, action) {
  switch(action.type) {
    case 'images_loaded': {
      return action.images
    }
    case 'add_image': {
      return [
        ...state,
        action.payload
      ]
    }
    case 'update_image_item': {
      return [...state].map(i => {
        if (i.id === action.payload.id) {
          return {...i, ...action.payload}
        }
        return i
      })
    }
    case 'update_images': {
      return Array.from(action.payload)
    }
    case 'delete_image': {
      return [...state].filter(i => i.id !== action.imageId)
    }
    default: {
      throw Error('Unknown action: ' + action.type);
    }
  }
}

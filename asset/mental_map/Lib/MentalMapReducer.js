export default function MentalMapReducer(state, action) {
  switch (action.type) {
    case 'mental_map_loaded': {
      return action.mentalMap
    }
    case 'add_image_to_mental_map': {
      return {
        ...state,
        map: {...state.map, images: [...state.map.images, action.payload]}
      }
    }
    case 'remove_image_from_mental_map': {
      return {
        ...state,
        map: {...state.map, images: state.map.images.filter(i => i.id !== action.imageId)}
      }
    }
    case 'upload_mental_map_image': {
      return {
        ...state,
        map: {...state.map, ...action.payload}
      }
    }
    case 'update_mental_map_images': {
      return {
        ...state,
        map: {
          ...state.map, images: state.map.images.map(i => {
            if (i.id === action.imageId) {
              return {...i, ...action.payload}
            }
            return i
          })
        }
      }
    }
    case 'update_mental_map_text': {
      return {
        ...state,
        text: action.text
      }
    }
    case 'update_settings': {
      return {
        ...state,
        settings: {...state.settings, ...action.payload}
      }
    }
    default: {
      throw Error('Unknown action: ' + action.type);
    }
  }
}

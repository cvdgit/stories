export default function TreeReducer(state, action) {
  switch(action.type) {
    case 'tree_loaded': {
      return action.tree
    }
    case 'update_tree': {
      console.log(action.treeData)
      return {...state, treeData: action.treeData}
    }
    case 'add_node': {
      return {
        ...state,
        treeData: [...state.treeData, action.payload]
      }
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

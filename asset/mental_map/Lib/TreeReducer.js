export default function TreeReducer(state, action) {
  switch (action.type) {
    case 'tree_loaded': {
      return action.tree
    }
    case 'update_tree': {
      return {...state, treeData: action.treeData}
    }
    case 'add_node': {
      return {
        ...state,
        treeData: [...state.treeData, action.payload]
      }
    }
    case 'update_node': {
      return {
        ...state, treeData: [...state.treeData].map(n => {
          if (n.id === action.nodeId) {
            return {...n, ...action.payload}
          }
          return n
        })
      }
    }
    default: {
      throw Error('Unknown action: ' + action.type);
    }
  }
}

export default function TreeReducer(state, action) {
  switch (action.type) {
    case 'tree_loaded': {
      return action.tree
    }
    case 'update_tree': {
      if (state.treeData === action.treeData) {
        return state
      }
      return {
        ...state,
        treeData: action.treeData,
        save: true
      }
    }
    case 'add_node': {
      return {
        ...state,
        treeData: [...state.treeData, action.payload],
        save: true
      }
    }
    case 'update_node': {
      const updateNodeRecursive = (nodes) => {
        let changed = false

        const updatedNodes = nodes.map(node => {
          if (node.id === action.nodeId) {
            changed = true
            return {
              ...node,
              ...action.payload
            }
          }
          if (!node.children || node.children.length === 0) {
            return node
          }
          const updatedChildren = updateNodeRecursive(node.children)
          if (updatedChildren === node.children) {
            return node
          }
          changed = true
          return {
            ...node,
            children: updatedChildren
          }
        })

        return changed ? updatedNodes : nodes
      }

      const updatedTree = updateNodeRecursive(state.treeData)
      if (updatedTree === state.treeData) {
        return state
      }

      return {
        ...state,
        treeData: updatedTree,
        save: true
      }
    }
    default: {
      throw Error('Unknown action: ' + action.type);
    }
  }
}

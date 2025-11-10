export default function ThreadReducer(state, action) {
  switch (action.type) {
    case 'threads_load': {
      return {
        threads: [...action.threads]
      };
    }
    case 'add_message': {
      return {
        ...state,
        thread: {...state.thread, messages: [...state.thread.messages, action.message]}
      }
    }
    case 'update_message': {
      return {
        ...state,
        thread: {
          ...state.thread, messages: state.thread.messages.map(m => {
            if (m.id === action.messageId) {
              return {...m, ...action.payload};
            }
            return m;
          })
        }
      }
    }
    case 'update_message_slide_status': {
      return {
        ...state,
        thread: {
          ...state.thread,
          messages: state.thread.messages.map(m => {
            if (m.id === action.messageId) {
              return {
                ...m, metadata: {
                  ...m.metadata, slides: m.metadata.slides.map(s => {
                    if (s.slideId === action.slideId) {
                      return {...s, status: action.status};
                    }
                    return s;
                  })
                }
              };
            }
            return m;
          })
        }
      }
    }
    case 'remove_message': {
      return {
        ...state,
        thread: {
          ...state.thread,
          messages: state.thread.messages.filter(m => m.id !== action.messageId)
        }
      }
    }
    case 'remove_speech_trainer': {
      return {
        ...state,
        thread: {
          ...state.thread,
          messages: state.thread.messages.map(m => {
            if (m.id === action.messageId) {
              return {...m, slides: null};
            }
            return m;
          })
        }
      }
    }
    case 'update_thread_story': {
      return {
        ...state,
        thread: {...state.thread, ...action.payload},
        threads: state.threads.map(t => {
          if (t.id === action.threadId) {
            return {...t, title: action.payload.title};
          }
          return t;
        })
      }
    }
    case 'update_thread': {
      return {
        ...state,
        thread: {...state.thread, ...action.payload},
      }
    }
    case 'thread_state': {
      return {
        ...state,
        thread: {...action.payload}
      }
    }
    case 'add_to_threads': {
      return {
        ...state,
        threads: [action.payload, ...state.threads]
      }
    }
    case 'add_or_update_in_threads': {
      const existsInThreads = state.threads.find(t => t.id === action.threadId);
      if (!existsInThreads) {
        return {
          ...state,
          thread: {...state.thread, ...action.payload, id: action.threadId},
          threads: [{id: action.threadId, ...action.payload}, ...state.threads]
        }
      }
      return {
        ...state,
        threads: state.threads.map(t => {
          if (t.id === action.threadId) {
            return {...t, ...action.payload};
          }
          return t;
        })
      }
    }
    case 'add_thread': {
      return {
        ...state,
        thread: action.payload,
        threads: [action.payload, ...state.threads]
      }
    }
    case 'create_thread': {
      return {
        ...state,
        thread: {id: action.threadId, title: 'Без имени', messages: []},
        threads: [{id: action.threadId, title: 'Без имени'}, ...state.threads]
      }
    }
    case 'set_thread_messages': {
      return {
        ...state,
        threads: [...state.threads].map(t => {
          if (t.id === action.threadId) {
            return {...t, messages: action.messages};
          }
          return t;
        })
      };
    }
    case 'delete_thread': {
      return {
        ...state,
        thread: {id: state.thread.id, title: 'Без имени', messages: []},
        threads: state.threads.filter(t => t.id !== action.threadId)
      }
    }
    default: {
      throw Error('Unknown action: ' + action.type);
    }
  }
}

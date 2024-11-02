export default function ScheduleReducer(state, action) {
  switch (action.type) {
    case 'schedules_loaded': {
      return action.schedules
    }
    default: {
      throw Error('Unknown action: ' + action.type);
    }
  }
}

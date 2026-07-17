/**
 * @param {function(): boolean} beforeStartRecordingEvent
 * @param {function(): void} startRecordingEvent
 * @param {function(): void} stopRecordingEvent
 * @return {{beforeStartRecordingEvent: function(): boolean, startRecordingEvent: function(): void, stopRecordingEvent: function(): void}}
 * @constructor
 */
export default function MentalMapEvents(beforeStartRecordingEvent, startRecordingEvent, stopRecordingEvent) {
  return {
    beforeStartRecordingEvent,
    startRecordingEvent,
    stopRecordingEvent
  }
}

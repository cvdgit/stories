import {saveData} from '../utils/saveData.js'

const socketByUser = {}
const dataChunks = {}

const folderName = (storyId, userId, session) => `story${Number(storyId)}/user${Number(userId)}/session${session}`;

export const onConnection = (socket) => {

  socket.on('user:connected', (payload) => {
    if (!socketByUser[socket.id]) {
      socketByUser[socket.id] = payload;
    }
  })

  socket.on('screenData:start', ({data, streamDataId}) => {
    if (!dataChunks[streamDataId]) {
      dataChunks[streamDataId] = [data];
      return;
    }
    dataChunks[streamDataId].push(data);
  })

  socket.on('screenData:end', ({session, streamDataId, storyId, userId}) => {
    console.log('end', {session, streamDataId, storyId, userId})
    /*const payload = socketByUser[socket.id];
    if (!payload) {
      return;
    }
    const {storyId, userId} = payload;*/
    if (dataChunks[streamDataId] && dataChunks[streamDataId].length) {
      const folder = folderName(storyId, userId, session);
      saveData(dataChunks[streamDataId], folder, streamDataId)
        .finally(() => delete dataChunks[streamDataId])
    }
  })

  socket.on('disconnect', () => {
    const payload = socketByUser[socket.id];
    console.log('disconnect', payload)
    if (!payload) {
      return;
    }
    const {session, storyId, userId} = payload;
    console.log(dataChunks)
    /*if (dataChunks[streamDataId] && dataChunks[streamDataId].length) {
      const folder = folderName(storyId, userId, session);
      saveData(dataChunks[streamDataId], folder, streamDataId)
        .finally(() => delete dataChunks[streamDataId])
    }*/
  })
}

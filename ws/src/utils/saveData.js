import {Blob, Buffer} from 'buffer'
import {mkdir, open, unlink, writeFile} from 'fs/promises'
import {join, dirname} from 'path'
import {fileURLToPath} from 'url'

import {path} from '@ffmpeg-installer/ffmpeg'
import ffmpeg from 'fluent-ffmpeg'

ffmpeg.setFfmpegPath(path)

const __dirname = dirname(fileURLToPath(import.meta.url));
const videoPath = join(__dirname, '../video');

export const saveData = async (data, folder, streamDataId) => {

  const dirPath = `${videoPath}/${folder}`;
  const fileName = `${Date.now()}-${streamDataId}.webm`;
  const tempFilePath = `${dirPath}/temp-${fileName}`;
  const finalFilePath = `${dirPath}/${fileName}`

  let fileHandle;
  try {
    fileHandle = await open(dirPath);
  } catch {
    await mkdir(dirPath, {recursive: true});
  } finally {
    if (fileHandle) {
      await fileHandle.close();
    }
  }

  try {
    const videoBlob = new Blob(data, {
      type: 'video/webm'
    })
    const videoBuffer = Buffer.from(await videoBlob.arrayBuffer())

    await writeFile(tempFilePath, videoBuffer)
    ffmpeg(tempFilePath)
      .outputOptions([
        '-c:v libvpx-vp9',
        '-preset veryfast',
        '-c:a copy',
        '-crf 35',
        '-b:v 0',
        '-threads 2',
        '-vf scale=1280:720'
      ])
      .nativeFramerate()
      .on('end', async () => {
        await unlink(tempFilePath)
        console.log(`*** File ${fileName} created`)
      })
      .save(finalFilePath, dirPath)
  } catch (e) {
    console.log('*** saveData', e)
  }
}

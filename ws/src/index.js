import express from 'express'
import cors from 'cors'

import { readFileSync, readdirSync, statSync, createReadStream } from "fs";
import path from 'node:path';
import { createServer } from "https";
import { Server } from "socket.io";

import { onConnection } from './socket_io/onConnection.js'
import 'dotenv/config'

const credentials = {
  key: readFileSync(import.meta.dirname + '/../cert/' + process.env.SSL_KEY_FILENAME),
  cert: readFileSync(import.meta.dirname + '/../cert/' + process.env.SSL_CERT_FILENAME)
};

const app = express();
const server = createServer(credentials, app);

const io = new Server(server, {
  cors: {
    origin: process.env.WIKIDS_ORIGIN
  }
});

io.on('connection', onConnection);

server.listen(4000, () => {
  console.log('Server ready 🚀 ');
});

app.all('/', function (req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "X-Requested-With");
  next();
});

var corsOptions = {
  origin: process.env.WIKIDS_ORIGIN,
  optionsSuccessStatus: 200 // some legacy browsers (IE11, various SmartTVs) choke on 204
}

app.get('/getScreenVideos/:story/:user', cors(corsOptions), (req, res) => {
  const { story, user } = req.params;
  const storyId = Number(story);
  const userId = Number(user);
  const videoDirPath = `${import.meta.dirname}/video/story${storyId}/user${userId}`;
  try {
    const sessions = readdirSync(videoDirPath);
    const files = [];
    sessions
      .sort((sessionNameA, sessionNameB) => {
        const { mtime: timeA } = statSync(`${videoDirPath}/${sessionNameA}`);
        const { mtime: timeB } = statSync(`${videoDirPath}/${sessionNameB}`);
        return new Date(timeB).getTime() - new Date(timeA).getTime();
      })
      .map(sessionName => {

        const videos = readdirSync(`${videoDirPath}/${sessionName}`);

        const inProcessVideos = videos
          .filter(v => v.startsWith('temp-'))
          .map(v => v.replace('temp-', ''));

        const { mtime: time } = statSync(`${videoDirPath}/${sessionName}`);

        files.push({
          session: sessionName,
          time,
          videos: videos.filter(v => !v.startsWith('temp-')).map(videoFileName => {
            const id = videoFileName.replace('.webm', '');
            const { mtime: time } = statSync(`${videoDirPath}/${sessionName}/${videoFileName}`);
            if (inProcessVideos.includes(videoFileName)) {
              return {
                id,
                status: 'process',
                time,
                url: `/screenVideo/${storyId}/${userId}/${sessionName.replace('session', '')}/${id}`
              }
            }
            return {
              id,
              status: 'ok',
              time,
              url: `/screenVideo/${storyId}/${userId}/${sessionName.replace('session', '')}/${id}`
            }
          }),
        })
      });
    res.send({
      ok: true,
      files
    });
  } catch (ex) {
    res.send({ ok: false })
  }
});

app.get('/screenVideo/:story/:user/:session/:video', cors(corsOptions), (req, res) => {

  const { story, user, session: sessionId, video: videoId } = req.params;
  const storyId = Number(story);
  const userId = Number(user);

  const filePath = `${import.meta.dirname}/video/story${storyId}/user${userId}/session${sessionId}/${videoId}.webm`;

  try {
    const stat = statSync(filePath);
    const fileSize = stat.size;
    const range = req.headers.range;
    if (range) {
      const parts = range.replace(/bytes=/, '').split('-');
      const start = parseInt(parts[0], 10);
      const end = parts[1] ? parseInt(parts[1], 10) : fileSize - 1;
      const chunkSize = end - start + 1;
      const file = createReadStream(filePath, { start, end });
      const headers = {
        'Content-Type': 'video/webm',
        'Content-Length': chunkSize,
        'Content-Range': `bytes ${start}-${end}/${fileSize}`,
        'Accept-Ranges': 'bytes',
      };
      res.status(206).set(headers);
      file.pipe(res);
    } else {
      const headers = {
        'Content-Type': 'video/webm',
        'Content-Length': fileSize,
        'Accept-Ranges': 'bytes',
      };
      res.status(200).set(headers);
      createReadStream(filePath).pipe(res);
    }
  } catch (err) {
    console.error(err);
    res.status(500).send('Internal server error');
  }
});

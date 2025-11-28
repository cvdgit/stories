import express from 'express'

import { readFileSync } from "fs";
import { createServer } from "https";
import { Server } from "socket.io";

import { onConnection } from './socket_io/onConnection.js'
import 'dotenv/config'

const credentials = {
  key: readFileSync(import.meta.dirname + '/../cert/frontend.key'),
  cert: readFileSync(import.meta.dirname + '/../cert/frontend.crt')
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

app.get('/getScreenVideos/:key', (req, res) => {
  res.send(req.params);
});

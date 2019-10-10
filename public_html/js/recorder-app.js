//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; 						//stream from getUserMedia()
var rec; 							//Recorder.js object
var input; 							//MediaStreamAudioSourceNode we'll be recording

// shim for AudioContext when it's not avb.
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext; //audio context to help us record

var recordButton = document.getElementById("audioRecord");
var stopButton = document.getElementById("audioStop");
var pauseButton = document.getElementById("audioPause");

//add events to those 2 buttons
recordButton.addEventListener("click", startRecording);
stopButton.addEventListener("click", stopRecording);
pauseButton.addEventListener("click", pauseRecording);

stopButton.disabled = true;
recordButton.disabled = false;
pauseButton.disabled = true;

function startRecording() {

    var constraints = { audio: true, video: false };

    recordButton.disabled = true;
    stopButton.disabled = false;
    pauseButton.disabled = false;

    navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {

        audioContext = new AudioContext();

        gumStream = stream;

        input = audioContext.createMediaStreamSource(stream);

        rec = new Recorder(input, {numChannels: 1});
        rec.record();
    })
    .catch(function(err) {
        recordButton.disabled = false;
        stopButton.disabled = true;
        pauseButton.disabled = true;
    });
}

function pauseRecording() {
    if (rec.recording) {
        rec.stop();
        pauseButton.innerHTML = "Возобновить";
    }
    else {
        rec.record();
        pauseButton.innerHTML = "Пауза";
    }
}

function stopRecording() {

    stopButton.disabled = true;
    recordButton.disabled = false;
    pauseButton.disabled = true;

    pauseButton.innerHTML = "Пауза";

    rec.stop();

    gumStream.getAudioTracks()[0].stop();

    rec.exportWAV(createDownloadLink);
}

function createDownloadLink(blob) {

    var li = document.createElement('li');

    var au = document.createElement('audio');
    au.controls = true;
    var url = URL.createObjectURL(blob);
    au.src = url;
    li.appendChild(au);

    var link = document.createElement('a');
    link.href = "#";
    link.innerHTML = "Применить";
    link.onclick = function(e) {
        e.preventDefault();
        WikidsPlayer.setSlideAudio(blob);
    };
    li.appendChild(link);

    var deleteLink = document.createElement("a");
    deleteLink.href = "#";
    deleteLink.innerText = "Удалить";
    deleteLink.onclick = function(e) {
        e.preventDefault();
        WikidsPlayer.removeAudioData(url);
        URL.revokeObjectURL(url);
        li.remove();
        mergeButtonVisible();
    };
    li.appendChild(deleteLink);

    var list = document.getElementById("recordingsList");
    list.appendChild(li);

    mergeButtonVisible();
    WikidsPlayer.addAudioData(url, blob);
}

function mergeButtonVisible() {
    var list = document.getElementById("recordingsList"),
        button = document.getElementById("mergeAllSlideAudio");
    if (list.childNodes.length >= 2) {
        button.style.display = "inline-block";
    }
    else {
        button.style.display = "none";
    }
}
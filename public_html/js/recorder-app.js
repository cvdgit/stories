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

    console.log("recordButton clicked");

    /*
        Simple constraints object, for more advanced audio features see
        https://addpipe.com/blog/audio-constraints-getusermedia/
    */

    var constraints = { audio: true, video: false };

    /*
       Disable the record button until we get a success or fail from getUserMedia()
   */

    recordButton.disabled = true;
    stopButton.disabled = false;
    pauseButton.disabled = false;

    /*
        We're using the standard promise based getUserMedia()
        https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
    */

    navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
        console.log("getUserMedia() success, stream created, initializing Recorder.js ...");

        /*
            create an audio context after getUserMedia is called
            sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
            the sampleRate defaults to the one set in your OS for your playback device
        */
        audioContext = new AudioContext();

        // update the format
        // document.getElementById("formats").innerHTML="Format: 1 channel pcm @ "+audioContext.sampleRate/1000+"kHz"

        /*  assign to gumStream for later use  */
        gumStream = stream;

        /* use the stream */
        input = audioContext.createMediaStreamSource(stream);

        /*
            Create the Recorder object and configure to record mono sound (1 channel)
            Recording 2 channels  will double the file size
        */
        rec = new Recorder(input, {numChannels: 1});

        //start the recording process
        rec.record();

        console.log("Recording started");

    })
    .catch(function(err) {
        //enable the record button if getUserMedia() fails
        recordButton.disabled = false;
        stopButton.disabled = true;
        pauseButton.disabled = true;
        console.log(err.message);
    });
}

function pauseRecording(){
    console.log("pauseButton clicked rec.recording=", rec.recording);
    if (rec.recording) {
        //pause
        rec.stop();
        pauseButton.innerHTML = "Возобновить";
    }
    else {
        //resume
        rec.record();
        pauseButton.innerHTML = "Пауза";
    }
}

function stopRecording() {
    console.log("stopButton clicked");

    //disable the stop button, enable the record too allow for new recordings
    stopButton.disabled = true;
    recordButton.disabled = false;
    pauseButton.disabled = true;

    //reset button just in case the recording is stopped while paused
    pauseButton.innerHTML = "Пауза";

    //tell the recorder to stop the recording
    rec.stop();

    //stop microphone access
    gumStream.getAudioTracks()[0].stop();

    //create the wav blob and pass it on to createDownloadLink
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
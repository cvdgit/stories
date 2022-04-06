<?php
$this->registerCss(<<<CSS
.question-recorder__wrap {
    padding: 20px 0 0 0;
}
.question-recorder__controls {
    display: flex;
}
.recorder__control {
    margin-right: 20px;
}
.recorder__control--recording,
.recorder__control--recording:hover {
    color: red;
}
.question-recorder__audio-list {
    margin: 20px 0;
}
.recorder__status {
    display: flex;
    align-items: center;
}
.audio-list__controls {
    text-align: center;
    padding: 10px 0;
}
.audio-list__wrap audio {
    width: 100%;
}
CSS
);
/** @var $questionId int */
/** @var $existsAudioUrl */
/** @var $callback */
?>
<div class="question-recorder__wrap">
    <div id="recorder-block" style="display: none" class="question-recorder__controls">
        <div class="recorder__control">
            <button id="recorder-control" type="button" class="btn btn-default">Записать аудио</button>
        </div>
        <div class="recorder__status">
            <span id="recorder-status"></span>
        </div>
    </div>
    <div id="audio-block" style="display: none" class="question-recorder__audio-list">
        <div class="audio-list__wrap" id="audio-list"></div>
        <div id="waveform"></div>
        <p class="text-center" style="margin-top: 10px">
            <button type="button" class="btn btn-xs btn-primary" id="wave-play"><i class="glyphicon glyphicon-play"></i> Прослушать</button>
            <button type="button" class="btn btn-xs btn-primary" id="wave-cut"><i class="glyphicon glyphicon-floppy-disk"></i> Обрезать</button>
        </p>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    
    var recorderButton = $('#recorder-control');
    var recorderTimeout;
    
    var statusElement = $('#recorder-status');
    function setStatusText(text) {
        statusElement.text(text);
    }
    
    function disableButton(button) {
        button.prop('disabled', true);
        button.addClass('disabled');
    }
    
    function enableButton(button) {
        button.prop('disabled', false);
        button.removeClass('disabled');
    }
    
    var audioBlock = $('#audio-block');
    
    var existsAudioUrl = '';
    function getAudioFilePath() {
        return existsAudioUrl;
    }
    function setAudioFilePath(path) {
        existsAudioUrl = path;
    }
    function audioFileExists() {
        return getAudioFilePath() !== '';
    }
    
    var callback = $callback;
    
    var audioList = $('#audio-list');
    var saveButton = $('#save-audio');
    var deleteButton = $('#delete-audio');
    
    var recorderBlock = $('#recorder-block');
    
    if (audioFileExists()) {
        var audio = createAudioElement(getAudioFilePath());
        audioList
            .empty()
            .append(audio);
        audioBlock.show();
        deleteButton.show();
    }
    else {
        recorderBlock.show();
    }

    function createSeconds(value) {
        var date = new Date(0);
        date.setSeconds(value);
        return date.toISOString().substr(11, 8);
    }
    
    var seconds = 0;
    
    recorderButton.on('click', function(e) {
        
        if (recorderTimeout) {
            clearInterval(recorderTimeout);
        }  
        
        var state = $(this).attr('data-state');
        if (state === 'recording') {
            recorderButton.removeAttr('data-state');
            recorderButton.html('Записать аудио');
            recorderButton.removeClass('recorder__control--recording');
            //setStatusText('');
            stopRecording();
        }
        else {
            
            setStatusText('...');
            disableButton(recorderButton);
            
            if (audioBlock.is(':visible')) {
                audioBlock.hide();
            }
            
            seconds = 0;
            recorderTimeout = setInterval(function() {
                setStatusText(createSeconds(seconds));
                seconds++;
            }, 1000);
            
            startRecording(function() {
                recorderButton.addClass('recorder__control--recording');
                recorderButton.html('<i class="glyphicon glyphicon-record"></i> Остановить');
                recorderButton.attr('data-state', 'recording');
                enableButton(recorderButton);
            });
        }
    });
    
    var AudioContext = window.AudioContext || window.webkitAudioContext;
    var userMediaStream;
    var recorder;

    function startRecording(startCallback) {
        var constraints = {audio: true, video: false};
        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {

                var audioContext = new AudioContext();
                userMediaStream = stream;
                recorder = new Recorder(audioContext.createMediaStreamSource(stream), {numChannels: 1});
                recorder.record();
                
                startCallback();
            })
            .catch(function(error) {
                console.log(error);
            });
    }
    
    function stopRecording() {
        recorder.stop();
        userMediaStream.getAudioTracks()[0].stop();
        recorder.exportWAV(createDownloadLink);
    }
    
    var URL = window.URL || window.webkitURL;
    
    function createAudioElement(src) {
        return $('<audio/>', {
            controls: true,
            src: src
        });
    }
    
    function bufferToWave(abuffer, offset, len) {
    
        var numOfChan = abuffer.numberOfChannels,
            length = len * numOfChan * 2 + 44,
            buffer = new ArrayBuffer(length),
            view = new DataView(buffer),
            channels = [], i, sample,
            pos = 0;
    
        // write WAVE header
        setUint32(0x46464952);                         // "RIFF"
        setUint32(length - 8);                         // file length - 8
        setUint32(0x45564157);                         // "WAVE"
        
        setUint32(0x20746d66);                         // "fmt " chunk
        setUint32(16);                                 // length = 16
        setUint16(1);                                  // PCM (uncompressed)
        setUint16(numOfChan);
        setUint32(abuffer.sampleRate);
        setUint32(abuffer.sampleRate * 2 * numOfChan); // avg. bytes/sec
        setUint16(numOfChan * 2);                      // block-align
        setUint16(16);                                 // 16-bit (hardcoded in this demo)
        
        setUint32(0x61746164);                         // "data" - chunk
        setUint32(length - pos - 4);                   // chunk length
    
        // write interleaved data
        for (i = 0; i < abuffer.numberOfChannels; i++) {
            channels.push(abuffer.getChannelData(i));
        }
    
        while (pos < length) {
            for (i = 0; i < numOfChan; i++) {             // interleave channels
                sample = Math.max(-1, Math.min(1, channels[i][offset])); // clamp
                sample = (0.5 + sample < 0 ? sample * 32768 : sample * 32767)|0; // scale to 16-bit signed int
                view.setInt16(pos, sample, true);          // update data chunk
                pos += 2;
            }
            offset++                                     // next source sample
        }
    
        // create Blob
        //return (URL || webkitURL).createObjectURL(new Blob([buffer], {type: "audio/wav"}));
        return new Blob([buffer], {type: "audio/wav"});
    
        function setUint16(data) {
            view.setUint16(pos, data, true);
            pos += 2;
        }
        
        function setUint32(data) {
            view.setUint32(pos, data, true);
            pos += 4;
        }
    }
    
    function trimBlob(wavesurfer, start, end) {

        var originalAudioBuffer = wavesurfer.backend.buffer;
        
        var lengthInSamples = Math.floor((end - start) * originalAudioBuffer.sampleRate);

        var offlineAudioContext = wavesurfer.backend.ac;
        
        var emptySegment = offlineAudioContext.createBuffer(
            originalAudioBuffer.numberOfChannels, 
            lengthInSamples + 1,
            originalAudioBuffer.sampleRate);
        
        var newAudioBuffer = offlineAudioContext.createBuffer(
            originalAudioBuffer.numberOfChannels, 
            (start === 0 ? (originalAudioBuffer.length - emptySegment.length) : originalAudioBuffer.length), 
            originalAudioBuffer.sampleRate);

        var new_channel_data, empty_segment_data, original_channel_data, before_data, after_data, mid_data;
        for (var channel = 0; channel < originalAudioBuffer.numberOfChannels; channel++) {

            new_channel_data = newAudioBuffer.getChannelData(channel);
            empty_segment_data = emptySegment.getChannelData(channel);
            original_channel_data = originalAudioBuffer.getChannelData(channel);
            
            before_data = original_channel_data.subarray(0, start * originalAudioBuffer.sampleRate);
            mid_data = original_channel_data.subarray(start * originalAudioBuffer.sampleRate, end * originalAudioBuffer.sampleRate);
            after_data = original_channel_data.subarray(Math.floor(end * originalAudioBuffer.sampleRate), (originalAudioBuffer.length * originalAudioBuffer.sampleRate));

            empty_segment_data.set(mid_data);
            if (start > 0) {
                new_channel_data.set(before_data);
                //new_channel_data.set(empty_segment_data, (start * newAudioBuffer.sampleRate));
                new_channel_data.set(after_data, (start * newAudioBuffer.sampleRate));
            } else {
                new_channel_data.set(after_data);
            }
        }

        return {
            newAudioBuffer,
            emptySegment
        };
    }
    
    function createDownloadLink(blob) {
        
        var url = URL.createObjectURL(blob);
        
        /*var audio = createAudioElement(url);
        audioList
            .empty()
            .append(audio);*/
        
        /*if (!audioFileExists()) {
            saveButton.show();
        }*/

        if (!audioBlock.is(':visible')) {
            audioBlock.fadeIn();
        }

        var wavesurfer = WaveSurfer.create({
            container: '#waveform',
            height: 100,
            scrollParent: true,
            normalize: true,
            plugins: [
                RegionPlugin.create()
            ]
        });

        wavesurfer.on('ready', function() {
            wavesurfer.addRegion({
                id: 'audio',
                start: 0,
                end: wavesurfer.getDuration()
            });
            callback(blob);
        });

        wavesurfer.load(url);
        
        $('#wave-play').on('click', function() {
            wavesurfer.regions.list['audio'].play();
        });

        $('#wave-cut').on('click', function() {
            
            var region = wavesurfer.regions.list['audio'];
            var buf = trimBlob(wavesurfer, region.start, region.end);
            var cutSelection = buf.emptySegment;

            var arrayBuffer = bufferToWave(cutSelection, 0, cutSelection.length);
            callback(arrayBuffer);
        });
    }
    
    function postForm(url, formData) {
        return $.ajax({
            'url': url,
            'type': 'POST',
            'data': formData,
            'cache': false,
            'contentType': false,
            'processData': false
        });
    }
    
    function doneCallback(response) {
        if (response) {
            if (response.success) {
                toastr.success('Действие выполнено успешно');
            }
            else {
                toastr.error(response.message);
            }
        }
        else {
            toastr.error('Неизвестная ошибка');
        }
    }
    
    var questionId = $questionId;
    saveButton.on('click', function() {
        var audio = audioList.find('audio:eq(0)');
        if (!audio.length) {
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', audio.attr('src'));
        xhr.responseType = 'blob';
        xhr.onload = function() {

            var formData = new FormData();
            formData.append('QuestionAudioModel[audio_file]', xhr.response, new Date().getTime() + '.wav');
            postForm('/admin/index.php?r=question/set-audio-file', formData)
                .done(doneCallback)
                .done(function(response) {
                    if (response && response.success) {
                        setAudioFilePath(response.url);
                        saveButton.hide();
                        deleteButton.show();
                        recorderBlock.fadeOut();
                    }
                });
        }
        xhr.send();
    });
    
    deleteButton.on('click', function(e) {
        if (!confirm('Удалить аудио из вопроса?')) {
            return;
        }
        var formData = new FormData();
        formData.append('DeleteQuestionAudioModel[question_id]', questionId);
        postForm('/admin/index.php?r=question/delete-audio-file', formData)
            .done(doneCallback)
            .done(function(response) {
                if (response && response.success) {
                    setAudioFilePath('');
                    recorderBlock.fadeIn();
                    audioBlock.hide();
                    saveButton.hide();
                    deleteButton.hide();
                }
            });
    });
})();
JS
);
<?php
\backend\assets\RecorderAsset::register($this);
$this->registerCss(<<<CSS
.question-recorder__wrap {
    padding: 20px 0;
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
/** @var $model backend\models\question\UpdateQuestion */
?>
<div class="question-recorder__wrap">
    <div id="recorder-block" style="display: none" class="question-recorder__controls">
        <div class="recorder__control">
            <button id="recorder-control" type="button" class="btn btn-default">Записать аудио</button>
        </div>
        <div class="recorder__status">
            <span id="recorder-status">Запись начнется через 3 секунды</span>
        </div>
    </div>
    <div id="audio-block" style="display: none" class="question-recorder__audio-list">
        <div class="audio-list__wrap" id="audio-list"></div>
        <div class="audio-list__controls">
            <button id="save-audio" type="button" style="display: none" class="btn btn-primary btn-sm">Сохранить аудио</button>
            <button id="delete-audio" type="button" style="display: none" class="btn btn-danger btn-sm">Удалить аудио</button>
        </div>
    </div>
</div>
<?php
$questionId = $model->getModelID();
$existsAudioUrl = $model->getAudioFileUrl();
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
    
    var existsAudioUrl = '$existsAudioUrl';
    function getAudioFilePath() {
        return existsAudioUrl;
    }

    function setAudioFilePath(path) {
        existsAudioUrl = path;
    }
    
    function audioFileExists() {
        return getAudioFilePath() !== '';
    }
    
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

    recorderButton.on('click', function(e) {
        var state = $(this).attr('data-state');
        if (state === 'recording') {
            recorderButton.removeAttr('data-state');
            recorderButton.html('Записать аудио');
            recorderButton.removeClass('recorder__control--recording');
            setStatusText('');
            stopRecording();
        }
        else {
            
            if (recorderTimeout) {
                clearInterval(recorderTimeout);
            }
            
            setStatusText('');
            disableButton(recorderButton);
            
            if (audioBlock.is(':visible')) {
                audioBlock.hide();
            }
            
            var second = 4;
            recorderTimeout = setInterval(function() {
                second--;
                setStatusText(second);
                if (second <= 0) {
                    
                    clearInterval(recorderTimeout);
                    setStatusText('Запись...');
                    
                    startRecording(function() {
                        recorderButton.addClass('recorder__control--recording');
                        recorderButton.html('<i class="glyphicon glyphicon-record"></i> Остановить');
                        recorderButton.attr('data-state', 'recording');
                        enableButton(recorderButton);
                    });
                }
            }, 1000);
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
    
    function createDownloadLink(blob) {
        
        var audio = createAudioElement(URL.createObjectURL(blob));
        audioList
            .empty()
            .append(audio);
        
        if (!audioFileExists()) {
            saveButton.show();
        }
        
        if (!audioBlock.is(':visible')) {
            audioBlock.fadeIn();
        }
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
    saveButton.on('click', function(e) {
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
            formData.append('QuestionAudioModel[question_id]', questionId);
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
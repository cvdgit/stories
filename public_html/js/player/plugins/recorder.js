var WikidsRecorder = window.WikidsRecorder || (function() {
    "use strict";

    var config = Reveal.getConfig().wikidsRecorder;

    var $recorder = $("<div/>");
    $recorder.addClass("wikids-recorder");

    var $recorderInner = $("<div/>");
    $recorderInner.addClass("wikids-recorder-inner");
    $recorderInner.appendTo($recorder);

    function getCurrentTrack() {
        return config.currentTrack;
    }

    var $closeButton = $("<button/>");
    $closeButton.addClass("close");
    $closeButton.html('×');
    $closeButton.on("click", showRecorder);
    $recorderInner.append($closeButton);

    var $trackWrapper = $("<div/>");
    $trackWrapper.addClass("recorder-tracks");

    config.tracks = config.tracks || [];
    var $trackSelect = $("<select/>");
    if (config.tracks.length === 0) {
        $trackSelect.css("display", "none");
    }
    $trackSelect.on("change", function () {
        var trackID = $(this).val();
        location.href = location.origin + location.pathname + '?track_id=' + trackID + location.hash;
    });
    $.each(config.tracks, function (i, track) {
        $("<option/>")
            .val(track.id)
            .text(track.name)
            .prop("selected", track.id === getCurrentTrack().id)
            .appendTo($trackSelect);
    });
    $trackWrapper.append($trackSelect);

    var $newTrackLink = $("<a/>");
    $newTrackLink
        .attr("href", "#")
        .html('<i class="glyphicon glyphicon-plus"></i>')
        .addClass("recorder-create-track")
        .attr("id", "create-audio-track")
        .attr("title", "Создать дорожку")
        .on("click", function(e) {
            e.preventDefault();
            var promise = $.get(config.createTrackAction);
            promise.done(function(data) {
                if (data && data.success) {
                    $("<option/>")
                        .val(data.track.id)
                        .text(data.track.name)
                        .attr("selected", "selected")
                        .appendTo($trackSelect);
                    $trackSelect.change();
                }
            });
        });
    $trackWrapper.append($newTrackLink);

    //console.log("getCurrentTrack", getCurrentTrack());
    //console.log("isUserTrack", isUserTrack());

    if (isUserTrack()) {
        var $deleteTrackLink = $("<a/>");
        $deleteTrackLink
            .attr("href", "#")
            .html('<i class="glyphicon glyphicon-remove"></i>')
            .addClass("recorder-delete-track")
            .attr("id", "delete-audio-track")
            .attr("title", "Удалить дорожку")
            .on("click", function (e) {
                e.preventDefault();
                var promise = $.get(config.deleteTrackAction + "?id=" + getCurrentTrack().id);
                promise.done(function (data) {
                    if (data && data.success) {
                        var href = location.origin + location.pathname + location.hash;
                        if (href === location.href) {
                            location.reload();
                        }
                        else {
                            location.href = href;
                        }
                    }
                });
            });
        $trackWrapper.append($deleteTrackLink);
    }

    $recorderInner.append($trackWrapper);

    var $controls = $("<div/>");
    $controls.addClass("recorder-controls");

    var timeout;
    var $recordButton = $("<button/>")
        .attr("id", "audioRecord")
        .html('Записать')
        .addClass("btn")
        .on("click", function() {
            if (timeout) {
                clearTimeout(timeout);
            }
            var second = 4;
            $recorderTimer.text('').show();
            timeout = setInterval(function() {
                second--;
                $recorderTimer.text(second);
                if (second <= 0) {
                    clearInterval(timeout);
                    $recorderTimer.hide();
                    startRecording();
                }
            }, 1000);
        });
    $recordButton.appendTo($controls);

    var $pauseButton = $("<button/>")
        .attr("id", "audioPause")
        .text("Пауза")
        .addClass("btn")
        .prop("disabled", true)
        .on("click", pauseRecording);
    $pauseButton.appendTo($controls);

    var $stopButton = $("<button/>")
        .attr("id", "audioStop")
        .text("Стоп")
        .addClass("btn")
        .prop("disabled", true)
        .on("click", stopRecording);
    $stopButton.appendTo($controls);

    var $recorderStatus = $("<div/>");
    $recorderStatus.addClass("recorder-status");
    $recorderStatus.appendTo($controls);

    $recorderInner.append($controls);

    function setRecordingStatus(state) {
        var color = '';
        if (state === "recording") {
            color = $recordButton.css("color");
            $recordButton.css({color: "red"}).html('<i class="glyphicon glyphicon-record"></i> Запись');
        }
        if (state === "pause") {
            $recordButton.css({color: color}).html('Записать');
        }
        if (state === "stop") {
            $recordButton.css({color: color}).html('Записать');
        }
    }

    var $recorderAudio = $("<div/>");
    $recorderAudio.addClass("recorder-audio");

    var $recorderList = $("<ul/>");
    $recorderList.addClass("list-unstyled");
    $recorderList.attr("id", "recordingsList");

    $recorderAudio.append($recorderList);

    var $mergeButton = $("<button/>");
    $mergeButton
        .addClass("recorder-merge-button btn")
        .attr("id", "mergeAllSlideAudio")
        .on("click", function() {
            WikidsPlayer.mergeAllAndSetSlideAudio(getCurrentTrack().id);
        })
        .text("Объединить все и применить");
    $recorderAudio.append($mergeButton);

    var $recorderTimer = $("<div/>");
    $recorderTimer.addClass("recorder-timer");
    $recorderAudio.append($recorderTimer);

    $recorderInner.append($recorderAudio);

    $(".reveal").append($recorder);

    function showRecorder() {
        $recorder.fadeToggle();
    }

    function isUserTrack() {
        return getCurrentTrack().type === 1;
    }

    function showRecorderControls() {
        if (isUserTrack()) {
            $controls.show();
        }
        else {
            $controls.hide();
        }
    }

    showRecorderControls();

    //webkitURL is deprecated but nevertheless
    URL = window.URL || window.webkitURL;

    var gumStream; 						//stream from getUserMedia()
    var rec; 							//Recorder.js object
    var input; 							//MediaStreamAudioSourceNode we'll be recording

    var AudioContext = window.AudioContext || window.webkitAudioContext;
    var audioContext;

    function startRecording() {

        var constraints = { audio: true, video: false };

        $recordButton.prop("disabled", true);
        $stopButton.prop("disabled", false);
        $pauseButton.prop("disabled", false);

        navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
            audioContext = new AudioContext();
            gumStream = stream;
            input = audioContext.createMediaStreamSource(stream);
            rec = new Recorder(input, {numChannels: 1});
            rec.record();
            setRecordingStatus('recording');
        })
        .catch(function(err) {
            $recordButton.prop("disabled", false);
            $stopButton.prop("disabled", true);
            $pauseButton.prop("disabled", true);
        });
    }

    function pauseRecording() {
        setRecordingStatus('pause');
        if (rec.recording) {
            rec.stop();
            $pauseButton.text("Возобновить");
        }
        else {
            rec.record();
            $pauseButton.text("Пауза");
        }
    }

    function stopRecording() {
        setRecordingStatus('stop');
        $stopButton.prop("disabled", true);
        $recordButton.prop("disabled", false);
        $pauseButton.prop("disabled", true);

        $pauseButton.text("Пауза");

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
        link.title = "Применить";
        link.innerHTML = '<i class="glyphicon glyphicon-ok"></i>';
        link.onclick = function(e) {
            e.preventDefault();
            WikidsPlayer.setSlideAudio(blob);
        };
        li.appendChild(link);

        var deleteLink = document.createElement("a");
        deleteLink.href = "#";
        deleteLink.title = "Удалить";
        deleteLink.innerHTML = '<i class="glyphicon glyphicon-remove"></i>';
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





    return {
        "showRecorder": showRecorder,
        "getCurrentTrackID": function() {
            return getCurrentTrack().id;
        }
    };
})();
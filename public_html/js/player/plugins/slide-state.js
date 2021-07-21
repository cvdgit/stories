
(function() {

    var config = Reveal.getConfig().slideState;
    const STORAGE_KEY = 'wikids_slide_state';

    function initStates() {
        var states = localStorage.getItem(STORAGE_KEY);
        if (states) {
            states = JSON.parse(states);
        }
        else {
            states = [];
        }
        return states;
    }

    function createStateItem(storyID, state) {
        return {'story_id': storyID, 'state': state};
    }

    function findState(states, storyID) {
        return states.filter(function(item) {
            return item.story_id === storyID;
        });
    }

    function updateState(states, state) {
        var current = findState(states, state.story_id);
        if (current.length > 0) {
            current[0].state = state.state;
        }
        else {
            states.push(state);
        }
    }

    var init = false;

    function updateStates() {
        var states = initStates();
        updateState(states, createStateItem(config.story_id, Reveal.getState()));
        saveStates(states);
    }

    function saveStates(states) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(states));
    }

    function createAlert() {

        var $recorderWrapper = $('<div/>', {'class': 'slide-state-alert-wrapper'});
        var $recorderInner = $('<div/>', {'class': 'slide-state-alert-inner'});
        var $recorder = $('<div/>', {'class': 'slide-state-alert'});

        $recorderInner.appendTo($recorderWrapper);
        $recorder.appendTo($recorderInner);

        $('<p/>', {'text': 'Вы хотите возобновить просмотр истории с прежнего места?'})
            .appendTo($recorder);
        $('<div/>', {'class': 'buttons'})
            .append(
                $('<button/>', {'text': 'Да', 'class': 'btn'})
                    .on('click', function() {
                        $recorderWrapper.fadeToggle('fast');
                        var states = initStates();
                        var current = findState(states, config.story_id);
                        Reveal.setState(current[0].state);
                    })
            )
            .append(
                $('<button/>', {'text': 'Нет', 'class': 'btn'})
                    .on('click', function() {
                        updateStates();
                        $recorderWrapper.fadeToggle('fast');
                    })
            )
            .appendTo($recorder);

        $('.reveal').append($recorderWrapper);
        $recorderWrapper.fadeToggle('fast');
    }

    function displayAlert() {
        if (init) {
            return false;
        }
        var states = initStates();
        var current = findState(states, config.story_id);
        if (current.length > 0) {
            current = current[0];
            if (current.state.indexh > 0 && current.state.indexh !== Reveal.getIndices().h) {
                createAlert();
                return true;
            }
        }
        return false;
    }

    Reveal.addEventListener('ready', function() {
        var display = displayAlert();
        init = true;
        if (!display) {
            updateStates();
        }
    });

    Reveal.addEventListener('slidechanged', function() {
        var display = displayAlert();
        init = true;
        if (!display) {
            updateStates();
        }
    });
})();

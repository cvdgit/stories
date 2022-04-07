class TestProgress {

    constructor(progress) {

        this.progress = progress;

        this.element = document.createElement('div');
        this.element.classList.add('wikids-progress');
    }

    render() {

        const barElement = document.createElement('div');
        barElement.classList.add('progress-bar');
        barElement.classList.add('progress-bar-info');
        barElement.style.width = this.calcPercent() + '%';
        barElement.style.minWidth = '2em';
        barElement.textContent = ' ' + this.calcPercent() + '%';

        this.element.appendChild(barElement);

        return this.element;
    }

    calcPercent() {
        return Math.round(this.progress.getCurrent() * 100 / this.progress.getTotal());
    }

    inc() {
        this.progress.incCurrent();
    }

    dec() {
        this.progress.decCurrent();
        if (this.progress.getCurrent() < 0) {
            this.progress.setCurrent(0);
        }
    }

    getCurrent() {
        return this.progress.getCurrent();
    }

    updateProgress() {
        let progress = this.calcPercent();
        //$('.wikids-progress', dom.header).attr('title', getCurrentProgressStateText()).tooltip('fixTitle');
        this.element.querySelector('.progress-bar').style.width = progress + '%';
        this.element.querySelector('.progress-bar').textContent = ' ' + progress + '%';
    }
}

export default TestProgress;
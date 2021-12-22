class QuestionStars {

    constructor(stars, repeat) {
        this.stars = stars;
        this.repeat = parseInt(repeat || '1');
    }

    render() {

        const starsElement = document.createElement('div');
        starsElement.classList.add('row');
        starsElement.classList.add('row-no-gutters');
        starsElement.classList.add('question-stars');

        starsElement.innerHTML =
            `<div class="col-md-6 hints"></div>
             <div class="col-md-6 stars"></div>`;

        for (let i = 0, starElement, className; i < this.repeat; i++) {
            starElement = document.createElement('i');
            className = 'star-empty';
            if (i + 1 <= this.stars.getCurrent()) {
                className = 'star';
            }
            starElement.classList.add('glyphicon');
            starElement.classList.add('glyphicon-' + className);
            starsElement.querySelector('.stars').appendChild(starElement);
        }

        return starsElement;
    }

    inc() {
        this.stars.incCurrent();
        let increased = true;
        if (this.stars.getCurrent() > this.repeat) {
            this.stars.setCurrent(this.repeat);
            increased = false;
        }
        return increased;
    }

    dec() {
        this.stars.decCurrent();
    }

    isDone() {
        return this.stars.getCurrent() >= this.repeat;
    }

    getCurrent() {
        let current = this.stars.getCurrent();
        if (current < 0) {
            this.stars.setCurrent(0);
        }
        return this.stars.getCurrent();
    }
}

export default QuestionStars;
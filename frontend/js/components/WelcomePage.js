class WelcomePage {

    constructor(model) {
        this.model = model;
        this.activeStudent = null;
    }

    renderStudents() {

        const listElement = document.createElement('div');
        listElement.classList.add('list-group');

        const setActiveItem = (item, student) => {
            listElement.querySelectorAll('a.list-group-item').forEach(elem => elem.classList.remove('active'));
            item.classList.add('active');
            this.activeStudent = student;
        };

        this.model.getStudents().forEach((student) => {

            const itemElement = document.createElement('a');
            itemElement.classList.add('list-group-item');
            itemElement.setAttribute('href', '#');
            itemElement.innerHTML = `<h4 class="list-group-item-heading">${student.getName()}</h4>`;
            if (student.getProgress() > 0) {
                const progressElement = document.createElement('p');
                progressElement.classList.add('list-group-item-text');
                progressElement.textContent = student.getProgress() + '% завершено';
                itemElement.appendChild(progressElement);
            }

            itemElement.addEventListener('click', (e) => {
                e.preventDefault();
                setActiveItem(e.target, student);
            });

            listElement.appendChild(itemElement);
        });

        setActiveItem(listElement.querySelector('a.list-group-item:first-child'), this.model.getStudents()[0]);

        return listElement;
    }

    render(testBeginCallback) {

        const element = document.createElement('div');
        element.classList.add('wikids-test-begin-page');

        const headerElement = document.createElement('h3');
        headerElement.textContent = this.model.getTestHeader();

        const wrapperElement = document.createElement('div');
        wrapperElement.classList.add('row-wrapper');
        wrapperElement.innerHTML =
            `<div class="row">
                 <div class="col-xs-12 col-sm-6 col-md-8">
                    <p class="wikids-test-description">${this.model.getTestDescription()}</p>
                 </div>
                 <div class="col-xs-12 col-sm-6 col-md-4 test-actions"></div>
             </div>`;

        const studentsHeaderElement = document.createElement('h4');
        studentsHeaderElement.textContent = 'Выберите ученика:';
        wrapperElement.querySelector('.test-actions').appendChild(studentsHeaderElement);
        wrapperElement.querySelector('.test-actions').appendChild(this.renderStudents());

        const buttonElement = document.createElement('button');
        buttonElement.classList.add('btn');
        buttonElement.classList.add('wikids-test-begin');
        buttonElement.textContent = 'Начать тест';
        buttonElement.addEventListener('click', (e) => {
            testBeginCallback(this.activeStudent);
        }, false);

        element.appendChild(headerElement);
        element.appendChild(wrapperElement);
        element.querySelector('.test-actions').appendChild(buttonElement);

        return element;
    }
}

export default WelcomePage;
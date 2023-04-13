import SlidesConfig from "../SlidesConfig";
import SlidesPlayer from "../SlidesPlayer";
import "./next_story.css";
import config from "reveal.js/js/config";

export default () => {
  return {

    id: 'next-story',

    init(deck) {

      const slidesPlayer = new SlidesPlayer(deck);

      const slidesConfig = new SlidesConfig();
      const config = this.config = slidesConfig.get(this.id);

      const createNextStory = (props={}) => {

        const createTitle = (props = {}) => {
          const title = document.createElement('h2');
          title.classList.add('h1');
          title.classList.add('next-story__text');
          title.textContent = props.is_complete ? 'История пройдена!' : `История пройдена на ${props.progress}%`;
          return title;
        };

        const element = document.createElement('div');
        element.setAttribute('style', 'width: 1280px; height: 720px; left: 0; top: 0');
        element.classList.add('sl-block');
        element.innerHTML = `
            <div class="sl-block-content">
                <div class="next-story"></div>
            </div>`;

        element.querySelector('.next-story').appendChild(createTitle(props));

        if (props.is_complete) {
          const nextButton = document.createElement('button');
          nextButton.setAttribute('type', 'button');
          nextButton.classList.add('btn');
          nextButton.classList.add('next-story__button');
          nextButton.textContent = 'Дальше';
          nextButton.addEventListener('click', props.onNextButtonClick);
          element.querySelector('.next-story').appendChild(nextButton);
        }

        const createTestingRow = (testingProps) => {
          const element = document.createElement('li');
          element.classList.add('text-left');
          element.innerHTML = `<a title="Перейти к слайду" class="story-test-row" href="#/${testingProps.slide_number}">${testingProps.test_name}</a> - <strong>${testingProps.progress}%</strong>`;
          return element;
        };

        if (props.tests.length > 0) {

          if (!props.is_complete) {
            const info = document.createElement('div');
            info.classList.add('alert');
            info.classList.add('alert-info');
            info.style.fontSize = '2rem';
            info.textContent = 'Чтобы завершить прохождение истории, необходимо пройти все тесты';
            element.querySelector('.next-story').appendChild(info);
          }

          const testHeader = document.createElement('h2');
          testHeader.classList.add('h3');
          testHeader.textContent = 'Прогресс прохождения тестов в истории:';
          element.querySelector('.next-story').appendChild(testHeader);

          const list = document.createElement('ul');
          list.style.fontSize = '2rem';
          props.tests.forEach(item => {
            list.appendChild(createTestingRow(item));
          });

          element.querySelector('.next-story').appendChild(list);
        }

        return element;
      }

      const transitionPlugin = deck.getPlugin('transition');
      const testingPlugin = deck.getPlugin('testing');
      const nextButtonHandler = () => {
        $.getJSON('/edu/default/get-next-story?story_id=' + config.story_id + '&program_id=' + config.program_id)
          .done(response => {
            if (response && response.success) {
              location.href = response.url;
            }
          });
      }

      deck.addEventListener('slidechanged', event => {

        if (deck.isLastSlide() && !transitionPlugin.getInTransition() && !testingPlugin.inTest()) {

          const $slide = $(deck.getCurrentSlide());
          $slide.addClass('next-story');

          $slide.empty();

          if (deck.hasPlugin('stat')) {

            const statPlugin = deck.getPlugin('stat');
            Promise.all(statPlugin.getStack()).then(values => {

              $.getJSON(`/edu/default/story-stat?story_id=${config.story_id}&student_id=${config.student_id}`)
                .done(response => {
                  if (response && response.success) {
                    $slide.append(createNextStory({...response.data, onNextButtonClick: nextButtonHandler}));
                  }
                });
            });
          }
        }
      });
    }
  }
}

import "./style.css";
import PresentationItemHandler from "../../common/PresentationVoiceControl";
import {userResponseChecker} from "../../mental_map_quiz/lib/userResponseProcessChain";

export default function SpeakSlideText(deck, config) {

  const {speakTextSlides, threshold, storyId} = config;

  async function saveUserResult(payload) {
    return await window.Api.post(`/retelling/save`, payload);
  }

  return {
    init() {

      deck.configure({keyboard: true});

      /** @var {HTMLElement} */
      const currentSlide = deck.getCurrentSlide();
      const slideId = currentSlide.dataset.id;
      const item = speakTextSlides.find(i => Number(i.slideId) === Number(slideId));
      if (!item) {
        return;
      }

      deck.configure({keyboard: false});
      $(currentSlide).off('click');

      currentSlide.querySelector('.speak-slide-text-info')?.remove();

      function createDoneInfo() {
        const info = document.createElement('div');
        info.className = 'speak-slide-text-info';
        info.innerHTML = `
        <div class="speak-slide-text-info-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
     stroke="currentColor" class="retelling-success">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg></div>
        <div>Пройдено</div>
        `;
        return info;
      }

      if (item.passed) {
        const info = createDoneInfo();
        currentSlide.appendChild(info);
        return;
      }

      const info = document.createElement('div');
      info.className = 'speak-slide-text-info';
      info.style.border = '1px #0dcaf0 solid';
      info.innerHTML = `
      <div class="speak-slide-text-info-icon" style="color: #0dcaf0"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
</svg>
</div>
      <div>Текст слайда нужно прочитать. Нажмите на текст и начнется запись с микрофона</div>
      `;
      currentSlide.appendChild(info);

      let presentationHandler;

      $(currentSlide)
        .on('click', "[data-block-type='text']", e => {

          if (presentationHandler && presentationHandler.isRecording()) {
            return;
          }

          const text = e.target.textContent;
          if (!text) {
            return;
          }

          currentSlide.querySelector('.speak-slide-text-info').style.display = 'none';

          presentationHandler = new PresentationItemHandler(
            async (userResponse) => {

              if (!userResponse) {
                currentSlide.querySelector('.speak-slide-text-info').style.display = 'flex';
                return true;
              }

              const response = await userResponseChecker(
                text,
                userResponse,
                threshold
              );

              const json = window.processOutputAsJson(response);
              const similarityPercentage = Number(json.similarity_percentage);

              await saveUserResult({
                overall_similarity: similarityPercentage,
                content: text,
                story_id: storyId,
                slide_id: slideId
              });

              if (similarityPercentage >= threshold) {
                item.passed = true;
                $(currentSlide).off('click');
                currentSlide.querySelector('.speak-slide-text-info')?.remove();
                currentSlide.appendChild(
                  createDoneInfo()
                );

                return;
              }
              currentSlide.querySelector('.speak-slide-text-info').style.display = 'flex';
            }
          );

          presentationHandler.handle(currentSlide);
        })
    },
    canNext() {
      const currentSlide = deck.getCurrentSlide();
      const slideId = currentSlide.dataset.id;
      const item = speakTextSlides.find(i => Number(i.slideId) === Number(slideId));
      if (!item) {
        return true;
      }
      return item.passed;
    }
  }
}

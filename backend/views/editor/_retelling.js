(function () {

  /**
   * Очищает текст от невидимых символов, лишних пробелов, дублирующихся переносов
   * и декодирует HTML-сущности.
   *
   * @param {string} text - Исходный текст
   * @param {object} options - Настройки очистки
   * @returns {string} - Очищенный текст
   */
  function cleanText(text, options = {}) {
    if (typeof text !== 'string') {
      return '';
    }

    const {
      decodeEntities = true, // Декодировать HTML сущности (&amp; -> &)
      stripHtml = true,     // Удалять ли HTML теги (опционально)
    } = options;

    // 1. Декодирование HTML-сущностей
    if (decodeEntities) {
      text = decodeHTMLEntities(text);
    }

    // 2. Удаление HTML-тегов (опционально, но часто требуется при очистке)
    if (stripHtml) {
      // Заменяем блочные теги и <br> на переносы строк, чтобы не слипать текст
      text = text.replace(/<\/(p|div|h[1-6]|li|tr)>/gi, '\n');
      text = text.replace(/<br\s*\/?>/gi, '\n');
      // Удаляем все остальные теги
      text = text.replace(/<[^>]+>/g, '');
    }

    // 3. Удаление невидимых и управляющих символов
    // \u200B-\u200F, \u202A-\u202E, \u2060-\u206F, \uFEFF - невидимые пробелы, BOM, маркеры направления
    // \x00-\x08, \x0B, \x0C, \x0E-\x1F, \x7F-\x9F - управляющие символы (кроме \n, \r, \t)
    text = text.replace(/[\u200B-\u200F\u202A-\u202E\u2060-\u206F\uFEFF\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/g, '');

    // 4. Нормализация и схлопывание переносов строк
    // Приводим все переносы (Windows \r\n, Mac \r) к стандарту Unix \n
    text = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    // Заменяем множественные переносы на одинарные
    text = text.replace(/\n{2,}/g, '\n');

    // 5. Схлопывание лишних пробелов и табуляций
    // Заменяем последовательности пробелов и табов на один пробел
    text = text.replace(/[ \t]+/g, ' ');

    // 6. Финальная_trim_ка (удаление пробелов в начале/конце строк и всего текста)
    text = text.split('\n').map(line => line.trim()).join('\n');
    text = text.trim();

    return text;
  }

  function decodeHTMLEntities(text) {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = text;
    return textarea.value;
  }

  window.EditorRetelling = function () {

    let isStreaming = false

    function addGenerateEvents($dialog) {
      $dialog.find('.retelling-questions-generate').on('click', e => {
        e.preventDefault()

        if (isStreaming) {
          return
        }

        const text = cleanText(
          $dialog.find('.retelling-slide-text').text()
        )
        if (!text) {
          return
        }

        isStreaming = true

        window.sendStreamMessage(
          '/admin/index.php?r=gpt/stream/retelling-answers',
          {slideTexts: text},
          message => {
            $dialog.find('.retelling-questions').html(message)
          },
          () => isStreaming = false,
          errorText => {
            isStreaming = false
            toastr.error(errorText)
          }
        )
      })
      $dialog.find('.retelling-with-questions').on('click', e => {
        $dialog.find('.retelling-questions-generate').toggle()
      })
    }

    /**
     * @param {int} storyId
     * @param {int} slideId
     * @param {boolean} withQuestions
     * @param {string} questions
     * @param {boolean} required
     * @param {number} threshold
     */
    async function createHandler(storyId, slideId, withQuestions, questions, required, threshold) {
      const formData = new FormData()
      formData.append('story_id', storyId.toString())
      formData.append('slide_id', slideId.toString())
      formData.append('with_questions', withQuestions ? 1 : 0)
      formData.append('questions', questions)
      formData.append('required', required ? 1 : 0)
      formData.append('threshold', threshold)
      return await window.Api.postForm(
        `/admin/index.php?r=editor/retelling&current_slide_id=${slideId}&story_id=${storyId}`,
        formData
      )
    }

    async function updateHandler(retellingId, slideId, blockId, withQuestions, questions, required, threshold) {
      const formData = new FormData()
      formData.append('with_questions', withQuestions ? '1' : '0')
      formData.append('questions', questions)
      formData.append('required', required ? '1' : '0')
      formData.append('threshold', threshold)
      return await window.Api.postForm(
        `/admin/index.php?r=editor/update-retelling&retelling_id=${retellingId}&slide_id=${slideId}&block_id=${blockId}`,
        formData
      )
    }

    const dialogTemplate = `<div class="modal rounded-0 fade" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <h5 class="modal-title" style="margin-right: auto">...</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body d-flex">
                <div style="display: flex; flex-direction: row; column-gap: 20px;margin-bottom: 10px">
                    <div style="flex: 1">
                        <label for="retelling-threshold">Точность пересказа:</label>
                        <select class="form-control" id="retelling-threshold" style="width: 50%">
                            <option value="90">90%</option>
                            <option value="85">85%</option>
                            <option value="80">80%</option>
                            <option value="75">75%</option>
                            <option value="70">70%</option>
                            <option value="60">60%</option>
                            <option value="50">50%</option>
                            <option value="40">40%</option>
                            <option value="30">30%</option>
                            <option value="20">20%</option>
                            <option value="10">10%</option>
                        </select>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column">
                        <div style="display: flex; justify-content: right">
                            <div class="dropdown">
                                <button class="btn-link dropdown-toggle" type="button" data-toggle="dropdown">
                                    Промты
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a target="_blank" href="/admin/index.php?r=llm-prompt/update-by-key-form&key=retelling-questions">Промт для генерации вопросов (ред.)</a></li>
                                </ul>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: row; width: 100%; align-items: end; gap: 10px">
                            <label style="margin: 0; display: flex; flex-direction: row; gap: 10px; align-items: center">
                                Пересказ с вопросами <input class="retelling-with-questions" style="margin: 0"
                                                            type="checkbox">
                            </label>
                            <a class="retelling-questions-generate" style="display: none" href="">Сгенерировать
                                вопросы</a>
                        </div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: row; column-gap: 20px">
                    <div style="flex: 1">
                        <textarea class="textarea retelling-slide-text" readonly
                                  style="width:100%; height: 500px; overflow-y: auto"></textarea>
                    </div>
                    <div style="flex: 1">
                        <div class="textarea retelling-questions" contenteditable="plaintext-only"
                             style="height: 500px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; font-size: 14px"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="position: relative">
                <div>
                    <label>
                        Обязательный для прохождения <input checked class="retelling-required" type="checkbox">
                    </label>
                    <button type="button" class="btn btn-primary retelling-action">...</button>
                </div>
                <div class="retelling-loader"
                     style="display: none; position:absolute; align-items: center; justify-content: center; left: 0; top: 0; width: 100%; height: 100%; background-color: #fff">
                    <img src="/img/loading.gif" width="30" alt="">
                </div>
            </div>
        </div>
    </div>
</div>`

    this.showModal = ({storyId, slideId, texts}) => {

      const $createModal = $(dialogTemplate).modal();

      addGenerateEvents($createModal);

      $('.modal-title', $createModal).text('Новый пересказ')

      const $loader = $createModal.find('.retelling-loader')
      $loader.css('display', 'none')

      $createModal.find('.retelling-slide-text').html(
        cleanText(texts)
      )

      $createModal.find('.retelling-action')
        .text('Создать')
        .on('click', async e => {

          const withQuestions = $createModal.find('.retelling-with-questions').is(':checked')
          const required = $createModal.find('.retelling-required').is(':checked')

          let questions = ''
          if (withQuestions) {
            questions = $createModal
              .find('.retelling-questions')
              .text()
              .replace(/```\n?|```/g, '')
              .trim()
          }

          if (withQuestions && !questions.length) {
            alert('Нет вопросов')
            return
          }

          $loader.css('display', 'flex')

          const threshold = Number($createModal.find('#retelling-threshold').val())

          const json = await createHandler(
            storyId,
            slideId,
            withQuestions,
            questions,
            required,
            threshold
          )

          $createModal.modal('hide')
          StoryEditor.loadSlides(json?.slide_id)
        })

      $createModal.modal('show')
    }

    this.showUpdateModal = ({storyId, slideId, blockId}) => {

      const $updateModal = $(dialogTemplate).modal();

      addGenerateEvents($updateModal);
      $('.modal-title', $updateModal).text('Изменить пересказ')

      const $loader = $updateModal.find('.retelling-loader')
      $loader.css('display', 'none')

      $updateModal.find('.retelling-action').text('Сохранить')

      $updateModal
        .on('show.bs.modal', async (e) => {

          const {
            storyId,
            slideId,
            blockId
          } = e.relatedTarget

          const json = await window.Api.get(`/admin/index.php?r=editor/load-retelling&story_id=${storyId}&slide_id=${slideId}&block_id=${blockId}`)

          const {
            texts,
            withQuestions,
            questions,
            required,
            retellingId,
            settings
          } = json?.retelling || {}

          $updateModal.find('.retelling-slide-text').html(
            cleanText(texts)
          )
          $updateModal.find('.retelling-with-questions').prop('checked', withQuestions)
          if (withQuestions) {
            $updateModal.find('.retelling-questions-generate').toggle()
          }
          $updateModal.find('.retelling-questions').html(questions)
          $updateModal.find('.retelling-required').prop('checked', required)

          if (settings) {
            const {threshold} = settings
            $updateModal.find('#retelling-threshold').val(threshold)
          }

          $updateModal
            .find('.retelling-action')
            .on('click', async (e) => {

              const withQuestions = $updateModal.find('.retelling-with-questions').is(':checked')
              const required = $updateModal.find('.retelling-required').is(':checked')

              let questions = ''
              if (withQuestions) {
                questions = $updateModal
                  .find('.retelling-questions')
                  .text()
                  .replace(/```\n?|```/g, '')
                  .trim()
              }

              if (withQuestions && !questions.length) {
                alert('Нет вопросов')
                return
              }

              const threshold = Number($updateModal.find('#retelling-threshold').val())

              $loader.css('display', 'flex')

              let json
              try {
                json = await updateHandler(retellingId, slideId, blockId, withQuestions, questions, required, threshold)
              } catch (ex) {
                toastr.error(ex.message)
                $loader.css('display', 'none')
                return
              }

              if (!json.success) {
                toastr.error(json?.message || 'Ошибка')
                $loader.css('display', 'none')
                return
              }

              StoryEditor.updateSlideBlock(json.block_id, json.html);
              toastr.success('Блок успешно изменен');

              $updateModal.modal('hide')
            })
        })

      $updateModal.modal('show', {storyId, slideId, blockId})
    }
  }
})()

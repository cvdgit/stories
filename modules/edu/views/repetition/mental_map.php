<?php

declare(strict_types=1);

use frontend\assets\MentalMapAsset;
use frontend\assets\TestAsset;
use modules\edu\models\MentalMap;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var MentalMap $mentalMap
 * @var int|null $storyId
 * @var int|null $slideId
 */

$this->title = 'Повторение ментальной карты';

TestAsset::register($this);
MentalMapAsset::register($this);

$this->registerCss(<<<CSS
.course-header-wrapper {
    height: 50px;
    position: relative;
}
.course-header {
    background: #fff;
    border-bottom: 0.1rem solid #eee;
}
.course-header-wrap {
    display: flex;
    position: relative;
    height: 50px;
    padding-right: 1.3rem;
    padding-left: 1.5rem;
}
.course-header-inner {
    display: flex;
    align-items: center;
    height: 5rem;
    flex: 0 0 auto;
}
.leave-course-button {
    color: #313537;
    --icon-color: #313537;
    display: flex;
    align-items: center;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0;
    border: none;
    padding: 0;
    background: transparent;
    text-transform: uppercase;
    letter-spacing: 0.1rem;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: 900;
    text-decoration: none;
}
.run-test {
    padding: 0;
    text-align: center;
    height: 100%;
}
.slide-hints {
    width: auto !important;
}
CSS
);
?>
<div class="container-fluid">
    <div class="course-header-wrapper">
        <div class="course-header">
            <div class="course-header-wrap">
                <div class="course-header-inner">
                    <a href="<?= Url::to(['/edu/student/index']); ?>" class="leave-course-button"><i style="font-size: 12px" class="glyphicon glyphicon-chevron-left"></i> Назад</a>
                </div>
                <div></div>
            </div>
        </div>
    </div>
</div>

<div class="story-box">
    <div class="story-container">
        <div class="story-container-inner" id="story-container">
            <section class="run-test">
                <div class="mental-map" data-mental-map-id="<?= $mentalMap->uuid ?>" data-story_id="<?= $storyId ?>" data-slide_id="<?= $slideId ?>"></div>
            </section>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {

    const mentalMapBuilder = window.mentalMapBuilder = new MentalMapManagerQuiz();

    const elem = $('#story-container div.mental-map')
    const mentalMapId = elem.attr('data-mental-map-id')
    if (!mentalMapId) {
      throw new Error('Mental map id not found')
    }

    const mentalMap = mentalMapBuilder.create(elem[0], undefined, {
      init: async () => {
        const response = await fetch(`/mental-map/init`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
          },
          body: JSON.stringify({
            id: mentalMapId,
            repetition_mode: true
          })
        })

        if (!response.ok) {
            throw new Error(response.statusText)
        }

        const json = await response.json()
        if (!json.success) {
            throw new Error(json?.message || 'Error')
        }

        return {...json}
      },
      repetitionMode: true,
      repetitionBackUrl: '/edu/student/index',
      ...elem.data()
    })
    mentalMap.run()
})();
JS
);

<?php

declare(strict_types=1);

use common\models\StoryTest;
use frontend\assets\TestAsset;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $testing
 * @var int $studentId
 */

$this->title = 'Повторение теста';

TestAsset::register($this);

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
                <div class="new-questions" data-test-id="<?= $testing->id; ?>" data-student-id="<?= $studentId; ?>"></div>
            </section>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {
    const elem = $('#story-container div.new-questions'),
          params = elem.data();
    const test = WikidsStoryTest.create(elem[0], {
        dataUrl: '/question/get',
        dataParams: params,
        forSlide: false,
        repetitionMode: true,
        init: () => $.getJSON('/question/init', params),
        onInitialized: () => test.addEventListener('finish', event => {
            console.log(event);
            const {testID, _, studentId} = event;
            const formData = new FormData();
            formData.append('test_id', testID);
            formData.append('student_id', studentId);
            sendForm(formData, '/repetition/testing/finish', 'post')
                .done(response => {
                    console.log(response);
                });
        })
    });
    test.run();
})();
JS
);

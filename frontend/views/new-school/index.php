<?php

declare(strict_types=1);

use frontend\ConsultRequest\ConsultRequestForm;
use frontend\models\ContactRequestForm;
use frontend\widgets\StoriesTabWidget;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ContactRequestForm $contactRequestModel
 * @var ConsultRequestForm $consultRequestModel
 */

$this->setMetaTags(
    'Онлайн школа домашнего обучения',
    'Онлайн школа домашнего обучения',
    'домашнее обучение, онлайн школа, обучение детей, wikids, сказки, истории',
);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
$this->registerJs($this->renderFile('@frontend/views/new-school/index.js'));
?>
<section class="hero-section">
    <div class="container-lg px-5 p-lg-0">
        <div class="d-flex flex-lg-row flex-column position-relative">
            <div style="flex: 1 0 50%" class="text-lg-left text-center hero-text-wrap">
                <div class="mb-3">
                    <h1 class="m-0 main-header">Удивительные возможности мозга ребенка</h1>
                </div>
                <div class="main-sub-header mb-lg-3 mb-5">Как использовать их для обучения?</div>
                <div class="main-text">Получите полезный чек-лист от Артура Муталова,
                    создателя школы домашнего обучения Wikids
                </div>
            </div>
            <div style="flex: 1 0 50%">
                <div class="position-relative mb-5 mb-lg-0"
                     style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div class="hero-image-arrow" style="left: 25%; top: 7%">
                        <div style="animation: sbs-723432649-1688644807140 4s infinite linear; backface-visibility: hidden;">
                            <div style="transform: rotate(185deg);">
                                <img style="max-width: 100%" src="/school/new/Shape.png" alt="">
                            </div>
                        </div>
                    </div>
                    <div style="position: absolute; width: 86%; right: -15px; z-index: -1">
                        <div style="animation: sbs-723432649-1688644048937 4s infinite linear; backface-visibility: hidden">
                            <img style="max-width: 100%"
                                 src="/school/new/1-1.png" alt="">
                        </div>
                    </div>
                    <img style="max-width: 100%; z-index: 2" src="/school/new/1.png" alt="">
                    <div class="hero-image-arrow" style="right: 0; bottom: 3%">
                        <div style="animation: sbs-723432649-1688644807140 4s infinite linear; backface-visibility: hidden;">
                            <div>
                                <img style="max-width: 100%" src="/school/new/Shape.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="check-button-wrap" style="left: 0; bottom: 0">
                <a target="_blank" class="btn custom-btn position-relative" href="https://t.me/wikids_bot">
                    Получить чек-лист
                    <span class="image-check-wrap">
                            <img class="image-check" src="/school/new/check-circle.gif" alt="check">
                        </span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container-lg px-5 p-lg-0">
        <div id="for" class="bg-light rounded-pill text-center" style="padding: .7rem; margin-bottom: 6rem">
            <h2 class="section-header m-0">Кому подходит обучение в Wikids</h2>
        </div>
        <div class="container-lg">
            <div class="row">
                <div class="col-md-6">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/_1.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                <strong>Ребёнок посвящает много времени тренировкам</strong> и соревнованиям, и трудно
                                совмещать их с обычным школьным расписанием.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_47.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                <strong>Ребёнок имеет особые физические потребности</strong>, и традиционная школьная
                                среда не всегда удовлетворяет их потребности.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_45.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                <strong>Ребёнок обладает выдающимися способностями</strong> и нуждается в возможности
                                учиться с опережением. Например, два учебных года за один календарный.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_46.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                <strong>Семья постоянно в разъездах</strong>, и вы хотите обеспечить стабильное
                                образование, несмотря на перемены в месте жительства.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="margin-bottom: 7rem;">
    <div class="container-lg px-5 p-lg-0">
        <div class="section-info">
            <span style="color: var(--green)">Wikids</span> — это персонализированное обучение для каждого ребёнка
        </div>
        <div class="text-center">
            <a target="_blank" class="btn custom-btn position-relative" href="https://t.me/wikids_bot">
                Получить чек-лист
            </a>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="container-lg px-5 p-lg-0">
        <div id="benefits" class="bg-light rounded-pill text-center" style="padding: .7rem; margin-bottom: 6rem">
            <h2 class="section-header m-0">Преимущества нашей школы</h2>
        </div>
        <div class="container-lg">
            <div class="row">
                <div class="col-md-4">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_53.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                <strong>Индивидуальный подход</strong> к каждому ученику.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_52.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                <strong>Результат достижим</strong> для любого ребёнка.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_48.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                У нас есть <strong>все необходимые лицензии.</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_49.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                Мы отработали технологию <strong>на наших собственных детях.</strong> Получили эффект,
                                которым довольны. Наши дети учатся эффективно.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_50.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                <strong>Ребёнок учится дома</strong> с родителями по нашим учебным пособиям.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="training-for-box bg-light">
                        <div class="position-relative" style="padding: 3rem 3rem 4rem; box-sizing: border-box">
                            <div class="position-relative training-for-box__header-wrap">
                                <div class="training-for-box__header"
                                     style="background-image: url(/school/new/Group_51.png);"></div>
                            </div>
                            <div class="training-for-box__text">
                                Мы разработали свою <strong>уникальную систему тестирования.</strong> Тесты не
                                проверочные, а обучающие с обратной связью ребёнку.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container-lg px-5 p-lg-0">
        <div class="section-header-new">
            <div class="row no-gutters">
                <div class="col-12 col-lg-8 col-xl-8 col-xxl-6">
                    <h2 class="section-header-new__header">Мы разработали более 1200 электронных учебников</h2>
                </div>
            </div>
            <div class="row no-gutters">
                <div class="col-12 col-lg-6">
                    <p class="section-header-new__info">Основа обучения — электронные учебники, которые мы делаем сами,
                        в них подробное и понятное объяснение для ребёнка и для родителя</p>
                </div>
            </div>
        </div>
        <div>
            <?= StoriesTabWidget::widget([
                'categories' => [
                    'obuchenie-chteniyu-s-pomoschyu-testov',
                    'obucheniye-gramote',
                    '3-klass-istoriya-rossii',
                    'russkie-skazki-i-byliny',
                    'drevnegrecheskie-mify',
                    'drevneegipetskie-mify-i-skazki',
                    'buryatskie-narodnye-skazki',
                    'skazanie-o-batyrah',
                    'bashkirskie-narodnye-skazki',
                    'altayskie-narodnye-skazki',
                    'istoricheskie',
                    'zhivaya-priroda',
                ],
            ]) ?>
        </div>
    </div>
</section>

<section style="margin-bottom: 10rem;">
    <div class="container-lg px-5 p-lg-0">
        <div class="d-flex flex-row justify-content-center position-relative" style="margin-bottom: 4rem">
            <div style="position: absolute; left: 10%; width: 8.7rem; top: 50%; transform: translateY(-50%);">
                <div style="transform: rotate(91deg);">
                    <img style="width: 100%" src="/school/new/5.png" alt="">
                </div>
            </div>
            <img style="max-width: 100%; border-radius: 20px" src="/school/new/10.jpg" alt="">
            <div style="position: absolute; right: 10%; width: 6.2rem; top: 50%; transform: translateY(-50%);">
                <div>
                    <img style="width: 100%" src="/school/new/Shape3.png" alt="">
                </div>
            </div>
        </div>
        <div class="w-100">
            <div class="text-center" style="max-width: 84rem; margin: 0 auto; font-size: 2rem; line-height: 3.1rem; font-weight: 400;">
                <strong>Мы предлагаем персонализированный подход к каждому ребёнку.</strong> Учитываем его способности и
                потребности, разрабатываем индивидуальную программу обучения, которая будет соответствовать его уровню и
                поможет ему достичь успеха.
            </div>
        </div>
    </div>
</section>

<section style="margin-bottom: 7rem">
    <div class="container-lg px-5 p-lg-0">
        <div id="certificates" class="bg-light rounded-pill text-center" style="padding: .7rem; margin-bottom: 6rem">
            <h2 class="section-header m-0">Сертификаты</h2>
        </div>
        <div class="row no-gutters">
            <div class="col-lg-6">
                <div class="bg-light mr-0 mr-lg-5 text-center" style="border-radius: 20px; padding: 3rem; font-size: 2rem; line-height: 3.1rem; font-weight: 400;">
                    <p><strong>Мы работаем ради результатов детей, школа домашнего обучения Wikids</strong> – это образовательный проект, призванный организовать процесс домашнего обучения вашего ребёнка.</p>
                    <p>Мы оказываем методическую и консультативную помощь родителям.</p>
                    <p><strong>Школа домашнего обучения Wikids</strong> обладает всеми необходимыми образовательными лицензиями.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex flex-md-row flex-column">
                    <div style="flex: 1 0 50%; height: 40rem" class="text-center">
                        <img style="height: 100%; border-radius: 20px" src="/school/new/license-1-small.jpg" alt="">
                    </div>
                    <div style="flex: 1 0 50%; height: 40rem" class="text-center">
                        <img style="height: 100%; border-radius: 20px" src="/school/new/license-2-small.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-contact" id="contact">
    <div class="container-lg px-5 p-lg-0">
        <div class="contact-main">
            <div class="row no-gutters">
                <div class="col-12 col-lg-6">
                    <div class="contact__wrap">
                        <h2 class="section-header-new__header">У&nbsp;вас остались вопросы?<br/>Свяжитесь с нами!</h2>
                        <div class="contact-text">
                            <p class="contact-text__text">Свяжитесь с нами, оставьте свой вопрос в форме или свяжитесь с нами удобным для вас способом.</p>
                        </div>
                        <div class="contact-block__wrap">
                            <div class="contact-social">
                                <a target="_blank" class="contact-social__link" href="https://vk.com/club184614838"><img src="/school/img/vk_white.svg" alt="vk"></a>
                            </div>
                            <div class="contact-contact">
                                <a class="contact-contact__phone" href="tel:+74997033525">+7 (926) 207−41−46</a>
                            </div>
                            <div class="contact-contact contact-contact--no-mb">
                                <a class="contact-contact__email" href="mailto:info@wikids.ru">info@wikids.ru</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div id="contact-request-block" class="contact-form__wrap">
                        <?= $this->render('@frontend/views/school/_request_form', ['model' => $contactRequestModel]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->render('_consult_request_form', ['formModel' => $consultRequestModel]); ?>

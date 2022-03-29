<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->setMetaTags('Сервис ускоренного развития речи ребёнка',
    'Сервис ускоренного развития речи ребёнка',
    'wikids, сказки, истории');
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
/** @var $contactRequestModel frontend\models\ContactRequestForm */
?>
<main class="main">
    <div class="container-lg p-0">
        <div class="row no-gutters">
            <div class="col-12 col-lg-6">
                <div class="main__inner">
                    <div class="main__title">
                        <h1>Онлайн&nbsp;школа домашнего обучения</h1>
                    </div>
                    <div class="main-description">
                        <p class="main-description__text">Более 1200 электронных учебников<br>Индивидуальный план обучения<br>Все необходимые лицензии</p>
                    </div>
                    <div class="main-class">
                        <div class="main-class__wrap">
                            <p class="main-class__text">1–4 класс</p>
                        </div>
                    </div>
                </div>
                <div class="main__button">
                    <?= Html::a('Оставить заявку', ['contact/create'], ['class' => 'button contact-request']) ?>
                </div>
            </div>
            <div class="col-12 col-lg-6"></div>
        </div>
    </div>
    <div class="container-fluid p-0 main__image-container">
        <div class="row no-gutters">
            <div class="col-lg-5"></div>
            <div class="col-12 col-lg">
                <div class="main-image__wrap">
                    <img src="/school/img/main.png" class="main-image" alt="main">
                </div>
            </div>
        </div>
    </div>
</main>
<section class="section">
    <div class="container-lg p-0">
        <div class="section-header">
            <div class="row no-gutters">
                <div class="col-12 col-lg-8 col-xl-8 col-xxl-7">
                    <h2 class="section-header__header">Преимущества нашей школы
                        домашнего обучения</h2>
                </div>
            </div>
        </div>
        <div class="box-list">
            <div class="row no-gutters">
                <div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="box box__1-1">
                        <div class="box-image">
                            <img src="/school/img/icon_1.png" alt="icon">
                        </div>
                        <div class="box-text">
                            <p>Индивидуальный подход к каждому ученику</p>
                        </div>
                    </div>
                    <div class="box box__1-2">
                        <div class="box-text box-text--big">
                            <p>Результат достижим для любого ребёнка</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="box box__2-1">
                        <div class="box-text">
                            <p>Мы отработали технологию на наших собственных детях. Получили эффект, которым довольны. Наши дети учатся эффективно.</p>
                        </div>
                    </div>
                    <div class="box box__2-2 box--green">
                        <div class="box-text box-text--big">
                            <p>Ребёнок учится
                                дома с родителями</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="box box__3-1">
                        <div class="box-image">
                            <img src="/school/img/icon_2.png" alt="icon">
                        </div>
                        <div class="box-text box-text--divider">
                            <p>Ребёнок занимается по нашим учебным пособиям</p>
                        </div>
                        <div class="box-text box-text--divider">
                            <p>Адаптивное обучающее тестирование в каждом пособии</p>
                        </div>
                        <div class="box-text box-text--divider">
                            <p>Приучаем ребёнка учиться самостоятельно</p>
                        </div>
                        <div class="box-text box-text--divider">
                            <p>Ребёнок учится без помощи родителя</p>
                        </div>
                        <div class="box-text">
                            <p>Родитель контролирует обучение по результатам тестов</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="box box__4-1 box--blue">
                        <div class="box-text box-text--big">
                            <p>У&nbsp;нас&nbsp;есть&nbsp;все необходимые лицензии</p>
                        </div>
                    </div>
                    <div class="box box__4-2">
                        <div class="box-text">
                            <p>Мы разработали свою уникальную систему тестирования. Тесты не проверочные, а обучающие. Они сразу дают обратную связь ребёнку.</p>
                        </div>
                        <div class="box-image">
                            <img src="/school/img/icon_3.png" alt="icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="fragment fragment__1 fragment--circle-blue"></div>
    <div class="fragment fragment__2 fragment--circle-green"></div>
    <div class="fragment fragment__3 fragment--circle-green"></div>
    <div class="fragment fragment__4 fragment--triangle-green"></div>
</section>
<section class="section">
    <div class="container-lg p-0">
        <div class="section-header">
            <div class="row no-gutters">
                <div class="col-12 col-lg-8 col-xl-8 col-xxl-6">
                    <h2 class="section-header__header">Мы разработали более 1200 электронных учебников</h2>
                </div>
            </div>
            <div class="row no-gutters">
                <div class="col-12 col-lg-6">
                    <p class="section-header__info">Основа обучения — электронные учебники, которые мы делаем сами,
                        в них подробное и понятное объяснение для ребёнка и для родителя</p>
                </div>
            </div>
        </div>
        <div>
            <?= \frontend\widgets\StoriesTabWidget::widget([
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
<section class="section">
    <div class="container-lg p-0">
        <div class="works-main">
            <div class="row no-gutters">
                <div class="col-12 col-lg-4">
                    <div class="works__wrap">
                        <div class="works">
                            <img class="works-box__image" src="/school/img/how.svg" alt="how">
                            <p class="works-box__text">Как&nbsp;это работает?</p>
                        </div>
                    </div>
                    <div class="fragment fragment__5 fragment--triangle-green"></div>
                    <div class="fragment fragment__6 fragment--triangle-blue"></div>
                    <div class="fragment fragment__7 fragment--circle-blue"></div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="box box__works">
                        <h3 class="box-header box-header--works">Задачи родителя</h3>
                        <div class="box-text box-text--text-left">Цель родителя — приучить ребёнка учиться самостоятельно, без участия родителя. Это реально и вполне достижимо. Родитель получает всё необходимое, чтобы понять материал самому и при необходимости объяснить ребёнку. На первом этапе для ребёнка важна моральная поддержка от родителя.</div>
                    </div>
                    <div class="fragment fragment__9 fragment--circle-green"></div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="box box__works">
                        <h3 class="box-header box-header--works">Задачи школы</h3>
                        <div class="box-text box-text--text-left">Мы разрабатываем учебные материалы и тесты.Высылаем задания и контролируем процесс обучения. Обеспечиваем индивидуальный подход к обучению каждого ребёнка. По результатам тестов назначаем темы для проработки учебного материала.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="fragment fragment__8 fragment--circle-green"></div>
    <div class="fragment fragment__10 fragment--circle-blue"></div>
</section>
<section class="section">
    <div class="container-lg p-0">
        <div class="section-header plan-header">
            <div class="row no-gutters">
                <div class="col-12 col-lg-8 col-xl-8 col-xxl-6">
                    <h2 class="section-header__header">Индивидуальный план&nbsp;обучения</h2>
                </div>
                <div class="col col-lg-4 col-xl-4 col-xxl-6">
                    <div class="fragment fragment__16 fragment--triangle-green"></div>
                </div>
            </div>
        </div>
        <div class="plan-main">
            <div class="row no-gutters">
                <div class="col-12 col-lg-6">
                    <div class="plan-image__wrap">
                        <div class="plan-image__inner">
                            <div class="plan-image__circle"></div>
                            <img class="plan-image__image" src="/school/img/plan.png" alt="plan">
                        </div>
                    </div>
                    <div class="fragment fragment__14 fragment--triangle-blue"></div>
                    <div class="fragment fragment__15 fragment--circle-blue"></div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="plan-content__wrap">
                        <div class="plan-content-item">
                            <h3 class="plan-content-item__title">Раннее развитие</h3>
                            <p class="plan-content-item__text">Если ребёнок готов к обучению с 5 лет, то есть возможность зачисления в нашу школу.</p>
                        </div>
                        <div class="plan-content-item">
                            <h3 class="plan-content-item__title">Обучение с опережением</h3>
                            <p class="plan-content-item__text">Для самых устремлённых возможно учиться с опережением стандартного учебного плана. Например, два учебных года за один календарный.</p>
                        </div>
                        <div class="plan-content-item">
                            <h3 class="plan-content-item__title">Обучение в своём темпе</h3>
                            <p class="plan-content-item__text">Ребёнок не обязан успевать за всеми. Он должен хорошо усвоить учебный материал.</p>
                        </div>
                    </div>
                    <div class="fragment fragment__17 fragment--circle-blue"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="fragment fragment__11 fragment--circle-blue"></div>
    <div class="fragment fragment__12 fragment--circle-green"></div>
    <div class="fragment fragment__13 fragment--triangle-blue"></div>
</section>
<section class="section price-section">
    <div class="container-fluid p-0">
        <div class="container-lg p-0">
            <div class="price-main">
                <div class="row no-gutters">
                    <div class="col-12 col-xl-4">
                        <div class="price-image__wrap">
                            <h2 class="section-header__header">Стоимость обучения</h2>
                            <div class="price-image">
                                <img src="/school/img/child.png" alt="child" class="price-image__image">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-8">
                        <div class="price-blocks">
                            <div class="box box__price">
                                <h2 class="box-header box-header--price">Основной<br>курс</h2>
                                <div class="price-content">
                                    <p class="price-content__item">Электронные учебники</p>
                                    <p class="price-content__item">Персональная проверка заданий</p>
                                    <p class="price-content__item">Тестирование и рекомендации</p>
                                </div>
                                <div class="price">
                                    <div class="price__price price__price--sub">30 000 ₽ первоначальный взнос</div>
                                    <div class="price__price">4900 ₽/месяц</div>
                                </div>
                                <div class="box-controls">
                                    <?= Html::a('Записаться', ['contact/create'], ['class' => 'button contact-request']) ?>
                                </div>
                            </div>
                            <div class="box box__price">
                                <h2 class="box-header box-header--price">Дополнительные<br/>Онлайн-услуги</h2>
                                <div class="price-content">
                                    <p class="price-content__item">Занятия в ZOOM или Skype</p>
                                    <p class="price-content__item">Консультации от наших методистов</p>
                                    <p class="price-content__item">Проработка отдельных тем</p>
                                </div>
                                <div class="price">
                                    <div class="price__price">2000 ₽/ак. час</div>
                                </div>
                                <div class="box-controls">
                                    <?= Html::a('Записаться', ['contact/create'], ['class' => 'button contact-request']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section discount-section">
    <div class="container-lg p-0">
        <div class="discount-main">
            <div class="row no-gutters">
                <div class="col-12 col-xl-6">
                    <div class="discount__wrap">
                        <h2 class="section-header__header section-header__header--white">Получите вводный онлайн урок с 50% скидкой</h2>
                        <div class="discount-rows">
                            <div class="discount-row">
                                <div class="discount-row__icon">
                                    <img src="/school/img/zoom.svg" alt="zoom">
                                </div>
                                <p class="discount-row__text">Проведем вводное онлайн занятие в ZOOM или Skype</p>
                            </div>
                            <div class="discount-row">
                                <div class="discount-row__icon">
                                    <img src="/school/img/comment.svg" alt="comment">
                                </div>
                                <p class="discount-row__text">Проконсультируем вас и дадим советы и рекомендации</p>
                            </div>
                        </div>
                        <div class="discount-controls">
                            <div class="discount-controls__wrap">
                                <?= Html::a('Записаться на занятие', ['contact/create'], ['class' => 'button contact-request']) ?>
                                <div class="discount-agree">
                                    <p class="discount-agree__text">Нажимая на кнопку вы принимаете<br><?= Html::a('пользовательское соглашение', ['site/policy'], ['class' => 'discount-agree__link']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="discount-image__wrap">
                        <img class="discount-image" src="/school/img/discount.png" alt="discount">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section-ask">
    <div class="container-fluid p-0">
        <div class="container-lg p-0">
            <div class="ask-main">
                <h2 class="section-header__header">Ответы на популярные вопросы</h2>
                <div class="row no-gutters">
                    <div class="col-12 order-lg-second col-lg-7">
                        <div class="box box__ask">
                            <div class="accordion" id="accord">
                                <div class="ask-card">
                                    <a class="ask-card-header" href="#" data-toggle="collapse" data-target="#rowOne">
                                        <div class="ask-card-header__title">Как перейти на семейный формат обучения?</div>
                                    </a>
                                    <div id="rowOne" class="collapse show" data-parent="#accord">
                                        <div class="ask-card-content">
                                            <p>Общий алгоритм перехода на семейный формат обучения (самообразование):</p>
                                            <ul>
                                                <li>Уведомить органы управления образованием (по месту жительства) о прекращении посещения школы и переходе на семейный формат обучения</li>
                                                <li>Уведомить администрацию школы о смене формы обучения, получить на руки личное дело ученика</li>
                                                <li>Прикрепиться к школе для прохождения промежуточных и итоговых аттестаций за учебный год</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="ask-card-header__icon">
                                        <i class="cross"></i>
                                    </div>
                                </div>
                                <div class="ask-card">
                                    <a class="ask-card-header" href="#" data-toggle="collapse" data-target="#rowTwo">
                                        <div class="ask-card-header__title">Какие нужны документы для зачисления?</div>
                                    </a>
                                    <div id="rowTwo" class="collapse" data-parent="#accord">
                                        <div class="ask-card-content">
                                            <ul>
                                                <li>Заявление</li>
                                                <li>Свидетельство о рождении</li>
                                                <li>Согласие на обработку персональных данных</li>
                                                <li>Личное дело ученика</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="ask-card-header__icon">
                                        <i class="cross"></i>
                                    </div>
                                </div>
                                <div class="ask-card">
                                    <a class="ask-card-header" href="#" data-toggle="collapse" data-target="#rowThree">
                                        <div class="ask-card-header__title">Как проходит процесс зачисления и обучения?</div>
                                    </a>
                                    <div id="rowThree" class="collapse" data-parent="#accord">
                                        <div class="ask-card-content">
                                            <p>Вы оставляете заявку на сайте или направляете заявку нам по электронной почте.</p>
                                            <p>В ответ Вы получаете: список необходимых для зачисления документов, проект Договора с ООО «ЦВД», реквизиты для оплаты первоначального взноса и первого месяца обучения.</p>
                                            <p>Вы собираете и предоставляете нам полный пакет документов, а также подтверждение об оплате.</p>
                                            <p>В течение 15 рабочих дней с момента получения нами платежа и документов происходит зачисление Вашего ребёнка в ОАНО СОШ «Пенаты». Справка о зачислении направляется на Вашу электронную почту.</p>
                                            <p>В течение 10 рабочих дней с момента зачисления Вы получаете от нас:</p>
                                            <ul>
                                                <li>учебную программу на год;</li>
                                                <li>список предметов для аттестации в конце учебного года;</li>
                                                <li>список рекомендованной литературы;</li>
                                                <li>учебный план на месяц (полный курс учебного года предполагает 9 учебных месяцев; в дальнейшем план может быть скорректирован в зависимости от темпов обучения);</li>
                                                <li>доступ к учебным пособиям нашего портала, а также доступ в личный кабинет, где будет вестись учёт результатов прохождения тестов по темам.</li>
                                            </ul>
                                            <p>В конце каждого учебного месяца необходимо выполнить проверочную работу. По результатам работы в течение 5 рабочих дней Вы получаете комментарии и рекомендации наших педагогов и методистов. А также Вам направляется счёт на оплату следующего календарного месяца обучения.</p>
                                            <p>После прохождения всех 9-ти блоков одного учебного класса ребёнок выполняет итоговое тестирование. Результаты тестирования являются критерием для успешного прохождения ПА (промежуточной аттестации) за соответствующий класс.</p>
                                            <p>В случае успешного прохождения ПА в течение 15 рабочих дней выдаётся справка о результатах прохождения аттестации за учебный класс.</p>
                                            <p>В случае если аттестация не пройдена с первой попытки, Вы получаете рекомендации по повторению материала (тесты, учебные пособия). Сроки повторного тестирования назначаются  индивидуально.</p>
                                        </div>
                                    </div>
                                    <div class="ask-card-header__icon">
                                        <i class="cross"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 order-lg-first col-lg-5">
                        <div class="ask__wrap">
                            <div class="ask-text">
                                <p class="ask-text__text">Не нашли ответ на свой вопрос? Свяжитесь с нами любым удобным способом, мы с радостью ответим на все интересующие вопросы.</p>
                            </div>
                            <div class="ask-social">
                                <a target="_blank" class="ask-social__link" href="https://vk.com/club184614838"><img src="/school/img/vk.svg" alt="vk"></a>
                                <!--a class="ask-social__link" href="#"><img src="./img/wa.svg" alt="wa"></a>
                                <a class="ask-social__link" href="#"><img src="./img/telegram.svg" alt="tg"></a-->
                            </div>
                            <div class="ask-contact">
                                <a class="ask-contact__phone" href="tel:+74997033525">+7 (926) 207−41−46</a>
                            </div>
                            <div class="ask-contact ask-contact--no-mb">
                                <a class="ask-contact__email" href="mailto:info@wikids.ru">info@wikids.ru</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section-license">
    <div class="container-lg p-0">
        <div class="license-main">
            <h2 class="section-header__header">Лицензии нашей школы домашнего обучения</h2>
            <div class="row no-gutters">
                <div class="col-12 order-xl-second col-xl-6">
                    <div class="row no-gutters">
                        <div class="col-12 col-md-6">
                            <div class="license-card">
                                <div class="license-image__wrap">
                                    <div class="license-image__inner">
                                        <img class="license-image" src="/school/img/license-1-small.jpg" alt="license">
                                    </div>
                                </div>
                                <div class="license-text">
                                    <p class="license-text__text">Образовательная лицензия СОШ «Пенаты»</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="license-card">
                                <div class="license-image__wrap">
                                    <div class="license-image__inner">
                                        <img class="license-image" src="/school/img/license-2-small.jpg" alt="license">
                                    </div>
                                </div>
                                <div class="license-text">
                                    <p class="license-text__text">Образовательная лицензия ООО «ЦВД»</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 order-xl-first col-xl-6">
                    <div class="license__wrap">
                        <div class="license-info">
                            <p class="license-info__text">Мы работаем ради результатов детей, школа домашнего обучения Wikids – это образовательный проект, призванный организовать процесс домашнего обучения вашего ребёнка. Мы оказываем методическую и консультативную помощь родителям. Школа домашнего обучения Wikids обладает всеми необходимыми образовательными лицензиями.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section-contact">
    <div class="container-lg p-0">
        <div class="contact-main">
            <div class="row no-gutters">
                <div class="col-12 col-lg-6">
                    <div class="contact__wrap">
                        <h2 class="section-header__header">У&nbsp;вас остались вопросы? Свяжитесь с нами!</h2>
                        <div class="contact-text">
                            <p class="contact-text__text">Свяжитесь с нами, оставьте свой вопрос в форме или свяжитесь с нами удобным для вас способом.</p>
                        </div>
                        <div class="contact-block__wrap">
                            <div class="contact-social">
                                <a target="_blank" class="contact-social__link" href="https://vk.com/club184614838"><img src="/school/img/vk_white.svg" alt="vk"></a>
                                <!--a class="contact-social__link" href="#"><img src="./img/wa_white.svg" alt="wa"></a>
                                <a class="contact-social__link" href="#"><img src="./img/tg_white.svg" alt="tg"></a-->
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
                        <?= $this->render('_request_form', ['model' => $contactRequestModel]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->render('_request') ?>
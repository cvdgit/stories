<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->setMetaTags('Сервис ускоренного развития речи ребёнка',
                   'Сервис ускоренного развития речи ребёнка',
                   'wikids, сказки, истории');
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="site-categories-bg"></div>
<section class="site-categories">
    <h2 class="header-h2 container"><span>Популярные</span> категории</h2>
    <div class="container">
        <div class="row">
            <div class="col-xs-offset-1 col-xs-10 col-sm-offset-0 col-sm-6 col-md-offset-0 col-md-6 col-lg-offset-0 col-lg-3">
                <div class="category-item">
                    <a href="<?= Url::toRoute(['/story/category', 'section' => 'stories', 'category' => 'drevnegrecheskie-mify']) ?>">
                        <div class="category-item-image-wrapper">
                            <img src="/img/category_2_mini.jpg" alt="">
                        </div>
                        <h3 class="header-h3">Древнегреческие мифы</h3>
                    </a>
                </div>
            </div>
            <div class="col-xs-offset-1 col-xs-10 col-sm-offset-0 col-sm-6 col-md-offset-0 col-md-6 col-lg-offset-0 col-lg-3">
                <div class="category-item">
                    <a href="<?= Url::toRoute(['/story/category', 'section' => 'stories', 'category' => 'russkie-skazki-i-byliny']) ?>">
                        <div class="category-item-image-wrapper">
                            <img src="/img/category_1_mini.jpg" alt="">
                        </div>
                        <h3 class="header-h3">Русские сказки и былины</h3>
                    </a>
                </div>
            </div>
            <div class="col-xs-offset-1 col-xs-10 col-sm-offset-0 col-sm-6 col-md-offset-0 col-md-6 col-lg-offset-0 col-lg-3">
                <div class="category-item">
                    <a href="<?= Url::toRoute(['/story/category', 'section' => 'stories', 'category' => 'poznavatelnye']) ?>">
                        <div class="category-item-image-wrapper">
                            <img src="/img/category_3_mini.jpg" alt="">
                        </div>
                        <h3 class="header-h3">Познавательные</h3>
                    </a>
                </div>
            </div>
            <div class="col-xs-offset-1 col-xs-10 col-sm-offset-0 col-sm-6 col-md-offset-0 col-md-6 col-lg-offset-0 col-lg-3">
                <div class="category-item">
                    <a href="<?= Url::toRoute(['/story/category', 'section' => 'stories', 'category' => 'altayskie-narodnye-skazki']) ?>">
                        <div class="category-item-image-wrapper">
                            <img src="/img/category_4_mini.jpg" alt="">
                        </div>
                        <h3 class="header-h3">Алтайские народные сказки</h3>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="container site-stories-controls">
        <?= Html::a('Все категории', ['/story/index', 'section' => 'stories'], ['class' => 'btn']) ?>
    </div>
</section>

<?= \frontend\widgets\Playlists::widget() ?>

  <section class="site-about">
    <h2 class="header-h2 container">О <span>портале</span></h2>
    <div class="container">
      <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-2">
          <span class="about-tree-number pull-right">01</span>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-0">
          <div class="about-tree-element-top"></div>
          <div class="about-tree-element-sep"></div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-10">
          <div class="about-note-block about-note-block-right">
            <div class="about-note-block-content">
              <h4><span>Формат</span> материала</h4>
              <p>Электронная библиотека для родителей<br>Похоже на диафильм или книгу с иллюстрациями</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-10">
          <div class="about-note-block about-note-block-left">
            <div class="about-note-block-content">
              <h4><span>Цель</span> сервиса</h4>
              <p>Ускоренная наработка словарного запаса ребёнка<br>Построение коммуникации с ребёнком</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-0">
          <div class="about-tree-element"></div>
          <div class="about-tree-element-sep"></div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-2">
          <span class="about-tree-number pull-left">02</span>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-2">
          <span class="about-tree-number pull-right">03</span>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-0">
          <div class="about-tree-element"></div>
          <div class="about-tree-element-sep"></div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-10">
          <div class="about-note-block about-note-block-right">
            <div class="about-note-block-content">
              <h4>Достигаемый <span>результат</span></h4>
              <p>Ребёнок начнёт говорить<br>Развитая русская речь у Ваших детей</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-10">
          <div class="about-note-block about-note-block-left">
            <div class="about-note-block-content">
              <h4>Наша <span>методика</span></h4>
              <p>Пополнение словарного запаса самым доступным способом<br />Сказка перед сном ежедневно</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-0">
          <div class="about-tree-element"></div>
          <div class="about-tree-element-sep"></div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-2">
          <span class="about-tree-number pull-left">04</span>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-2">
          <span class="about-tree-number pull-right">05</span>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-0">
          <div class="about-tree-element"></div>
          <div class="about-tree-element-sep"></div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-10">
          <div class="about-note-block about-note-block-right">
            <div class="about-note-block-content">
              <h4><span>Возраст</span> начала занятий</h4>
              <p>В 1,5 года дети уже будут нормально слушать Вас<br />Но можно и раньше</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-10">
          <div class="about-note-block about-note-block-left">
            <div class="about-note-block-content">
              <h4><span>Роль</span> родителя</h4>
              <p>Активная роль. Вы - источник знаний и авторитет<br />Вы читаете текст и объясняете, если что-то непонятно</p>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-0">
          <div class="about-tree-element-bottom"></div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-2">
          <span class="about-tree-number pull-left">06</span>
        </div>
      </div>
    </div>
  </section>

  <section class="site-stories">
    <h2 class="header-h2 container"><span>Новые</span> истории</h2>
    <div class="container">
        <?= \frontend\widgets\StoryWidget::widget() ?>
    </div>
    <div class="container site-stories-controls">
      <?= Html::a('Посмотреть все истории', ['/story/index', 'section' => 'stories'], ['class' => 'btn']) ?>
    </div>
  </section>

  <section class="site-questions">
    <h2 class="header-h2 container">Часто задаваемые <span>вопросы</span></h2>
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="question-block">
            <h4>В чем особенность современных детей?</h4>
            <p>Нарушение речевого развития у каждого четвёртого к первому классу. Отстающие плохо говорят, плохо понимают и тянут назад весь класс.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="question-block">
            <h4>Не повредит ли это моему ребенку?</h4>
            <p>Всё опробовал на своих детях. Занимаюсь ежедневно. Дети развитые и хорошо разговаривают.</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="question-block">
            <h4>Проблемы с дисциплиной, что посоветуете?</h4>
            <p>Если не слушает, безобразничает - прекращайте занятие и укладывайте спать. Между спать и сказкой ребёнок будет выбирать сказку и стараться успокоиться.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="question-block">
            <h4>А может просто мультик показать?</h4>
            <p>Ценность мультфильмов невелика. Дети перенимают речь от другого человека. В основном, лично от Вас. От телевизора не могут. Почему - науке пока не известно.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="site-ask-question">
    <div class="container">
      <div class="row row-no-gutters ask-question-wrapper">
        <div class="col-xs-12 col-sm-12 col-md-3">
          <img src="/img/ask-icon.png" alt="">
          <span class="ask-text-1">Нужна консультация?</span>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7">
          <span class="ask-text-2">Подробно расскажем о нашей методике, приведем примеры, поможем подобрать сказки</span>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2">
          <button class="btn btn-white pull-right" data-toggle="modal" data-target="#wikids-feedback-modal">Задать вопрос</button>
        </div>
      </div>
    </div>
  </section>

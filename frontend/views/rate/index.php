<?php

/** @var $this yii\web\View */
/** @var $hasSubscription bool */
/** @var $hasFreeSubscription bool */

use frontend\widgets\SubscriptionBlock;
use yii\helpers\Url;
use yii\web\View;

$title = 'Подписки';
$this->setMetaTags($title, 'Подписка wikids', 'подписка, wikids, истории, сказки', $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="container">
    <main class="site-pricing">
        <h1><span>Улучши возможность</span> просмотра историй</h1>
        <?php if (!$hasSubscription && !$hasFreeSubscription): ?>
        <div class="row">
            <?= SubscriptionBlock::widget(['code' => 'free', 'viewName' => 'subscription_block_free', 'hasSubscription' => $hasSubscription]) ?>
        </div>
        <?php endif ?>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="row">
                    <?= SubscriptionBlock::widget(['code' => '3months', 'image' => '/img/price-3month.png', 'hasSubscription' => $hasSubscription]) ?>
                    <?= SubscriptionBlock::widget(['code' => '1year', 'image' => '/img/price-1year.png', 'hasSubscription' => $hasSubscription]) ?>
                    <?= SubscriptionBlock::widget(['code' => '1month', 'image' => '/img/price-1month.png', 'hasSubscription' => $hasSubscription]) ?>
                </div>
                <div class="payment-form-placeholder"></div>
            </div>
        </div>
    </main>
</div>

<?php
$this->registerJsFile('https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js');
$js = <<< JS
function paymentOnBeforeSubmit(e)
{
    e.preventDefault();
    let form = $(this),
        submitButton = $('button[type=submit]', form);
    submitButton.button('loading');
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: new FormData(form[0]),
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
            if (data) {
                if (data.success) {
                    $('.payment-form-placeholder').html(data.html);
                    $('form', '.payment-form-placeholder').submit();
                }
              else {
                $.each(data.message, function(i, message) {
                  toastr.warning('', message);
                });
              }
            }
            else {
              toastr.warning('', 'Произошла неизвестная ошибка');
            }
        },
        error: function(data) {
          if (data && data.message) {
            toastr.warning('', data.message);
          }
          else {
            toastr.warning('', 'Произошла неизвестная ошибка');
          }
        }
    }).always(function() {
      submitButton.button('reset');
    });
    return false;
}
$('.payment-form')
  .on('beforeSubmit', paymentOnBeforeSubmit)
  .on('submit', function(e) {
      e.preventDefault();
  });
JS;
$this->registerJs($js, View::POS_READY);
?>
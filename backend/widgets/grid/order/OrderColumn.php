<?php

declare(strict_types=1);

namespace backend\widgets\grid\order;

use Yii;
use yii\base\InvalidConfigException;
use yii\grid\DataColumn;
use yii\helpers\Html;

class OrderColumn extends DataColumn
{

    public $url;
    public $fieldName;
    public $container;

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if (empty($this->url)) {
            throw new InvalidConfigException('The "url" property must be set.');
        }

        if (empty($this->fieldName)) {
            throw new InvalidConfigException('The "fieldName" property must be set.');
        }

        if (empty($this->container)) {
            throw new InvalidConfigException('The "container" property must be set.');
        }

        $this->registerClientScript();
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        return $this->createCellContent();
    }

    private function createCellContent(): string
    {
        $contents = [];
        $contents[] = Html::a('<i class="glyphicon glyphicon-arrow-up"></i>', '#', ['class' => 'move-up', 'data-pjax' => 0]);
        $contents[] = Html::a('<i class="glyphicon glyphicon-arrow-down"></i>', '#', ['class' => 'move-down', 'data-pjax' => 0]);
        return implode('', $contents);
    }

    private function registerClientScript(): void
    {
        $action = $this->url;
        $attr = $this->fieldName;
        $container = $this->container;
        $this->grid->getView()->registerJs(<<<JS
window.gridOrderColumn = window['gridOrderColumn'] || (function() {

    let moveTimeout;
    function saveOrder(grid) {

      if (moveTimeout) {
        clearTimeout(moveTimeout);
      }

      moveTimeout = setTimeout(() => {

          const rows = grid.find('tbody tr:visible').map((index, elem) => {
              return parseInt($(elem).attr('data-key'));
          }).get();

         $.post({
            'url': '$action',
            'dataType': 'json',
            'data': {'$attr': rows},
            'cache': 'no-cache'
         })
             .done((response) => {
                 if (response && response.success) {
                     toastr.success('Порядок успешно сохранен');
                 }
             });

      }, 1500);
    }

    const grid = $('$container');

    grid
        .on('click', '.move-up', function(e) {
            e.preventDefault();

            const elem = $(e.target).parents('tr[data-key]:eq(0)');
            const sibling = elem.prev();
            if (sibling) {
                elem.insertBefore(sibling);
                saveOrder(grid);
            }
        })
        .on('click', '.move-down', function(e) {
            e.preventDefault();

            const elem = $(e.target).parents('tr[data-key]:eq(0)');
            const sibling = elem.next();
            if (sibling) {
                elem.insertAfter(sibling);
                saveOrder(grid);
            }
        });

    return {};
})();
JS
);
    }
}

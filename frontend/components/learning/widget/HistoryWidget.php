<?php

declare(strict_types=1);

namespace frontend\components\learning\widget;

use yii\base\Widget;
use yii\helpers\Html;

class HistoryWidget extends Widget
{
    public $caption;
    public $columns;
    public $models;
    public $tableRowRenderCallback;

    public function run(): void
    {
        $content = $this->renderTable();
        echo Html::tag('div', $content, ['class' => 'history-main']);
    }

    private function renderTable(): string
    {
        $caption = $this->renderCaption();
        $tableHeader = $this->renderTableHeader();
        $tableBody = $this->renderTableBody();
        $content = [
            $caption,
            $tableHeader,
            $tableBody,
        ];
        return Html::tag('table', implode("\n", $content), ['class' => 'table table-bordered']);
    }

    private function renderCaption(): string
    {
        if (!empty($this->caption)) {
            return Html::tag('caption', $this->caption);
        }
        return '';
    }

    private function renderTableHeader(): string
    {
        $cells = [];
        foreach ($this->columns as $column) {
            $cells[] = Html::tag('th', $column['label']);
        }
        $content = Html::tag('tr', implode('', $cells));
        return "<thead>\n" . $content . "\n</thead>";
    }

    private function renderTableBody(): string
    {
        $rows = [];
        foreach ($this->models as $model) {
            $rows[] = $this->renderTableRow($model);
        }
        if (empty($rows)) {
            $colspan = count($this->columns);
            return "<tbody>\n<tr><td colspan=\"$colspan\">Нет данных</td></tr>\n</tbody>";
        }
        $rows[] = $this->renderSummary();
        return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
    }

    private function renderTableRow($model): string
    {
        $cells = [];
        foreach ($this->columns as $index => $column) {
            $value = $model[$index] ?? '';
            if ($this->tableRowRenderCallback) {
                $value = call_user_func($this->tableRowRenderCallback, $value, $column);
            }
            $cells[] = Html::tag('td', $value);
        }
        return Html::tag('tr', implode('', $cells));
    }

    private function renderSummary(): string
    {
        $total = [];
        foreach ($this->models as $model) {
            foreach ($this->columns as $index => $column) {
                if (!isset($total[$index])) {
                    $total[$index] = 0;
                }
                $value = $model[$index] ?? '0';
                if (is_array($value)) {
                    $value = $value['count'];
                }
                $total[$index] += (int) $value;
            }
        }
        $cells = [];
        foreach ($this->columns as $index => $column) {
            $value = $total[$index] ?? '';
            if ($index === 0) {
                $value = 'Всего';
            }
            $cells[] = Html::tag('td', $value);
        }
        return Html::tag('tr', implode('', $cells));
    }
}

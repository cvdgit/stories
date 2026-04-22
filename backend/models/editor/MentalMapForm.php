<?php

declare(strict_types=1);

namespace backend\models\editor;

class MentalMapForm extends BaseForm
{
    public $mental_map_id;
    public $required;
    public $use_slide_image;
    public $name;
    public $texts;
    public $image;
    public $treeMapKind;

    private const MAP_TREE = 'tree';
    private const MAP_TREE_DIALOG_PLAN = 'tree-dialog-plan';

    public function init(): void
    {
        parent::init();
        $this->name = 'Ментальная карта';
        $this->use_slide_image = true;
        $this->required = true;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['story_id', 'name'], 'required'],
            [['story_id', 'required'], 'integer'],
            [['use_slide_image'], 'boolean'],
            ['treeMapKind', 'in', 'range' => array_keys(self::mapValues())],
            ['texts', 'safe'],
            [['image', 'name'], 'string'],
            ['mental_map_id', 'string', 'max' => 36],
        ]);
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'mental_map_id' => 'Ментальная карта',
            'required' => 'Тест обязателен для прохождения',
            'use_slide_image' => 'Использовать изображение со слайда в качестве фона для ментальной карты',
            'name' => 'Название',
            'treeMapKind' => 'Ментальная карта',
        ]);
    }

    public function isRequired(): bool
    {
        return $this->required === '1';
    }

    public function isMentalMap(): bool
    {
        return empty($this->treeMapKind);
    }

    public function isTreeMentalMap(): bool
    {
        return $this->treeMapKind === self::MAP_TREE;
    }

    public function isTreeDialogPlanMentalMap(): bool
    {
        return $this->treeMapKind === self::MAP_TREE_DIALOG_PLAN;
    }

    public static function mapValues(): array
    {
        return [
            self::MAP_TREE => 'В виде дерева',
            self::MAP_TREE_DIALOG_PLAN => 'В виде дерева с диалогом (план)',
        ];
    }

    public function isUserSlideImage(): bool
    {
        return $this->use_slide_image === '1';
    }
}

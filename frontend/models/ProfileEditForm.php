<?php


namespace frontend\models;


use common\models\Profile;
use yii\base\Model;

class ProfileEditForm extends Model
{

    public $first_name;
    public $last_name;
    public $photoForm;

    private $_profile;

    public function __construct($profile, $config = [])
    {
        $this->_profile = $profile;
        if ($this->_profile === null) {
            $this->_profile = new Profile();
        }
        $this->photoForm = new ProfileImageForm();
        parent::__construct($config);
    }

    public function init()
    {
        $this->first_name = $this->_profile->first_name;
        $this->last_name = $this->_profile->last_name;
        // $this->photo = $this->_profile->photo;
        parent::init();
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
        ];
    }

    public function getProfile()
    {
        return $this->_profile;
    }

}
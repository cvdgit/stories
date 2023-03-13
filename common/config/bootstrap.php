<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@public', dirname(dirname(__DIR__)) . '/public_html');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@modules', dirname(dirname(__DIR__)) . '/modules');
Yii::setAlias('@backendModules', dirname(dirname(__DIR__)) . '/backend/modules');
Yii::setAlias('@frontedModules', dirname(dirname(__DIR__)) . '/frontend/modules');
Yii::setAlias('@tests', dirname(dirname(__DIR__)) . '/tests');

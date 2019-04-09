<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;

class UserController extends Controller {

    public function behaviors() {
        $behaviors = parent::behaviors();
        //$behaviors['corsFilter'] = [
        //    'class' => \yii\filters\Cors::class
        //];
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\QueryParamAuth::class,
            'except' => ['login'],
        ];
        return $behaviors;
    }

    public function actionLogin() {
        return ['asdfda' => 'asdfads'];
    }

}

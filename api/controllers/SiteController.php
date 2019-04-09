<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use yii\base\InvalidRouteException;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        throw new InvalidRouteException('Choose version do you want or please contact to administrator for more information.');
    }

    public function actionError(){
        //if (Yii::$app->getErrorHandler()->exception === null) {
            return [
                
            ];
        //}
    }
}

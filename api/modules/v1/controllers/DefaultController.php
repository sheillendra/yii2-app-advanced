<?php

namespace api\modules\v1\controllers;

use yii\rest\Controller;
use yii\base\InvalidRouteException;

/**
 * Site controller
 */
class DefaultController extends Controller {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        throw new InvalidRouteException('Contact administrator for more information.');
    }

}

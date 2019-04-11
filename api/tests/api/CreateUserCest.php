<?php

namespace api\tests;

use api\tests\ApiTester;

class CreateUserCest {

    public function _before(ApiTester $I) {
        
    }

    public function _after(ApiTester $I) {
        
    }

    // tests
    public function createUserViaAPI(ApiTester $I) {
        $I->amHttpAuthenticated('service_user', '123456');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/user', ['name' => 'davert', 'email' => 'davert@codeception.com']);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"result":"ok"}');
    }

}

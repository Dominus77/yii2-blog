<?php

namespace api\tests\api;

use api\tests\ApiTester;

/**
 * Class UserCest
 * @package api\tests\api
 */
class UserCest
{
    /**
     * @param ApiTester $I
     */
    public function checkUsers(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/v1/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}

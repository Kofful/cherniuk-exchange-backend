<?php
namespace App\Tests\api;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Faker\Factory;
use Faker\Generator;

class RegistrationCest
{
    private Generator $faker;

    public function _before(ApiTester $I)
    {
        $this->faker = Factory::create();
    }

    public function trySuccessfulValidation(ApiTester $I) {
        $I->sendPostAsJson(
            "/en/api/register",
            [
                "email" => $this->faker->email(),
                "username" => $this->faker->userName(),
                "password" => $this->faker->password(8, 64)
            ]
        );
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContains("[]");
    }

    public function tryToRegisterWithoutEmail(ApiTester $I)
    {
        $I->sendPostAsJson(
            "/en/api/register",
            [
                "username" => $this->faker->userName(),
                "password" => $this->faker->password(8, 64)
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Email is required");
    }

    public function tryToRegisterWithoutUsername(ApiTester $I)
    {
        $I->sendPostAsJson(
            "/en/api/register",
            [
                "email" => $this->faker->email(),
                "password" => $this->faker->password(8, 64)
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Username is required");
    }

    public function tryToRegisterWithoutPassword(ApiTester $I)
    {
        $I->sendPostAsJson(
            "/en/api/register",
            [
                "email" => $this->faker->email(),
                "username" => $this->faker->userName(),
            ]
        );
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
        $I->seeResponseContains("Password is required");
    }
}

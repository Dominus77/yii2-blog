<?php
namespace frontend\tests\unit\models;

use Yii;
use Codeception\Test\Unit;
use modules\users\models\PasswordResetRequestForm;
use common\fixtures\User as UserFixture;
use modules\users\models\User;
use frontend\tests\UnitTester;
use yii\mail\MessageInterface;

/**
 * Class PasswordResetRequestFormTest
 * @package frontend\tests\unit\models
 */
class PasswordResetRequestFormTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @inheritdoc
     */
    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function testSendMessageWithWrongEmailAddress()
    {
        $model = new PasswordResetRequestForm();
        $model->email = 'not-existing-email@example.com';
        expect_not($model->sendEmail());
    }

    /**
     * @inheritdoc
     */
    public function testNotSendEmailsToInactiveUser()
    {
        $user = $this->tester->grabFixture('user', 1);
        $model = new PasswordResetRequestForm();
        $model->email = $user['email'];
        expect_not($model->sendEmail());
    }

    /**
     * @inheritdoc
     */
    public function testSendEmailSuccessfully()
    {
        $userFixture = $this->tester->grabFixture('user', 2);
        
        $model = new PasswordResetRequestForm();
        $model->email = $userFixture['email'];
        $user = User::findOne(['password_reset_token' => $userFixture['password_reset_token']]);

        expect_that($model->sendEmail());
        expect_that($user->password_reset_token);

        $emailMessage = $this->tester->grabLastSentEmail();
        expect('valid email is sent', $emailMessage)->isInstanceOf(MessageInterface::class);
        expect($emailMessage->getTo())->hasKey($model->email);
        expect($emailMessage->getFrom())->hasKey(Yii::$app->params['supportEmail']);
    }
}

<?php

namespace modules\comment\widgets\form;

use Yii;
use Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use modules\comment\widgets\form\assets\FormAsset;
use modules\comment\models\Comment;
use modules\comment\Module;

/**
 * Class CommentForm
 * @package modules\comment\widgets\form
 */
class CommentForm extends Widget
{
    /** @var bool */
    public $status = true;
    /** @var Comment */
    public $model;
    /** @var Url */
    public $formUrl;
    /** @var Url */
    public $captchaUrl;
    /** @var array */
    public $formOptions = [];
    /** @var Comment */
    public $comment;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->getCommentModel();
        if (!$this->model instanceof Model) {
            throw new InvalidConfigException('The model is not an instance of the class' . ' ' . Model::class);
        }
        $this->formUrl = $this->formUrl ?: Url::to(['/comment/default/create']);
        $this->captchaUrl = $this->captchaUrl ?: Url::to('/comment/default/captcha');
        $this->formOptions = ArrayHelper::merge([
            'id' => 'reply-form',
            'action' => $this->formUrl
        ], $this->formOptions);
    }

    /**
     * @return string|void
     * @throws Exception
     */
    public function run()
    {
        if ($this->status === true) {
            $this->registerResource();
            echo Html::beginTag('div', ['id' => 'form-container', 'style' => 'display: none;']);
            echo $this->renderForm();
            echo Html::endTag('div');
        }
    }

    /**
     * Render form
     * @throws Exception
     */
    public function renderForm()
    {
        $form = ActiveForm::begin($this->formOptions);
        echo $form->field($this->comment, 'author')->textInput([
            'class' => 'form-control',
            'placeholder' => true
        ]);

        echo $form->field($this->comment, 'email')->textInput([
            'class' => 'form-control',
            'placeholder' => true
        ])->hint(Module::t('module', 'No one will see'));

        echo $form->field($this->comment, 'comment')->textarea([
            'rows' => 6,
            'class' => 'form-control',
            'placeholder' => true
        ]);

        if ($this->comment->scenario === Comment::SCENARIO_GUEST) {
            echo Html::beginTag('div', ['class' => 'row']);
            echo Html::beginTag('div', ['class' => 'col-md-6']);
            echo $this->renderCaptcha($form);
            echo Html::endTag('div');
            echo Html::endTag('div');
        }

        echo $form->field($this->comment, 'entity')->hiddenInput()->label(false);
        echo $form->field($this->comment, 'entity_id')->hiddenInput()->label(false);
        echo $form->field($this->comment, 'rootId')->hiddenInput()->label(false);
        echo $form->field($this->comment, 'parentId')->hiddenInput()->label(false);

        echo Html::beginTag('div', ['class' => 'form-group']);
        echo Html::submitButton('<span class="glyphicon glyphicon-send"></span> ' . Module::t('module', 'Submit comment'), [
            'class' => 'btn btn-primary',
            'name' => 'comment-submit-button',
            'value' => $this->comment->scenario
        ]);
        echo Html::endTag('div');

        ActiveForm::end();
    }

    /**
     * @param ActiveForm $form
     * @return ActiveField
     * @throws Exception
     */
    public function renderCaptcha(ActiveForm $form)
    {
        return $form->field($this->comment, 'verifyCode')->widget(Captcha::class, [
            'captchaAction' => $this->captchaUrl,
            'imageOptions' => [
                'style' => 'display:block; border:none; cursor: pointer',
                'alt' => Module::t('module', 'Code'),
                'title' => Module::t('module', 'Click on the picture to change the code.')
            ],
        ])->label(false);
    }

    /**
     * @return Comment
     */
    public function getCommentModel()
    {
        if ($this->comment === null) {
            $this->comment = new Comment();
            $this->comment->scenario = $this->model->scenario;
            if (Yii::$app->user->isGuest) {
                $this->comment->scenario = Comment::SCENARIO_GUEST;
            }
            $this->comment->entity = get_class($this->model);
            $this->comment->entity_id = $this->model->id;
            /** @var \modules\users\models\User $user */
            if ($user = Yii::$app->user->identity) {
                $this->comment->author = $user->username;
                $this->comment->email = $user->email;
            }
        }
        return $this->comment;
    }

    /**
     * Register resource
     */
    public function registerResource()
    {
        $view = $this->getView();
        FormAsset::register($view);
    }
}

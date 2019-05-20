<?php
namespace borysenko\UserComponent\components;

use Yii;
use yii\base\InvalidValueException;
use yii\web\IdentityInterface;


class User extends \yii\web\User
{

    public function init()
    {
        $this->enableAutoLogin = true;
        $this->getIdentityAndDurationFromCookie();
        parent::init();
    }

    protected function getIdentityAndDurationFromCookie()
    {
        $value = Yii::$app->getRequest()->getCookies()->getValue($this->identityCookie['name']);
        if ($value === null) {
            return null;
        }
        $data = json_decode($value, true);
        if (is_array($data) && count($data) == 3) {
            list($id, $authKey, $duration) = $data;
            /* @var $class IdentityInterface */
            $class = $this->identityClass;
            $identity = $class::findIdentity($id);
            if ($identity !== null) {
                if (!$identity instanceof IdentityInterface) {
                    throw new InvalidValueException("$class::findIdentity() must return an object implementing IdentityInterface.");
                } elseif (!$identity->validateAuthKey($authKey)) {
                    if(Yii::$app->session->has($this->idParam)) {
                        Yii::$app->session->remove($this->idParam);
                        $this->removeIdentityCookie();
                    }
                } else {
                    return ['identity' => $identity, 'duration' => $duration];
                }
            }
        }
        $this->removeIdentityCookie();
        return null;
    }
}
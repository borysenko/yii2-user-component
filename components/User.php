<?php
namespace borysenko\UserComponent\components;

use Yii;
use yii\base\InvalidValueException;
use yii\web\IdentityInterface;


class User extends \yii\web\User
{

    protected function renewIdentityCookie()
    {
        $this->getIdentityAndDurationFromCookie();
        $name = $this->identityCookie['name'];
        $value = Yii::$app->getRequest()->getCookies()->getValue($name);
        if ($value !== null) {
            $data = json_decode($value, true);
            if (is_array($data) && isset($data[2])) {
                $cookie = Yii::createObject(array_merge($this->identityCookie, [
                    'class' => 'yii\web\Cookie',
                    'value' => $value,
                    'expire' => time() + (int) $data[2],
                ]));
                Yii::$app->getResponse()->getCookies()->add($cookie);
            }
        }
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
                        return Yii::$app->response->redirect(['/']);
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

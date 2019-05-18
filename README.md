# yii2-user-component

Installation
------------
```
php composer.phar require --prefer-dist borysenko/yii2-user-component "*"
```

или

```
"borysenko/yii2-user-component": "*"
```

Компонент исправляет баг проверки auth_key у авторизованных пользователей, когда меняется поле auth_key в таблице юзеров.
При каждом обновлении страницы в браузере запускается валидация свойства  auth_key и если в базе мы при смене пароля или просто поменяли  auth_key то пользователя разлогинет во всех браузерах.

Данная тема осуждалась тут: https://www.yiiframework.ru/forum/viewtopic.php?f=19&t=50810

Подключение и настройка
---------------------------------
В конфигурационный файл приложения добавить в компонент 'user' -> 'class' => '\borysenko\UserComponent\components\User'

```php
    'components' => [
        'user' => [
            'class' => '\borysenko\UserComponent\components\User',
            //...
        ],
        //...
    ],
```
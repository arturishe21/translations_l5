
В composer.json добавляем в блок require
```json
 "vis/translations_l5": "1.0.*"
```

Выполняем
```json
composer update
```

Добавляем в app.php
```php
  'Vis\Translations\TranslationsServiceProvider',
```

Выполняем миграцию таблиц
```json
   php artisan migrate --path=packages/vis/translations/src/Migrations
```

Публикуем config и js файлы
```json
   php artisan vendor:publish --tag=translations --force
```

В файле config/builder/admin.php в массив menu добавляем
```php
 	array(
            'title' => 'Переводы',
            'icon'  => 'language',
            'link'  => '/translations/phrases',
            'check' => function() {
                return true;
            }
        ),
```
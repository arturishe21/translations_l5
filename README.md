
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
  Vis\Translations\TranslationServiceProvider::class,
```

Выполняем миграцию таблиц
```json
   php artisan migrate --path=vendor/vis/translations_l5/src/Migrations
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

Использование в blade laravel ниже 5.4 функция __()
```php
 	{{__('привет')}}
```

Использование в blade laravel 5.4 и выше, функция __t()
```php
 	{{__t('привет')}}
```

Генерация переводов
```php
 	php artisan translate:generate
```

Генерация переводов и создания полей переводов в таблицах
```php
 	php artisan translate:table {tables} {fields}
```
где {tables} - таблицы, например: user,news
и {fields} - поля в таблицы, например title,description



Требования
----------

SQLite3 версии 3.24+ для поддержки `insert ... on conflict ...`.


Деплой
------

Для создания БД, структуры и добавления тестовых данных пользователей:

`make init`               Создать таблицы (внимание: обнулит БД!)
`make seed-users`         Вставить 5000000 случайных юзеров в БД

Далее необходимо добавить в cron (например, раз в 5 минут):

`*/5 * * * * make populate-check-emails`        Заполняет очередь на проверку емейлов
`1-59/5 * * * * make check-emails`              Обрабатывает очередь на проверку емейлов
`5-59/5 * * * * make populate-send-emails`      Заполняет очередь на отправку
`6-59/5 * * * * make send-emails`               Обрабатывает очередь на отправку

В воркерах реализована логика локов, поэтому одновременный запуск нескольких экземпляров не приведет к дублированию писем.
Записи в крон желательно добавлять со смещением в 1-2-5-N минут в порядке: заполнить очередь на проверку, проверка, заполнить очередь на отправку, отправка.

Работа
------

Комментарии
-----------

* В sqlite3 нет типов полей для boolean и date, вместо этого используются int и string.
* Подразумевается, что все сервера, пользователи и вообще все данные содержат дату/время в одном часовом поясе. Для расчетов используется timestamp без таймзоны, используются строковые даты без таймзоны.

Требования
----------

SQLite3 версии 3.24+ для поддержки `insert ... on conflict ...`.

Расширение php8-sqlite

Деплой
------

Для создания БД, структуры и добавления тестовых данных пользователей:

`make init`               Создать таблицы (внимание: обнулит БД!)

`make seed-users`         Вставить 5000000 случайных юзеров в БД

Далее необходимо добавить в cron (например, раз в 5 минут):

`*/5 * * * * make populate-check-emails`        Заполняет очередь на проверку емейлов батчами по 100000

`1-59/5 * * * * make check-emails`              Обрабатывает очередь на проверку емейлов батчами по 200 с локами

`5-59/5 * * * * make populate-send-emails`      Заполняет очередь на отправку

`6-59/5 * * * * make send-emails`               Обрабатывает очередь на отправку батчами по 200 с локами

В воркерах реализована логика локов, поэтому одновременный запуск нескольких экземпляров не приведет к дублированию писем.
Записи в крон желательно добавлять со смещением в 1-2-5-N минут в порядке: заполнить очередь на проверку, проверка, заполнить очередь на отправку, отправка.

Запуск
------

Если последовательно запустить все имеющиеся Makefile-команды, то будет обработан создана БД и обработан первый батч пользователей с подписками, которые кончаются скоро.
**Можно передать переменную окружения QUICK=1 чтобы убрать задержки send_email и check_email**.

```
make init && make seed-users && make populate-check-emails && QUICK=1 make check-emails && make populate-send-emails && QUICK=1 make send-emails
```

Результатом выполнения этой команды будет появление файлов в `runtime/emails` с письмами, которые были "отправлены" этим пользователям.

Пример однократного запуска (при нормальной эксплуатации, команды исполняются из cron, а не вручную):

```
rm -f db/db.sqlite && touch db/db.sqlite
sqlite3 db/db.sqlite < db/init.sql
php tools/SeedUsers.php
[2025-02-13 18:16:27] seeding 5000000 users (80% w/ subscription and 15% w/ confirmed email)
[2025-02-13 18:17:00] inserted 5000000 users in 33.107100 seconds
php tools/PopulateCheckEmailQueue.php
[2025-02-13 18:17:01] added 750960 emails to check queue
php tools/CheckEmails.php
[2025-02-13 18:17:01] checking 200 emails: user_2@mail.com, user_26@mail.com, ... ... ..., user_1143@mail.com, user_1152@mail.com, user_1160@mail.com, user_1184@mail.com, user_1188@mail.com, user_1193@mail.com, user_1206@mail.com, user_1207@mail.com, user_1208@mail.com
[2025-02-13 18:17:01] checked email user_2@mail.com: ❌
[2025-02-13 18:17:01] checked email user_26@mail.com: ✅
[2025-02-13 18:17:01] checked email user_28@mail.com: ✅
.....
.....
.....
[2025-02-13 18:17:06] checked email user_1206@mail.com: ❌
[2025-02-13 18:17:06] checked email user_1207@mail.com: ✅
[2025-02-13 18:17:06] checked email user_1208@mail.com: ✅
php tools/PopulateSendEmailQueue.php
[2025-02-13 18:17:06] added 1 emails to 1 day queue
[2025-02-13 18:17:06] added 2 emails to 3 day queue
php tools/SendEmails.php
[2025-02-13 18:17:06] sending 3 emails
[2025-02-13 18:17:06] sent email to user_704@mail.com
[2025-02-13 18:17:06] sent email to user_682@mail.com
[2025-02-13 18:17:06] sent email to user_686@mail.com

```

Результат выполнения:

```
ls runtime/email/
user_682@mail.com_0_67ae372249d7a.txt  user_686@mail.com_0_67ae372249e4d.txt  user_704@mail.com_0_67ae372249b91.txt
```

Комментарии
-----------

* В sqlite3 нет типов полей для boolean и date, вместо этого используются int и string.
* Подразумевается, что все сервера, пользователи и вообще все данные содержат дату/время в одном часовом поясе. Для расчетов используется timestamp без таймзоны, используются строковые даты без таймзоны.

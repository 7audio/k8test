Задача 2
===

Собирать такое большое количество условий и логики в один запрос нерационально. Такой запрос невозможно будет поддерживать или модифицировать, он будет нечитаем для других разработчиков.

Я бы выделил две view-шки для статистики платежей и статистики заказов. Таким образом их можно будет отлаживать, переиспользовать в других запросах, материализовать для производительности и так далее.

VIEW - Статистика по заказам:
---

```
    CREATE VIEW order_stats AS
    SELECT o.user_id,
        SUM(CASE WHEN o.payed = TRUE THEN 1 ELSE 0 END) AS paid_orders,
        SUM(CASE WHEN o.payed = FALSE THEN 1 ELSE 0 END) AS unpaid_orders
    FROM orders o
    GROUP BY o.user_id;
```

VIEW - Статистика по платежам:
---

```
    CREATE VIEW payment_stats AS
    SELECT o.user_id,
        COUNT(p.id) AS total_payments,
        SUM(CASE WHEN p.status <> 'success' THEN 1 ELSE 0 END) AS failed_payments
    FROM orders o
    JOIN payments p ON o.id = p.order_id
    GROUP BY o.user_id;
```

Итоговый запрос:
---

```
    SELECT u.id, u.username FROM users u
    JOIN order_stats os ON u.id = os.user_id JOIN payment_stats ps ON u.id = ps.user_id
    WHERE (os.paid_orders > 2 * os.unpaid_orders) AND (ps.failed_payments / NULLIF(ps.total_payments, 0) < 0.15);
```

Допущения:
---

Запросы будут работать в Postgres, для MySQL/MariaDB надо будет менять приведение типов bool/tinyint
Поле `status` - текстовое, успешный статус это `success`
Опечатка `payed` --> `paid` оставлена как есть

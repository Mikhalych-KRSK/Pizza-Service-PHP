
**PizzaService API**

Данный проект предоставляет API для управления заказами на пиццу. Вы можете создавать заказы, добавлять товары в существующие заказы, получать информацию о заказе, помечать заказы как выполненные и просматривать список заказов.

**Инструкция по запуску**
1. Убедитесь, что у вас установлен Docker и Docker Compose.
2. В корневой директории проекта выполните команду: ```docker-compose up --build```


**Примеры использования API**

**1. Создание заказа**
```http
POST /orders
Content-Type: application/json

{
  "items": ["pizza", "drink", "dessert"]
}
```

**Ответ:**
```json
{
  "order_id": "order_001",
  "items": ["pizza", "drink", "dessert"],
  "done": false
}
```

**2. Добавление товаров в заказ**
```http
POST /orders/order_001
Content-Type: application/json

{
  "items": ["extra cheese"]
}
```

**Ответ:**
```json
{
  "order_id": "order_001",
  "items": ["pizza", "drink", "dessert", "extra cheese"],
  "done": false
}
```

**3. Получение информации о заказе**
```http
GET /orders/order_001
```

**Ответ:**
```json
{
  "order_id": "order_001",
  "items": ["pizza", "drink", "dessert", "extra cheese"],
  "done": false
}
```

**4. Пометить заказ как выполненный**
```http
POST /orders/order_001/done
X-Auth-Key: qwerty123
```

**Ответ:**
```json
{
  "order_id": "order_001",
  "items": ["pizza", "drink", "dessert", "extra cheese"],
  "done": true
}
```

**5. Список заказов**
```http
GET /orders?done=false
X-Auth-Key: qwerty123
```

**Ответ:**
```json
[
  {
    "order_id": "order_001",
    "items": ["pizza", "drink", "dessert", "extra cheese"],
    "done": false
  }
]
```

**Примечания**
- Все запросы и ответы обрабатываются в формате JSON.
- Для аутентификации выполнения операций требуется заголовок `X-Auth-Key` с правильным ключом.

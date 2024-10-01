<?php

declare(strict_types=1);

class PizzaService {
    private array $orders = [];
    private int $nextId = 1;

    public function createOrder(array $items): string {
        if (empty($items)) {
            return $this->response(400, ['error' => 'Items cannot be empty']);
        }

        $orderId = $this->generateOrderId();
        $this->orders[$orderId] = [
            'order_id' => $orderId,
            'items' => $items,
            'done' => false
        ];

        return $this->response(201, $this->orders[$orderId]);
    }

    // Добавление товаров в заказ
    public function addItemsToOrder(string $orderId, array $items): string {
        if (!isset($this->orders[$orderId]) || $this->orders[$orderId]['done']) {
            return $this->response(400, ['error' => 'Order does not exist or is already done']);
        }

        $this->orders[$orderId]['items'] = array_merge($this->orders[$orderId]['items'], $items);
        return $this->response(200, $this->orders[$orderId]);
    }

    // Получение информации о заказе
    public function getOrder(string $orderId): string {
        if (isset($this->orders[$orderId])) {
            return $this->response(200, $this->orders[$orderId]);
        }
        return $this->response(404, ['error' => 'Order not found']);
    }

    // Пометить заказ как выполненный
    public function markOrderAsDone(string $orderId, ?string $authKey): string {
        if ($authKey !== 'qwerty123') {
            return $this->response(403, ['error' => 'Unauthorized']);
        }

        if (isset($this->orders[$orderId]) && !$this->orders[$orderId]['done']) {
            $this->orders[$orderId]['done'] = true;
            return $this->response(200, $this->orders[$orderId]);
        }

        return $this->response(400, ['error' => 'Order does not exist or is already done']);
    }

    public function listOrders(?bool $done, ?string $authKey): string {
        if ($authKey !== 'qwerty123') {
            return $this->response(403, ['error' => 'Unauthorized']);
        }

        $filteredOrders = array_filter($this->orders, function ($order) use ($done) {
            return $done === null || $order['done'] === $done;
        });

        return $this->response(200, array_values($filteredOrders));
    }

    private function generateOrderId(): string {
        return 'order_' . str_pad((string)$this->nextId++, 3, '0', STR_PAD_LEFT);
    }

    private function response(int $statusCode, array $data): string {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }
}

// Обработка HTTP-запросов
header('Content-Type: application/json');
$service = new PizzaService();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$authKey = $_SERVER['HTTP_X_AUTH_KEY'] ?? null;

switch ($requestMethod) {
    case 'POST':
        if ($requestUri[0] === 'orders' && count($requestUri) === 1) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            echo $service->createOrder($data['items'] ?? []);
        } elseif (count($requestUri) === 3 && $requestUri[1] === 'orders' && $requestUri[2] === 'done') {
            echo $service->markOrderAsDone($requestUri[1], $authKey);
        } elseif (count($requestUri) === 3 && $requestUri[1] === 'orders') {
            $orderId = $requestUri[2];
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            echo $service->addItemsToOrder($orderId, $data['items'] ?? []);
        }
        break;
    case 'GET':
        if (count($requestUri) === 2 && $requestUri[1] === 'orders') {
            $done = isset($_GET['done']) ? filter_var($_GET['done'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
            echo $service->listOrders($done, $authKey);
        } elseif (count($requestUri) === 3 && $requestUri[1] === 'orders') {
            $orderId = $requestUri[2];
            echo $service->getOrder($orderId);
        }
        break;
}

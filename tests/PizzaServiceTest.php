<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PizzaServiceTest extends TestCase {
    private PizzaService $service;

    protected function setUp(): void {
        $this->service = new PizzaService();
    }

    public function testCreateOrder(): void {
        $response = $this->service->createOrder(['pizza', 'drink']);
        $this->assertJsonStringEqualsJsonString(
            $response,
            json_encode(['order_id' => 'order_001', 'items' => ['pizza', 'drink'], 'done' => false])
        );
    }

    public function testCreateOrderWithEmptyItems(): void {
        $response = $this->service->createOrder([]);
        $this->assertJsonStringEqualsJsonString($response, json_encode(['error' => 'Items cannot be empty']));
    }

    public function testGetOrder(): void {
        $this->service->createOrder(['pizza']);
        $response = $this->service->getOrder('order_001');
        $this->assertJsonStringEqualsJsonString(
            $response,
            json_encode(['order_id' => 'order_001', 'items' => ['pizza'], 'done' => false])
        );
    }

    public function testGetNonExistentOrder(): void {
        $response = $this->service->getOrder('order_999');
        $this->assertJsonStringEqualsJsonString($response, json_encode(['error' => 'Order not found']));
    }

    public function testMarkOrderAsDone(): void {
        $this->service->createOrder(['pizza']);
        $response = $this->service->markOrderAsDone('order_001', 'qwerty123');
        $this->assertJsonStringEqualsJsonString(
            $response,
            json_encode(['order_id' => 'order_001', 'items' => ['pizza'], 'done' => true])
        );
    }

    public function testUnauthorizedMarkOrderAsDone(): void {
        $this->service->createOrder(['pizza']);
        $response = $this->service->markOrderAsDone('order_001', 'wrong_auth_key');
        $this->assertJsonStringEqualsJsonString($response, json_encode(['error' => 'Unauthorized']));
    }

    public function testListOrders(): void {
        $this->service->createOrder(['pizza']);
        $this->service->createOrder(['drink']);
        $response = $this->service->listOrders(null, 'qwerty123');
        $this->assertCount(2, json_decode($response, true));
    }
}
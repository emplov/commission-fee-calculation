<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories\Persistence;

class InMemoryPersistence implements Persistence
{
    private array $data = [];
    private int $lastId = 0;

    public function generateId(): int
    {
        ++$this->lastId;

        return $this->lastId;
    }

    public function persist(array $data): void
    {
        $this->data[$data['user_id']] = $data;
    }

    public function retrieve(int $id): ?array
    {
        if (!isset($this->data[$id])) {
            return null;
        }

        return $this->data[$id];
    }

    public function delete(int $id): void
    {
        if (!isset($this->data[$id])) {
            return;
        }

        unset($this->data[$id]);
    }
}

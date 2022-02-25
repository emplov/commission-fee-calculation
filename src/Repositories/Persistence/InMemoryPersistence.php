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

    public function persist(array $data): int
    {
        if (!isset($data['id'])) {
            $data['id'] = $this->generateId();
        }

        $this->data[$data['id']] = $data;

        return $data['id'];
    }

    public function retrieve(int $id): ?array
    {
        return $this->data[$id] ?? null;
    }

    public function delete(int $id): void
    {
        if (!isset($this->data[$id])) {
            return;
        }

        unset($this->data[$id]);
    }
}

<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions;

use CommissionFeeCalculation\Exceptions\CommissionTypeNotExistsException;
use CommissionFeeCalculation\Repositories\User;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class TypesContext
{
    private ?TypeAbstract $type = null;

    private User $user;

    public function setStrategy(TypeAbstract $type): void
    {
        $this->type = $type;
        $this->user = Container::getInstance()->get(User::class);
    }

    public function execute(int $userKey, string $commissionType, string $amount, string $currency, array $extra = []): void
    {
        if (is_null($this->type)) {
            $this->showError($userKey, $commissionType);
        }

        $this->type->handle($userKey, $amount, $currency, $extra);
    }

    private function showError(int $userKey, string $commissionType): void
    {
        throw new CommissionTypeNotExistsException('['.$this->user->users[$userKey]['type'].'_'.$commissionType.'] is not exists.'.PHP_EOL, );
    }
}

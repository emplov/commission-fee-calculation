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

    /**
     * Set strategy.
     *
     * @return void
     */
    public function setStrategy(TypeAbstract $type)
    {
        $this->type = $type;
        $this->user = Container::getInstance()->get(User::class);
    }

    /**
     * Execute type.
     *
     * @throws CommissionTypeNotExistsException
     */
    public function execute(int $userKey, string $commissionType, string $amount, string $currency, array $extra = []): void
    {
        if (is_null($this->type)) {
            $this->showError($userKey, $commissionType);
        }

        $this->type->handle($userKey, $amount, $currency, $extra);
    }

    /**
     * @throws CommissionTypeNotExistsException
     */
    private function showError(int $userKey, string $commissionType)
    {
        throw new CommissionTypeNotExistsException('['.$this->user->users[$userKey]['type'].'_'.$commissionType.'] is not exists.'.PHP_EOL, );
    }
}

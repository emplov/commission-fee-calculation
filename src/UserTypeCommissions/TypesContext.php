<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions;

use CommissionFeeCalculation\Exceptions\CommissionTypeNotExistsException;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Repositories\User;

class TypesContext
{
    private ?TypeAbstract $type = null;

    private User $user;

    /**
     * Set strategy
     *
     * @param TypeAbstract $type
     * @return void
     */
    public function setStrategy(TypeAbstract $type)
    {
        $this->type = $type;
        $this->user = Container::getInstance()->get(User::class);
    }

    /**
     * Execute type
     *
     * @param int $userKey
     * @param string $commissionType
     * @param string $amount
     * @param string $currency
     * @param array $extra
     * @return void
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
        throw new CommissionTypeNotExistsException(
            '[' . $this->user->users[$userKey]['type'] . '_' . $commissionType . '] is not exists.' . PHP_EOL,
        );
    }
}

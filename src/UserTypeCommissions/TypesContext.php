<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions;

use CommissionFeeCalculation\Exceptions\CommissionTypeNotExistsException;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class TypesContext
{
    private ?TypeAbstract $type = null;

    private UserRepository $userRepository;

    public function setStrategy(TypeAbstract $type, UserRepository $userRepository): void
    {
        $this->type = $type;
        $this->userRepository = $userRepository;
    }

    public function execute(
        int $userKey,
        string $commissionType,
        string $amount,
        string $currency,
        string $date,
        int $decimalsCount,
    ): string {
        if (is_null($this->type)) {
            $this->showError($userKey, $commissionType);
        }

        return $this->type->handle(
            $userKey,
            $amount,
            $currency,
            $date,
            $decimalsCount,
        );
    }

    private function showError(int $userKey, string $commissionType): void
    {
        $user = $this->userRepository->find($userKey);

        throw new CommissionTypeNotExistsException('['.$user->getUserType().'_'.$commissionType.'] is not exists.'.PHP_EOL);
    }
}

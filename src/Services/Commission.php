<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use CommissionFeeCalculation\DTO\CommissionDTO;
use CommissionFeeCalculation\Entities\User;
use CommissionFeeCalculation\UserTypeCommissions\TypesContext;

class Commission
{
    private User $user;

    private Config $config;

    public function __construct(User $user, Config $config)
    {
        $this->user = $user;
        $this->config = $config;
    }

    public function addTransaction(
        string $date,
        int $userID,
        string $userType,
        string $operationType,
        string $operationAmount,
        string $operationCurrency,
    ): string {
        // Get user if exists
        $user = $this->user->find($userID);

        // Create user if not exists
        if (is_null($user)) {
            $this->user->addUser($userID, $userType);

            $user = $this->user->find($userID);
        }

        // Get decimals count
        $decimalsCount = mb_strlen(explode('.', $operationAmount)[1] ?? '');

        $dto = new CommissionDTO(
            userKey: $user['user_id'],
            date: $date,
            operationAmount: $operationAmount,
            operationCurrency: $operationCurrency,
            userType: $userType,
            decimalsCount: $decimalsCount,
        );

        // Withdrawal check statements
        $context = $this->getTypeHandler(
            commissionType: $operationType,
            userType: $dto->userType,
        );

        return $context->execute(
            userKey: $dto->userKey,
            commissionType: $operationType,
            amount: $dto->operationAmount,
            currency: $dto->operationCurrency,
            date: $dto->date,
            decimalsCount: $dto->decimalsCount,
        );
    }

    public function getTypeHandler(string $commissionType, string $userType): TypesContext
    {
        $context = new TypesContext();

        foreach ($this->config->get('user_types')[$userType] as $type => $object) {
            if (
                mb_strtolower($userType.'_'.$type) === mb_strtolower($userType.'_'.$commissionType)
            ) {
                $context->setStrategy(new $object());

                break;
            }
        }

        return $context;
    }
}

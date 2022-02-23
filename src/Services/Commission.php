<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use CommissionFeeCalculation\DTO\CommissionDTO;
use CommissionFeeCalculation\Entities\User;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\UserTypeCommissions\TypesContext;

class Commission
{
    private UserRepository $userRepository;

    private Config $config;

    public function __construct(UserRepository $userRepository, Config $config)
    {
        $this->userRepository = $userRepository;
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
        $user = $this->userRepository->find($userID);

        // Create user if not exists
        if (is_null($user)) {
            $this->userRepository->save(new User($userID, $userType));

            $user = $this->userRepository->find($userID);
        }

        // Get decimals count
        $decimalsCount = $this->config->get('currency_decimal_part.'.$operationCurrency);

        $dto = new CommissionDTO(
            userKey: $user->getUserID(),
            date: $date,
            operationAmount: $operationAmount,
            operationCurrency: $operationCurrency,
            userType: $user->getUserType(),
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
                $context->setStrategy(new $object(), $this->userRepository);

                break;
            }
        }

        return $context;
    }
}

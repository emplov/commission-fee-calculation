<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use CommissionFeeCalculation\DTO\CommissionDTO;
use CommissionFeeCalculation\Entities\User;
use CommissionFeeCalculation\Exceptions\ScriptException;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class Commission
{
    private UserRepository $userRepository;

    private Config $config;

    public function __construct(UserRepository $userRepository, Config $config)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
    }

    /**
     * @throws ScriptException
     */
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
        $decimalsCount = $this->config->get('currency_decimal_part.'.$operationCurrency, 2);

        $dto = new CommissionDTO(
            userKey: $user->getUserID(),
            date: $date,
            operationAmount: $operationAmount,
            operationCurrency: $operationCurrency,
            userType: $user->getUserType(),
            decimalsCount: $decimalsCount,
        );

        // Withdrawal check statements
        $handler = $this->getTypeHandler(
            commissionType: $operationType,
            userType: $dto->userType,
        );

        return $handler->handle(
            userKey: $dto->userKey,
            amount: $dto->operationAmount,
            currency: $dto->operationCurrency,
            date: $dto->date,
            decimalsCount: $dto->decimalsCount,
        );
    }

    public function getTypeHandler(string $commissionType, string $userType): TypeAbstract
    {
        $handlerClass = $this->config->get('user_type_handlers.'.$userType.'.'.$commissionType);

        if (is_null($handlerClass)) {
            throw new ScriptException(ScriptException::ERROR_USER_TYPE_HANDLER_NOT_FOUND);
        }

        return Container::getInstance()->get($handlerClass);
    }
}

<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

use CommissionFeeCalculation\DTO\CommissionDTO;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\UserTypeCommissions\TypesContext;

class Commission
{
    public const WITHDRAW_TYPE = 'withdraw';

    public const DEPOSIT_TYPE = 'deposit';

    private User $user;

    public function __construct()
    {
        $this->user = Container::getInstance()->get(User::class);
    }

    public function addData(
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

        $calculatedCommission = '';

        // Save operation
        if ($operationType === self::WITHDRAW_TYPE) {
            $calculatedCommission = $this->addWithdrawal($dto);
        } elseif ($operationType === self::DEPOSIT_TYPE) {
            $calculatedCommission = $this->addDeposit($dto);
        }

        return $calculatedCommission;
    }

    public function addWithdrawal(CommissionDTO $dto): string
    {
        // Save withdrawal
        $this->user->addTransaction($dto->userKey, new Transaction(
            $dto->date,
            Transaction::TYPE_WITHDRAWAL,
            $dto->operationAmount,
            $dto->operationCurrency,
        ), 'withdrawals');

        // Withdrawal check statements
        $context = $this->getTypeHandler(
            commissionType: self::WITHDRAW_TYPE,
            userType: $dto->userType,
        );

        return $context->execute(
            userKey: $dto->userKey,
            commissionType: self::WITHDRAW_TYPE,
            amount: $dto->operationAmount,
            currency: $dto->operationCurrency,
            date: $dto->date,
            decimalsCount: $dto->decimalsCount,
        );
    }

    public function addDeposit(CommissionDTO $dto): string
    {
        // Save deposit
        $this->user->addTransaction($dto->userKey, new Transaction(
            $dto->date,
            Transaction::TYPE_WITHDRAWAL,
            $dto->operationAmount,
            $dto->operationCurrency,
        ), 'deposits');

        // Deposit check statements
        $context = $this->getTypeHandler(
            commissionType: self::DEPOSIT_TYPE,
            userType: $dto->userType,
        );

        return $context->execute(
            userKey: $dto->userKey,
            commissionType: self::DEPOSIT_TYPE,
            amount: $dto->operationAmount,
            currency: $dto->operationCurrency,
            date: $dto->date,
            decimalsCount: $dto->decimalsCount,
        );
    }

    public function getTypeHandler(string $commissionType, string $userType): TypesContext
    {
        $context = new TypesContext();

        foreach (Config::get('user_types')[$userType] as $type => $object) {
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

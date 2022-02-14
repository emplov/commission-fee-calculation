<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

use CommissionFeeCalculation\DTO\Objects\CommissionDataDTO;
use CommissionFeeCalculation\Exceptions\CommissionTypeNotExistsException;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\UserTypeCommissions\TypesContext;

class Commission
{
    public array $result = [];

    public const WITHDRAW_TYPE = 'withdraw';

    public const DEPOSIT_TYPE = 'deposit';

    private User $user;

    public function __construct()
    {
        $this->user = Container::getInstance()->get(User::class);
    }

    /**
     * @throws CommissionTypeNotExistsException
     */
    public function addData(
        string $date,
        int $userID,
        string $userType,
        string $operationType,
        string $operationAmount,
        string $operationCurrency,
    ): void {
        // Get user if exists
        $userKey = $this->user->find($userID);

        // Create user if not exists
        if (is_null($userKey)) {
            $this->user->users[] = [
                'user_id' => $userID,
                'user_type' => $userType,
                'deposits_count' => 0,
                'withdraws_count' => 0,
                'last_withdraw_date' => null,
                'last_deposit_date' => null,
                'withdrawals' => [],
                'deposits' => [],
            ];

            $userKey = $this->user->find($userID);
        }

        // Get decimals count
        $decimalsCount = mb_strlen(explode('.', $operationAmount)[1] ?? '');

        $dto = new CommissionDataDTO(
            userKey: $userKey,
            date: $date,
            operationAmount: $operationAmount,
            operationCurrency: $operationCurrency,
            userType: $userType,
            decimalsCount: $decimalsCount,
        );

        // Save operation
        if ($operationType === self::WITHDRAW_TYPE) {
            $this->addWithdrawal($dto);
        } elseif ($operationType === self::DEPOSIT_TYPE) {
            $this->addDeposit($dto);
        }
    }

    /**
     * @return void
     *
     * @throws CommissionTypeNotExistsException
     */
    public function addWithdrawal(CommissionDataDTO $dto)
    {
        // Save withdrawal
        $this->user->users[$dto->userKey]['withdrawals'][] = new Transaction(
            $dto->date,
            Transaction::TYPE_WITHDRAWAL,
            $dto->operationAmount,
            $dto->operationCurrency,
        );

        // Withdrawal check statements
        $context = $this->getTypeHandler(
            commissionType: self::WITHDRAW_TYPE,
            userType: $dto->userType,
        );

        $context->execute(
            userKey: $dto->userKey,
            commissionType: self::WITHDRAW_TYPE,
            amount: $dto->operationAmount,
            currency: $dto->operationCurrency,
            extra: [
                'date' => $dto->date,
                'decimals_count' => $dto->decimalsCount,
            ],
        );

        // Save last withdraw date
        $this->user->users[$dto->userKey]['last_withdraw_date'] = $dto->date;
    }

    /**
     * @return void
     *
     * @throws CommissionTypeNotExistsException
     */
    public function addDeposit(CommissionDataDTO $dto)
    {
        // Save deposit
        $this->user->users[$dto->userKey]['deposits'][] = new Transaction(
            $dto->date,
            Transaction::TYPE_DEPOSIT,
            $dto->operationAmount,
            $dto->operationCurrency,
        );

        // Deposit check statements
        $context = $this->getTypeHandler(
            commissionType: self::DEPOSIT_TYPE,
            userType: $dto->userType,
        );

        $context->execute(
            userKey: $dto->userKey,
            commissionType: self::DEPOSIT_TYPE,
            amount: $dto->operationAmount,
            currency: $dto->operationCurrency,
            extra: [
                'date' => $dto->date,
                'decimals_count' => $dto->decimalsCount,
            ],
        );

        // Save last deposit date
        $this->user->users[$dto->userKey]['last_deposit_date'] = $dto->date;
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

    /**
     * Get result.
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * Get result.
     */
    public function addResult(mixed $result): mixed
    {
        return $this->result[] = $result;
    }
}

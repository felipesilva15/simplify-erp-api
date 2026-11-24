<?php

namespace App\Modules\Finance\Enums;

/**
 * @OA\Schema(
 *   schema="PaymentTypeEnum",
 *   type="string",
 *   description="Activity actions: 1, 2, 3, 4, 5, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 90, 91, 99",
 *   enum={"1", "2", "3", "4", "5", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "90", "91", "99"}
 * )
 */
enum PaymentTypeEnum: string
{
    case Cash = '1';
    case Check = '2';
    case CreditCard = '3';
    case DebitCard = '4';
    case StoreCard = '5';
    case FoodVoucher = '10';
    case MealVoucher = '11';
    case GiftVoucher = '12';
    case FuelVoucher = '13';
    case TradeBill = '14';
    case BankSlip = '15';
    case BankDeposit = '16';
    case PixDinamyc = '17';
    case BankTransfer = '18';
    case LoayaltyCashback = '19';
    case PixStatic = '20';
    case StoreCredit = '21';
    case ElectronicNotInformed = '22';
    case NoPayment = '90';
    case DeferredPayment = '91';
    case Other = '99';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Dinheiro',
            self::Check => 'Cheque',
            self::CreditCard => 'Cartão de crédito',
            self::DebitCard => 'Cartão de débito',
            self::StoreCard => 'Cartão da loja',
            self::FoodVoucher => 'Vale alimentação',
            self::MealVoucher => 'Vale refeição',
            self::GiftVoucher => 'Vale presente',
            self::FuelVoucher => 'Vale combustível',
            self::TradeBill => 'Duplicata mercantil',
            self::BankSlip => 'Boleto bancário',
            self::BankDeposit => 'Depósito bancário',
            self::PixDinamyc => 'Pagamento Instantâneo (Pix) - Dinâmico',
            self::BankTransfer => 'Transferência bancária, Carteira digital',
            self::LoayaltyCashback => 'Programa de fidelidade, Cashback, Crédito virtual',
            self::PixStatic => 'Pagamento Instantâneo (Pix) - Estático',
            self::StoreCredit => 'Crédito em loja',
            self::ElectronicNotInformed => 'Pagamento eletrônico não informado - Falha de hardware do sistema emissor',
            self::NoPayment => 'Sem pagamento',
            self::DeferredPayment => 'Pagamento posterior',
            self::Other => 'Outros',
        };
    }
}

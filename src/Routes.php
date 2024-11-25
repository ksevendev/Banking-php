<?php

namespace Banking;

use Banking\Anonymous;

class Routes
{

    /**
     * @return \Banking\Anonymous
     */
    public static function manager()
    {
        $anonymous = new Anonymous();

        // Verificar conexão
        $anonymous->base = static function () {
            return '/';
        };

        // Conectar
        $anonymous->connect = static function () {
            return "connect";
        };

        // Desconectar
        $anonymous->disconnect = static function () {
            return "disconnect";
        };

        // Alterar status
        $anonymous->changeStatus = static function () {
            return "status";
        };

        return $anonymous;
    }

    /**
     * @return \Banking\Anonymous
     */
    public static function user()
    {
        $anonymous = new Anonymous();

        // Obter dados do usuario
        $anonymous->base = static function () {
            return 'user';
        };

        // Logar na conta do usuario
        $anonymous->auth = static function () {
            return "user/auth";
        };

        return $anonymous;
    }

    /**
     * @return \Banking\Anonymous
     */
    public static function finance()
    {
        $anonymous = new Anonymous();

        // Obter contas (P/Saque)
        $anonymous->base = static function () {
            return 'finance/accounts';
        };

        // Obter total de faturamento
        $anonymous->invoicing = static function () {
            return 'finance/invoicing';
        };

        // Obter total de saldo
        $anonymous->balance = static function () {
            return 'finance/balance';
        };

        // Sacar
        $anonymous->cashout = static function () {
            return 'finance/cashout';
        };

        return $anonymous;
    }

    /**
     * @return \Banking\Anonymous
     */
    public static function sell()
    {
        $anonymous = new Anonymous();

        // Obter vendas & vender
        $anonymous->base = static function () {
            return "orders";
        };

        // Obter venda
        $anonymous->getOrder = static function ($id) {
            return "orders/$id";
        };

        return $anonymous;
    }

}

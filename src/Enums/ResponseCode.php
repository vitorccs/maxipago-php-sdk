<?php

namespace Vitorccs\Maxipago\Enums;

enum ResponseCode: int
{
    // Sucesso (transação, cancelamento ou reembolso)
    case APPROVED = 0;

    // Negado
    case DENIED = 1;

    // Rejeitado por duplicidade ou fraude
    case REJECTED = 2;

    // Em revisão (análise manual de fraude)
    case AWAIT_REVIEW = 5;

    // Erro na operadora do cartão
    case CARD_ISSUER_ERROR = 1022;

    // Erro nos parâmetros enviados
    case PARAMETER_ERROR = 1024;

    // Erro nas credenciais
    case CREDENTIALS_ERROR = 1025;

    // Erro interno na Maxipago
    case INTERNAL_ERROR = 2048;

    // Timeout da adquirente
    case acquirer_timeout = 4097;
}

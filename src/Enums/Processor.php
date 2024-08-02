<?php

namespace Vitorccs\Maxipago\Enums;

enum Processor: int
{
    // for sandbox
    case SIMULATOR = 1;

    // Credit Card
    case REDE = 2;
    case GETNET = 3;
    case CIELO = 4;
    case TEF = 5;
    case ELAVON = 6;
    case CHASEPAYMENTECH = 8;
    case STONE = 9;

    // PIX
    case PIXITAU = 200;

    // Boleto
    case BOLETO_ITAU_V2 = 207;
    case BOLETO_BRADESCO = 12;
    case BOLETO_SANTANDER = 15;
}

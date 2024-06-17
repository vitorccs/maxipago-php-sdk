<?php

namespace Vitorccs\Maxipago\Enums;

enum Processor: int
{
    case TEST = 1;
    case REDE = 2;
    case GETNET = 3;
    case CIELO = 4;
    case TEF = 5;
    case ELAVON = 6;
    case CHASEPAYMENTECH = 8;
    case STONE = 9;
    case PIXITAU = 200;
}

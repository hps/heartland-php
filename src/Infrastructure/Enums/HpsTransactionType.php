<?php

abstract class HpsTransactionType
{
    const AUTHORIZE        = 1;
    const CAPTURE          = 2;
    const CHARGE           = 3;
    const REFUND           = 4;
    const REVERSE          = 5;
    const VERIFY           = 6;
    const LIST_TRANSACTION = 7;
    const GET              = 8;
    const VOID             = 9;
    const SECURITY_ERROR   = 10;
    const BATCH_CLOSE      = 11;
}

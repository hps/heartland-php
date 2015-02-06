<?php

class HpsTransactionType {
    static public $AUTHORIZE = 0;
    static public $CAPTURE = 1;
    static public $CHARGE = 2;
    static public $REFUND = 3;
    static public $REVERSE = 4;
    static public $VERIFY = 5;
    static public $LIST = 6;
    static public $GET = 7;
    static public $VOID = 8;
    static public $SECURITY_ERROR = 9;
    static public $BATCH_CLOSE = 10;
} 
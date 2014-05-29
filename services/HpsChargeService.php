<?php

class HpsChargeService extends HpsCreditService {
    function __construct(HpsConfiguration $config=null){
        error_log('HpsChargeService is Deprecated and will soon be removed.', E_USER_DEPRECATED);
        parent::__construct($config);
    }
} 
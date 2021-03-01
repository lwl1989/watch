<?php

namespace App\Exceptions;


class ErrorConstant
{
    const SYSTEM_ERR = 500000;
    const SYSTEM_ERR_PDO = 500001;
    const SYSTEM_ATTEMPT_MORE = 500002;
    const SYSTEM_EXCEEDS_LIMIT = 50003;
    const SYSTEM_TIMEOUT = 50004;

    const PARAMS_LOST = 500417;
    const PARAMS_ERROR = 500418;
    const DATA_ERR = 500404;

    const UN_AUTH_ERROR = 500401;

    const USER_MOBILE_EXITS = 500301;
    const USER_CARD_EXISTS = 500302;
    const USER_NUMBER_EXISTS = 500303;

    const USER_MOBILE_NOT_EXITS = 500311;

    const USER_ACCOUNT_OR_PASSWORD_ERROR = 500330;
    const USER_NO_HAS_PERMISSION = 500331;
    const USER_INPUT_VERIFY = 500332;
    const USER_ACCOUNT_DISABLED = 500334;
    const USER_ACCOUNT_DELETED = 500335;
    const USER_SHOP_DISABLED = 500336;
}
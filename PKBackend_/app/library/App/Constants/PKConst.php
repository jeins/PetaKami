<?php


namespace PetaKami\Constants;

use PhalconRest\Constants\Services as PRServices;

class PKConst extends PRServices
{
    const DB_GEO = 'db_geo';
    const DB_PK = 'db_pk';

    const RESPONSE_KEY = 'data';

    const CONFIG = 'config';

    const USER_SERVICE = 'userService';
    const API_SERVICE = 'apiService';
    const QUERY = 'query';
    const PHQL_QUERY_PARSER = 'phqlQueryParser';
    const URL_QUERY_PARSER = 'urlQueryParser';
}
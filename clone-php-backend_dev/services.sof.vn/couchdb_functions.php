<?php

if (!function_exists('lv_trim_value')) {
    function lv_trim_value($value)
    {
        if ($value === null) {
            return '';
        }
        return trim((string)$value);
    }
}

if (!function_exists('lv_get_couchdb_config')) {
    function lv_get_couchdb_config()
    {
        return array(
            'host' => defined('COUCHDB_HOST') ? COUCHDB_HOST : '192.168.1.20',
            'port' => defined('COUCHDB_PORT') ? COUCHDB_PORT : '5984',
            'user' => defined('COUCHDB_USER') ? COUCHDB_USER : 'admin',
            'pass' => defined('COUCHDB_PASS') ? COUCHDB_PASS : 'rootsof',
            'database' => defined('COUCHDB_DATABASE') ? COUCHDB_DATABASE : 'couchdb',
            'dispatcher_db' => defined('COUCHDB_DISPATCHER_DB') ? COUCHDB_DISPATCHER_DB : 'couchdb',
            'route_doc_prefix' => defined('COUCHDB_ROUTE_DOC_PREFIX') ? COUCHDB_ROUTE_DOC_PREFIX : 'dispatcher:prefix:',
            'user_table' => defined('COUCHDB_USER_TABLE') ? COUCHDB_USER_TABLE : 'lv_lv0066',
            'fallback_host' => defined('COUCHDB_FALLBACK_HOST') ? COUCHDB_FALLBACK_HOST : '',
            'fallback_port' => defined('COUCHDB_FALLBACK_PORT') ? COUCHDB_FALLBACK_PORT : (defined('COUCHDB_PORT') ? COUCHDB_PORT : '5984'),
            'fallback_user' => defined('COUCHDB_FALLBACK_USER') ? COUCHDB_FALLBACK_USER : (defined('COUCHDB_USER') ? COUCHDB_USER : 'admin'),
            'fallback_pass' => defined('COUCHDB_FALLBACK_PASS') ? COUCHDB_FALLBACK_PASS : (defined('COUCHDB_PASS') ? COUCHDB_PASS : 'rootsof'),
            'fallback_dispatcher_db' => defined('COUCHDB_FALLBACK_DISPATCHER_DB') ? COUCHDB_FALLBACK_DISPATCHER_DB : (defined('COUCHDB_DISPATCHER_DB') ? COUCHDB_DISPATCHER_DB : 'couchdb'),
        );
    }
}

if (!function_exists('makeCouchRequest')) {
    function makeCouchRequest($path, $method = 'GET', $data = null, $authUser = '', $authPass = '', $requestHost = '', $requestPort = '')
    {
        $cfg = lv_get_couchdb_config();

        $host = lv_trim_value($requestHost) !== '' ? lv_trim_value($requestHost) : lv_trim_value($cfg['host']);
        $port = lv_trim_value($requestPort) !== '' ? lv_trim_value($requestPort) : lv_trim_value($cfg['port']);
        $requestUser = lv_trim_value($authUser) !== '' ? lv_trim_value($authUser) : lv_trim_value($cfg['user']);
        $requestPass = lv_trim_value($authUser) !== '' ? (string)$authPass : (string)$cfg['pass'];

        $baseUrl = 'http://' . $host . ':' . $port;
        $url = preg_match('/^https?:\/\//i', $path) ? $path : $baseUrl . '/' . ltrim($path, '/');

        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $requestUser . ':' . $requestPass,
        );

        $method = strtoupper((string)$method);
        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            if ($data !== null) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        } elseif ($method === 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if ($data !== null) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        } elseif ($method !== 'GET') {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
            if ($data !== null) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = null;
        if ($response === false) {
            $error = curl_error($curl);
        }
        curl_close($curl);

        return array(
            'code' => $httpCode,
            'body' => $response,
            'error' => $error,
        );
    }
}

if (!function_exists('lv_same_couch_target')) {
    function lv_same_couch_target($hostA, $portA, $dbA, $hostB, $portB, $dbB)
    {
        $hostA = strtolower(lv_trim_value($hostA));
        $portA = lv_trim_value($portA);
        $dbA = strtolower(lv_trim_value($dbA));

        $hostB = strtolower(lv_trim_value($hostB));
        $portB = lv_trim_value($portB);
        $dbB = strtolower(lv_trim_value($dbB));

        return ($hostA !== '' && $hostA === $hostB && $portA === $portB && $dbA !== '' && $dbA === $dbB);
    }
}

if (!function_exists('lv_resolve_route_connection')) {
    function lv_resolve_route_connection($routeDoc, $defaultHost = '', $defaultPort = '')
    {
        $cfg = lv_get_couchdb_config();
        $routeDoc = is_array($routeDoc) ? $routeDoc : array();

        $host = lv_trim_value(isset($routeDoc['couchdb_host']) ? $routeDoc['couchdb_host'] : '');
        if ($host === '') {
            $host = lv_trim_value(isset($routeDoc['host']) ? $routeDoc['host'] : '');
        }
        if ($host === '') {
            $host = lv_trim_value($defaultHost);
        }
        if ($host === '') {
            $host = lv_trim_value($cfg['host']);
        }

        $port = lv_trim_value(isset($routeDoc['couchdb_port']) ? $routeDoc['couchdb_port'] : '');
        if ($port === '') {
            $port = lv_trim_value(isset($routeDoc['port']) ? $routeDoc['port'] : '');
        }
        if ($port === '') {
            $port = lv_trim_value($defaultPort);
        }
        if ($port === '') {
            $port = lv_trim_value($cfg['port']);
        }
        if ($port === '') {
            $port = '5984';
        }

        $fallbackHost = strtolower(lv_trim_value($cfg['fallback_host']));
        $useFallbackAuth = ($fallbackHost !== '' && strtolower($host) === $fallbackHost);

        $authUser = lv_trim_value(isset($routeDoc['couchdb_user']) ? $routeDoc['couchdb_user'] : '');
        $authPass = isset($routeDoc['couchdb_password']) ? (string)$routeDoc['couchdb_password'] : '';

        if ($authUser === '') {
            $authUser = $useFallbackAuth ? lv_trim_value($cfg['fallback_user']) : lv_trim_value($cfg['user']);
            $authPass = $useFallbackAuth ? (string)$cfg['fallback_pass'] : (string)$cfg['pass'];
        } elseif ($authPass === '') {
            $authPass = $useFallbackAuth ? (string)$cfg['fallback_pass'] : (string)$cfg['pass'];
        }

        if ($authUser === '') {
            $authUser = lv_trim_value($cfg['user']);
        }
        if ($authPass === '' && $authUser !== '') {
            $authPass = $useFallbackAuth ? (string)$cfg['fallback_pass'] : (string)$cfg['pass'];
        }

        return array(
            'host' => $host,
            'port' => $port,
            'auth_user' => $authUser,
            'auth_pass' => $authPass,
        );
    }
}

if (!function_exists('lv_collect_dispatch_routes_from')) {
    function lv_collect_dispatch_routes_from($host = '', $port = '', $dispatcherDb = '', $authUser = '', $authPass = '')
    {
        $cfg = lv_get_couchdb_config();

        $host = lv_trim_value($host) !== '' ? lv_trim_value($host) : lv_trim_value($cfg['host']);
        $port = lv_trim_value($port) !== '' ? lv_trim_value($port) : lv_trim_value($cfg['port']);
        $dispatcherDb = lv_trim_value($dispatcherDb) !== '' ? lv_trim_value($dispatcherDb) : lv_trim_value($cfg['dispatcher_db']);

        if ($dispatcherDb === '') {
            return array();
        }

        $authUser = lv_trim_value($authUser) !== '' ? lv_trim_value($authUser) : lv_trim_value($cfg['user']);
        if (lv_trim_value($authPass) === '') {
            $authPass = (string)$cfg['pass'];
        }

        $result = makeCouchRequest(
            $dispatcherDb . '/_all_docs?include_docs=true',
            'GET',
            null,
            $authUser,
            $authPass,
            $host,
            $port
        );
        if ((int)$result['code'] !== 200) {
            return array();
        }

        $response = json_decode($result['body'], true);
        if (!is_array($response) || !isset($response['rows']) || !is_array($response['rows'])) {
            return array();
        }

        $prefix = lv_trim_value($cfg['route_doc_prefix']);
        $routes = array();

        foreach ($response['rows'] as $row) {
            if (!isset($row['doc']) || !is_array($row['doc'])) {
                continue;
            }

            $doc = $row['doc'];
            $docId = isset($doc['_id']) ? lv_trim_value($doc['_id']) : '';
            if ($prefix !== '' && strpos($docId, $prefix) !== 0) {
                continue;
            }

            if (isset($doc['active']) && (string)$doc['active'] === '0') {
                continue;
            }

            $database = lv_trim_value(isset($doc['database']) ? $doc['database'] : '');
            if ($database === '') {
                continue;
            }

            $doc['__dispatch_host'] = $host;
            $doc['__dispatch_port'] = $port;
            $doc['__dispatch_auth_user'] = $authUser;
            $doc['__dispatch_auth_pass'] = $authPass;
            $routes[] = $doc;
        }

        return $routes;
    }
}

if (!function_exists('lv_collect_dispatch_routes')) {
    function lv_collect_dispatch_routes()
    {
        $cfg = lv_get_couchdb_config();

        $primaryHost = lv_trim_value($cfg['host']);
        $primaryPort = lv_trim_value($cfg['port']);
        $primaryDb = lv_trim_value($cfg['dispatcher_db']);

        $allRoutes = lv_collect_dispatch_routes_from(
            $primaryHost,
            $primaryPort,
            $primaryDb,
            lv_trim_value($cfg['user']),
            (string)$cfg['pass']
        );

        $fallbackHost = lv_trim_value($cfg['fallback_host']);
        $fallbackPort = lv_trim_value($cfg['fallback_port']);
        $fallbackDb = lv_trim_value($cfg['fallback_dispatcher_db']);

        if (
            $fallbackHost !== ''
            && !lv_same_couch_target($fallbackHost, $fallbackPort, $fallbackDb, $primaryHost, $primaryPort, $primaryDb)
        ) {
            $fallbackRoutes = lv_collect_dispatch_routes_from(
                $fallbackHost,
                $fallbackPort,
                $fallbackDb,
                lv_trim_value($cfg['fallback_user']),
                (string)$cfg['fallback_pass']
            );
            $allRoutes = array_merge($allRoutes, $fallbackRoutes);
        }

        $deduped = array();
        foreach ($allRoutes as $routeDoc) {
            $routeId = lv_trim_value(isset($routeDoc['_id']) ? $routeDoc['_id'] : '');
            $dispatchHost = lv_trim_value(isset($routeDoc['__dispatch_host']) ? $routeDoc['__dispatch_host'] : '');
            $dispatchPort = lv_trim_value(isset($routeDoc['__dispatch_port']) ? $routeDoc['__dispatch_port'] : '');
            $key = strtolower($dispatchHost) . '|' . $dispatchPort . '|' . $routeId;
            if ($key === '||') {
                $key = md5(json_encode($routeDoc));
            }
            if (!isset($deduped[$key])) {
                $deduped[$key] = $routeDoc;
            }
        }

        return array_values($deduped);
    }
}

if (!function_exists('lv_collect_database_candidates')) {
    function lv_collect_database_candidates(&$routeCandidates)
    {
        $cfg = lv_get_couchdb_config();
        $routeCandidates = array();
        $seen = array();

        $primaryDatabase = lv_trim_value($cfg['database']);
        $primaryHost = lv_trim_value($cfg['host']);
        $primaryPort = lv_trim_value($cfg['port']);

        if ($primaryDatabase !== '') {
            $key = strtolower($primaryHost) . '|' . $primaryPort . '|' . strtolower($primaryDatabase);
            $seen[$key] = true;
            $routeCandidates[] = array(
                'database' => $primaryDatabase,
                'route' => array(),
                'host' => $primaryHost,
                'port' => $primaryPort,
                'auth_user' => lv_trim_value($cfg['user']),
                'auth_pass' => (string)$cfg['pass'],
            );
        }

        $fallbackHost = lv_trim_value($cfg['fallback_host']);
        $fallbackPort = lv_trim_value($cfg['fallback_port']);
        if (
            $fallbackHost !== ''
            && $primaryDatabase !== ''
            && !lv_same_couch_target($fallbackHost, $fallbackPort, $primaryDatabase, $primaryHost, $primaryPort, $primaryDatabase)
        ) {
            $key = strtolower($fallbackHost) . '|' . $fallbackPort . '|' . strtolower($primaryDatabase);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $routeCandidates[] = array(
                    'database' => $primaryDatabase,
                    'route' => array(),
                    'host' => $fallbackHost,
                    'port' => $fallbackPort,
                    'auth_user' => lv_trim_value($cfg['fallback_user']),
                    'auth_pass' => (string)$cfg['fallback_pass'],
                );
            }
        }

        $routes = lv_collect_dispatch_routes();
        foreach ($routes as $routeDoc) {
            $database = lv_trim_value(isset($routeDoc['database']) ? $routeDoc['database'] : '');
            if ($database === '') {
                continue;
            }

            $dispatchHost = lv_trim_value(isset($routeDoc['__dispatch_host']) ? $routeDoc['__dispatch_host'] : '');
            $dispatchPort = lv_trim_value(isset($routeDoc['__dispatch_port']) ? $routeDoc['__dispatch_port'] : '');
            $connection = lv_resolve_route_connection($routeDoc, $dispatchHost, $dispatchPort);

            $host = lv_trim_value(isset($connection['host']) ? $connection['host'] : '');
            $port = lv_trim_value(isset($connection['port']) ? $connection['port'] : '');
            $authUser = lv_trim_value(isset($connection['auth_user']) ? $connection['auth_user'] : '');
            $authPass = isset($connection['auth_pass']) ? (string)$connection['auth_pass'] : '';

            if ($host === '') {
                $host = $primaryHost;
            }
            if ($port === '') {
                $port = $primaryPort;
            }

            $key = strtolower($host) . '|' . $port . '|' . strtolower($database);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $routeCandidates[] = array(
                'database' => $database,
                'route' => $routeDoc,
                'host' => $host,
                'port' => $port,
                'auth_user' => $authUser,
                'auth_pass' => $authPass,
            );
        }

        return $routeCandidates;
    }
}

if (!function_exists('lv_build_token_result')) {
    function lv_build_token_result($userData, $token, $routedDatabase = '', $routeDoc = array())
    {
        $cfg = lv_get_couchdb_config();
        $userTable = lv_trim_value(isset($routeDoc['user_table']) ? $routeDoc['user_table'] : '');
        if ($userTable === '') {
            $userTable = $cfg['user_table'];
        }
        $prefix = $userTable . ':';

        $docId = isset($userData['_id']) ? lv_trim_value($userData['_id']) : '';
        $username = $docId;
        if ($docId !== '' && strpos($docId, $prefix) === 0) {
            $username = substr($docId, strlen($prefix));
        } elseif (isset($userData['lv001'])) {
            $username = lv_trim_value($userData['lv001']);
        }

        $deviceType = '';
        if (isset($userData['lv097']) && lv_trim_value($userData['lv097']) !== '' && lv_trim_value($userData['lv097']) === $token) {
            $deviceType = 'web';
        } elseif (isset($userData['lv297']) && lv_trim_value($userData['lv297']) !== '' && lv_trim_value($userData['lv297']) === $token) {
            $deviceType = 'mobile';
        } elseif (isset($userData['lv397']) && lv_trim_value($userData['lv397']) !== '' && lv_trim_value($userData['lv397']) === $token) {
            $deviceType = 'desktop';
        } elseif (isset($userData['lv497']) && lv_trim_value($userData['lv497']) !== '' && lv_trim_value($userData['lv497']) === $token) {
            $deviceType = 'chamcongdes';
        } elseif (isset($userData['lv597']) && lv_trim_value($userData['lv597']) !== '' && lv_trim_value($userData['lv597']) === $token) {
            $deviceType = 'chamcongapp';
        }

        $systemName = '';
        if (isset($routeDoc['system_name'])) {
            $systemName = lv_trim_value($routeDoc['system_name']);
        }
        if ($systemName === '' && isset($routeDoc['service_name'])) {
            $systemName = lv_trim_value($routeDoc['service_name']);
        }
        if ($systemName === '' && isset($routeDoc['note'])) {
            $systemName = lv_trim_value($routeDoc['note']);
        }

        $welcomeMessage = isset($routeDoc['welcome_message']) ? lv_trim_value($routeDoc['welcome_message']) : '';
        if ($welcomeMessage === '' && $systemName !== '') {
            $welcomeMessage = 'Xin chao, day la he thong cham cong ' . $systemName;
        }

        return array(
            'success' => true,
            'username' => $username,
            'deviceType' => $deviceType,
            'userData' => $userData,
            'routed_database' => lv_trim_value($routedDatabase),
            'route' => $routeDoc,
            'system_name' => $systemName,
            'welcome_message' => $welcomeMessage,
        );
    }
}

if (!function_exists('lv_find_token_in_database_fallback')) {
    function lv_find_token_in_database_fallback($database, $vToken, $routeDoc = array(), $host = '', $port = '', $authUser = '', $authPass = '')
    {
        $vToken = lv_trim_value($vToken);
        $database = lv_trim_value($database);
        if ($database === '' || $vToken === '') {
            return array('success' => false, 'message' => 'Token not found');
        }

        $cfg = lv_get_couchdb_config();

        $host = lv_trim_value($host) !== '' ? lv_trim_value($host) : lv_trim_value($cfg['host']);
        $port = lv_trim_value($port) !== '' ? lv_trim_value($port) : lv_trim_value($cfg['port']);
        $authUser = lv_trim_value($authUser) !== '' ? lv_trim_value($authUser) : lv_trim_value($cfg['user']);
        if ($authPass === '' && $authUser !== '') {
            $authPass = (string)$cfg['pass'];
        }

        $userTable = lv_trim_value(isset($routeDoc['user_table']) ? $routeDoc['user_table'] : '');
        if ($userTable === '') {
            $userTable = $cfg['user_table'];
        }
        $prefix = $userTable . ':';

        $result = makeCouchRequest(
            $database . '/_all_docs?include_docs=true',
            'GET',
            null,
            $authUser,
            $authPass,
            $host,
            $port
        );
        if ((int)$result['code'] !== 200) {
            return array('success' => false, 'message' => 'Database error');
        }

        $response = json_decode($result['body'], true);
        if (!is_array($response) || !isset($response['rows']) || !is_array($response['rows'])) {
            return array('success' => false, 'message' => 'Token not found');
        }

        foreach ($response['rows'] as $row) {
            if (!isset($row['doc']) || !is_array($row['doc'])) {
                continue;
            }

            $userData = $row['doc'];
            $docId = isset($userData['_id']) ? lv_trim_value($userData['_id']) : '';
            if ($docId !== '' && strpos($docId, $prefix) !== 0) {
                continue;
            }

            $webToken = isset($userData['lv097']) ? lv_trim_value($userData['lv097']) : '';
            $mobileToken = isset($userData['lv297']) ? lv_trim_value($userData['lv297']) : '';
            $desktopToken = isset($userData['lv397']) ? lv_trim_value($userData['lv397']) : '';
            $desktopChamCongToken = isset($userData['lv497']) ? lv_trim_value($userData['lv497']) : '';
            $mobileChamCongToken = isset($userData['lv597']) ? lv_trim_value($userData['lv597']) : '';

            if (
                ($webToken !== '' && $webToken === $vToken)
                || ($mobileToken !== '' && $mobileToken === $vToken)
                || ($desktopToken !== '' && $desktopToken === $vToken)
                || ($desktopChamCongToken !== '' && $desktopChamCongToken === $vToken)
                || ($mobileChamCongToken !== '' && $mobileChamCongToken === $vToken)
            ) {
                return lv_build_token_result($userData, $vToken, $database, $routeDoc);
            }
        }

        return array('success' => false, 'message' => 'Token not found');
    }
}

if (!function_exists('lv_find_token_in_database')) {
    function lv_find_token_in_database($database, $vToken, $routeDoc = array(), $host = '', $port = '', $authUser = '', $authPass = '')
    {
        $vToken = lv_trim_value($vToken);
        $database = lv_trim_value($database);
        if ($database === '' || $vToken === '') {
            return array('success' => false, 'message' => 'Token not found');
        }

        $cfg = lv_get_couchdb_config();

        $host = lv_trim_value($host) !== '' ? lv_trim_value($host) : lv_trim_value($cfg['host']);
        $port = lv_trim_value($port) !== '' ? lv_trim_value($port) : lv_trim_value($cfg['port']);
        $authUser = lv_trim_value($authUser) !== '' ? lv_trim_value($authUser) : lv_trim_value($cfg['user']);
        if ($authPass === '' && $authUser !== '') {
            $authPass = (string)$cfg['pass'];
        }

        $query = array(
            'selector' => array(
                '$or' => array(
                    array('lv097' => $vToken),
                    array('lv297' => $vToken),
                    array('lv397' => $vToken),
                    array('lv497' => $vToken),
                    array('lv597' => $vToken),
                )
            ),
            'limit' => 1,
        );

        $result = makeCouchRequest(
            $database . '/_find',
            'POST',
            $query,
            $authUser,
            $authPass,
            $host,
            $port
        );
        if ((int)$result['code'] === 200) {
            $response = json_decode($result['body'], true);
            if (is_array($response) && isset($response['docs']) && is_array($response['docs']) && count($response['docs']) > 0) {
                return lv_build_token_result($response['docs'][0], $vToken, $database, $routeDoc);
            }
        }

        return lv_find_token_in_database_fallback(
            $database,
            $vToken,
            $routeDoc,
            $host,
            $port,
            $authUser,
            $authPass
        );
    }
}

if (!function_exists('findUserByToken')) {
    function findUserByToken($vToken)
    {
        $vToken = lv_trim_value($vToken);
        if ($vToken === '') {
            return array('success' => false, 'message' => 'Token empty');
        }

        $candidates = array();
        $entries = lv_collect_database_candidates($candidates);
        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $database = lv_trim_value(isset($entry['database']) ? $entry['database'] : '');
            if ($database === '') {
                continue;
            }

            $routeDoc = isset($entry['route']) && is_array($entry['route']) ? $entry['route'] : array();
            $host = lv_trim_value(isset($entry['host']) ? $entry['host'] : '');
            $port = lv_trim_value(isset($entry['port']) ? $entry['port'] : '');
            $authUser = lv_trim_value(isset($entry['auth_user']) ? $entry['auth_user'] : '');
            $authPass = isset($entry['auth_pass']) ? (string)$entry['auth_pass'] : '';

            $result = lv_find_token_in_database($database, $vToken, $routeDoc, $host, $port, $authUser, $authPass);
            if (is_array($result) && !empty($result['success'])) {
                return $result;
            }
        }

        return array('success' => false, 'message' => 'Token not found');
    }
}

if (!function_exists('findUserByTokenFallback')) {
    function findUserByTokenFallback($vToken)
    {
        $vToken = lv_trim_value($vToken);
        if ($vToken === '') {
            return array('success' => false, 'message' => 'Token empty');
        }

        $candidates = array();
        $entries = lv_collect_database_candidates($candidates);
        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $database = lv_trim_value(isset($entry['database']) ? $entry['database'] : '');
            if ($database === '') {
                continue;
            }

            $routeDoc = isset($entry['route']) && is_array($entry['route']) ? $entry['route'] : array();
            $host = lv_trim_value(isset($entry['host']) ? $entry['host'] : '');
            $port = lv_trim_value(isset($entry['port']) ? $entry['port'] : '');
            $authUser = lv_trim_value(isset($entry['auth_user']) ? $entry['auth_user'] : '');
            $authPass = isset($entry['auth_pass']) ? (string)$entry['auth_pass'] : '';

            $result = lv_find_token_in_database_fallback($database, $vToken, $routeDoc, $host, $port, $authUser, $authPass);
            if (is_array($result) && !empty($result['success'])) {
                return $result;
            }
        }

        return array('success' => false, 'message' => 'Token not found');
    }
}

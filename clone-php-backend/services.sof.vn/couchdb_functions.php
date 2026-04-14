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
            'database' => defined('COUCHDB_DATABASE') ? COUCHDB_DATABASE : 'couchdb20',
            'user_table' => defined('COUCHDB_USER_TABLE') ? COUCHDB_USER_TABLE : 'lv_lv0066',
        );
    }
}

if (!function_exists('makeCouchRequest')) {
    function makeCouchRequest($path, $method = 'GET', $data = null)
    {
        $cfg = lv_get_couchdb_config();
        $baseUrl = 'http://' . $cfg['host'] . ':' . $cfg['port'];
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
            CURLOPT_USERPWD => $cfg['user'] . ':' . $cfg['pass'],
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

if (!function_exists('lv_build_token_result')) {
    function lv_build_token_result($userData, $token)
    {
        $cfg = lv_get_couchdb_config();
        $prefix = $cfg['user_table'] . ':';

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
        }

        return array(
            'success' => true,
            'username' => $username,
            'deviceType' => $deviceType,
            'userData' => $userData,
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

        $cfg = lv_get_couchdb_config();
        $query = array(
            'selector' => array(
                '$or' => array(
                    array('lv097' => $vToken),
                    array('lv297' => $vToken),
                    array('lv397' => $vToken),
                )
            ),
            'limit' => 1,
        );

        $result = makeCouchRequest($cfg['database'] . '/_find', 'POST', $query);
        if ((int)$result['code'] !== 200) {
            return findUserByTokenFallback($vToken);
        }

        $response = json_decode($result['body'], true);
        if (is_array($response) && isset($response['docs']) && is_array($response['docs']) && count($response['docs']) > 0) {
            return lv_build_token_result($response['docs'][0], $vToken);
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

        $cfg = lv_get_couchdb_config();
        $result = makeCouchRequest($cfg['database'] . '/_all_docs?include_docs=true', 'GET');

        if ((int)$result['code'] !== 200) {
            return array('success' => false, 'message' => 'Database error');
        }

        $response = json_decode($result['body'], true);
        if (!is_array($response) || !isset($response['rows']) || !is_array($response['rows'])) {
            return array('success' => false, 'message' => 'Token not found');
        }

        $prefix = $cfg['user_table'] . ':';
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

            if (
                ($webToken !== '' && $webToken === $vToken) ||
                ($mobileToken !== '' && $mobileToken === $vToken) ||
                ($desktopToken !== '' && $desktopToken === $vToken)
            ) {
                return lv_build_token_result($userData, $vToken);
            }
        }

        return array('success' => false, 'message' => 'Token not found');
    }
}

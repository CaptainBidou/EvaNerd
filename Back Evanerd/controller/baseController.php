<?php

function getAuthHeader() {
    return valider("HTTP_AUTHTOKEN" , "SERVER");
}

/**
 * Permet de récupère la chaine de requête envoyer par le client
 * @return array
 */
function getRequestParams() {
    parse_str($_SERVER['QUERY_STRING'], $query);
    return $query;
}

/**
 * Fonction qui permet de séparer la route de l'api
 * @return array
 */
function getUriSegments()
{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode('/', $uri);
    return $uri;
}

/**
 * Permet d'envoyer une réponse api
 * @param mixed $data un tableau associatif
 * @param array $httpHeaders Tableau contenant des entêtes de reponse HTTP
 */
function sendResponse($data, $httpHeaders = array())
{
    header_remove('Set-Cookie');
    if (is_array($httpHeaders) && count($httpHeaders)) {
        foreach ($httpHeaders as $httpHeader) {
            header($httpHeader);
        }
    }
    echo $data;
    exit;
}

/**
 * Renvoie l'entête de reponse correspond au status passé en paramètre
 * @param int $status
 * @Note Par defaut cette fonction renvoie le status 200
 */
function getStatusHeaders($status = 200) {
    switch($status) {
        case 200: return "HTTP/1.0 200 OK";
        case 201: return "HTTP/1.0 201 Created";
        case 202: return "HTTP/1.0 202 Accepted";
        case 204: return "HTTP/1.0 204 No Content";
        case 400: return "HTTP/1.0 400 Bad Request";
        case 401: return "HTTP/1.0 401 Unauthorized";
        case 403: return "HTTP/1.0 403 Forbidden";
        case 404: return "HTTP/1.0 404 Not Found";
        default: return "HTTP/1.0 200 OK";
            
    }
}

?>
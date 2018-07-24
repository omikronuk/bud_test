<?php
/**
 * Created by PhpStorm.
 * User: kwaku
 * Date: 23/07/2018
 * Time: 16:31
 */

namespace App\Models;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;

class DeathStarClient
{
    /**
     * @var string
     */
    private $client_secret;

    /**
     * @var string
     */
    private $client_id;

    /**
     * @var string
     */
    public $base_url;

    /**
     * @var string
     */
    public $endpoint;

    /**
     * @var string
     */
    public $client;

    /**
     * @var string
     */
    public $access_token;

    /**
     * @var string
     */
    public $mock_server;

    /**
     * @var boolean
     */
    public $raw_response;


    function __construct()
    {
        $this->client_secret = DS_CLIENT_SECRET;
        $this->client_id = DS_CLIENT_ID;
        $this->base_url = DS_BASE_URL;
        $this->client_secret = DS_CLIENT_SECRET;
        $this->raw_response = false;

        $this->client = new Client(['base_uri' => $this->base_url]);
    }

    public function setAccessToken() {
        $response = $this->getToken();

        if ( property_exists($response, 'access_token')) {
            $this->access_token = $response->access_token;
            $_SESSION['access_token'] = $this->access_token;
        }
    }

    /**
     * Get a new token from the external server
     */
    public function getToken()
    {
        $response = false;

        try {
            $response = $this->client->request('POST', '/token', [
                'form_params' => [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'grant_type' => 'client_credentials'
                ],
                'cert' => [SSL_CERT],
                'ssl_key' => [CERT_KEY],
                'handler' => $this->mock_server,
                'timeout' => TIMEOUT
            ]);

            return $this->processResponse($response);

        } catch (RequestException $ex) {
            error_log(sprintf("====== Message: %s ======", $ex->getMessage()));
            error_log(sprintf("====== Request: %s ======", $ex->getRequest()->getMethod()));
        }

        return $response;
    }

    /**
     * @param int $reactorId
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteReactor($reactorId)
    {
        $response = false;
        $endpoint = sprintf('/%s/%s',DS_REACTOR_URL,$reactorId);

        if (!filter_var($reactorId)) {
            trigger_error("An invalid ID was supplied", E_USER_ERROR);
        }

        try {
            $response = $this->client->request('DELETE', $endpoint, [
                'header' => [
                    'Authorization' => ['Bearer '.$_SESSION['access_token']],
                    'x-torpedoes' => 2
                ],
                'json'=>[],
                'cert' => [SSL_CERT],
                'ssl_key' => [CERT_KEY],
                'handler' => $this->mock_server
            ]);

            return $this->processResponse($response);

        } catch (RequestException $ex) {
            error_log(sprintf("====== Message: %s ======", $ex->getMessage()));
            error_log(sprintf("====== Request: %s ======", $ex->getRequest()->getMethod()));
        }

        return $response;
    }

    /**
     *
     * @param string $prisonerName
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface - the prisoner object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPrisoner($prisonerName)
    {
        $response = false;
        $endpoint = sprintf('/%s/%s',DS_PRISONER_URL,$prisonerName);

        if (!filter_var($prisonerName, FILTER_SANITIZE_STRING)) {
            trigger_error("An invalid name was supplied", E_USER_ERROR);
        }

        try {
            $response = $this->client->request('GET', $endpoint, [
                'header' => [
                    'Authorization' => ['Bearer '.$_SESSION['access_token']]
                ],
                'json'=>[],
                'cert' => [SSL_CERT],
                'ssl_key' => [CERT_KEY],
                'handler' => $this->mock_server,
                'timeout' => TIMEOUT
            ]);

            return $this->processResponse($response);

        } catch (RequestException $ex) {
            error_log(sprintf("====== Message: %s ======", $ex->getMessage()));
            error_log(sprintf("====== Request: %s ======", $ex->getRequest()->getMethod()));
        }

        return $response;
    }

    /**
     * Handle API responses as a Guzzlehttp response
     * @param $response
     * @return mixed
     */
    private function processResponse($response) {
        error_log(
            sprintf("======= Connection Response status: %s | %s ====== ",$response->getStatusCode(), $response->getReasonPhrase())
        );

        return !$this->raw_response ? json_decode($response->getBody()) : $response;
    }

}
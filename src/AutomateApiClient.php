<?php

namespace Phizzl\Browserstack\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class AutomateApiClient
{
    /**
     * @var string
     */
    public static $apiEndpoint = "https://www.browserstack.com/automate/";

    /**
     * @var string
     */
    private $username;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $key;

    public function __construct()
    {
        $this->username = "";
        $this->key = "";
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        if($this->client === null) {
            $this->client = new Client([
                "base_uri" => static::$apiEndpoint,
                "auth" => [$this->username, $this->key],
                "curl" => [
                    CURLOPT_SSL_VERIFYPEER => false
                ]
            ]);
        }

        return $this->client;
    }

    /**
     * @param Response $response
     * @return array
     * @throws ApiException
     */
    private function parseJsonResponse(Response $response)
    {
        if($response->getStatusCode() > 500){
            throw new ApiException("Invalid response from REST API");
        }

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $parameter
     * @return array
     */
    private function send($method, $uri, array $parameter = [])
    {
        $response = $this->getClient()->request($method, $uri, ["json" => $parameter]);
        return $this->parseJsonResponse($response);
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->send("GET", "plan.json");
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->send("GET", "projects.json");
    }

    /**
     * @return array
     */
    public function getProject($id)
    {
        return $this->send("GET", "projects/{$id}.json");
    }

    /**
     * @param int $id
     * @return array
     */
    public function deleteProject($id)
    {
        return $this->send("DELETE", "projects/{$id}.json");
    }

    /**
     * @return array
     */
    public function getBuilds()
    {
        return $this->send("GET", "builds.json");
    }

    /**
     * @param int $id
     * @return array
     */
    public function deleteBuild($id)
    {
        return $this->send("DELETE", "builds/{$id}.json");
    }

    /**
     * @param string $buildId
     * @return array
     */
    public function getSessions($buildId)
    {
        return $this->send("GET", "builds/{$buildId}/sessions.json");
    }

    /**
     * @param string $sessionId
     * @return array
     */
    public function getSession($sessionId)
    {
        return $this->send("GET", "sessions/{$sessionId}.json");
    }

    public function markSessionFailed($sessionId, $reason = "")
    {
        return $this->send("PUT", "sessions/{$sessionId}.json", ["status" => "failed", "reason" => $reason]);
    }

    public function markSessionPassed($sessionId)
    {
        return $this->send("PUT", "sessions/{$sessionId}.json", ["status" => "passed"]);
    }

    public function deleteSession($sessionId)
    {
        return $this->send("DELETE", "sessions/{$sessionId}.json");
    }
}
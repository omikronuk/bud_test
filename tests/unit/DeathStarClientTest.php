<?php
/**
 * Created by PhpStorm.
 * User: kwaku
 */

namespace App\Models;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class DeathStarClientTest extends \PHPUnit\Framework\TestCase
{

    public function testSetAccessTokenFail()
    {
        $client = new \App\Models\DeathStarClient;
        // API request simulation
        $mock = new MockHandler([
            new Response(200,[], json_encode([
                'access_token' => "e31a726c4b90462ccb7619e1b51f3d0068bf8006",
                'expires_in' => 99999999999,
                'token_type' => "Bearer",
                'scope' => "TheForce"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('POST', ''))
        ]);

        $client->mock_server = HandlerStack::create($mock);

        $this->assertEquals($client->access_token, 'e31a726c4b90462ccb7619e1b51f3d0068bf8006');
    }


    public function testSetAccessTokenSuccess()
    {
        $client = new \App\Models\DeathStarClient;
        // API request simulation
        $mock = new MockHandler([
            new Response(200,[], json_encode([
                'access_token' => "e31a726c4b90462ccb7619e1b51f3d0068bf8006",
                'expires_in' => 99999999999,
                'token_type' => "Bearer",
                'scope' => "TheForce"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('POST', ''))
        ]);

        $client->mock_server = HandlerStack::create($mock);
        $client->setAccessToken();

        $this->assertEquals($client->access_token, 'e31a726c4b90462ccb7619e1b51f3d0068bf8006');
    }

    public function testGetTokenSuccess()
    {
        $client = new \App\Models\DeathStarClient;
        $mock = new MockHandler([
            new Response(200,[], json_encode([
                'access_token' => "e31a726c4b90462ccb7619e1b51f3d0068bf8006",
                'expires_in' => 99999999999,
                'token_type' => "Bearer",
                'scope' => "TheForce"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('POST', 'test'))
        ]);

        $client->mock_server = HandlerStack::create($mock);

        $this->assertObjectHasAttribute('access_token', $client->getToken());

    }


    public function testGetPrisonerSuccess()
    {
        $_SESSION['access_token'] = 'e31a726c4b90462ccb7619e1b51f3d0068bf8006';
        $client = new \App\Models\DeathStarClient;
        $endpoint = sprintf('/%s/leia',DS_PRISONER_URL);
        $mock = new MockHandler([
            new Response(200,[
                'Authorization' => ['Bearer e31a726c4b90462ccb7619e1b51f3d0068bf8006']
            ], json_encode([
                    'cell' => "01000011 01100101 01101100 01101100 00100000 00110010 00110001 00111000 00110111",
                    'block' => "01000100 01100101 01110100 01100101 01101110 01110100 01101001 01101111 0110111000100000 01000010 01101100 01101111 01100011 01101011 00100000 01000001 01000001 00101101 00110010 00110011 00101100"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('GET', $endpoint))
        ]);

        $client->mock_server = HandlerStack::create($mock);

        $this->assertObjectHasAttribute('cell', $client->getPrisoner('leia'));
    }


    public function testEmptyParamOnGetPrisoner()
    {
        $_SESSION['access_token'] = 'e31a726c4b90462ccb7619e1b51f3d0068bf8006';
        $client = new \App\Models\DeathStarClient;
        $endpoint = sprintf('/%s/leia',DS_PRISONER_URL);
        $mock = new MockHandler([
            new Response(200,[
                'Authorization' => ['Bearer e31a726c4b90462ccb7619e1b51f3d0068bf8006']
            ], json_encode([
                    'cell' => "01000011 01100101 01101100 01101100 00100000 00110010 00110001 00111000 00110111",
                    'block' => "01000100 01100101 01110100 01100101 01101110 01110100 01101001 01101111 0110111000100000 01000010 01101100 01101111 01100011 01101011 00100000 01000001 01000001 00101101 00110010 00110011 00101100"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('GET', $endpoint))
        ]);

        $client->mock_server = HandlerStack::create($mock);
        $expected = $client->getPrisoner('');

        $this->assertObjectHasAttribute('cell', $expected);
    }

    public function testAuthorisationHeaderExistOnGetPrisoner()
    {
        $_SESSION['access_token'] = 'e31a726c4b90462ccb7619e1b51f3d0068bf8006';
        $client = new \App\Models\DeathStarClient;
        $endpoint = sprintf('/%s/leia',DS_PRISONER_URL);
        $mock = new MockHandler([
            new Response(200,[
                'Authorization' => ['Bearer e31a726c4b90462ccb7619e1b51f3d0068bf8006']
            ], json_encode([
                    'cell' => "01000011 01100101 01101100 01101100 00100000 00110010 00110001 00111000 00110111",
                    'block' => "01000100 01100101 01110100 01100101 01101110 01110100 01101001 01101111 0110111000100000 01000010 01101100 01101111 01100011 01101011 00100000 01000001 01000001 00101101 00110010 00110011 00101100"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('GET', $endpoint))
        ]);

        $client->mock_server = HandlerStack::create($mock);
        $client->raw_response = true;
        $expected = $client->getPrisoner('leia');

        $this->assertTrue($expected->hasHeader('Authorization'));
    }


    public function testAccessTokenPresenceCheckOnDeleteReactor()
    {

        $client = new \App\Models\DeathStarClient;
        $endpoint = sprintf('/%s/leia',DS_REACTOR_URL);

        $mock = new MockHandler([
            new Response(200,[
                'Authorization' => ['Bearer e31a726c4b90462ccb7619e1b51f3d0068bf8006'],
                'x-torpedoes' => 2
            ], json_encode([
                'id' => "1"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('GET', $endpoint))
        ]);

        $client->mock_server = HandlerStack::create($mock);
        $expected = $client->deleteReactor(1);

        $this->assertEquals('e31a726c4b90462ccb7619e1b51f3d0068bf8006', $client->access_token);
    }

    public function testDeleteReactor()
    {
        $_SESSION['access_token'] = 'e31a726c4b90462ccb7619e1b51f3d0068bf8006';
        $endpoint = sprintf('/%s/leia',DS_REACTOR_URL);

        $mock = new MockHandler([
            new Response(200,[
                'Authorization' => ['Bearer e31a726c4b90462ccb7619e1b51f3d0068bf8006'],
                'x-torpedoes' => 2
            ], json_encode([
                'id' => "1"
            ])),
            new Response(202, ['Content-Length' => 0]),
            new RequestException("Error Communicating with Server", new Request('GET', $endpoint))
        ]);

        $client = new \App\Models\DeathStarClient;
        $client->mock_server = HandlerStack::create($mock);

        $expected = $client->deleteReactor(1);
        $this->assertEquals('1', $expected->id);
    }

}

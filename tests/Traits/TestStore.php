<?php
declare(strict_types=1);

namespace Tests\Traits;


use Illuminate\Foundation\Testing\TestResponse;

trait TestStore
{

    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    public function assertStore(array $sendData, array $testDatabase, array $testJson = null): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json('POST', $this->routeStore(), $sendData);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Response status code must be 201, given ' . $response->getStatusCode());
        }

        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContain($response, $testDatabase, $testJson);

        return $response;
    }

    public function assertUpdate(array $sendData, array $testDatabase, array $testJson = null): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json('PUT', $this->routeStore(), $sendData);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Response status code must be 200, given ' . $response->getStatusCode());
        }

        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContain($response, $testDatabase, $testJson);

        return $response;
    }


    public function assertInDatabase(TestResponse $response, array $testDatabase)
    {
        $model = $this->model();
        $table = (new $model)->getTable();

        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json('id')]);
    }

    public function assertJsonResponseContain(TestResponse $response, array $testDatabase, array $testJsonData = null)
    {
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
    }


}
<?php
declare(strict_types=1);

namespace Tests\Traits;


use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations
{
    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected $route;

    protected function assertInvalidationStoreAction
    (
        array $data,
        string $rule,
        $ruleParams = []
    )
    {
        $response = $this->json('POST', $this->routeStore(), $data);
        $fields = array_keys($data);

        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);

    }

    protected function assertInvalidationUpdateAction
    (
        array $data,
        string $rule,
        $ruleParams = []
    )
    {

        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $fields = array_keys($data);

        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);

    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        String $rule,
        array $rulesParams = []

    )
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

           foreach ($fields as $field) {
                $fieldName = str_replace('_', ' ', $field);

               $response->assertJsonFragment([
                   \Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $rulesParams)
               ]);
           }

    }
}
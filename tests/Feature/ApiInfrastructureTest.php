<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiInfrastructureTest extends TestCase
{
    public function test_protected_api_routes_return_json_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/v1/produtos');

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Nao autenticado.',
            ]);
    }

    public function test_pre_vendas_api_routes_are_protected(): void
    {
        $this->getJson('/api/v1/pre-vendas')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Nao autenticado.',
            ]);
    }

    public function test_missing_api_resource_returns_json_404(): void
    {
        $response = $this->getJson('/api/v1/rota-inexistente');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Recurso nao encontrado.',
            ]);
    }

    public function test_swagger_documentation_is_available(): void
    {
        $this->get('/api/documentation')->assertOk();
        $this->get('/docs/openapi.yaml')
            ->assertOk()
            ->assertSee('openapi: 3.0.3', false)
            ->assertSee('speedNFE API', false);
    }
}

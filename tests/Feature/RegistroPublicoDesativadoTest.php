<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * O antigo POST /register era público e gravava o cargo enviado na requisição (inclusive master).
 * Enquanto o cadastro autônomo não existir (docs/cadastro-autonomo-clientes.md), a rota deve ficar fora.
 */
class RegistroPublicoDesativadoTest extends TestCase
{
    public function test_rota_de_registro_nao_existe()
    {
        $this->assertFalse(Route::has('register'));
    }

    public function test_get_e_post_register_retornam_404()
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'Invasor',
            'email' => 'invasor@example.com',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
            'cargo' => 'master',
        ])->assertNotFound();
    }
}

<?php

namespace App\Services;

use App\Models\Servico;

class ServicosService
{
    public function todos($empresaId)
    {
        if ($empresaId == 1) {
            $empresaId = '%';
        }

        return Servico::where('empresa_id', 'like', $empresaId)->orderBy('descricao')->get();
    }

    public function store(array $data)
    {
        return Servico::create($data);
    }

    public function update($id, array $data)
    {
        $servico = Servico::findOrFail($id);
        $servico->update($data);

        return $servico;
    }

    public function destroy($id)
    {
        return Servico::findOrFail($id)->delete();
    }
}

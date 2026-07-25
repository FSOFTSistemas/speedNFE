<?php

namespace App\Http\Controllers;

use App\Services\AjudaService;
use Illuminate\Http\Request;

class AjudaController extends Controller
{
    private $ajudaService;

    public function __construct(AjudaService $ajudaService)
    {
        $this->ajudaService = $ajudaService;
    }

    public function index(Request $request)
    {
        $termo = $request->query('q', '');

        return view('ajuda.index', [
            'termo' => $termo,
            'resultados' => $termo !== '' ? $this->ajudaService->buscar($termo) : null,
            'categorias' => $this->ajudaService->categorias(),
            'populares' => $this->ajudaService->artigosPopulares(),
        ]);
    }

    public function categoria(string $categoriaSlug)
    {
        $categoria = $this->ajudaService->categoriaPorSlug($categoriaSlug);

        abort_if(!$categoria, 404);

        return view('ajuda.categoria', [
            'categoria' => $categoria,
            'artigos' => $this->ajudaService->artigosPorCategoria($categoriaSlug),
        ]);
    }

    public function artigo(string $artigoSlug)
    {
        $artigo = $this->ajudaService->artigoPorSlug($artigoSlug);

        abort_if(!$artigo, 404);

        $categoria = $this->ajudaService->categoriaPorSlug($artigo['categoria']);

        $relacionados = array_values(array_filter(
            $this->ajudaService->artigosPorCategoria($artigo['categoria']),
            fn ($item) => $item['slug'] !== $artigo['slug']
        ));

        return view('ajuda.artigo', [
            'artigo' => $artigo,
            'categoria' => $categoria,
            'relacionados' => $relacionados,
        ]);
    }
}

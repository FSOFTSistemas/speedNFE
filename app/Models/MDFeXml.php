<?php

namespace App\Models;

use App\Enums\EstadoEnum;
use Illuminate\Database\Eloquent\Model;

class MDFeXml extends Model
{
    public const TIPO_AUTORIZADO = 'autorizado';

    public const TIPO_ENCERRADO = 'encerrado';

    public const TIPO_CANCELADO = 'cancelado';

    protected $table = 'mdfe_xmls';

    protected $fillable = [
        'mdfe_id',
        'tipo',
        'xml',
    ];

    public static function tipoPorModo(int $modo): ?string
    {
        return match ($modo) {
            0 => self::TIPO_AUTORIZADO,
            1 => self::TIPO_ENCERRADO,
            2 => self::TIPO_CANCELADO,
            default => null,
        };
    }

    public static function tipoPorSituacao(EstadoEnum|string $situacao): ?string
    {
        $valor = $situacao instanceof EstadoEnum ? $situacao->value : $situacao;

        return match ($valor) {
            EstadoEnum::AUTORIZADO->value => self::TIPO_AUTORIZADO,
            EstadoEnum::ENCERRADO->value => self::TIPO_ENCERRADO,
            EstadoEnum::CANCELADO->value => self::TIPO_CANCELADO,
            default => null,
        };
    }

    public function mdfe()
    {
        return $this->belongsTo(MDFE::class, 'mdfe_id');
    }
}

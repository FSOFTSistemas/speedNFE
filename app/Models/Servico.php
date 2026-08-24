<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'codigo',
        'descricao',
        'cClass',
        'cTribNac',
        'cTribMun',
        'cNBS',
        'cIndOp',
        'cClassTrib',
        'tribISSQN',
        'tpRetISSQN',
        'pAliqISSQN',
        'cst_ibs_cbs',
        'pRedutorIBSCBS',
        'finNFSe',
        'indFinal',
        'indDest',
        'cfop',
        'uMed',
        'valor',
        'icms_cst',
        'icms_pICMS',
        'icms_pFCP',
        'icms_orig',
        'icms_csosn',
        'pis_cst',
        'pis_pPIS',
        'cofins_cst',
        'cofins_pCOFINS',
        'fust_pFUST',
        'funttel_pFUNTTEL',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function nfses()
    {
        return $this->hasMany(NFSe::class, 'servico_id');
    }
}

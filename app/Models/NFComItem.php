<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFComItem extends Model
{
    use HasFactory;

    protected $table = 'nfcom_itens';

    protected $fillable = [
        'nfcom_id',
        'cProd',
        'xProd',
        'cClass',
        'cfop',
        'uMed',
        'qFaturada',
        'vItem',
        'vDesc',
        'vOutro',
        'vProd',
        'icms_cst',
        'icms_vBC',
        'icms_pICMS',
        'icms_vICMS',
        'icms_pFCP',
        'icms_vFCP',
        'icms_orig',
        'icms_csosn',
        'pis_cst',
        'pis_vBC',
        'pis_pPIS',
        'pis_vPIS',
        'cofins_cst',
        'cofins_vBC',
        'cofins_pCOFINS',
        'cofins_vCOFINS',
        'fust_vBC',
        'fust_pFUST',
        'fust_vFUST',
        'funttel_vBC',
        'funttel_pFUNTTEL',
        'funttel_vFUNTTEL',
    ];

    public function nfcom()
    {
        return $this->belongsTo(NFCom::class, 'nfcom_id');
    }
}

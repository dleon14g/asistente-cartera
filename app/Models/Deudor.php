<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deudor extends Model
{
    /** @use HasFactory<\Database\Factories\DeudorFactory> */
    use HasFactory;

    protected $table = 'deudores';

    protected $fillable = [
        'nombre',
        'dni',
        'provincia',
        'deuda',
        'dias_mora',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'deuda' => 'decimal:2',
            'dias_mora' => 'integer',
        ];
    }

    /**
     * Un deudor tiene muchos pagos.
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}

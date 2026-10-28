<?php

namespace App\Notifications;

use App\Models\Consulta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsultaSolicitada extends Notification
{
    use Queueable;

    public function __construct(protected Consulta $consulta) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Hay una nueva solicitud de consulta pendiente de revisión.',
            'consulta_id' => $this->consulta->id,
            'fecha' => $this->consulta->fecha,
            'hora' => $this->consulta->hora,
            'paciente' => $this->consulta->user?->name,
            'especialidad' => $this->consulta->especialidad?->name,
        ];
    }
}

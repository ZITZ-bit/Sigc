<?php

namespace App\Notifications;

use App\Models\Consulta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsultaAsignada extends Notification
{
    use Queueable;

    public function __construct(protected Consulta $consulta)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Se te ha asignado una consulta.',
            'consulta_id' => $this->consulta->id,
            'fecha' => $this->consulta->fecha,
            'hora' => $this->consulta->hora,
            'estado' => $this->consulta->estado,
        ];
    }
}

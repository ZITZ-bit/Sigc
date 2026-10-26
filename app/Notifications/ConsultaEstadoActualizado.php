<?php

namespace App\Notifications;

use App\Models\Consulta;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsultaEstadoActualizado extends Notification
{
    use Queueable;

    public function __construct(protected Consulta $consulta, protected string $estadoAnterior = '')
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $estado = $this->consulta->estado;

        if ($estado === 'confirmada') {
            $message = 'Tu consulta ha sido confirmada';
        } elseif ($estado === 'cancelada') {
            $message = 'Tu consulta ha sido cancelada';
        } elseif ($estado === 'completada') {
            $message = 'Tu consulta ha sido completada';
        } else {
            $message = 'El estado de tu consulta ha sido actualizado';
        }

        return [
            'message' => $message,
            'consulta_id' => $this->consulta->id,
            'fecha' => $this->consulta->fecha,
            'hora' => $this->consulta->hora,
            'estado' => $estado,
        ];
    }
}

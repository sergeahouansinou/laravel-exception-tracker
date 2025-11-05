<?php

namespace ExceptionTracker;

use ExceptionTracker\Models\ExceptionLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ExceptionReporter
{
    public static function report(Throwable $e): void
    {
        try {
            // Enregistrer dans la base
            $log = ExceptionLog::create([
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'context' => [],
            ]);

            // Envoyer un email si activé
            if ($email = config('exception-tracker.notify_email')) {
                $subject = "🚨 Exception Laravel détectée";
                $body = "Type: {$log->type}\n"
                    . "Message: {$log->message}\n"
                    . "Fichier: {$log->file}\n"
                    . "Ligne: {$log->line}\n"
                    . "Lien vers le log: " . url("/api/exception-tracker/{$log->id}");

                Mail::raw($body, function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
            }
        } catch (\Throwable $internalError) {
            // On ne veut pas casser l’application même si le tracker échoue
            Log::error("Erreur dans ExceptionReporter: {$internalError->getMessage()}");
        }
    }
}

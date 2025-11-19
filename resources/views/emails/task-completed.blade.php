@extends('emails.layouts.base')

@section('title', 'Tâche terminée')

@section('content')
    <h2 class="greeting">Cher {{ $client->user->full_name }},</h2>

    <p class="message">
        {{ $freelance->user->full_name }} vient de marquer une tâche comme terminée pour votre projet
        <span class="highlight">"{{ $project->title }}"</span>.
    </p>

    <div class="info-box">
        <h3>✅ Tâche complétée</h3>
        <div class="info-item">
            <span class="info-label">Titre de la tâche :</span>
            {{ $task->title }}
        </div>
        @if($task->description)
            <div class="info-item">
                <span class="info-label">Description :</span>
            </div>
            <p style="margin-top: 10px; padding: 15px; background-color: #fff; border-radius: 4px;">
                {{ $task->description }}
            </p>
        @endif
        <div class="info-item">
            <span class="info-label">Priorité :</span>
            <span style="text-transform: uppercase;">{{ $task->priority }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Complétée le :</span>
            {{ $task->completed_at->format('d/m/Y à H:i') }}
        </div>
    </div>

    <div class="info-box">
        <h3>📊 Progression du projet</h3>
        <div class="info-item">
            <span class="info-label">Progression globale :</span>
            <strong style="color: #8B5CF6; font-size: 18px;">{{ $project->progress }}%</strong>
        </div>
        <div style="background-color: #e2e8f0; border-radius: 10px; height: 20px; margin-top: 15px; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%); height: 100%; width: {{ $project->progress }}%; transition: width 0.3s ease;"></div>
        </div>
        <div class="info-item" style="margin-top: 15px;">
            <span class="info-label">Tâches complétées :</span>
            {{ $project->getCompletedTasksCount() }} / {{ $project->getTotalTasksCount() }}
        </div>
    </div>

    <p class="message">
        Nous vous recommandons de vérifier le travail effectué et de valider la tâche.
    </p>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/client/projects/{{ $project->id }}" class="cta-button">
            Voir le projet
        </a>
    </div>
@endsection

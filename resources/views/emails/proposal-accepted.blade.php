@extends('emails.layouts.base')

@section('title', 'Votre proposition a été acceptée')

@section('content')
    <h2 class="greeting">Félicitations {{ $freelance->user->full_name }} ! 🎉</h2>

    <p class="message">
        Votre proposition pour le projet <span class="highlight">"{{ $project->title }}"</span> a été acceptée par le client !
    </p>

    <div class="info-box">
        <h3>📋 Détails du projet</h3>
        <div class="info-item">
            <span class="info-label">Titre :</span>
            {{ $project->title }}
        </div>
        <div class="info-item">
            <span class="info-label">Client :</span>
            {{ $project->client->user->full_name }}
        </div>
        <div class="info-item">
            <span class="info-label">Montant convenu :</span>
            {{ number_format($proposal->proposed_amount, 2) }} $
        </div>
        <div class="info-item">
            <span class="info-label">Durée :</span>
            {{ $proposal->proposed_duration }} jours
        </div>
        @if($project->deadline)
            <div class="info-item">
                <span class="info-label">Date limite :</span>
                {{ $project->deadline->format('d/m/Y') }}
            </div>
        @endif
    </div>

    <div class="info-box" style="background-color: #fef3c7; border-left-color: #f59e0b;">
        <h3 style="color: #92400e;">📝 Prochaines étapes</h3>
        <ol style="margin-left: 20px; color: #78350f;">
            <li style="margin: 10px 0;">Consultez le contrat généré automatiquement</li>
            <li style="margin: 10px 0;">Contactez le client via la messagerie</li>
            <li style="margin: 10px 0;">Commencez le travail dès que possible</li>
            <li style="margin: 10px 0;">Utilisez les tâches pour suivre votre progression</li>
        </ol>
    </div>

    <p class="message">
        Un contrat a été automatiquement créé pour ce projet. Vous pouvez maintenant communiquer avec le client et commencer le travail.
    </p>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/freelance/projects/{{ $project->id }}" class="cta-button">
            Accéder au projet
        </a>
    </div>

    <div class="divider"></div>

    <p class="message" style="font-size: 14px; color: #718096;">
        💡 <strong>Conseil :</strong> Maintenez une communication régulière avec le client et livrez un travail de qualité pour obtenir de bonnes évaluations.
    </p>
@endsection

@extends('emails.layouts.base')

@section('title', 'Nouvelle proposition reçue')

@section('content')
    <h2 class="greeting">Cher {{ $client->user->full_name }},</h2>

    <p class="message">
        Excellente nouvelle ! Un freelance vient de soumettre une proposition pour votre projet
        <span class="highlight">"{{ $project->title }}"</span>.
    </p>

    <div class="info-box">
        <h3>👤 Informations du freelance</h3>
        <div class="info-item">
            <span class="info-label">Nom :</span>
            {{ $freelance->user->full_name }}
        </div>
        <div class="info-item">
            <span class="info-label">Titre :</span>
            {{ $freelance->title }}
        </div>
        @if($freelance->hourly_rate)
            <div class="info-item">
                <span class="info-label">Taux horaire :</span>
                {{ number_format($freelance->hourly_rate, 2) }} €/h
            </div>
        @endif
        @if($freelance->experience_years)
            <div class="info-item">
                <span class="info-label">Expérience :</span>
                {{ $freelance->experience_years }} ans
            </div>
        @endif
    </div>

    <div class="info-box">
        <h3>💼 Détails de la proposition</h3>
        <div class="info-item">
            <span class="info-label">Montant proposé :</span>
            {{ number_format($proposal->proposed_amount, 2) }} $
        </div>
        <div class="info-item">
            <span class="info-label">Durée proposée :</span>
            {{ $proposal->proposed_duration }} jours
        </div>
        <div class="info-item">
            <span class="info-label">Lettre de motivation :</span>
        </div>
        <p style="margin-top: 10px; padding: 15px; background-color: #fff; border-radius: 4px; font-style: italic;">
            {{ Str::limit($proposal->cover_letter, 200) }}
        </p>
    </div>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/client/projects/{{ $project->id }}/proposals" class="cta-button">
            Consulter la proposition
        </a>
    </div>

    <div class="divider"></div>

    <p class="message" style="font-size: 14px; color: #718096;">
        💡 <strong>Astuce :</strong> Prenez le temps d'examiner le profil du freelance et posez des questions si nécessaire avant d'accepter une proposition.
    </p>
@endsection

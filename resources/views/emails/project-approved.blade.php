@extends('emails.layouts.base')

@section('title', 'Votre projet a été approuvé')

@section('content')
    <h2 class="greeting">Cher {{ $client->user->full_name }},</h2>

    <p class="message">
        Bonne nouvelle ! Votre projet <span class="highlight">"{{ $project->title }}"</span> a été approuvé par notre équipe et est maintenant visible par tous les freelances sur la plateforme.
    </p>

    <div class="info-box">
        <h3>📋 Détails du projet</h3>
        <div class="info-item">
            <span class="info-label">Titre :</span>
            {{ $project->title }}
        </div>
        <div class="info-item">
            <span class="info-label">Catégorie :</span>
            {{ $project->category->name }}
        </div>
        <div class="info-item">
            <span class="info-label">Budget :</span>
            {{ number_format($project->budget, 2) }} €
        </div>
        <div class="info-item">
            <span class="info-label">Durée :</span>
            {{ $project->duration }} jours
        </div>
    </div>

    <p class="message">
        Les freelances peuvent maintenant consulter votre projet et vous envoyer des propositions. Vous recevrez une notification dès qu'une nouvelle proposition sera soumise.
    </p>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/client/projects/{{ $project->id }}" class="cta-button">
            Voir mon projet
        </a>
    </div>

    <div class="divider"></div>

    <p class="message" style="font-size: 14px; color: #718096;">
        💡 <strong>Astuce :</strong> Répondez rapidement aux propositions pour montrer votre engagement et attirer les meilleurs freelances.
    </p>
@endsection

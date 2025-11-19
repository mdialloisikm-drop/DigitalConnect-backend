@extends('emails.layouts.base')

@section('title', 'Mise à jour sur votre proposition')

@section('content')
    <h2 class="greeting">Cher {{ $freelance->user->full_name }},</h2>

    <p class="message">
        Nous vous informons que votre proposition pour le projet <span class="highlight">"{{ $project->title }}"</span> n'a pas été retenue cette fois-ci.
    </p>

    <div class="info-box">
        <h3>📋 Projet concerné</h3>
        <div class="info-item">
            <span class="info-label">Titre :</span>
            {{ $project->title }}
        </div>
        <div class="info-item">
            <span class="info-label">Catégorie :</span>
            {{ $project->category->name }}
        </div>
        <div class="info-item">
            <span class="info-label">Votre proposition :</span>
            {{ number_format($proposal->proposed_amount, 2) }} € en {{ $proposal->proposed_duration }} jours
        </div>
    </div>

    <p class="message">
        Ne vous découragez pas ! Cela fait partie du processus. Chaque proposition est une opportunité d'apprentissage.
    </p>

    <div class="info-box" style="background-color: #dbeafe; border-left-color: #3b82f6;">
        <h3 style="color: #1e40af;">💪 Conseils pour augmenter vos chances</h3>
        <ul style="margin-left: 20px; color: #1e3a8a;">
            <li style="margin: 10px 0;">Personnalisez votre lettre de motivation pour chaque projet</li>
            <li style="margin: 10px 0;">Mettez en avant votre expérience pertinente</li>
            <li style="margin: 10px 0;">Proposez un prix compétitif mais juste</li>
            <li style="margin: 10px 0;">Complétez votre profil avec vos compétences et portfolio</li>
            <li style="margin: 10px 0;">Répondez rapidement aux questions du client</li>
        </ul>
    </div>

    <p class="message">
        De nombreuses autres opportunités vous attendent sur Digital Connects. Continuez à postuler !
    </p>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/freelance/projects" class="cta-button">
            Découvrir d'autres projets
        </a>
    </div>

    <div class="divider"></div>

    <p class="message" style="font-size: 14px; color: #718096;">
        💡 <strong>Astuce :</strong> Les clients reçoivent souvent de nombreuses propositions. Soyez patient et persévérant !
    </p>
@endsection

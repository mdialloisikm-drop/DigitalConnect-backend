@extends('emails.layouts.base')

@section('title', 'Nouvelle commande reçue')

@section('content')
    <h2 class="greeting">Cher {{ $freelance->user->full_name }},</h2>

    <p class="message">
        Excellente nouvelle ! Vous avez reçu une nouvelle commande pour votre service
        <span class="highlight">"{{ $service->title }}"</span>.
    </p>

    <div class="info-box">
        <h3>👤 Informations du client</h3>
        <div class="info-item">
            <span class="info-label">Nom :</span>
            {{ $client->user->full_name }}
        </div>
        @if($client->company_name)
            <div class="info-item">
                <span class="info-label">Entreprise :</span>
                {{ $client->company_name }}
            </div>
        @endif
    </div>

    <div class="info-box">
        <h3>💼 Détails de la commande</h3>
        <div class="info-item">
            <span class="info-label">Service :</span>
            {{ $service->title }}
        </div>
        <div class="info-item">
            <span class="info-label">Offre choisie :</span>
            {{ $order->serviceOffer->title }}
        </div>
        <div class="info-item">
            <span class="info-label">Montant :</span>
            {{ number_format($order->amount, 2) }} €
        </div>
        <div class="info-item">
            <span class="info-label">Délai de livraison :</span>
            {{ $order->serviceOffer->delivery_days }} jours
        </div>
        <div class="info-item">
            <span class="info-label">Date limite :</span>
            {{ $order->due_date->format('d/m/Y') }}
        </div>
        @if($order->serviceOffer->number_of_revisions)
            <div class="info-item">
                <span class="info-label">Révisions incluses :</span>
                {{ $order->serviceOffer->number_of_revisions }}
            </div>
        @endif
    </div>

    @if($order->requirements)
        <div class="info-box">
            <h3>📝 Exigences du client</h3>
            <p style="margin-top: 10px; padding: 15px; background-color: #fff; border-radius: 4px; white-space: pre-line;">{{ $order->requirements }}</p>
        </div>
    @endif

    <div class="info-box" style="background-color: #fef3c7; border-left-color: #f59e0b;">
        <h3 style="color: #92400e;">⏰ Actions à effectuer</h3>
        <ol style="margin-left: 20px; color: #78350f;">
            <li style="margin: 10px 0;">Consultez la commande complète sur la plateforme</li>
            <li style="margin: 10px 0;">Démarrez la commande dans votre tableau de bord</li>
            <li style="margin: 10px 0;">Communiquez avec le client si besoin</li>
            <li style="margin: 10px 0;">Livrez le travail avant la date limite</li>
        </ol>
    </div>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/freelance/orders/{{ $order->id }}" class="cta-button">
            Voir la commande
        </a>
    </div>

    <div class="divider"></div>

    <p class="message" style="font-size: 14px; color: #718096;">
        💡 <strong>Important :</strong> Respectez les délais et communiquez régulièrement avec le client pour garantir sa satisfaction.
    </p>
@endsection

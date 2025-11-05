## 🚨 Laravel Exception Tracker

[![Packagist Version](https://img.shields.io/packagist/v/sergeahouansinou/laravel-exception-tracker.svg?style=flat-square)](https://packagist.org/packages/sergeahouansinou/laravel-exception-tracker)
[![Packagist Downloads](https://img.shields.io/packagist/dt/sergeahouansinou/laravel-exception-tracker.svg?style=flat-square)](https://packagist.org/packages/sergeahouansinou/laravel-exception-tracker)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

Un package Laravel simple, auto-hébergé et open source pour surveiller, enregistrer et notifier par e-mail les exceptions de votre application — à la manière de Sentry, mais 100% sous votre contrôle. Pas de données envoyées à des tiers !

## 🧩 Table des matières

- [Aperçu](#-aperçu)
- [Fonctionnalités](#-fonctionnalités)
- [Installation](#️-installation)
- [Configuration](#️-configuration)
- [Utilisation](#-utilisation)
- [API REST](#-api-rest)
- [Commandes Artisan](#-commandes-artisan)
- [Contribution](#-contribution)
- [Licence](#-licence)

## 🔍 Aperçu

Laravel Exception Tracker capture automatiquement toutes les exceptions et erreurs fatales de votre application Laravel. Il les enregistre dans une table dédiée (`exception_logs`) et envoie une notification e-mail instantanée contenant les détails de l’erreur.

🧠 Idéal pour les équipes qui veulent un système de suivi d’erreurs auto-hébergé, sans dépendre de Sentry ou Bugsnag. Tout reste sur votre serveur !

## ✨ Fonctionnalités

- 📦 Capture automatique des exceptions via le hook `report()` de Laravel.
- 💾 Sauvegarde en base de données (table `exception_logs` avec champs : id, type, code, message, file, line, trace, occurred_at).
- 📬 Notification instantanée par e-mail (configurable avec destinataires multiples).
- ⚙️ Middleware optionnel pour tracker des exceptions sur des routes spécifiques.
- 📡 API REST pour consulter, filtrer et supprimer les logs (protégée par authentification).
- 🧹 Commande Artisan pour purger les anciens logs (par date ou quantité).
- 🔒 100% privé : Aucun envoi de données externes.
- 🧰 Compatible avec Laravel 9 à 12.
- 📊 Support pour traces stack complètes et contextes (request, user, etc.).

## ⚙️ Installation

### 1. Ajouter le package via Composer

```bash
composer require sergeahouansinou/laravel-exception-tracker
```

### 2. Publier la configuration et les migrations

```bash
php artisan vendor:publish --provider="Sergeahouansinou\\ExceptionTracker\\ExceptionTrackerServiceProvider" --tag="config"
php artisan vendor:publish --provider="Sergeahouansinou\\ExceptionTracker\\ExceptionTrackerServiceProvider" --tag="migrations"
```

### 3. Exécuter les migrations

```bash
php artisan migrate
```

### 4. Configurer l'envoi d'e-mails

Assurez-vous que votre fichier `.env` est configuré pour l'envoi d'e-mails (ex. via Mailtrap pour les tests).

## 🛠️ Configuration

Le fichier de configuration est publié dans `config/exception-tracker.php`. Voici un exemple :

```php
<?php

return [
    'enabled' => env('EXCEPTION_TRACKER_ENABLED', true),  // Activer/désactiver le tracking
    'notify_emails' => explode(',', env('EXCEPTION_TRACKER_NOTIFY_EMAILS', 'admin@example.com')),  // Destinataires des notifications
    'ignore_exceptions' => [  // Exceptions à ignorer
        \Illuminate\Validation\ValidationException::class,
    ],
    'purge_days' => 30,  // Nombre de jours avant purge automatique
];
```

Ajoutez ces variables à votre `.env` :

```
EXCEPTION_TRACKER_ENABLED=true
EXCEPTION_TRACKER_NOTIFY_EMAILS=admin@example.com,dev@example.com
```

## 📝 Utilisation

### Capture Automatique
Le package surcharge automatiquement le handler d'exceptions de Laravel. Toute exception non gérée sera loguée et notifiée.

### Middleware
Ajoutez le middleware à vos routes pour un tracking ciblé :

```php
// Dans web.php ou api.php
Route::middleware('exception-tracker')->group(function () {
    // Vos routes ici
});
```

### Exemple de Log
Un log typique en BD ressemblerait à :

| id | type              | code | message                  | file                  | line | trace              | occurred_at         |
|----|-------------------|------|--------------------------|-----------------------|------|--------------------|---------------------|
| 1  | RuntimeException | 500  | Something went wrong    | /app/Controller.php  | 42   | [stack trace]     | 2025-11-05 10:00:00 |

### Notification E-mail
L'e-mail inclut : type d'erreur, message, fichier/ligne, trace complète, et URL de la request.

## 📡 API REST

Endpoints protégés (utilisez Sanctum ou similaire pour l'auth) :

- `GET /api/exception-logs` : Lister les logs (avec pagination et filtres).
- `GET /api/exception-logs/{id}` : Détails d'un log.
- `DELETE /api/exception-logs/{id}` : Supprimer un log.

Ajoutez les routes dans votre `routes/api.php` si nécessaire.

## 🧹 Commandes Artisan

- Purger les anciens logs :
  ```bash
  php artisan exception-tracker:purge
  ```

## 🤝 Contribution

Contributions bienvenues ! Forkez le dépôt, créez une branche, et soumettez une pull request. Respectez les standards PSR-12 pour le code PHP.

- Signalez les bugs via les issues.
- Suggestions : Ajout de dashboard, support pour autres notificateurs (Slack, Discord).

## 📄 Licence

Ce package est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

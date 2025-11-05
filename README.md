# 🚨 Laravel Error Tracker

> Un package Laravel simple, auto-hébergé et open source pour **surveiller**, **enregistrer** et **notifier par e-mail** les exceptions de votre application — à la manière de Sentry, mais 100% sous votre contrôle.

---

## 🧩 Sommaire
- [Aperçu](#-aperçu)
- [Fonctionnalités](#-fonctionnalités)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Envoi automatique d’e-mails](#-envoi-automatique-de-mails)
- [Commandes artisan](#-commandes-artisan)
- [API REST](#-api-rest)
- [Exemples](#-exemples)
- [Roadmap](#-roadmap)
- [Licence](#-licence)

---

## 🔍 Aperçu

Laravel Error Tracker capture automatiquement **toutes les exceptions** et **erreurs fatales** de votre application Laravel.  
Il les enregistre dans une table dédiée (`exception_logs`) et envoie une **notification e-mail instantanée** contenant les détails de l’erreur.

> 🧠 Idéal pour les équipes qui veulent un système de suivi d’erreurs **auto-hébergé**, sans dépendre de Sentry ou Bugsnag.

---

## ✨ Fonctionnalités

- 📦 Capture automatique des exceptions Laravel (`report()` hook)
- 💾 Sauvegarde en base de données (`exception_logs`)
- 📬 Notification instantanée par e-mail (configurable)
- ⚙️ Middleware optionnel pour suivre des exceptions spécifiques
- 📡 API REST pour consulter les logs
- 🧹 Commande artisan pour purger les vieux logs
- 🔒 Aucun envoi de données externes (100% privé)
- 🧰 Compatible Laravel **9 → 12**

---

## ⚙️ Installation

### 1. Ajouter le package

```bash
composer require sergeahouansinou/laravel-error-tracker
```

Pour tester localement :

composer require sergeahouansinou/laravel-error-tracker --path=../laravel-error-tracker-full

Publier la configuration et la migration

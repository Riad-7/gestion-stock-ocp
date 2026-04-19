# Diagramme des cas d'utilisation

Ce diagramme UML decrit les acteurs et les cas d'utilisation principaux du projet **GS-OCP**.

## Acteurs

- **Utilisateur** : utilisateur authentifie qui exploite l'application au quotidien
- **Administrateur** : utilisateur avec responsabilites de supervision
- **Planificateur Laravel** : acteur technique qui execute les taches automatiques

## Diagrammes disponibles

- version PlantUML : [diagramme-cas-utilisation.puml](/c:/Users/Gros%20Info/Desktop/gs-backend-ocp/gestion-stock-ocp/gs-laravel/docs/diagramme-cas-utilisation.puml)
- version draw.io simplifiee pour soutenance : [diagramme-cas-utilisation.drawio](/c:/Users/Gros%20Info/Desktop/gs-backend-ocp/gestion-stock-ocp/gs-laravel/docs/diagramme-cas-utilisation.drawio)

## Cas d'utilisation retenus

- authentification et gestion du profil
- consultation du dashboard
- gestion des clients et fournisseurs
- gestion des articles avec changement de statut et restock
- gestion des ventes avec facture PDF et annulation
- gestion des commandes fournisseurs avec livraison et facture PDF
- consultation de l'historique et lancement d'un backup manuel
- consultation et lecture des notifications
- automatisations de stock, alertes, verification des expirations et backup planifie

## Remarque

Le role **Administrateur** est represente dans le diagramme pour couvrir les taches de supervision, meme si le code actuel n'applique pas encore une separation de roles complete dans les routes web.

La version `draw.io` a ete volontairement simplifiee pour une presentation de soutenance:

- moins de cas d'utilisation affiches
- regroupement par grands blocs fonctionnels
- couleurs sobres pour mieux distinguer les modules

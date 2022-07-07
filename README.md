# UNH Report Manager

[![License: CC BY 4.0](https://img.shields.io/badge/License-CC_BY_4.0-lightgrey.svg)](https://creativecommons.org/licenses/by/4.0/) 
[![Lint](https://github.com/unhorizons/report-manager/actions/workflows/lint.yaml/badge.svg)](https://github.com/unhorizons/report-manager/actions/workflows/lint.yaml)

Application Web de gestion de rapport de l'administration UNH

# Prérequis 
Le projet fonctionne avec docker pour la phase de développement, 
il est donc nécessaire d'avoir docker afin de lancer l'application en local,
sinon vous devrait installer toute la stack de technologies utilisées sur votre poste.

# Technologies
- php:8.1
- mariadb:10.4*
- redis:6.*
- nodejs:14.*
- maildev:2.*
- nginx:latest
- adminer:4.8.1

# Installation
cette installation est faites uniquement pour le développement

```bash
$ git clone https://github.com/unhorizons/report-manager unh-report
$ cd unh-report

$ make build-docker
$ make dev
```

# Tests et Lint
Pour se rassurer que le projet fonctionne et qu'aucun bug ne présent,
vous pouvez lancer les tests et l'analyse statique avec les commandes suivantes :

```bash
$ make lint
$ make test
```
Avant toutes modifications rassurez-vous d'avoir effectué ces commandes, sinon le commit sera rejeté.


### contributeurs

- [Bernard Ng](https://github.com/bernard-ng) (Project Lead)
- [Wilfried Musanzi](https://github.com/willfried-musanzi)
- [Robert MAKILA](https://github.com/romhack1) (Info. Sec. Adviser)

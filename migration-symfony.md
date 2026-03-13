# Migration Laravel -> Symfony : Etude de faisabilite

> Date : 2026-03-13
> Projet : TrailMarker Backend

---

## Inventaire du projet actuel

| Couche | Laravel | Fichiers concernes |
|---|---|---|
| Framework | Laravel 12.x | Tout le projet |
| ORM | Eloquent (Models, Relations, Observers) | 5 models, 1 observer |
| Auth | Sanctum (session SPA) + Breeze | Auth controllers, middleware |
| Validation | Form Requests | 6 fichiers |
| Authorization | Policies | 3 policies |
| API Resources | JsonResource | 2 resources |
| Filtrage/Tri | `spatie/laravel-query-builder` | TrailController |
| GPX | `sibyx/phpgpx` | GpxService |
| Actions | Pattern Action classes | 5 actions |
| Code style | Laravel Pint | Config |
| DB | Eloquent + doctrine/dbal | 11 migrations |
| Tests | PHPUnit 11 + Faker | ~8 fichiers |
| Dev tools | Sail, Ignition, Collision | Dev only |

---

## Mapping Laravel -> Symfony

| Composant Laravel | Alternative Symfony | Complexite |
|---|---|---|
| **laravel/framework** | `symfony/framework-bundle` | Haute |
| **Eloquent ORM** | **Doctrine ORM** (`doctrine/orm` + `doctrine/doctrine-bundle`) | Haute |
| **Migrations Eloquent** | **Doctrine Migrations** (`doctrine/doctrine-migrations-bundle`) | Moyenne |
| **Laravel Sanctum** | **`lexik/jwt-authentication-bundle`** (JWT) ou **`symfony/security-bundle`** (session) | Haute |
| **Laravel Breeze** | Pas d'equivalent direct. Implem manuelle avec `symfony/security-bundle` + `symfony/form` | Haute |
| **Form Requests (validation)** | **`symfony/validator`** (constraints/annotations sur les DTO) | Moyenne |
| **Policies (authorization)** | **Symfony Voters** (`symfony/security-bundle`) | Faible |
| **API Resources (JsonResource)** | **`symfony/serializer`** (normalizers/groups) ou **API Platform** | Moyenne |
| **spatie/laravel-query-builder** | **API Platform** (filtres natifs) ou **implem manuelle** avec Doctrine QueryBuilder | Moyenne |
| **sibyx/phpgpx** | **`sibyx/phpgpx`** (identique, pas de dep Laravel) | Aucune |
| **doctrine/dbal** | **Natif** dans Doctrine ORM | Aucune |
| **Laravel Pint (CS Fixer)** | **`friendsofphp/php-cs-fixer`** (Pint est un wrapper autour) | Faible |
| **PHPUnit + Faker** | **PHPUnit + Faker** (identiques) | Faible |
| **Laravel Sail** | **Docker Compose classique** ou **Symfony Docker** (dunglas) | Faible |
| **Spatie Ignition** | **Symfony Profiler/Web Debug Toolbar** (natif) | Aucune |
| **PHP CodeSniffer** | **PHP CodeSniffer** (identique) | Aucune |
| **Eloquent Observers** | **Doctrine Event Listeners / Entity Listeners** | Moyenne |
| **Storage Facade** | **`league/flysystem-bundle`** (Symfony l'utilise aussi) | Faible |
| **Route::apiResource** | **Attributs `#[Route]`** sur les controllers ou **API Platform** | Moyenne |

---

## Plan de migration par phases

### Phase 1 - Fondations (CRITIQUE)

> Sans cela, rien ne fonctionne.

1. **Initialiser le projet Symfony 7.x** (`symfony new trailmarker-api --webapp`)
2. **Configurer Doctrine ORM** - Convertir les 5 Eloquent Models en Doctrine Entities (`Trail`, `User`, `Avis`, `TrailList`, `TrailImage`)
   - Les `$fillable`, `$casts`, relations deviennent des annotations/attributs Doctrine
   - L'enum `TrailDifficulty` reste tel quel (Doctrine supporte les PHP enums nativement)
3. **Migrer les migrations** - Reecrire les 11 migrations via `doctrine:migrations:diff` ou manuellement
4. **Configurer le routing API** - Passer des `Route::apiResource` aux attributs `#[Route]` sur les controllers

**Effort estime :** ~3-5 jours

---

### Phase 2 - Authentification & Securite (CRITIQUE)

> L'API est inutilisable sans auth.

1. **Remplacer Sanctum** par `lexik/jwt-authentication-bundle` (si API stateless) ou `symfony/security-bundle` sessions (si SPA cookie-based comme actuellement)
2. **Reimplementer les controllers Auth** (register, login, logout, password reset, email verification) - Breeze n'a pas d'equivalent Symfony, tout est manuel
3. **Convertir les 3 Policies en Voters Symfony** - Le pattern est tres similaire :

```php
// Laravel Policy
public function update(User $user, Trail $trail): bool {
    return $user->id === $trail->user_id;
}

// Symfony Voter
protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool {
    return $token->getUser()->getId() === $subject->getUserId();
}
```

**Effort estime :** ~3-4 jours

---

### Phase 3 - Logique metier & Services (HAUTE)

> Le coeur fonctionnel de l'app.

1. **Migrer les 5 Actions** (StoreTrailAction, UpdateTrailAction, etc.) - Le pattern Action se transpose quasi 1:1 en services Symfony avec injection de dependances
2. **Migrer le GpxService** - `sibyx/phpgpx` n'a aucune dep Laravel, seul l'`UploadedFile` change (`Symfony\Component\HttpFoundation\File\UploadedFile` -- c'est deja la meme classe sous le capot)
3. **Convertir le TrailObserver** en Doctrine Entity Listener (evenement `postRemove`)
4. **Migrer le file storage** - Symfony utilise aussi Flysystem via `league/flysystem-bundle`, la transition est directe

**Effort estime :** ~2-3 jours

---

### Phase 4 - Validation & Serialisation (MOYENNE)

> Formatage des entrees/sorties.

1. **Remplacer les 6 Form Requests** par des DTO + `symfony/validator` constraints :

```php
// Symfony DTO equivalent
class TrailStoreDto {
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\Choice(callback: [TrailDifficulty::class, 'cases'])]
    public TrailDifficulty $difficulty;
}
```

2. **Remplacer les 2 API Resources** par des groupes de serialisation Symfony ou des DTO de reponse
3. **Remplacer `spatie/laravel-query-builder`** - Soit API Platform (si vous voulez un framework API complet), soit implementation manuelle avec le Doctrine QueryBuilder + `ParamConverter`

**Effort estime :** ~2-3 jours

---

### Phase 5 - Tests & Outillage (BASSE)

> Adaptation de l'infra de test et dev.

1. **Adapter les tests PHPUnit** - PHPUnit et Faker restent identiques, seul le bootstrap change (`KernelTestCase` au lieu de `TestCase` Laravel)
2. **Remplacer Laravel Pint** par `php-cs-fixer` directement (Pint est un wrapper autour)
3. **Configurer Docker** - Remplacer Sail par un `docker-compose.yml` standard (ou le template Symfony Docker de Dunglas)
4. **Configurer le Profiler Symfony** (remplace Ignition/Debugbar) -- installe par defaut

**Effort estime :** ~1-2 jours

---

## Synthese

| Phase | Priorite | Effort | Risque |
|---|---|---|---|
| 1. Fondations (Doctrine, routing) | Critique | 3-5j | Haut - Eloquent->Doctrine est le plus gros chantier |
| 2. Auth & Securite | Critique | 3-4j | Haut - Reimplementation complete de Breeze |
| 3. Logique metier | Haute | 2-3j | Faible - Les patterns se transposent bien |
| 4. Validation & Serialisation | Moyenne | 2-3j | Moyen - Spatie Query Builder n'a pas d'equivalent direct |
| 5. Tests & Outillage | Basse | 1-2j | Faible |
| **Total** | | **~11-17 jours** | |

---

## Focus : Auth session + CSRF (equivalent Sanctum)

### Comment Sanctum fonctionne (mode SPA)

Sanctum en mode SPA n'utilise **pas** de token API. Le flux est :

1. Le frontend appelle `GET /sanctum/csrf-cookie` -> recoit un cookie `XSRF-TOKEN`
2. Le frontend envoie `POST /login` avec credentials + le header `X-XSRF-TOKEN`
3. Le backend cree une **session PHP classique** (cookie `laravel_session`)
4. Toutes les requetes suivantes utilisent le **cookie de session** + le **cookie CSRF** pour s'authentifier

C'est donc de l'**auth par session + protection CSRF**, pas du token API.

### Equivalent Symfony

Symfony supporte ca nativement sans aucun bundle supplementaire, via `symfony/security-bundle` :

```yaml
# config/packages/security.yaml
security:
    firewalls:
        api:
            pattern: ^/api
            stateless: false          # session activee
            json_login:
                check_path: /api/login
                username_path: email
                password_path: password
            logout:
                path: /api/logout
```

```yaml
# config/packages/framework.yaml
framework:
    session:
        cookie_samesite: lax
        cookie_secure: auto
    csrf_protection:
        enabled: true
```

### Flux equivalent en Symfony

```
1. GET  /api/csrf-token       -> retourne le token CSRF (cookie ou JSON)
2. POST /api/login            -> json_login authenticator + CSRF validation -> session creee
3. GET  /api/trails           -> session cookie = authentifie
```

### Controller CSRF token

```php
#[Route('/api/csrf-token', methods: ['GET'])]
public function csrfToken(CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
{
    $token = $csrfTokenManager->getToken('authenticate')->getValue();

    // Option 1 : retourner en JSON
    return new JsonResponse(['csrf_token' => $token]);

    // Option 2 : set en cookie comme Sanctum (XSRF-TOKEN)
    $response = new JsonResponse();
    $response->headers->setCookie(
        Cookie::create('XSRF-TOKEN', $token)
            ->withSameSite('lax')
            ->withSecure(true)
            ->withHttpOnly(false) // le JS doit pouvoir le lire
    );
    return $response;
}
```

### Validation CSRF sur les requetes

```php
class CsrfValidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return;
        }

        $token = $request->headers->get('X-XSRF-TOKEN');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $token))) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
    }
}
```

### Comparaison directe Sanctum vs Symfony

| Fonctionnalite | Sanctum (Laravel) | Symfony natif |
|---|---|---|
| Session cookie auth | `auth:sanctum` middleware | `stateless: false` firewall |
| CSRF cookie endpoint | `GET /sanctum/csrf-cookie` | Controller custom (~10 lignes) |
| CSRF validation | Automatique via `VerifyCsrfToken` middleware | `CsrfTokenManagerInterface` |
| JSON login | Via Breeze controllers | `json_login` authenticator (natif) |
| Cookie SameSite/Secure | Config `sanctum.php` | Config `framework.yaml` |
| CORS | `config/cors.php` | `nelmio/cors-bundle` |

Le comportement est **100% reproductible** en Symfony. La seule difference : Sanctum package tout ca en un seul bundle "clef en main", alors qu'en Symfony il faut assembler les briques (~50 lignes de code custom).

---

## Verdict

**La migration est faisable** mais le ROI est discutable vu la taille du projet (~55 fichiers PHP). Les deux points de friction majeurs :

1. **Eloquent -> Doctrine** : C'est le changement de paradigme le plus lourd (Active Record -> Data Mapper). Toutes les relations, scopes, et le lazy loading doivent etre repenses.
2. **Breeze/Sanctum -> Security Bundle** : Aucun scaffolding equivalent en Symfony, tout doit etre reimplemente manuellement.

Les points positifs : `phpgpx`, PHPUnit, Faker, Flysystem, et PHP CS Fixer sont deja compatibles ou identiques. Le pattern Action se transpose parfaitement. Les Policies/Voters sont tres similaires.

**Recommandation** : Si la migration est motivee par des besoins specifiques (performance, scalabilite, equipe plus a l'aise avec Symfony), ca vaut le coup. Si c'est juste par preference, le projet est trop petit pour justifier 2-3 semaines de travail sans nouvelle fonctionnalite.

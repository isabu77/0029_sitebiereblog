# Création d'un BLOG en MVC (Model - View - Controller)

Modèle MVC avec dossier 'Core' contenant les classes génériques réutilisables en MVC

## Le blog :
- Navigation : un menu 'Home Catégories'
- Page d'accueil : Liste des articles avec leurs catégories et un lien 'lire plus"
- Page Catégories : liste des catégories avec lien sur chacune et la liste de ses articles
- Page d'un article : contenu et catégories de l'article

## dossier 'blog' : l'environnement de travail

- les fichiers :

- .env.sample : variables d'environnement à déclarer
- .gitignore : fichiers et dossiers à ne pas versionner
- README.md : description du projet
- docker-compose.yml : description des containers à créer par :
    docker-compose build
    docker-compose -f docker-compose.yml up -d
- start.sh : lancement de l'environnement avec docker-compose
- stop.sh : arrêt et destruction de l'environnement docker

- 
- les dossiers :

### docker : contient le fichier 'Dockerfile' 
- Dockerfile : décrit l'image 'blog' à construire par :
    docker build -t blog .

### www : Dossier physique associé au dossier virtuel /var/wwww

- composer.json : liste des outils requis par l'application et autoload
- phpcs.xml : liste des dossiers à vérifier par l'outil phpcs
- phpunit.xml : liste des dossiers à tester par l'outil phpunit

#### www/public : Dossier exposé sur le navigateur internet                                                                          
- .htaccess : contient les règles pour utiliser Altorouter
- adminer.php : pour administrer la base
- dossier assets : contient les dossiers css, js et img 
- index.php : le fichier principal de l'application qui charge la classe principale App.php

#### www/commande : Dossier des outils externes

- createsql.php : requêtes SQL de création de la base lancées par :
docker exec blog php commande/createsql.php

#### www/core : Environnement générique MVC

- www/core/Controller : les classes génériques de contrôleurs 
    controller.php : contrôleur général
    RouterController.php : contrôleur des routes
    URLController.php : contrôleur des url
    PaginatedQueryController.php : contrôleur de la pagination
    Database/DatabaseController.php et Database/DatabaseMysqlController.php : contrôleur des bases
    Helpers/TextController.php : contrôleur des méthodes sur chaines

- www/core/Model : les classes génériques du modèle 
    Table.php : requêtes au tables
    Entity.php : Description d'un enregistrement de table

#### www/src : Environnement spécifique de l'application

- App.php : la classe principale de l'application, chargée par www/public/index.php

- www/src/Controller : les classes spécifiques du contrôleur qui héritent de core/controller
    CategoryController.php : contrôleur des routes
    PostController.php : contrôleur des url
    PaginatedQueryAppController.php : contrôleur de la pagination

- www/src/Model : les classes spécifiques du modèle qui héritent de core/model
    Table.php : requêtes au tables
    Entity.php : Description d'un enregistrement de table

#### www/views : les vues HTML des pages de l'application 

- www/views/layout : template default.twig d'une page 'modèle' (header + contenu + footer)
- www/views/category : templates .twig des pages pour les catégories (all.twig et show.twig)
- www/views/post : templates .twig des pages pour les articles (card.twig, all.twig et show.twig)

#### www/tests : les tests unitaires des classes de l'application 

- www/tests/Core/Controller/Helpers/TextTest.php : classe de tests unitaires de la classe \Core\Controller\Helpers\TextController

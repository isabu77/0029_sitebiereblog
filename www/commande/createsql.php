<?php
// Fichier principal qui initialise l'application et définit les routes
$basepath = dirname(__dir__) . DIRECTORY_SEPARATOR; // contient /var/www/

require_once $basepath . 'vendor/autoload.php';

require '.'. DIRECTORY_SEPARATOR. 'src'. DIRECTORY_SEPARATOR . 'config.php';

$sql = "mysql:host=" . $env['MYSQL_HOSTNAME'] . ";dbname=" . $env['MYSQL_DATABASE'];
$pdo = new PDO($sql, $env['MYSQL_USER'], $env['MYSQL_PASSWORD'] );
$prefix = $env['TABLE_PREFIX'];

/**
 * suppression des tables
 *  
 */
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
//blog
$etape = $pdo->exec("DROP TABLE {$prefix}post_category");
$etape = $pdo->exec("DROP TABLE {$prefix}post");
$etape = $pdo->exec("DROP TABLE {$prefix}category");
$etape = $pdo->exec("DROP TABLE {$prefix}comment");

//utilisateurs
$etape = $pdo->exec("DROP TABLE {$prefix}user");
$etape = $pdo->exec("DROP TABLE {$prefix}user_infos");

//shop
$etape = $pdo->exec("DROP TABLE {$prefix}config");
$etape = $pdo->exec("DROP TABLE {$prefix}beer");

//order
$etape = $pdo->exec("DROP TABLE {$prefix}status");
$etape = $pdo->exec("DROP TABLE {$prefix}order");
$etape = $pdo->exec("DROP TABLE {$prefix}order_line");

// anciennes tables obsolètes
$etape = $pdo->exec("DROP TABLE post_category");
$etape = $pdo->exec("DROP TABLE post");
$etape = $pdo->exec("DROP TABLE category");
$etape = $pdo->exec('DROP TABLE comment');
$etape = $pdo->exec("DROP TABLE user");
$etape = $pdo->exec("DROP TABLE user_infos");
$etape = $pdo->exec("DROP TABLE config");
$etape = $pdo->exec("DROP TABLE beer");
$etape = $pdo->exec("DROP TABLE status");
$etape = $pdo->exec("DROP TABLE order");
$etape = $pdo->exec("DROP TABLE order_line");
$etape = $pdo->exec("DROP TABLE orderline");
$etape = $pdo->exec("DROP TABLE order_line");
$etape = $pdo->exec("DROP TABLE orders");
$etape = $pdo->exec("DROP TABLE userblog");
$etape = $pdo->exec("DROP TABLE users");
$etape = $pdo->exec("DROP TABLE client");

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1'); 

/**
 * creation des tables
 *  
 */
echo "[";

$etape = $pdo->exec("CREATE TABLE {$prefix}post(
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content TEXT(650000) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        )");
echo "-||-" . $etape;
$etape = $pdo->exec("CREATE TABLE {$prefix}category(
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        )");
echo "-||-" . $etape;

$pdo->exec("CREATE TABLE {$prefix}post_category(
            post_id INT UNSIGNED NOT NULL,
            category_id INT UNSIGNED NOT NULL,
            PRIMARY KEY(post_id, category_id),
            CONSTRAINT fk_post
                FOREIGN KEY(post_id)
                REFERENCES {$prefix}post(id)
                ON DELETE CASCADE
                ON UPDATE RESTRICT,
            CONSTRAINT fk_category
                FOREIGN KEY(category_id)
                REFERENCES {$prefix}category(id)
                ON DELETE CASCADE
                ON UPDATE RESTRICT
        )");
echo "-||-" . $etape;

$pdo->exec("CREATE TABLE {$prefix}comment (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `post_id` INT not null,
    `user_id` INT not null,
    `name` VARCHAR(255) not null,
    `content` TEXT(65000) NOT null,
    `postedAt` datetime default CURRENT_TIMESTAMP
    )");
echo "-||-" . $etape;



//=================== le site bière

// la table de configuration

$etape = $pdo->exec("CREATE TABLE {$prefix}config (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` timestamp NULL DEFAULT current_timestamp(),
    `tva` float NOT NULL,
    `port` float,
    `ship_limit` float,
            PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
echo "-||-" . $etape;

// la table des bières

$etape = $pdo->exec("CREATE TABLE {$prefix}beer (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `img` text NOT NULL,
    `content` longtext NOT NULL,
    `price` float NOT NULL,
    `stock` int,
            PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
echo "-||-" . $etape;

// la table des status de commandes

$etape = $pdo->exec("CREATE TABLE {$prefix}status (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `libelle` varchar(24) NOT NULL,
            PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
echo "-||-" . $etape;

// la table des commandes

$etape = $pdo->exec("CREATE TABLE {$prefix}order (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_client` int(11) NOT NULL,
    `token` varchar(24) NOT NULL,
    `number` varchar(24),
    `priceHT` float NOT NULL,
    `priceTTC` float NOT NULL,
    `createdAt` timestamp NULL DEFAULT current_timestamp(),
    `id_status` int NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
echo "-||-" . $etape;

// la table des lignes de commande

$etape = $pdo->exec("CREATE TABLE {$prefix}order_line (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_user` int(11) NOT NULL,
    `id_product` int(11) NOT NULL,
    `token` varchar(24) NOT NULL,
    `quantity` int NOT NULL,
    `priceHT` float NOT NULL,
    `priceTTC` float NOT NULL,
             PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
echo "-||-" . $etape;

// les adresses user_infos
$etape = $pdo->exec("CREATE TABLE {$prefix}user_infos (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_user` int(11),
    `lastname` varchar(255) NOT NULL,
    `firstname` varchar(255) NOT NULL,
    `address` varchar(255) NOT NULL,
    `zipCode` varchar(255) NOT NULL,
    `city` varchar(255) NOT NULL,
    `country` varchar(255) NOT NULL,
    `phone` varchar(255) NOT NULL,
            PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
echo "-||-" . $etape;

// les utilisateurs connectés
$etape = $pdo->exec("CREATE TABLE {$prefix}user (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `mail` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `token` varchar(24) NOT NULL,
    `createdAt` timestamp NULL DEFAULT current_timestamp(),
    `verify` tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1");
echo "-||-" . $etape;


/**
 * vidage des tables inutile car on les a supprimées
 */

/* $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE post_category');
$pdo->exec('TRUNCATE TABLE post');
$pdo->exec('TRUNCATE TABLE category');
$pdo->exec('TRUNCATE TABLE config');
$pdo->exec('TRUNCATE TABLE beer');
$pdo->exec('TRUNCATE TABLE status');
$pdo->exec('TRUNCATE TABLE order');
$pdo->exec('TRUNCATE TABLE order_line');
$pdo->exec('TRUNCATE TABLE user');
$pdo->exec('TRUNCATE TABLE client');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1'); 
*/

echo "||||||||||||";
$faker = Faker\Factory::create('fr_FR');
echo "-- INSERT --";

$posts = [];
$categories = [];

// post
for ($i = 0; $i < 50; $i++) {
    $pdo->exec("INSERT INTO {$prefix}post SET
        name='{$faker->sentence()}',
        slug='{$faker->slug}',
        created_at ='{$faker->date} {$faker->time}',
        content='{$faker->paragraphs(rand(3, 15), true)}'");
    $posts[] = $pdo->lastInsertId();
    echo "|";
}

// category
for ($i = 0; $i < 20; $i++) {
    $pdo->exec("INSERT INTO {$prefix}category SET
        name='{$faker->sentence(3, false)}',
        slug='{$faker->slug}'");
    $categories[] = $pdo->lastInsertId();
    echo "|";
}

// post_category
foreach ($posts as $post) {
    $randomCategories = $faker->randomElements($categories, 2);
    foreach ($randomCategories as $category) {
        $pdo->exec("INSERT INTO {$prefix}post_category SET
                            post_id={$post},
                            category_id={$category}");
        echo "|";
    }
}

// userblog
$password = password_hash('admin', PASSWORD_BCRYPT);
$token = substr(md5(uniqid()), 0, 24);
echo "-||-";
$pdo->exec("INSERT INTO {$prefix}user SET
        mail='admin@admin.fr',
        password ='{$password}',
        token = 'ADMIN',
        verify = 1
        ");

$pdo->exec("INSERT INTO {$prefix}beer (`id`, `title`, `img`, `content`, `price`, `stock`) VALUES
(1, 'La Chouffe', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/la-chouffe-blonde-d-ardenne_opt.png?h=500&rev=899257661', 'Bière dorée légèrement trouble à mousse dense, avec un parfum épicé aux notes d’agrumes et de coriandre qui ressortent également au goût.', 1.91, 10),
(2, 'Duvel', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/duvel_opt.png?h=500&rev=899257661', 'Robe jaune pâle, légèrement trouble, avec une mousse blanche incroyablement riche. L’arôme associe le citron jaune, le citron vert et les épices. La saveur incorpore des agrumes frais, le sucre de l’alcool et une note épicée due au houblon qui tire sur le poivre. En dépit de son taux d’alcool, c’est une bière fraîche qui se déguste facilement. ', 1.66, 10),
(3, 'Duvel Tripel Hop', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/duvel-tripel-hop-citra.png?h=500&rev=39990364', 'Une variété supplémentaire de houblon est ajoutée à cette Duvel traditionnelle. Le HBC 291 lui procure un caractère légèrement plus épicé et poivré. Cette bière présente un fort taux d’alcool mais reste très facile à déguster grâce à ses arômes d’agrumes frais et acides, entre autres.', 2.24, 10),
(4, 'Tremens', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/blond/delirium_tremens_2.png?h=500&rev=204392068', 'Bière dorée, claire à la mousse blanche pleine. Bière belge classique fortement gazéifiée et alcoolisée à la levure fruitée, arrière-goût doux.', 2.08, 10),
(5, 'Nocturnum', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/delirium_nocturnum.png?h=500&rev=1038477262', 'Une bière rouge foncée brassée selon la tradition belge: à la fois forte et accessible. Des saveurs de fruits secs, de caramel et chocolat. Légèrement sucrée avec une touche épicée (réglisse et coriandre). La finale en bouche est chaude et agréable.', 2.24, 10),
(6, 'Cuvée des Trolls', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/cuvee_des_trolls_2.png?h=500&rev=923839745', 'Bière brumeuse jaune paille à la mousse blanche consistante. Full body aux arômes fruités d’agrumes et de fruits jaunes. Grande douceur et petite touche acide rafraîchissante, levure. ', 1.29, 10),
(7, 'Chimay Rouge', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/chimay---rood_v2.png?h=500&rev=420719671', 'Bière brune à la robe cuivrée avec une mousse durable, délicate et généreuse. Elle présente des arômes fruités de banane. D’autres parfums comme le caramel sucré, le pain frais, le pain grillé et même une touche d’amande sont aussi présents. Les mêmes arômes sucrés se retrouvent au goût et conduisent à une fin de bouche douce et légèrement amère. ', 1.49, 10),
(8, 'Chimay Bleue', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/chimay---blauw_v2.png?h=500&rev=420719671', 'La Chimay Blauw, aussi connue sous le nom de Grande Réserve, est une bière trappiste reconnue. Il s’agissait au départ d’une bière de Noël, mais elle est disponible toute l’année depuis 1954. Une bière puissante et chaleureuse aux arômes de caramel et de fruits secs.', 1.74, 10),
(9, 'Chimay Triple', 'https://www.beerwulf.com/globalassets/catalog/beerwulf/beers/chimay---wit_v2.png?h=500&rev=420719671', 'Robe de couleur doré clair, légèrement trouble avec une belle mousse blanche qui fera saliver les amateurs. Le nez et la bouche sont chargés de fruits comme le raisin et de levure. Une amertume ronde se dégage en fin de bouche.', 1.57, 10)");

echo "-||-";

$pdo->exec("INSERT INTO {$prefix}config (`tva` , `port`, `ship_limit`) VALUES
(1.2, 5.4, 30)
");
echo "-||-";

$pdo->exec("INSERT INTO {$prefix}status (`libelle`) VALUES
('En attente de paiement'),
('En cours de préparation'),
('Expédiée'),
('Terminée')
");

echo "-||]";

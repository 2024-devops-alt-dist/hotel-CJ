<?php

namespace App\DataFixtures;

use App\Entity\Chambre;
use App\Entity\Reservation;
use App\Entity\Hotel;
use App\Entity\Picture;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    // Variable USER ADMIN
    private const ADMIN_EMAIL = "admin@admin.com";
    // private const ADMIN_PASSWORD = "admin";

    // Variable USER GERANTS
    private const NAME_GERANT = ["Margot", "Mathilde", "Adrien", "Maxime"];
    private const EMAIL_GERANT = ["margot@gerant.com", "mathilde@gerant.com", "adrien@gerant.com", "maxime@gerant.com"];

    // Variable Hotel
    private const NAME_HOTELS = [
        "Hôtel du Clair de Lune Toulouse",
        "Hôtel du Clair de Lune Marseille",
        "Hôtel du Clair de Lune Lyon",
        "Hôtel du Clair de Lune Nantes"];
    private const CITY_HOTELS = ["Toulouse","Marseille","Lyon","Nantes"];
    private const ADDRESS_HOTELS = [
        "3 Av. Irène Joliot-Curie, 31100 Toulouse",
        "141 Av. du Maréchal de Lattre de Tassigny, 13009 Marseille",
        "9 Imp. Saint-Loup, 69009 Lyon",
        "Rue de l'Îlot des Grèves, 44100 Nantes"];
    private const DESCRIPTION_HOTELS = [
        "Niché au cœur de la ville rose, l'Hôtel du Clair de Lune Toulouse est un havre de paix alliant charme et modernité. Ses chambres élégantes offrent une vue imprenable sur la nature Toulousaine. Profitez de la cuisine locale dans notre restaurant raffiné, avant de vous détendre dans notre jardin secret. À quelques pas des attractions emblématiques, cet hôtel est l'endroit idéal pour explorer l'âme vibrante de la région.",
        "L'Hôtel du Clair de Lune Marseille est un bijou méditerranéen, parfaitement situé pour admirer la nature. Avec ses chambres spacieuses et lumineuses, cet hôtel vous invite à savourer le rythme de la vie marseillaise. Dégustez des plats locaux dans notre restaurant avec vue sur la mer, ou promenez-vous le long des plages dorées. Un séjour ici vous plongera dans l'énergie et la culture de cette ville fascinante.",
        "Imprégnez-vous de l'élégance lyonnaise à l'Hôtel du Clair de Lune Lyon. Situé dans un quartier calme et verdoyant, cet hôtel allie luxe et confort pour une expérience inoubliable. Ses chambres au design contemporain sont parfaites pour se reposer après une journée à explorer les célèbres bouchons et les sites historiques de Lyon. Découvrez notre bar à vin unique, où vous pourrez déguster des crus locaux tout en admirant la vue sur la ville.",
        "L'Hôtel du Clair de Lune Nantes vous accueille dans un cadre enchanteur, mêlant histoire et modernité. Avec ses chambres élégamment décorées, cet hôtel est idéal pour les voyageurs en quête de tranquillité. À proximité des jardins des Plantes et de la cathédrale de Nantes, profitez de notre service chaleureux et de notre petit-déjeuner buffet copieux pour bien commencer la journée. Un lieu parfait pour découvrir les merveilles de Nantes."];

        // Variable Chambres
        private const TITLE = [
            // Toulouse
            "Chambre Rose Pastel",
            "Suite de la Garonne",
            "Chambre des Artistes",
            // Marseille
            "Chambre Calanques",
            "Suite Vieux-Port",
            "Chambre Olives et Herbes",
            // Lyon
            "Chambre des Bouchons",
            "Suite Bellecour",
            "Chambre Rhône et Saône",
            // Nantes
            "Chambre des Machines",
            "Suite Jardin des Plantes",
            "Chambre Éclats de Loire",
        ];
        private const DESCRIPTION = [
            // Toulouse
            "Cette chambre délicate arbore des teintes douces et apaisantes, créant une atmosphère sereine. Équipée d’un lit king-size et d’un coin détente, elle est parfaite pour un séjour romantique au cœur de la ville rose.",
            "Spacieuse et lumineuse, cette suite offre une vue imprenable sur la Garonne. Son décor moderne et élégant, avec des matériaux naturels, invite à la détente et à l’évasion.",
            "Inspirée par l'effervescence artistique de Toulouse, cette chambre est décorée avec des œuvres d'art locales. Elle propose un espace créatif et inspirant, idéal pour les voyageurs en quête de culture",
            // Marseille
            "Lumineuse et aérée, cette chambre évoque les magnifiques paysages des calanques. Avec ses grandes fenêtres et ses couleurs marines, elle crée un environnement relaxant où il fait bon se ressourcer.",
            "Élégante et raffinée, cette suite offre une vue spectaculaire sur le Vieux-Port de Marseille. Dotée d’un salon séparé et d’un coin repas, elle est parfaite pour un séjour en famille ou entre amis",
            "Cette chambre chaleureuse est inspirée des saveurs méditerranéennes, avec une décoration typiquement provençale. Les parfums d’olive et d’herbes aromatiques créent une ambiance accueillante et conviviale.",
            // Lyon
            "Avec une ambiance chaleureuse et conviviale, cette chambre rend hommage aux célèbres bouchons lyonnais. Elle est équipée d’un lit douillet et d’un coin lecture, parfaite pour les gastronomes en quête d’authenticité.",
            " Modernité et confort définissent cette suite qui offre une vue imprenable sur la colline de la Croix-Rousse. Un espace élégant pour se détendre après une journée d’exploration de Lyon.",
            "Cette chambre apaisante évoque la tranquillité des rivières lyonnaises. Sa décoration douce et ses couleurs naturelles offrent un cadre idéal pour un séjour relaxant.",
            // Nantes
            "Inspirée par l’univers fantastique des machines de l'île de Nantes, cette chambre allie design contemporain et éléments industriels. Un espace unique pour les amateurs d'innovation et d'art.",
            "Avec sa vue imprenable sur le jardin des Plantes, cette suite verdoyante invite à la contemplation. La décoration florale et les matériaux naturels créent un havre de paix en pleine ville.",
            "Évoquant la beauté des paysages de la Loire, cette chambre élégante est décorée avec des nuances douces et naturelles. C'est un lieu où le confort et la sérénité se rencontrent pour une expérience inoubliable.",
        ];

        // Variable Image 
        private const PICTURE_PATH = [
            // Toulouse
                'Chambre Rose Pastel' => [
                    "chambre-rose-pastel-1.jpg",
                    "chambre-rose-pastel-2.jpg",
                    "chambre-rose-pastel-3.jpg",
                    "chambre-rose-pastel-4.jpg"
                ],
                'Suite de la Garonne' => [
                    "suite-de-la-garonne-1.jpg",
                    "suite-de-la-garonne-2.jpg",
                    "suite-de-la-garonne-3.jpg",
                    "suite-de-la-garonne-4.jpg"
                ],
                'Chambre des Artistes' => [
                    "chambre-des-artistes-1.jpg",
                    "chambre-des-artistes-2.jpg",
                    "chambre-des-artistes-3.jpg",
                    "chambre-des-artistes-4.jpg"
                ],
            // Marseille
                'Chambre Calanques' => [
                    "chambre-calanques-1.jpg",
                    "chambre-calanques-2.jpg",
                    "chambre-calanques-3.jpg"
                ],
                'Suite Vieux-Port' => [
                    "suite-vieux-port-1.jpeg",
                    "suite-vieux-port-2.jpeg",
                    "suite-vieux-port-3.jpeg",
                    "suite-vieux-port-4.jpeg"
                ],
                'Chambre Olives et Herbes' => [
                    "chambre-olives-herbes-1.jpg",
                    "chambre-olives-herbes-2.jpg",
                    "chambre-olives-herbes-3.jpg",
                    "chambre-olives-herbes-4.jpg"
                ],
            // Lyon
                'Chambre des Bouchons' => [
                    "chambre-bouchon-1.jpg",                    
                    "chambre-bouchon-2.jpg",
                    "chambre-bouchon-3.jpg"
                ],
                'Suite Bellecour' => [
                    "suite-bellecour-1.jpg",
                    "suite-bellecour-2.jpg",
                    "suite-bellecour-3.jpg"
                ],
                'Chambre Rhône et Saône' => [
                    "chambre-rhone-saone-1.jpg",
                    "chambre-rhone-saone-2.jpg",
                    "chambre-rhone-saone-3.jpg",
                    "chambre-rhone-saone-4.jpg",
                    "chambre-rhone-saone-5.jpg"
                ],
            // Nantes
                'Chambre des Machines' => [
                    "chambre-des-machines-1.jpg",
                    "chambre-des-machines-2.jpg",
                    "chambre-des-machines-3.jpg",
                    "chambre-des-machines-4.jpg"
                ],
                'Suite Jardin des Plantes' => [
                    "suite-jardin-des-plantes-1.jpg",
                    "suite-jardin-des-plantes-2.jpg",
                    "suite-jardin-des-plantes-3.jpg",
                    "suite-jardin-des-plantes-4.jpg"
                ],
                'Chambre Éclats de Loire' => [
                    "chambre-eclat-de-loire-1.jpg",
                    "chambre-eclat-de-loire-2.jpg",
                    "chambre-eclat-de-loire-3.jpg",
                    "chambre-eclat-de-loire-4.jpg"
                ],
        ];
        private const IMG_PRINC = [
            // Toulouse
                // Chambre Rose Pastel
                1, 0, 0, 0,
                // Suite de la Garonne
                1, 0, 0, 0,
                // Chambre des Artistes
                1, 0, 0, 0,
            // Marseille
                // Chambre Calanques
                1, 0, 0,
                // Suite Vieux-Port
                1, 0, 0, 0,
                // Chambre Olives et Herbes
                1, 0, 0, 0,
            // Lyon
                // Chambre des Bouchons
                1, 0, 0,
                // Suite Bellecour
                1, 0, 0,
                // Chambre Rhône et Saône
                1, 0, 0, 0, 0,
            // Nantes
                // Chambre des Machines
                1, 0, 0, 0,
                // Suite Jardin des Plantes
                1, 0, 0, 0,
                // Chambre Éclats de Loire
                1, 0, 0, 0,
        ];

        private $passwordHasher;

        // Injection du service de hashage de mot de passe dans le constructeur
        public function __construct(UserPasswordHasherInterface $passwordHasher)
        {
            $this->passwordHasher = $passwordHasher;
        }

    public function load(ObjectManager $manager): void
    {
        $faker= Factory::create('fr_FR');

    // Fixtures pour les utilisateurs
        // ADMIN
        $admin = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin'
        );
        $admin
            ->setEmail(self::ADMIN_EMAIL)
            ->setPassword($hashedPassword)
            ->setRoles(["ROLE_ADMIN"])
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName());
        
        $manager->persist($admin);

        // GERANTS
        $gerants = [];

        for($i = 0; $i < count(self::NAME_GERANT); $i++) {
            $gerant = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $gerant,
                'gerant'
            );
            $gerant
                ->setEmail(self::EMAIL_GERANT[$i])
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_GERANT"])
                ->setFirstname(self::NAME_GERANT[$i])
                ->setLastname($faker->lastName());
            
            $manager->persist($gerant);
            $gerants[] = $gerant;
        }

        // CLIENTS
        $userclients = [];

        for($i = 0; $i < 10; $i++) {
            $client = new User;
            $hashedPassword = $this->passwordHasher->hashPassword(
                $client,
                'test'
            );
            $client
                ->setEmail($faker->email())
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_USER"])
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName());

            $manager->persist($client);
            $userclients[] = $client;
        }
    
    // Fixtures pour les images
    $pictures = [];
    foreach (self::PICTURE_PATH as $roomName => $images) {
        foreach ($images as $i => $imagePath) {
            $imagePic = new Picture(); 

            $imagePic
                ->setPath($imagePath)
                ->setPrincipale(self::IMG_PRINC[$i]);

            $manager->persist($imagePic);
            $pictures[$roomName][] = $imagePic;
        }}

    // Fixtures pour les hôtels
        $hotels = [];
        for($i = 0; $i < count(self::NAME_HOTELS); $i++) {
            $hotel = new Hotel();
            $hotel
                ->setGerant($gerants[$i])
                ->setName(self::NAME_HOTELS[$i])
                ->setCity(self::CITY_HOTELS[$i])
                ->setAddress(self::ADDRESS_HOTELS[$i])
                ->setDescription(self::DESCRIPTION_HOTELS[$i]);
            
            $manager->persist($hotel);
            $hotels[] = $hotel;
        }
    
    // Fixtures pour les chambres
        $chambresArray = [];
        for($i = 0; $i < count(self::TITLE); $i++) {
            $chambres = new Chambre();
            $chambres
                ->setTitle(self::TITLE[$i])
                ->setDescription(self::DESCRIPTION[$i])
                ->setDisponible(true)
                ->setPrice($faker->numberBetween(50, 356)) 
                ->setHotel($hotels[intval($i / 3)]);
            
            $manager->persist($chambres);
            $chambresArray[] = $chambres;

            // Associer les images à la chambre
        if (isset($pictures[self::TITLE[$i]])) {
            foreach ($pictures[self::TITLE[$i]] as $picture) {
                $chambres->addImage($picture); 
            }
        }
        }

    // Fixtures pour les réservations
        $occupiedRooms = []; 
        for ($i = 0; $i < 5; $i++) {
            $reservation = new Reservation(); 
            $startDate = $faker->dateTimeBetween('now', '+1 month');
            $endDate = (clone $startDate)->modify('+' . $faker->numberBetween(1, 10) . ' days'); 

            // Trouver une chambre disponible pour cette période
            $availableRooms = [];
            foreach ($chambresArray as $room) {
                // Vérifier la disponibilité de la chambre
                $isAvailable = true;
                foreach ($occupiedRooms as $occupied) {
                    if ($occupied['room'] === $room && 
                        !($endDate < $occupied['start'] || $startDate > $occupied['end'])) {
                        $isAvailable = false;
                        break;
                    }
                }
                if ($isAvailable) {
                    $availableRooms[] = $room;
                }
            }

            // Si des chambres sont disponibles, en choisir une
            if (!empty($availableRooms)) {
                $chosenRoom = $availableRooms[array_rand($availableRooms)];
                $reservation->addChambre($chosenRoom);
                $totalPrice = $chosenRoom->getPrice() * (int) $endDate->diff($startDate)->format('%d');
                $occupiedRooms[] = ['room' => $chosenRoom, 'start' => $startDate, 'end' => $endDate];
            }

            $reservation
                ->setDateStart($startDate)
                ->setDateEnd($endDate)
                ->setUser($userclients[$i])
                ->setTotalPrice($totalPrice); 

            $manager->persist($reservation);
        }

        $manager->flush();
    }
}


// Données des jeux (recherche + fiche)
(function () {
    const data = {
        'counter-strike-2': {
            name: 'Counter-Strike 2',
            price: 'Free',
            date: '2023-09-27',
            image: './img/games/CS2.png',
            downloadUrl: 'https://store.steampowered.com/app/730/CounterStrike_2/',
            longDesc: "Counter-Strike 2 modernise la formule competitive culte de Valve avec une architecture graphique plus recente, un gameplay ultra precis et une scene e-sport toujours active. C'est un excellent choix pour les joueurs qui aiment progresser sur la strategie d'equipe, la communication et la maitrise mecanique.",
            trailer: 'https://www.youtube.com/embed/edYCtaNueQY'
        },
        'red-dead-redemption-2': {
            name: 'Red Dead Redemption 2',
            price: '59EUR',
            date: '2018-10-26',
            image: './img/games/rdr2.jpg',
            downloadUrl: 'https://store.steampowered.com/app/1174180/Red_Dead_Redemption_2/',
            longDesc: "Red Dead Redemption 2 propose un immense monde ouvert riche en details, des personnages marquants et une narration cinematographique. Entre missions, exploration libre, chasse, quetes secondaires et decouverte de l'Ouest americain, le jeu offre une aventure longue et immersive.",
            trailer: 'https://www.youtube.com/embed/eaW0tYpxyp0'
        },
        'silent-hill-2-remake': {
            name: 'Silent Hill 2 Remake',
            price: '79EUR',
            date: '2024',
            image: './img/games/SH2R.jpg',
            downloadUrl: 'https://store.steampowered.com/app/2124490/SILENT_HILL_2/',
            longDesc: "Silent Hill 2 Remake reinterprete un classique du survival horror avec une mise en scene plus moderne et une ambiance psychologique oppressante. Le jeu met l'accent sur l'atmosphere, l'exploration et les combats tendus, avec une narration centree sur la culpabilite et le traumatisme.",
            trailer: 'https://www.youtube.com/watch?v=xRShSWXFDFA'
        },
        'resident-evil-requiem': {
            name: 'Resident Evil Requiem',
            price: '79EUR',
            date: '2026',
            image: './img/games/RE9.jpg',
            downloadUrl: 'https://www.residentevil.com/',
            longDesc: "Resident Evil Requiem poursuit la tradition de la serie avec de nouvelles zones dangereuses, des ressources limitees et des affrontements intenses. Ce type d'opus melange en general action et horreur de survie avec une progression tendue et de nombreux moments memorables.",
            trailer: 'https://www.youtube.com/watch?v=T54OWinnymM'
        },
        'cyberpunk-2077': {
            name: 'Cyberpunk 2077',
            price: '39EUR',
            date: '2020-12-10',
            image: './img/games/cyberpunk2077.jpg',
            downloadUrl: 'https://store.steampowered.com/app/1091500/Cyberpunk_2077/',
            longDesc: "Cyberpunk 2077 plonge le joueur dans Night City, une megapole futuriste ou l'ambition, la technologie et la violence cohabitent. Avec ses quetes narratives, ses choix de dialogue et sa progression RPG, le jeu offre plusieurs facons de jouer selon le style que tu preferes.",
            trailer: 'https://www.youtube.com/watch?v=F1nxEGme7e8'
        },
        'metal-gear-solid-3': {
            name: 'Metal Gear Solid 3',
            price: '59EUR',
            date: '2025',
            image: './img/games/mgs3.jpg',
            downloadUrl: 'https://store.steampowered.com/app/2417610/METAL_GEAR_SOLID_Delta_SNAKE_EATER/',
            longDesc: "Metal Gear Solid 3 est une reference du jeu d'infiltration, connue pour son ambiance espionnage et sa mise en scene forte. Dans cette version moderne, on retrouve une aventure tactique basee sur la discretion, l'observation et l'adaptation a l'environnement.",
            trailer: 'https://www.youtube.com/watch?v=cGJ-vqsG4Js'
        },
        'the-legend-of-zelda-breath-of-the-wild': {
            name: 'The Legend of Zelda: Breath of the Wild',
            price: '59EUR',
            date: '2017-03-03',
            image: './img/games/TLOZBOTW.jpg',
            downloadUrl: 'https://www.nintendo.com/store/products/the-legend-of-zelda-breath-of-the-wild-switch/',
            longDesc: "Breath of the Wild reinvente l'exploration en monde ouvert avec une grande liberte d'approche. Chaque montagne, sanctuaire ou village invite a la curiosite, et les systemes physiques du jeu permettent des solutions creatives en combat comme en exploration.",
            trailer: 'https://www.youtube.com/embed/zw47_q9wbBE'
        },
        'god-of-war-ragnarok': {
            name: 'God of War Ragnarok',
            price: '59EUR',
            date: '2022-11-09',
            image: './img/games/GOWR.jpg',
            downloadUrl: 'https://www.playstation.com/games/god-of-war-ragnarok/',
            longDesc: "God of War Ragnarok poursuit l'histoire de Kratos et Atreus dans une aventure epique inspiree de la mythologie nordique. Le jeu combine combats nerveux, mise en scene spectaculaire et narration emotionnelle avec des environnements tres soignes.",
            trailer: 'https://www.youtube.com/embed/EE-4GvjKcfs'
        },
        'clair-obscur-expedition-33': {
            name: 'Clair Obscur: Expedition 33',
            price: '79EUR',
            date: '2025',
            image: './img/games/expedition33.jpg',
            downloadUrl: 'https://store.steampowered.com/app/1903340/Clair_Obscur_Expedition_33/',
            longDesc: "Clair Obscur: Expedition 33 mise sur une direction artistique marquee et un univers original. Le jeu propose un RPG narratif ou les combats, la construction de groupe et l'ambiance visuelle contribuent a une experience plus dramatique et immersive.",
            trailer: 'https://www.youtube.com/embed/rmDvdB0ZXZc'
        },
        'the-last-of-us-part-ii': {
            name: 'The Last of Us Part II',
            price: '49EUR',
            date: '2020-06-19',
            image: './img/games/tlou2.jpg',
            downloadUrl: 'https://www.playstation.com/games/the-last-of-us-part-ii-remastered/',
            longDesc: "The Last of Us Part II est une aventure narrative intense axee sur la survie, les consequences morales et des personnages complexes. Le gameplay alterne infiltration, affrontements brutaux et exploration, avec une mise en scene tres cinematographique.",
            trailer: 'https://www.youtube.com/watch?v=vhII1qlcZ4E'
        },
        'fallout-new-vegas': {
            name: 'Fallout: New Vegas',
            price: '19EUR',
            date: '2010-10-22',
            image: './img/games/FNV.jpg',
            downloadUrl: 'https://store.steampowered.com/app/22380/Fallout_New_Vegas/',
            longDesc: "Fallout: New Vegas est un RPG culte qui valorise les choix du joueur et leurs consequences. Entre factions rivales, quetes alternatives et dialogues tres riches, il reste une reference pour les amateurs de roleplay et d'univers post-apocalyptique.",
            trailer: 'https://www.youtube.com/embed/l-x-1fm2cq8'
        },
        'the-witcher-3-wild-hunt': {
            name: 'The Witcher 3: Wild Hunt',
            price: '39EUR',
            date: '2015-05-19',
            image: './img/games/TW3.jpg',
            downloadUrl: 'https://store.steampowered.com/app/292030/The_Witcher_3_Wild_Hunt/',
            longDesc: "The Witcher 3 combine quetes memorables, combats dynamiques et exploration d'un vaste monde medieval-fantastique. Le jeu se distingue surtout par la qualite de ses histoires secondaires et la profondeur de son univers.",
            trailer: 'https://www.youtube.com/embed/c0i88t0Kacs'
        },
        'grand-theft-auto-v': {
            name: 'Grand Theft Auto V',
            price: '29EUR',
            date: '2013-09-17',
            image: './img/games/gta5.jpg',
            downloadUrl: 'https://store.steampowered.com/app/271590/Grand_Theft_Auto_V/',
            longDesc: "GTA V propose une campagne solo ambitieuse, un monde ouvert vivant et un mode en ligne extremement populaire. Entre missions, conduite, activites annexes et contenu communautaire, c'est un jeu tres complet qui se renouvelle facilement.",
            trailer: 'https://www.youtube.com/embed/QkkoHAzjnUs'
        },
        'battlefield-6': {
            name: 'Battlefield 6',
            price: '59EUR',
            date: '2025-10-15',
            image: './img/games/Battlefield6.png',
            downloadUrl: 'https://www.ea.com/games/battlefield',
            longDesc: "Battlefield mise historiquement sur de grandes batailles multijoueur avec vehicules, destruction et jeu en escouade. Cet opus est pense pour les joueurs qui aiment les affrontements a grande echelle et la cooperation tactique.",
            trailer: 'https://www.youtube.com/watch?v=pgNCgJG0vnY'
        },
        'forza-horizon-5': {
            name: 'Forza Horizon 5',
            price: '49EUR',
            date: '2021-11-09',
            image: './img/games/FH5.jpg',
            downloadUrl: 'https://store.steampowered.com/app/1551360/Forza_Horizon_5/',
            longDesc: "Forza Horizon 5 offre un monde ouvert vaste et coloré inspire du Mexique, avec une conduite accessible mais exigeante a haut niveau. Les courses, defis, evenements saisonniers et options de personnalisation en font une reference arcade-simulation.",
            trailer: 'https://www.youtube.com/embed/d_20X1YM28U'
        },
        'hearts-of-iron-iv': {
            name: 'Hearts of Iron IV',
            price: '39EUR',
            date: '2016-06-06',
            image: './img/games/HOI4.jpg',
            downloadUrl: 'https://store.steampowered.com/app/394360/Hearts_of_Iron_IV/',
            longDesc: "Hearts of Iron IV est un jeu de grande strategie focalise sur la Seconde Guerre mondiale. Gestion economique, diplomatie, doctrine militaire et production sont au coeur de l'experience pour les joueurs qui aiment la profondeur strategique.",
            trailer: 'https://www.youtube.com/watch?v=F-uGP2DkZKE'
        },
        'bodycam': {
            name: 'Bodycam',
            price: '29EUR',
            date: '2024-05-01',
            image: './img/games/BODYCAM.jpg',
            downloadUrl: 'https://store.steampowered.com/app/2406770/Bodycam/',
            longDesc: "Bodycam se distingue par son rendu tres realiste et sa perspective immersive. Le gameplay met l'accent sur la tension, les duels rapides et la precision, ce qui en fait un titre ideal pour les amateurs de shooters tactiques intenses.",
            trailer: 'https://www.youtube.com/embed/oMPf9iL8Rqk'
        },
        'ghost-of-tsushima': {
            name: 'Ghost of Tsushima',
            price: '59EUR',
            date: '2024-05-16',
            image: './img/games/GOT.jpg',
            downloadUrl: 'https://store.steampowered.com/app/2215430/Ghost_of_Tsushima_DIRECTORS_CUT/',
            longDesc: "Ghost of Tsushima combine exploration poetique, combats au sabre tres lisibles et narration inspiree du cinema japonais. Son monde ouvert et son ambiance sonore soignee en font une experience contemplative autant qu'action.",
            trailer: 'https://www.youtube.com/embed/Zbq7BnsQhrw'
        },
        'euro-truck-simulator-2': {
            name: 'Euro Truck Simulator 2',
            price: '19EUR',
            date: '2012-10-19',
            image: './img/games/ETS2.jpg',
            downloadUrl: 'https://store.steampowered.com/app/227300/Euro_Truck_Simulator_2/',
            longDesc: "Euro Truck Simulator 2 propose une simulation relaxante et detaillee de transport routier en Europe. Entre gestion de flotte, optimisation de trajets et conduite longue distance, le jeu offre un rythme calme mais tres addictif.",
            trailer: 'https://www.youtube.com/watch?v=5uvwfskYwl0'
        },
        'helldivers-2': {
            name: 'Helldivers 2',
            price: '39EUR',
            date: '2024-02-08',
            image: './img/games/Helldivers.jpg',
            downloadUrl: 'https://store.steampowered.com/app/553850/HELLDIVERS_2/',
            longDesc: "Helldivers 2 est un shooter cooperatif nerveux ou la coordination d'equipe fait toute la difference. Les missions dynamiques, l'humour militaire satirique et les outils strategiques creent des parties chaotiques et memorables.",
            trailer: 'https://www.youtube.com/watch?v=UC5EpJR0GBQ'
        },
        'dead-space-remake': {
            name: 'Dead Space Remake',
            price: '59EUR',
            date: '2023-01-27',
            image: './img/games/DeadSpace.jpg',
            downloadUrl: 'https://store.steampowered.com/app/1693980/Dead_Space/',
            longDesc: "Dead Space Remake remet au gout du jour un classique du survival horror spatial avec une ambiance sonore glaçante et des affrontements oppressants. La progression dans l'USG Ishimura reste une reference en matiere de tension et d'immersion.",
            trailer: 'https://www.youtube.com/watch?v=ctQl9wa3ydE'
        }
    };

    window.GAME_DETAILS = data;
})();

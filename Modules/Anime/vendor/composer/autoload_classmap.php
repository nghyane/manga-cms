<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'Modules\\Anime\\Console\\AnimeUploader' => $baseDir . '/Console/AnimeUploader.php',
    'Modules\\Anime\\Console\\HentaiHaven' => $baseDir . '/Console/HentaiHaven.php',
    'Modules\\Anime\\Console\\ScraperInit' => $baseDir . '/Console/ScraperInit.php',
    'Modules\\Anime\\Crawlers\\WatchHentai' => $baseDir . '/Crawlers/WatchHentai.php',
    'Modules\\Anime\\Database\\Seeders\\AnimeDatabaseSeeder' => $baseDir . '/Database/Seeders/AnimeDatabaseSeeder.php',
    'Modules\\Anime\\Database\\factories\\AnimeFactory' => $baseDir . '/Database/factories/AnimeFactory.php',
    'Modules\\Anime\\Entities\\Anime' => $baseDir . '/Entities/Anime.php',
    'Modules\\Anime\\Entities\\Country' => $baseDir . '/Entities/Country.php',
    'Modules\\Anime\\Entities\\Episode' => $baseDir . '/Entities/Episode.php',
    'Modules\\Anime\\Entities\\EpisodeQueue' => $baseDir . '/Entities/EpisodeQueue.php',
    'Modules\\Anime\\Entities\\Genres' => $baseDir . '/Entities/Genres.php',
    'Modules\\Anime\\Entities\\Studio' => $baseDir . '/Entities/Studio.php',
    'Modules\\Anime\\Entities\\Tag' => $baseDir . '/Entities/Tag.php',
    'Modules\\Anime\\Entities\\Video' => $baseDir . '/Entities/Video.php',
    'Modules\\Anime\\Http\\Controllers\\AnimeController' => $baseDir . '/Http/Controllers/AnimeController.php',
    'Modules\\Anime\\Http\\Controllers\\ApiController' => $baseDir . '/Http/Controllers/ApiController.php',
    'Modules\\Anime\\Http\\Controllers\\HomeController' => $baseDir . '/Http/Controllers/HomeController.php',
    'Modules\\Anime\\Jobs\\DownloadM3u8' => $baseDir . '/Jobs/DownloadM3u8.php',
    'Modules\\Anime\\Jobs\\UploadBlogger' => $baseDir . '/Jobs/UploadBlogger.php',
    'Modules\\Anime\\Providers\\AnimeServiceProvider' => $baseDir . '/Providers/AnimeServiceProvider.php',
    'Modules\\Anime\\Providers\\RouteServiceProvider' => $baseDir . '/Providers/RouteServiceProvider.php',
    'Modules\\Anime\\Services\\AnimeCrawler' => $baseDir . '/Services/AnimeCrawler.php',
    'Modules\\Anime\\Services\\Hentaihaven' => $baseDir . '/Services/Hentaihaven.php',
    'Modules\\Anime\\View\\Components\\ItemList' => $baseDir . '/View/Components/ItemList.php',
);

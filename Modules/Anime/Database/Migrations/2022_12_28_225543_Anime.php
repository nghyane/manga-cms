<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// cache
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // anime table migration: id (int), name (string), description (text), slug (string), meta_data (json), created_at (timestamp), updated_at (timestamp)
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            // status: 1 = ongoing, 2 = completed, 0 = unknown
            $table->tinyInteger('status')->default(0);
            // type: tv, movie, ova, etc ...
            $table->string('type')->default('tv');

            $table->string('adult')->default('false');

            $table->text('description')->nullable()->default(null);
            $table->timestamps();
        }); // to create anime model run: php artisan make:model Anime -m

        // episode table migration: id (int), anime_id (int), name (string), slug (string), meta_data (json), created_at (timestamp), updated_at (timestamp)
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->text('description')->nullable()->default(null); // episode description (optional)
            $table->string('name');
            $table->double('number')->default(0); // episode number
            $table->tinyInteger('subbed')->default(0); // has subbed version
            $table->tinyInteger('dubbed')->default(0); // has dubbed version
            $table->string('slug');
            $table->string('thumbnail')->nullable()->default(null); // episode thumbnail (optional)

            $table->timestamps();
        }); // to create episode model run: php artisan make:model Episode -m

        // video table migration: id (int), episode_id (int), type (string), url, subtitle (string), language (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained('episodes');
            $table->string('type')->default('embed');
            $table->string('url');
            $table->string('subtitle')->nullable()->default(null);
            // subtitle language - json format [{language: 'English', url: 'https://example.com/subtitle.srt'}}]
            $table->string('language')->nullable()->default(null);
            $table->string('server')->nullable()->default(null);

            $table->tinyInteger('dubbed')->default(0);
            $table->tinyInteger('subbed')->default(0);

            $table->timestamps();
        });

        // tags table migration: id (int), name (string), slug (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        // anime_tags table migration: id (int), anime_id (int), tag_id (int), created_at (timestamp), updated_at (timestamp)
        Schema::create('anime_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->foreignId('tag_id')->constrained('tags');
            $table->timestamps();
        });

        // genres table migration: id (int), name (string), slug (string), description (text), created_at (timestamp), updated_at (timestamp)
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable()->default(null);
            $table->timestamps();
        });

        // anime_genres table migration: id (int), anime_id (int), genre_id (int), created_at (timestamp), updated_at (timestamp)
        Schema::create('anime_genres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->foreignId('genre_id')->constrained('genres');
            $table->timestamps();
        });


        // studio table migration: id (int), name (string), slug (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });

        // anime_studios table migration: id (int), anime_id (int), studio_id (int), created_at (timestamp), updated_at (timestamp)
        Schema::create('anime_studios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->foreignId('studio_id')->constrained('studios');
            $table->timestamps();
        });

        // countries table migration: id (int), name (string), slug (string), code (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('code');
            $table->timestamps();
        });

        // anime_countries table migration: id (int), anime_id (int), country_id (int), created_at (timestamp), updated_at (timestamp)
        Schema::create('anime_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->foreignId('country_id')->constrained('countries');
            $table->timestamps();
        });

        // schedule table migration: id (int), anime_id (int), day (string), time (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->string('day');
            $table->string('time');
            $table->timestamps();
        });


        // episodes quque table migration: id (int), episode_id (int), status (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('episodes_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained('episodes');
            $table->string('status');
            $table->string('url');
            $table->string('source');
            $table->timestamps();
        }); // to create episode model run: php artisan make:model EpisodeQueue -m
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule');

        Schema::dropIfExists('anime_genres');
        Schema::dropIfExists('genres');

        Schema::dropIfExists('anime_tags');
        Schema::dropIfExists('tags');

        Schema::dropIfExists('episodes_queue');
        Schema::dropIfExists('video');
        Schema::dropIfExists('episodes');

        Schema::dropIfExists('anime_studios');
        Schema::dropIfExists('studios');

        Schema::dropIfExists('anime_countries');
        Schema::dropIfExists('countries');

        Schema::dropIfExists('animes');


        // clear cache
        Cache::flush();
    }
};

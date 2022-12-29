<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->text('description')->nullable()->default(null);
            $table->timestamps();
        }); // to create anime model run: php artisan make:model Anime -m

        // episode table migration: id (int), anime_id (int), name (string), slug (string), meta_data (json), created_at (timestamp), updated_at (timestamp)
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->text('description')->nullable()->default(null);
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        }); // to create episode model run: php artisan make:model Episode -m

        // video table migration: id (int), episode_id (int), type (string), url, subtitle (string), language (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained('episodes');
            $table->string('type');
            $table->string('url');
            $table->string('subtitle');
            $table->string('language');
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

        // schedule table migration: id (int), anime_id (int), day (string), time (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->string('day');
            $table->string('time');
            $table->timestamps();
        });

        // anime_metas table migration: id (int), anime_id (int), meta_key (string), meta_value (string), created_at (timestamp), updated_at (timestamp)
        Schema::create('anime_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained('animes');
            $table->string('meta_key');
            $table->string('meta_value');
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
        Schema::dropIfExists('anime_metas');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('video');
        Schema::dropIfExists('episodes_queue');
        Schema::dropIfExists('episodes');

        Schema::dropIfExists('animes');
    }
};

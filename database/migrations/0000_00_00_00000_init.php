<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('music_providers', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->unique();
        });

        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->unsignedSmallInteger('provider_id');
            $table->string('provider_key');
            $table->string('title');
            $table->string('image');
            $table->boolean('urge')->default(0);
            $table->timestamps();

            $table->foreign('provider_id')
                ->references('id')
                ->on('music_providers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::create('track_genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('genre_track', function (Blueprint $table) {
            $table->unsignedBigInteger('track_id');
            $table->unsignedBigInteger('genre_id');
            $table->primary(['track_id', 'genre_id']);

            $table->foreign('track_id')
                ->references('id')
                ->on('tracks')
                ->cascadeOnDelete();

            $table->foreign('genre_id')
                ->references('id')
                ->on('track_genres')
                ->cascadeOnDelete();
        });

        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->unsignedSmallInteger('provider_id');
            $table->string('provider_key');
            $table->string('title');
            $table->timestamps();

            $table->foreign('provider_id')
                ->references('id')
                ->on('music_providers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->unique();
        });

        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('country_id');
            $table->string('url');
            $table->text('links');
            $table->timestamps();

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::create('label_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('tag_label', function (Blueprint $table) {
            $table->unsignedBigInteger('label_id');
            $table->unsignedBigInteger('tag_id');
            $table->primary(['label_id', 'tag_id']);

            $table->foreign('label_id')
                ->references('id')
                ->on('labels')
                ->cascadeOnDelete();

            $table->foreign('tag_id')
                ->references('id')
                ->on('label_tags')
                ->cascadeOnDelete();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('country_id');
            $table->string('url');
            $table->text('links');
            $table->timestamps();

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::create('store_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('tag_store', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('tag_id');
            $table->primary(['store_id', 'tag_id']);

            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->cascadeOnDelete();

            $table->foreign('tag_id')
                ->references('id')
                ->on('store_tags')
                ->cascadeOnDelete();
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('country_id');
            $table->string('url');
            $table->text('links');
            $table->timestamps();

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::create('bookmark_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('tag_bookmark', function (Blueprint $table) {
            $table->unsignedBigInteger('bookmark_id');
            $table->unsignedBigInteger('tag_id');
            $table->primary(['bookmark_id', 'tag_id']);

            $table->foreign('bookmark_id')
                ->references('id')
                ->on('bookmarks')
                ->cascadeOnDelete();

            $table->foreign('tag_id')
                ->references('id')
                ->on('bookmark_tags')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_resets');

        Schema::dropIfExists('music_providers');

        Schema::dropIfExists('tracks');
        Schema::dropIfExists('track_genres');
        Schema::dropIfExists('genre_track');

        Schema::dropIfExists('playlists');

        Schema::dropIfExists('countries');

        Schema::dropIfExists('labels');
        Schema::dropIfExists('label_tags');
        Schema::dropIfExists('tag_label');

        Schema::dropIfExists('stores');
        Schema::dropIfExists('store_tags');
        Schema::dropIfExists('tag_store');

        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('bookmark_tags');
        Schema::dropIfExists('tag_bookmark');
    }
};

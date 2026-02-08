# Makefile for Docker + Laravel setup

# Name of the PHP/Laravel container
APP_CONTAINER=laravel_app

# Name of the DB container
DB_CONTAINER=db

# Laravel environment
ENV_FILE=.env

# Default target
.PHONY: setup
setup: build composer migrate permissions
	@echo "✅ Laravel Docker setup complete! Visit http://localhost"

# Build Docker containers
.PHONY: build
build:
	@echo "🛠️  Building Docker containers..."
	docker compose up -d --build

# Install PHP dependencies via Composer
.PHONY: composer
composer:
	@echo "📦 Installing PHP dependencies (Composer)..."
	docker exec -it $(APP_CONTAINER) composer install --no-dev --optimize-autoloader

# Run Laravel migrations and seeders
.PHONY: migrate
migrate:
	@echo "🗄️  Running migrations..."
	docker exec -it $(APP_CONTAINER) php artisan migrate --force

# Set storage and cache permissions
.PHONY: permissions
permissions:
	@echo "🔑 Setting storage permissions..."
	docker exec -it $(APP_CONTAINER) sh -c "chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache"

# Generate application key if missing
.PHONY: key
key:
	@echo "🔑 Generating Laravel application key..."
	docker exec -it $(APP_CONTAINER) php artisan key:generate

# Reset the database (drops + migrates)
.PHONY: reset-db
reset-db:
	@echo "🧹 Resetting database..."
	docker exec -it $(APP_CONTAINER) php artisan migrate:fresh --seed

# Stop and remove containers, networks, and volumes
.PHONY: clean
clean:
	@echo "🧹 Cleaning up Docker containers and volumes..."
	docker compose down -v

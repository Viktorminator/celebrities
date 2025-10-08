# Celebrity Spotlight Laravel - Docker Setup

This project includes Docker configuration for easy deployment and development.

## Quick Start

### Prerequisites
- Docker Desktop for Mac (with Apple Silicon support)
- Docker Compose

### Setup Instructions

1. **Clone and navigate to the project:**
   ```bash
   cd CelebritySpotlightLaravel
   ```

2. **Copy environment file:**
   ```bash
   cp env.example .env
   ```

3. **Generate application key:**
   ```bash
   docker-compose run --rm app php artisan key:generate
   ```

4. **Build and start containers:**
   ```bash
   docker-compose up -d --build
   ```

5. **Run database migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

6. **Seed the database (optional):**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

7. **Create storage link:**
   ```bash
   docker-compose exec app php artisan storage:link
   ```

## Access Points

- **Application:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8080
- **Database:** localhost:3306
- **Redis:** localhost:6379

## Container Services

- **app:** Laravel application with Apache, PHP 8.2, Node.js
- **database:** MySQL 8.0 database
- **redis:** Redis cache/session store
- **phpmyadmin:** Database management interface

## Useful Commands

### Development
```bash
# View logs
docker-compose logs -f app

# Access application container
docker-compose exec app bash

# Run Artisan commands
docker-compose exec app php artisan [command]

# Install Composer packages
docker-compose exec app composer install

# Install NPM packages
docker-compose exec app npm install

# Build assets
docker-compose exec app npm run build
```

### Database
```bash
# Access database
docker-compose exec database mysql -u laravel -p celebrity_spotlight

# Reset database
docker-compose exec app php artisan migrate:fresh --seed
```

### Queue Management
```bash
# Process queue
docker-compose exec app php artisan queue:work

# Clear failed jobs
docker-compose exec app php artisan queue:flush
```

## Environment Configuration

The Docker setup uses the following default configuration:

- **Database:** MySQL with user `laravel` and password `laravelpassword`
- **Cache/Sessions:** Redis
- **Queue:** Database driver
- **Storage:** Local filesystem

Modify the `docker-compose.yml` and `.env` files as needed for your environment.

## Production Considerations

For production deployment:

1. Change default passwords in `docker-compose.yml`
2. Set `APP_DEBUG=false` in `.env`
3. Use proper SSL certificates
4. Configure proper logging
5. Set up proper backup strategies
6. Use Docker secrets for sensitive data

## Troubleshooting

### Apple Silicon (ARM64) Mac Issues

If you encounter platform compatibility issues:

1. **Force ARM64 platform for all services:**
   ```bash
   docker-compose build --build-arg BUILDPLATFORM=linux/arm64
   ```

2. **Check Docker Desktop settings:**
   - Ensure "Use Rosetta for x86/amd64 emulation" is enabled
   - Or use "Use Apple Virtualization Framework" for better ARM64 support

### Common Issues

1. **Permission errors:**
   ```bash
   docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Database connection issues:**
   - Ensure database container is running: `docker-compose ps`
   - Check database credentials in `.env`

3. **Asset build issues:**
   ```bash
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

4. **Clear all caches:**
   ```bash
   docker-compose exec app php artisan cache:clear
   docker-compose exec app php artisan config:clear
   docker-compose exec app php artisan view:clear
   ```


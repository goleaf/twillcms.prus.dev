#!/bin/bash

echo "Cleaning database and migrations..."

# Remove database file if it exists
if [ -f "database/database.sqlite" ]; then
    rm database/database.sqlite
    echo "Removed existing database file"
fi

# Create fresh database file
touch database/database.sqlite
echo "Created fresh database file"

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Database cleanup complete!"

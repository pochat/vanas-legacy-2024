#!/bin/bash

# Variables
BACKUP_DIR="/var/www/html/vanas/backup"
MYSQL_USER="vanas"
MYSQL_PASSWORD="D#v3L0p3rr2025"
DATABASE="vanas_prod"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Realizar respaldo
mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD $DATABASE | gzip > $BACKUP_DIR/$DATABASE-$TIMESTAMP.sql.gz

# Opcional: Borrar respaldos antiguos (más de 7 días)
find $BACKUP_DIR -name "*.sql.gz" -type f -mtime +7 -delete
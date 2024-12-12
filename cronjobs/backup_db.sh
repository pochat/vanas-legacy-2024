#!/bin/bash

# Variables
BACKUP_DIR="/efs/vanas_uploads/backup_db"
MYSQL_USER="vanas"
MYSQL_PASSWORD="D#v3L0p3rr2025"
DATABASE="vanas_prod"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Realizar respaldo
mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD $DATABASE > $BACKUP_DIR/vanas_prod-$TIMESTAMP.sql

mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD vanas_plus > $BACKUP_DIR/vanas_plus-$TIMESTAMP.sql

# Opcional: Borrar respaldos antiguos (más de 7 días)
find $BACKUP_DIR -name "*.sql" -type f -mtime +7 -delete

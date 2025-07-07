#!/bin/bash

# Arranca el WebSocket en segundo plano
php notificaciones-server.php &

# Sirve archivos PHP como ping.php en el puerto asignado por Railway
php -S 0.0.0.0:$PORT

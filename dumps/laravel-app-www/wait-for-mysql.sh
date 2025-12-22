#!/bin/sh

echo "⏳ Esperando a MySQL..."

until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e 'select 1;' "$DB_DATABASE"; do
  >&2 echo "❌ MySQL no está listo. Esperando..."
  sleep 3
done

echo "✅ MySQL está listo. Iniciando Laravel..."
exec "$@"

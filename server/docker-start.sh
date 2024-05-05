#!/usr/bin/dumb-init /bin/sh
set -e
cd /opt/app

alembic upgrade head

echo "Starting szurubooru API on port ${PORT} - Running on ${THREADS} threads"
waitress-serve-3 --port ${PORT} --threads ${THREADS} --expose-tracebacks szurubooru.facade:app

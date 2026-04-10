#!/bin/bash
set -e

# O Railway passa a variável $PORT (geralmente 8080).
# Vamos forçar o Apache a escutar nela.
PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Se a chave da aplicação não foi setada (a princípio foi pelo `.env`, 
# mas se não estiver em ambiente de execução a gente tenta criar):
if [ -z "$APP_KEY" ]; then
    echo "Gerando APP_KEY..."
    php artisan key:generate --force
fi

echo "Executando migrações no banco..."
# Só falhá se de fato houver um erro crítico. `|| true` garante que se der erro ele tenta seguir, 
# embora o ideal num pipeline seja falhar. O `--force` é obrigatório pra rodar em produção.
php artisan migrate --force

echo "Fazendo cache das configurações para performance..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Limpando conflitos de modulos MPM do Apache..."
a2dismod mpm_event mpm_worker || true
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_worker.load

echo "Iniciando Apache..."
# Executa o comando passado pelo CMD do Dockerfile (apache2-foreground)
exec "$@"

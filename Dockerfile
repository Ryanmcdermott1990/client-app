FROM php:8
ENV PORT=8080
RUN apt-get update; apt-get install -y wget libzip-dev
RUN docker-php-ext-install zip pdo_mysql
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer -O - -q | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY . /app
RUN composer install
ENV NODE_VERSION=18.0.0
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version
RUN npm i
RUN npm run dev 
RUN touch /app/database/database.sqlite
RUN DB_CONNECTION=sqlite php artisan migrate
RUN echo "#!/bin/sh\n" \
  "php artisan migrate\n" \
  "php artisan serve --host 0.0.0.0 --port \$PORT" > /app/start.sh
RUN chmod +x /app/start.sh
CMD ["/app/start.sh"]
FROM ghcr.io/sylius/sylius-php:8.3-fixuid-xdebug-alpine

# Switch to root to install packages
USER root

# Install Chromium + fonts + required deps
RUN apk add --no-cache \
      chromium \
      chromium-chromedriver \
      nss \
      freetype \
      harfbuzz \
      ca-certificates \
      ttf-freefont \
      wqy-zenhei \
      && fc-cache -f

# Switch back to default user
USER sylius

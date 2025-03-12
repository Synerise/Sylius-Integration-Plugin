ARG WORKING_DIR=/srv/sylius

FROM nginx:stable-alpine

ARG WORKING_DIR

ENV WORKING_DIR=${WORKING_DIR}
ENV FASTCGI_PASS=php:9000

RUN mkdir -p ${WORKING_DIR}

RUN echo 'copy config'

COPY nginx/templates/default.conf.template /etc/nginx/templates/

WORKDIR ${WORKING_DIR}

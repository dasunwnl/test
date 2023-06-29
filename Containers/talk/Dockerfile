FROM nats:2.9.19-scratch as nats
FROM strukturag/nextcloud-spreed-signaling:1.1.2 as signaling

FROM alpine:3.18.2 as janus
ARG JANUS_VERSION=v0.13.4
WORKDIR /src
RUN apk add --no-cache \
        ca-certificates \
        git autoconf automake build-base pkgconfig libtool util-linux \
        glib-dev zlib-dev openssl-dev jansson-dev libnice-dev libconfig-dev libsrtp-dev gengetopt-dev \
        libwebsockets-dev; \
        git clone --recursive https://github.com/meetecho/janus-gateway -b $JANUS_VERSION /src; \
        /src/autogen.sh; \
        /src/configure; \
        make; \
        make install; \
        make configs; \
        rename -v ".jcfg.sample" ".jcfg" /usr/local/etc/janus/*.jcfg.sample

FROM coturn/coturn:4.6.2-alpine
USER root
# Pin alpine version manually as long as https://github.com/coturn/coturn/issues/1226 is not done
ARG ALPINE_VERSION=3.18

RUN set -ex; \
    grep VERSION_ID /etc/os-release | grep -q "$ALPINE_VERSION.[0-9]\+$"; \
    apk add --no-cache \
        ca-certificates \
        tzdata \
        bash \
        openssl \
        supervisor \
        bind-tools \
        netcat-openbsd \
        \
        glib zlib libssl3 libcrypto3 jansson libnice libconfig libsrtp \
        libwebsockets \
        \
        shadow; \
    useradd --system talk; \
    apk del --no-cache \
        shadow; \
    \
# Give root a random password
    echo "root:$(openssl rand -base64 12)" | chpasswd; \
    \
    touch \
        /etc/nats.conf \
        /etc/signaling.conf \
        /etc/turnserver.conf; \
    echo "listen: 127.0.0.1:4222" | tee /etc/nats.conf; \
    mkdir -p \
        /var/tmp \
        /var/lib/turn \
        /var/log/supervisord \
        /var/run/supervisord \
        /usr/local/lib/janus/loggers; \
    chown talk:talk -R \
        /usr \
        /etc/nats.conf \
        /etc/signaling.conf \
        /etc/turnserver.conf \
        /var/lib/turn \
        /var/log/supervisord \
        /var/run/supervisord;

COPY --from=janus /usr/local /usr/local
COPY --from=nats /nats-server /usr/local/bin/nats-server
COPY --from=signaling /usr/bin/nextcloud-spreed-signaling /usr/local/bin/nextcloud-spreed-signaling

COPY --chmod=775 start.sh /start.sh
COPY --chmod=664 supervisord.conf /supervisord.conf

# Set default talk port https://github.com/nextcloud/all-in-one/issues/1011
ENV TALK_PORT=3478

USER talk
ENTRYPOINT ["/start.sh"]
CMD ["supervisord", "-c", "/supervisord.conf"]

HEALTHCHECK CMD (nc -z localhost 8081 && nc -z localhost 8188 && nc -z localhost 4222 && nc -z localhost "$TALK_PORT" && nc -z "$NC_DOMAIN" "$TALK_PORT") || exit 1
LABEL com.centurylinklabs.watchtower.enable="false"

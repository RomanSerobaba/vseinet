FROM manticoresearch/manticore

RUN apk --no-cache add shadow
RUN apk --no-cache add su-exec

RUN adduser -D -h /home/manticore -u 1000 -s /bin/sh manticore manticore

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]

CMD [ "/usr/bin/searchd", "--nodetach" ]

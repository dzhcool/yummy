# dockerfile

FROM 192.168.40.5:5000/base/alpine:latest
MAINTAINER dangzihao dzhcool@gmail.com

RUN mkdir -p /app/bin && mkdir -p /app/conf && mkdir -p /app/data/log && mkdir -p /storage

WORKDIR /app


ADD search_api ./bin
ADD app.ini ./conf
ADD version.txt ./conf

ENV BAA_ROOT .

EXPOSE 80

CMD ["./bin/search_api"]

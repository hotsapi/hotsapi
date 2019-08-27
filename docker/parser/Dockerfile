# building heroprotocol -> parser -> hotsapi sequentially in a single container
# FROM ubuntu:16.04
FROM hotsapi/heroprotocol

RUN apt update && \
	  apt install -y git curl apt-transport-https && \
	  rm -rf /var/lib/apt/lists/*
RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/microsoft.gpg
RUN echo "deb [arch=amd64] https://packages.microsoft.com/repos/microsoft-ubuntu-xenial-prod xenial main" > /etc/apt/sources.list.d/dotnetdev.list
RUN apt update && \
	  apt install -y dotnet-runtime-2.0.0 && \
	  rm -rf /var/lib/apt/lists/*

WORKDIR /opt/hotsapi-parser
RUN curl https://s3-eu-west-1.amazonaws.com/hotsapi-public/Hotsapi.Parser/master/latest/parser.tar.gz > parser.tar.gz && \
    tar -xzf parser.tar.gz && \
    rm parser.tar.gz
RUN ln -s /opt/hotsapi-parser/parser.sh /usr/bin/hotsapi-parser
ENTRYPOINT ['hotsapi-parser']

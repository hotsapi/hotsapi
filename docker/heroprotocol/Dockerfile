# building heroprotocol -> parser -> hotsapi sequentially in a single container
FROM ubuntu:16.04

RUN apt update && \
	apt install -y python git && \
	rm -rf /var/lib/apt/lists/*

WORKDIR /opt/heroprotocol
RUN git clone https://github.com/Blizzard/heroprotocol.git /opt/heroprotocol
RUN chmod a+x /opt/heroprotocol/heroprotocol.py
RUN ln -s /opt/heroprotocol/heroprotocol.py /usr/bin/heroprotocol
ENTRYPOINT ['heroprotocol']

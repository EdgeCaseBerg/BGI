#Configurations and setup
CC = cc
CFLAGS = -std=gnu99 -pedantic -Wall -Wextra -Werror -g -I../headers
LINKFLAGS = $(CFLAGS)
LIBS = ../lib/wolkykim-qdecoder-63888fc/src/libqdecoder.a

TARGETS = heartbeat.cgi login.cgi
UTIL = fasthash ctl

#Use Phony to keep clean
.PHONY: clean 

OBJECTS := $(patsubst src/%.c,obj/%.o,$(wildcard src/*.c))
TARGETS := $(patsubst src/%.c,bin/%.cgi,$(wildcard src/*.c))
     
#Commands to help test and run programs:	
valgrind = valgrind --tool=memcheck --leak-check=yes --show-reachable=yes --num-callers=20 --track-fds=yes

all: copyobjects ${TARGETS} 

$(TARGETS): $(OBJECTS) ../obj/*.o
	${CC} ${LINKFLAGS} -g -o $@ $(patsubst bin/%.cgi, obj/%.o, $@ ) $(patsubst %, obj/%.o, $(UTIL)) ${LIBS}

clean:
	rm -f obj/*.o ${TARGETS}

copyobjects:
	cp ../obj/*.o obj/
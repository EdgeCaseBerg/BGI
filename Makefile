#Configurations and setup
CC = cc
CFLAGS = -std=gnu99 -pedantic -Wall -Wextra -Werror -g
LIBS = lib/wolkykim-qdecoder-63888fc/src/libqdecoder.a

TARGETS = heartbeat.cgi

#Use Phony to keep clean
.PHONY: clean 

#Commands to help test and run programs:	
valgrind = valgrind --tool=memcheck --leak-check=yes --show-reachable=yes --num-callers=20 --track-fds=yes

all: ${TARGETS}

heartbeat.cgi: obj/heartbeat.o
	${CC} ${CFLAGS} -o bin/$@ obj/heartbeat.o ${LIBS}

clean:
	rm -f obj/*.o ${TARGETS}

src/%.c : obj/%.o
	${CC} ${CFLAGS} -c -o $@ $<

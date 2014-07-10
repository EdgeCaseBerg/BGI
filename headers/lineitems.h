#ifndef __LINEITEMS_H__
#define __LINEITEMS_H__

#include <time.h>

struct lineitem {
	time_t date;
	char name[BUFFER_LENGTH]; /* Magic number, sue me later */
	double amount;
	double latitude;
	double longitude;
};

/* Simple Linked List For accounts */
struct lineItemChain {
	struct lineitem * data; 
	struct lineItemChain * next;
};



#endif
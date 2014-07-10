#ifndef __LINEITEMS_H__
#define __LINEITEMS_H__

#include <time.h>

struct lineitem {
	time_t date;
	char  * name;
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
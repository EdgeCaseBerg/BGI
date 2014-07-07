#ifndef __ACCOUNTS_H__
#define __ACCOUNTS_H__

struct account {
	int id;
	char name[64]; /* Magic number, sue me later */
	double balance;
};

/* Simple Linked List For accounts */
struct accountChain {
	struct account * data; 
	struct accountChain * next;
};



#endif
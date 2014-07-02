#ifndef __REQUIRED_H__
#define __REQUIRED_H__

#ifdef ENABLE_FASTCGI
	#include "fcgi_stdio.h"
#else
	#include <stdio.h>
#endif
#include <stdlib.h>
#include <stdbool.h>
#include "qdecoder.h"

#include "config.h"

#endif
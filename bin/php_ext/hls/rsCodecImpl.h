
#ifndef _RS_CODEC_IMPL_H_
#define _RS_CODEC_IMPL_H_

#ifndef MIN
#define MIN(a,b) (((a)<(b))?(a):(b))
#endif

#define DTYPE unsigned char

typedef struct tagRS
{
	int mm;
	int nn;
	DTYPE *alpha_to;
	DTYPE *index_of;
	DTYPE *genpoly;
	DTYPE *buff;
	int nroots;
	int fcr;
	int prim;
	int iprim;
	int pad;
}RS_S;

static __inline int modnn( RS_S *rs, int x )
{
	while (x >= rs->nn)
	{
		x -= rs->nn;
		x = (x >> rs->mm) + (x & rs->nn);
	}
	return x;
}
#define MODNN(x) modnn(rs,x)

#define MM (rs->mm)
#define NN (rs->nn)
#define ALPHA_TO (rs->alpha_to) 
#define INDEX_OF (rs->index_of)
#define GENPOLY (rs->genpoly)
#define NROOTS (rs->nroots)
#define FCR (rs->fcr)
#define PRIM (rs->prim)
#define IPRIM (rs->iprim)
#define PAD (rs->pad)
#define A0 (NN)

#endif
